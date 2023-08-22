<?php
namespace vbac;

use itdq\DbTable;
use itdq\FormClass;
use itdq\Loader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbRecord;


class assetRequestsTable extends DbTable{

    const RETURN_WITH_BUTTONS = true;
    const RETURN_WITHOUT_BUTTONS = false;

    public $currentVarb;
    public $assetRequestEventsTable;

    private $lastSql;

    private $preparedUpdateUidsStmt;
    private $preparedSetRequestOrderItStatus;
    private $preparedUpdateComment;
    private $preparedOrderitResponded;
    private $preparedGetComment;
    private $preparedRefToOrderIt;
    private $preparedDevarb;

    private static $portalHeaderCells = array(
        'REFERENCE',
        'CT_ID',
        'PERSON',
        'ASSET',
        'STATUS',
        'JUSTIFICATION',
        'REQUESTOR',
        'APPROVER','FM',
        'LOCATION'
        ,'PRIMARY_UID',
        'SECONDARY_UID',
        'DATE_ISSUED_TO_IBM',
        'DATE_ISSUED_TO_USER',
        'DATE_RETURNED',
        'LBG_VARB_REF',
        'LBG_REF_NUMBER',
        'LBG_STATUS',
        'LBG_TYPE',
        'COMMENT',
        'USER_CREATED',
        'REQUESTEE_EMAIL',
        'REQUESTEE_NOTES',
        'APPROVER_EMAIL', 
        'FM_EMAIL',
        'FM_NOTES',
        'CTB_RTB',
        'TT_BAU',
        'LOB', 
        'WORK_STREAM',
        'PRE_REQ_REQUEST',
        'REQUEST_RETURN'
    );

    private static $statusChangeEmail = "This is to inform you that your vBAC Request<b>&&requestReference&& (&&assetTitle&&)</b> has now moved to status :<b>&&status&&</b>.
<br/>The associated comment is :
<br/>&&comment&&
<br/>Reference Information : Request:<b>&&requestReference&&</b> Varb:<b>&&varbNumber&&</b> LBG Number:<b>&&orderItNumber&&</b> Vbac Status:<b>&&vbacStatus&&</b> </b> Order IT Status:<b>&&orderItStatus&&";

    private static $statusChangeEmailPattern = array('/&&requestReference&&/','/&&assetTitle&&/','/&&status&&/','/&&comment&&/','/&&varbNumber&&/','/&&orderItNumber&&/','/&&vbacStatus&&/','/&&orderItStatus&&/');

    function __construct($table,$pwd=null,$log=null){
        parent::__construct($table,$pwd,$log);
        $this->assetRequestEventsTable = new assetRequestsEventsTable(allTables::$ASSET_REQUESTS_EVENTS);
    }

    function saveRecord(DbRecord $record,$populatedColumns=true,$nullColumns=true,$commit=true){
        $created = parent::saveRecord($record,$populatedColumns,$nullColumns,false);

        if($created){
            $requestReference = $this->lastId();
            $this->assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_CREATED, $requestReference);
        }
        if($commit){
            $this->commitUpdates();
        }
    }

    static function portalHeaderCells(){
        $headerCells = null;
         foreach (self::$portalHeaderCells as $key => $value) {
                $headerCells .= "<th>";
                $headerCells .= str_replace("_", " ", $value);
                $headerCells .= "</th>";
         }
         return $headerCells;
    }

    static function returnForPortal($predicate=null,$withButtons=true){
        $loader = new Loader();
        $myCnum = personTable::myCnum();
        $amADelegateForRaw = $loader->load('EMAIL_ADDRESS',allTables::$DELEGATE," DELEGATE_CNUM='" . htmlspecialchars($myCnum) . "' ");
        $amADelegateFor = array_map('strtolower',$amADelegateForRaw);

        $sql  = " SELECT distinct";
//        $sql .= " concat('000000',AR.REQUEST_REFERNCE) as car,";
        $sql .= " AR.REQUEST_REFERENCE as reference, ";
        $sql .= " P.CT_ID as CT_ID, P.EMAIL_ADDRESS as REQUESTEE_EMAIL, P.NOTES_ID as REQUESTEE_NOTES, AR.ASSET_TITLE as ASSET, STATUS, ";
        $sql .= " BUSINESS_JUSTIFICATION as JUSTIFICATION, REQUESTOR_EMAIL as REQUESTOR_EMAIL, REQUESTED as REQUESTED_DATE,  ";
        $sql .= " APPROVER_EMAIL, APPROVED as APPROVED_DATE, ";
        $sql .= " F.EMAIL_ADDRESS as FM_EMAIL, F.NOTES_ID as FM_NOTES, P.FM_CNUM,";
        $sql .= " USER_LOCATION as LOCATION, ";
        $sql .= " PRIMARY_UID, SECONDARY_UID, DATE_ISSUED_TO_IBM, DATE_ISSUED_TO_USER, DATE_RETURNED,   ";
        $sql .= " ORDERIT_VARB_REF, ORDERIT_NUMBER, ORDERIT_STATUS, ";
        $sql .= " RAL.ORDER_IT_TYPE as ORDERIT_TYPE ";
        $sql .= " ,RAL.ASSET_PRIMARY_UID_TITLE ";
        $sql .= " ,RAL.ASSET_SECONDARY_UID_TITLE ";
        $sql .= " , COMMENT ";
        $sql .= " , REQUEST_RETURN ";
        $sql .= " , USER_CREATED ";
        $sql .= " , P.CTB_RTB,P.TT_BAU,P.LOB, P.WORK_STREAM ";
        $sql .= " , PRE_REQ_REQUEST ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON P.FM_CNUM = F.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON TRIM(RAL.ASSET_TITLE) = TRIM(AR.ASSET_TITLE) ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE . " as D "; // needed for the predicate.
        $sql .= " ON F.CNUM = D.CNUM ";
        $sql .= " WHERE 1=1 ";
        $sql .=  $predicate;

    //    AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);
        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            var_dump($sql);
            return false;
        }

        $data = array();

        while(($preTrimmed=sqlsrv_fetch_array($rs))==true){

            $row = array_map('trim', $preTrimmed);

            $userRaised = strtoupper($row['USER_CREATED'])=='YES';
            $approved   = $row['STATUS'] == assetRequestRecord::STATUS_APPROVED;

            $reference = trim($row['REFERENCE']);
            $preReq = !empty(trim($row['PRE_REQ_REQUEST']))  ?  trim($row['PRE_REQ_REQUEST']): null;
            $sortableReference = substr('0000000'.$reference,-6);

            $startItalics = $row['USER_CREATED']==assetRequestRecord::CREATED_USER ? "<i>" : null;
            $endItalics = $row['USER_CREATED']==assetRequestRecord::CREATED_USER ? "</i>" : null;

            $justificationEditAllowed = $row['STATUS'] == assetRequestRecord::STATUS_CREATED || $row['STATUS'] == assetRequestRecord::STATUS_REJECTED;

            $isRequestor = (strtolower(trim($row['REQUESTOR_EMAIL'])) == strtolower(trim($_SESSION['ssoEmail'])) or (in_array(strtolower(trim($row['REQUESTOR_EMAIL'])), $amADelegateFor)));
            $isRequestee = (strtolower(trim($row['REQUESTEE_EMAIL'])) == strtolower(trim($_SESSION['ssoEmail'])) or (in_array(strtolower(trim($row['REQUESTEE_EMAIL'])), $amADelegateFor)));
            $isApprover  = (strtolower(trim($row['APPROVER_EMAIL']))  == strtolower(trim($_SESSION['ssoEmail'])) or (in_array(strtolower(trim($row['APPROVER_EMAIL'])), $amADelegateFor)));

            $row['REFERENCE'] =  $startItalics . trim($row['ORDERIT_NUMBER']) . ":" . $reference;
            $row['REFERENCE'] .= !empty($preReq) ? "<small> requires </small>" . $preReq : null;
            $row['REFERENCE'] .= $endItalics;

            $row['REFERENCE'] = empty($row['ORDERIT_VARB_REF']) ? array('display'=>$row['REFERENCE'],'reference'=>$sortableReference) : array('display'=>$row['REFERENCE'] . "<br/><small>" . $row['ORDERIT_VARB_REF'] . "</small>",'reference'=>$sortableReference);

            $status = trim($row['STATUS']);
            $orderItStatus = trim($row['ORDERIT_STATUS']);
            $statusWithOitStatus = trim($row['ORDERIT_STATUS']) != null ? $status . " (" . trim($row['ORDERIT_STATUS']) . ") " : $status;

            $approveButton  = "<button type='button' class='btn btn-default btn-xs btnAssetRequestApprove btn-success' aria-label='Left Align' ";
            $approveButton .= "data-reference='" .trim($reference) . "' ";
            $approveButton .= "data-requestee='" .trim($row['REQUESTEE_EMAIL']) . "' ";
            $approveButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $approveButton .= "data-orderitstatus='".trim($row['ORDERIT_STATUS']) . "' ";
            $approveButton .= "data-status='".trim($row['STATUS']) . "' ";
            $approveButton .= "data-ispmo='".$_SESSION['isPmo'] . "' ";
            $approveButton .= "data-toggle='tooltip' data-placement='top' title='Approve the request'";
            $approveButton .= " > ";
            $approveButton .= "<span class='glyphicon glyphicon-ok ' aria-hidden='true'></span>";
            $approveButton .= " </button> ";

            $approveButton = $withButtons ? $approveButton : '';

            $rejectButton  = "<button type='button' class='btn btn-default btn-xs btnAssetRequestReject btn-danger' aria-label='Left Align' ";
            $rejectButton .= "data-reference='" .trim($reference) . "' ";
            $rejectButton .= "data-requestee='" .trim($row['REQUESTEE_EMAIL']) . "' ";
            $rejectButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $rejectButton .= "data-orderitstatus='".trim($row['ORDERIT_STATUS']) . "' ";
            $rejectButton .= "data-toggle='tooltip' data-placement='top' title='Reject the request'";
            $rejectButton .= " > ";
            $rejectButton .= "<span class='glyphicon glyphicon-remove ' aria-hidden='true'></span>";
            $rejectButton .= " </button> ";

            $rejectButton = $withButtons ? $rejectButton : '';
            $rejectButton = $userRaised & $approved  ? '' : $rejectButton;

            $amendOITButton  = "<button type='button' class='btn btn-default btn-xs btnAmendOrderItNumber btn-warning' aria-label='Left Align' ";
            $amendOITButton .= "data-reference='" .trim($reference) . "' ";
            $amendOITButton .= "data-orderit='".trim($row['ORDERIT_NUMBER']) . "' ";
            $amendOITButton .= "data-toggle='tooltip' data-placement='top' title='Amend LBG Number'";
            $amendOITButton .= " > ";
            $amendOITButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $amendOITButton .= " </button> ";

            $amendOITButton = $withButtons && $_SESSION['isPmo'] && !empty($row['ORDERIT_NUMBER']) ? "&nbsp;" . $amendOITButton : '';

            $row['ORDERIT_NUMBER'] = $amendOITButton . $row['ORDERIT_NUMBER'];

            $pmoOrFm = ($_SESSION['isFm'] || $_SESSION['isPmo']);
            $notTheirOwnRecord = ( trim(strtolower($row['REQUESTEE_EMAIL'])) != trim(strtolower($_SESSION['ssoEmail'])));

            $allowedtoApproveReject = $withButtons && $pmoOrFm && $notTheirOwnRecord;
            $allowedtoReject = $withButtons && !$notTheirOwnRecord;

            switch (true) {
                case $status == assetRequestRecord::STATUS_APPROVED:
                    $rejectable = true;
                    $approvable = false;
                break;
                case $status == assetRequestRecord::STATUS_CREATED:
                    $rejectable = true;
                    $approvable = true;
                    break;
                case $status == assetRequestRecord::STATUS_REJECTED:
                    $rejectable = false;
                    $approvable = true;
                    break;
                case $status == assetRequestRecord::STATUS_AWAITING_IAM:
                    $rejectable = true;
                    $approvable = $_SESSION['isPmo']; // Only PMO get the approve button now.
                    break;

                default:
                    $rejectable = false;
                    $approvable = false;
                break;
            }

            switch (true) {
                case $allowedtoApproveReject && $rejectable && $approvable:
                    $row['STATUS'] =  $rejectButton . $approveButton . $statusWithOitStatus;
                    break;
                case $allowedtoApproveReject && $approvable && !$rejectable:
                    $row['STATUS'] =  $approveButton . $statusWithOitStatus;
                    break;
                case $allowedtoApproveReject && $rejectable && !$approvable:
                case $allowedtoReject && $rejectable:
                    $row['STATUS'] =  $rejectButton . $statusWithOitStatus;
                    break;
                default:
                    $row['STATUS'] =  $statusWithOitStatus;
                break;
            }

            $row['PERSON'] = $row['REQUESTEE_NOTES'] . "<br/><small>" . $row['REQUESTEE_EMAIL'] . "</small>";

            if(strtolower(trim($row['FM_EMAIL'])) == (strtolower(trim($row['APPROVER_EMAIL'])))){
               $indicatorIfApproverIsFm = "<span class='bg-success'>&nbsp;";
            } else {
               $indicatorIfApproverIsFm = "<span class='bg-warning'>&nbsp;";
            }

            $indicatorIfApproverIsFm = $_SESSION['isPmo'] ? $indicatorIfApproverIsFm : "<span>&nbsp;";

            $row['APPROVER'] = $indicatorIfApproverIsFm .  $row['APPROVER_EMAIL'] . "&nbsp;</span><br/><small>" . $row['APPROVED_DATE'] . "</small>";

            $requestedDate = new \DateTime($row['REQUESTED_DATE']);
            $sortableRequestedDate =  $requestedDate->format('YmdHis');

            $row['REQUESTOR'] = array('display'=> $row['REQUESTOR_EMAIL'] . "<br/><small>" . $row['REQUESTED_DATE'] . "</small>",'timestamp'=>$sortableRequestedDate);
            $row['FM'] = !empty($row['FM_NOTES']) ? $row['FM_NOTES'] . "<br/><small>" . $row['FM_EMAIL'] . "</small>" : $row['FM_CNUM'];

            $editUidButton  = "<button type='button' class='btn btn-default btn-xs btnEditUid btn-primary' aria-label='Left Align' ";
            $editUidButton .= "data-reference='" .$reference . "' ";
            $editUidButton .= "data-requestee='" .trim($row['PERSON']) . "' ";
            $editUidButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $editUidButton .= "data-primarytitle='".trim($row['ASSET_PRIMARY_UID_TITLE']) . "' ";
            $editUidButton .= "data-secondarytitle='".trim($row['ASSET_SECONDARY_UID_TITLE']) . "' ";
            $editUidButton .= "data-primaryuid='".trim($row['PRIMARY_UID']) . "' ";
            $editUidButton .= "data-secondaryuid='".trim($row['SECONDARY_UID']) . "' ";
            $editUidButton .= "data-toggle='tooltip' data-placement='top' title='Update UID'";
            $editUidButton .= " > ";
            $editUidButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $editUidButton .= " </button> ";

            $editUidButton = $withButtons ? $editUidButton : '';

            $pUid = trim($row['PRIMARY_UID']);
            $sUid = trim($row['SECONDARY_UID']);

            $primaryUid = empty($row['PRIMARY_UID']) ? "<i>unknown</i>" : $row['PRIMARY_UID'];

            $row['PRIMARY_UID'] = !empty($row['ASSET_PRIMARY_UID_TITLE']) ? $editUidButton . $primaryUid :  "<i>Not Applicable</i>";
            $row['SECONDARY_UID'] = !empty($row['ASSET_SECONDARY_UID_TITLE']) ? $row['SECONDARY_UID'] :  "<i>Not Applicable</i>";

            $asset = $row['ASSET'];

            $returnedButton  = "<button type='button' class='btn btn-default btn-xs btnAssetReturned btn-success' aria-label='Left Align' ";
            $returnedButton .= "data-reference='" .trim($reference) . "' ";
            $returnedButton .= "data-requestee='" .trim($row['PERSON']) . "' ";
            $returnedButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $returnedButton .= "data-primarytitle='".trim($row['ASSET_PRIMARY_UID_TITLE']) . "' ";
            $returnedButton .= "data-secondarytitle='".trim($row['ASSET_SECONDARY_UID_TITLE']) . "' ";
            $returnedButton .= "data-primaryuid='". $pUid . "' ";
            $returnedButton .= "data-secondaryuid='".$sUid . "' ";
            $returnedButton .= "data-toggle='tooltip' data-placement='top' title='Report asset returned/removed'";
            $returnedButton .= " > ";
            $returnedButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $returnedButton .= " </button> ";

            switch (true) {
                case $status== assetRequestRecord::STATUS_PROVISIONED:
                case $orderItStatus== assetRequestRecord::STATUS_ORDERIT_APPROVED:
                    $returnable = true;
                    break;
                case $row['USER_CREATED'] == assetRequestRecord::CREATED_USER && $status==assetRequestRecord::STATUS_RAISED_ORDERIT && $orderItStatus != assetRequestRecord::STATUS_ORDERIT_REJECTED:
                    $returnable = true;
                    break;
                default:
                    $returnable = false;
                break;
            }

            $returnedButton = $returnable && $status!= assetRequestRecord::STATUS_RETURNED ? $returnedButton : null;

//             $row['ASSET'] =  ($returnable && $row['REQUEST_RETURN']!='Yes' ) ? $returnedButton . "&nbsp;<i>" .  $asset . "(Return/Remove)</i>" : $asset;

//             $row['ASSET'] .= $row['REQUEST_RETURN']=='Yes' ? "&nbsp;<small>(Return Request)</small>" : "";

            /*
             * Allow requestor and approver to add to the Justification Text
             */

            $canAddToJusification = $isApprover || $isRequestor || $isRequestee;

            $justification = trim($row['JUSTIFICATION']);

            $justificationButton = "<button type='button' class='btn btn-default btn-xs btnAddToJustification btn-success' aria-label='Left Align' ";
            $justificationButton .= "data-reference='" .trim($reference) . "' ";
            $justificationButton .= "data-requestee='" .trim($row['PERSON']) . "' ";
            $justificationButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $justificationButton .= "data-status='"     .trim($status) . "' ";

//             $justificationButton .= "data-justification='" .trim($justification) . "' ";
//             $justificationButton .= "data-comment='" .trim($row['COMMENT']) . "' ";
            $justificationButton .= "data-toggle='tooltip' data-placement='top' title='Amend Justification'";
            $justificationButton .= " > ";
            $justificationButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $justificationButton .= " </button> ";

            $row['JUSTIFICATION'] = $canAddToJusification && $justificationEditAllowed ? $justificationButton . "&nbsp;" . $justification : $justification;
            $row['JUSTIFICATION'] .= "<hr/>" . $row['COMMENT'];

            $data[] = $row;
        }

        return array('data'=>$data,'sql'=>$sql);
    }

    private function getNextVarb(){
        $sql  = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ORDER_IT_VARB_TRACKER;
        $sql .= " ( CREATED_BY ) VALUES ('" . htmlspecialchars($_SESSION['ssoEmail']) . "' )" ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $varbRef = db2_last_insert_id($_SESSION ['conn']);

        $nextVarb = 'vARB' . substr('000000' . $varbRef ,-5);
        $this->currentVarb = $nextVarb;
        return $nextVarb;
    }

    public function eligibleForOrderItPredicate($orderItType=0){
        /*
         *   ORDERIT_VARB_REF is null - Has not previously been exported.
         *   ORDER_IT_NUMBER is null  - Hasn't already been raised by the individual
         *   RAL.ORDER_IT_TYPE = '" . htmlspecialchars($orderItGroup) . "'  - the ASSET_TITLE has a TYPE that matches the type we're processing
         *   AR.STATUS='" . assetRequestRecord::STATUS_APPROVED . "'  - It's approved for processing
         *
         *   '" . htmlspecialchars($orderItType) . "' == 1  - It's a TYPE1 - ie it doesn't need a CT ID
         *   or P.CONTRACTOR_ID is not null                  - It's not a TYPE 1 - so it does need a CT ID, so CONTRACTOR ID can't be empty.
         *
         *
         */
        $predicate  = "";
        $predicate .= "   AND ORDERIT_VARB_REF is null and ORDERIT_NUMBER is null and RAL.ORDER_IT_TYPE = '" . htmlspecialchars($orderItType) . "' AND AR.STATUS='" . assetRequestRecord::STATUS_APPROVED . "' ";
        $predicate .= "   AND ('" . htmlspecialchars($orderItType) . "' = '1' or P.CT_ID is not null)";
        $predicate .= $this->predicateForPmoExportableRequest();

        return $predicate;
    }


    function getRequestsForOrderIt($orderItType, $first=false, $predicate = null ){


        $nextVarb = $this->getNextVarb();

        $commitState  = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

        // Get the Ref's for this export - so we can timestamp the export.

        $sql = " SELECT REQUEST_REFERENCE ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql.= " WHERE REQUEST_REFERENCE in ";
        $sql.= " (SELECT REQUEST_REFERENCE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql.= "  LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql.= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql.= "  LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= "  ON AR.CNUM = P.CNUM ";
        $sql.= "   WHERE 1=1 ";
        $sql.= !empty($predicate) ? $predicate : null;
        $sql.= $this->eligibleForOrderItPredicate($orderItType);
        $sql.= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql.= "   FETCH FIRST 20 ROWS ONLY) ";

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        while (($row=sqlsrv_fetch_array($rs))==true) {
            $this->assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_EXPORTED, $row['REQUEST_REFERENCE']);
        }

        $sql =  "UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql .= " SET ORDERIT_VARB_REF = '$nextVarb', STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";
        $sql .= " WHERE REQUEST_REFERENCE in ";
        $sql .= " (SELECT REQUEST_REFERENCE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= "  LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= "  LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= "  ON AR.CNUM = P.CNUM ";
        $sql .= "   WHERE 1=1 ";
        $sql .= !empty($predicate) ? $predicate : null;
        $sql .= $this->eligibleForOrderItPredicate($orderItType);
        $sql .= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql .= "   FETCH FIRST 20 ROWS ONLY) ";

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->lastSql = $sql;

        $sql = " SELECT ORDERIT_VARB_REF, REQUEST_REFERENCE, ";
        $sql .= " P.CT_ID as CT_ID, ";
        $sql .= " P.CTB_RTB as CTB_RTB, ";
        $sql .= " P.TT_BAU as TT_BAU, ";
        $sql .= " P.LOB as LOB, ";
        $sql .= " P.WORK_STREAM as WORK_STREAM, ";
        $sql .= " ASSET_TITLE, ";
        $sql .= " CASE when P.EMAIL_ADDRESS is null then P.NOTES_ID else P.EMAIL_ADDRESS end as IDENTITY, ";
        $sql .= " case when BUSINESS_JUSTIFICATION is null then 'N/A' else BUSINESS_JUSTIFICATION end as JUSTIFICATION, ";
        $sql .= " STATUS,  USER_LOCATION, REQUESTOR_EMAIL, date(REQUESTED) as REQUESTED,  APPROVER_EMAIL, DATE(APPROVED) as APPROVED,";
        $sql .= " F.EMAIL_ADDRESS as FM_EMAIL, ";
        $sql .= " current date as EXPORTED ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";


        $sql .= " WHERE ORDERIT_VARB_REF = '" . $nextVarb . "' ";
        $sql .= " ORDER BY ASSET_TITLE, REQUEST_REFERENCE desc";

        $data = array();
        $data[] = $first ? '"VARB","REQUEST","CT ID","CTB/RTB","TT/BAU","LOB","WORK STREAM","ASSET TITLE","REQUESTEE EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","FM EMAIL","EXPORTED"' : null;

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        while(($row=sqlsrv_fetch_array($rs2))==true){
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }

        $requestData = '';
        foreach ($data as $request){
            $requestData .= $request . "\n";
        }

//         $base64Encoded = base64_encode($requestData);

        sqlsrv_commit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],$commitState);

        return $requestData;
    }

    function getRequestsForNonPmo(){
        $commitState  = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

        $sql = " SELECT 'User Created' as ORDERIT_VARB_REF, ORDERIT_NUMBER, REQUEST_REFERENCE, ";
        $sql .= " P.CT_ID as CT_ID, ";
        $sql .= " P.CTB_RTB as CTB_RTB, ";
        $sql .= " P.TT_BAU as TT_BAU, ";
        $sql .= " P.LOB as LOB, ";
        $sql .= " P.WORK_STREAM as WORK_STREAM, ";
        $sql .= " ASSET_TITLE, ";
        $sql .= " CASE when P.EMAIL_ADDRESS is null then P.NOTES_ID else P.EMAIL_ADDRESS end as IDENTITY, ";
        $sql .= " case when BUSINESS_JUSTIFICATION is null then 'N/A' else BUSINESS_JUSTIFICATION end as JUSTIFICATION, ";
        $sql .= " STATUS,  USER_LOCATION, REQUESTOR_EMAIL, date(REQUESTED) as REQUESTED,  APPROVER_EMAIL, DATE(APPROVED) as APPROVED,";
        $sql .= " F.EMAIL_ADDRESS as FM_EMAIL, ";
        $sql .= " current date as EXPORTED ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";


        $sql .= " WHERE 1=1 ";
        $sql .= $this->predicateExportNonPmoRequests();

        $sql .= " ORDER BY ORDERIT_NUMBER desc, ASSET_TITLE, REQUEST_REFERENCE desc";

        $data = array();
        $data[] = '"VARB","LBG","REQUEST","CT ID","CTB/RTB","TT/BAU","LOB","WORK STREAM","ASSET TITLE","REQUESTEE EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","FM EMAIL","EXPORTED"';

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        while(($row=sqlsrv_fetch_array($rs2))==true){
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }

        $requestData = '';
        foreach ($data as $request){
            $requestData .= $request . "\n";
        }

        sqlsrv_commit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],$commitState);

        return $requestData;
    }


    function countRequestsForNonPmoExport(){
        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";

        $sql .= " WHERE 1=1 ";
        $sql .= $this->predicateExportNonPmoRequests();


        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function countRequestsAll(){

        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function countRequestsAwaitingIam(){

        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " WHERE STATUS='" . assetRequestRecord::STATUS_AWAITING_IAM . "' ";

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function countRequestsForPmoExport($bauRequest){

        $bau = false;
        $nonBau = false;

        if(!empty($bauRequest)){
            $bau = (strtolower(trim($bauRequest))=='true');
            $nonBau = (strtolower(trim($bauRequest))!='true');
        }

        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";
        $sql .= "  LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";

        $sql .= " WHERE 1=1 ";
        $sql .= $bau ? " AND P.TT_BAU='BAU' " : null;
        $sql .= $nonBau ? " AND ( P.TT_BAU!='BAU' or P.TT_BAU is null) " : null;

        $pred = $this->predicateForPmoExportableRequest();

        $sql.= $pred;

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function countRequestsExported(){

        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";

        $sql .= " WHERE 1=1 ";
        $sql .= " AND STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function countRequestsRaised($bau=true){

        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " WHERE 1=1 ";
        $sql .= " AND ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' ";
        $sql .= " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' ";
        $sql .= $bau ? " AND P.TT_BAU='BAU' " : " AND ( P.TT_BAU!='BAU' or P.TT_BAU is null )  ";

        $rs2 = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs2){
            sqlsrv_rollback($GLOBALS['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=sqlsrv_fetch_array($rs2);

        return $row['TICKETS'];
    }

    function predicateExportNonPmoRequests(){
        $predicate = " AND STATUS IN('" . assetRequestRecord::STATUS_APPROVED . "','" . assetRequestRecord::STATUS_RAISED_ORDERIT . "') AND ORDERIT_NUMBER is not NULL AND USER_CREATED='" . assetRequestRecord::CREATED_USER . "' ";
        $predicate .= " AND ORDERIT_STATUS in ('" . assetRequestRecord::STATUS_ORDERIT_RAISED . "') ";
        $predicate .= " AND ORDERIT_VARB_REF is null  AND APPROVED is not null";

        return $predicate;

    }

    function exportForOrderIT($orderItGroup = 0){
        $rows = $this->getRequestsForOrderIt($orderItGroup);
        return $rows;
    }

    function predicateForPmoExportableRequest() {
        $predicate  = " AND  ( ";
        $predicate .= " STATUS IN('" . assetRequestRecord::STATUS_APPROVED . "') AND ORDERIT_NUMBER is NULL AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' ";
        $predicate .= " AND ORDERIT_VARB_REF is null ";
        $predicate .= " AND (ORDER_IT_TYPE = '1' or P.CT_ID is not null)";
        $predicate .= " and ( pre_req_Request is null or pre_req_request  in (  select AR2.pre_req_request
						  	from " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR2
						  	left join " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR3
						  	on AR2.PRE_REQ_REQUEST = AR3.REQUEST_REFERENCE
						    where AR3.ORDERIT_STATUS in ('" . assetRequestRecord::STATUS_ORDERIT_APPROVED . "','" . assetRequestRecord::STATUS_PROVISIONED . "')
							) )";
        $predicate .= " )";
        return $predicate;
    }




    function countApprovedForOrderItType($orderItType = 0, $predicate = null){
        $sql  = " SELECT COUNT(*) as REQUESTS ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " WHERE 1=1 ";
        $sql .= !empty($predicate) ? $predicate : null;
        $sql .= $this->eligibleForOrderItPredicate($orderItType);

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->lastSql = $sql;

        $row = sqlsrv_fetch_array($rs);
        return $row['REQUESTS'];
    }

    function approveRejectModal(){
        ?>
       <!-- Modal -->
    <div id="approveRejectModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Approve/Reject</h4>
          </div>
          <div class="modal-body" >

          	<form class="form-horizontal" role="form" id='assetRequestApproveRejectForm' onSubmit='return false;' >
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="approveRejectRequestReference">Reference</label>
                    <div class="col-sm-10">
        				<input class='form-control' id='approveRejectRequestReference' name='approveRejectRequestReference'
                				value=''
                				type='text' disabled
                		>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="approveRejectRequestee" >Requestee</label>
                    <div class="col-sm-10">
       					<input class='form-control' id='approveRejectRequestee'  name='approveRejectRequestee'
                			   value=''
                			   type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="approveRejectAssetTitle" >Asset</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='approveRejectAssetTitle' name='approveRejectAssetTitle'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="<?=assetRequestRecord::STATUS_APPROVED?>" data-off="<?=assetRequestRecord::STATUS_REJECTED?>" id='assetRequestApprovalToggle' name='assetRequestApproval' value='Yes' data-onstyle='success' data-offstyle='warning'>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
						 <textarea class='form-control justification' rows='4' style='min-width: 100%' id='approveRejectRequestComment' name='approveRejectRequestComment' placeholder='Please provide comment if rejecting' min='0' max='500' ></textarea><span disabled>500 characters max</span>
                    </div>
                  </div>
                </form>
          </div>
          <div class='modal-footer'>
          		<?php
                $form = new FormClass();
                $allButtons = null;
                $confirmButton =  $form->formButton('submit','Submit','assetRequestApproveRejectConfirm',null,'Confirm','btn btn-primary');
                $allButtons[] = $confirmButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('assetRequestApproverRejector',$_SESSION['ssoEmail'],'assetRequestApproverRejector');
                $form->formHiddenInput('approveRejectRequestStatus','','approveRejectRequestStatus');
                $form->formHiddenInput('approveRejectRequestIsPmo','','approveRejectRequestIsPmo');
                ?>
	      		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          	</div>
          	<input type='hidden' val='' id='approveRejectRequestOrderItStatus' name='approveRejectRequestOrderItStatus' />
          </form>
          </div>
        </div>
      </div>
    <?php
    }

    function justificationEditModal(){
        ?>
       <!-- Modal -->
    <div id="justificationEditModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Edit Justification</h4>
          </div>
          <div class="modal-body" >

          	<form class="form-horizontal" role="form" id='editJustificationForm' onSubmit='return false;' >
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="editJustificationRequestReference">Reference</label>
                    <div class="col-sm-10">
        				<div class='div-like-input'  id='editJustificationRequestReference' name='editJustificationRequestReference' ></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="editJustificationRequestee" >Requestee</label>
                    <div class="col-sm-10">
       					<div  class='div-like-input' id='editJustificationRequestee'  name='editJustificationRequestee' ></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="editJustificationAssetTitle" >Asset</label>
                    <div class="col-sm-10">
          				<div class='div-like-input'  id='editJustificationAssetTitle' name='editJustificationAssetTitle'></div>
                    </div>
                  </div>
                  <div class="form-group">
                     <label  class="col-sm-2 control-label"
                              for="editJustificationComment">Comment</label>
                    <div class="col-sm-10">
						 <div class='div-like-input' id='editJustificationComment' name='editJustificationComment' ></div>
                    </div>
                  </div>
                  <div class="form-group">
                   <label  class="col-sm-2 control-label"
                              for="editJustificationJustification">Business Justification</label>
                    <div class="col-sm-10">
						 <textarea class='form-control justification' rows='4' style='min-width: 100%' id='editJustificationJustification' name='editJustificationJustification' placeholder='Supply business justification here' min='0' max='500' required ></textarea><span disabled>500 characters max</span>
                    </div>
                  </div>
				<input type='hidden' id='editJustificationStatus' name='editJustificationStatus' />



                </form>
          </div>
          <div class='modal-footer'>
          		<?php
                $form = new FormClass();
                $allButtons = null;
                $confirmButton =  $form->formButton('submit','Submit','editJustificationConfirm',null,'Confirm','btn btn-primary');
                $allButtons[] = $confirmButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('editJustificationEditor',$_SESSION['ssoEmail'],'editJustificationEditor');
                ?>
	      		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          	</div>
          </form>
          </div>
        </div>
      </div>
    <?php
    }

    function confirmReturnedModal(){
        $now = new \DateTime();
        $nowFormatted = $now->format('d M Y');
        $nowFormattedDb2 = $now->format('Y-m-d');
       ?>
       <!-- Modal -->
    	<div id="confirmReturnedModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Confirm Returned</h4>
            </div>
             <div class="modal-body" >
             	<form class="form-horizontal" role="form" id='confirmReturnedForm' onSubmit='return false;' >

				<div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="useridRet" >User</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='useridRet' name='userid'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="assetRet" >Asset</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='assetRet' name='asset'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>

                  <div class="form-group" id='primaryUidFormGroupRet' >
                  <label class="col-sm-2 control-label" id='primaryLabelRet'
                          for="primaryUidRet" >Primary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='primaryUidRet' name='primaryUid'
                			value=''
                			type='text'
                			placeholder='Primary UID'
                			>
                    </div>
                  </div>

                 <div class="form-group" id='secondaryUidFormGroupRet' style='display:none' >
                 <label class="col-sm-2 control-label" id='secondaryLabelRet'
                          for="secondaryUidRet" >Secondary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='secondaryUidRet' name='secondaryUid'
                			value=''
                			type='text'
                			placeholder='Secondary UID'
                			>
                    </div>
                  </div>
                 <div class="form-group" >
                 <label class="col-sm-2 control-label" id='dateReturnedLabel'
                          for="date_returned" >Date Returned</label>
                    <div class="col-sm-10">
          				<input class="form-control" id="date_returned" value="<?=$nowFormatted?>" type="text" placeholder='Date Asset Returned' data-toggle='tooltip' title='Date Returned' required>
          				<input class="form-control" id="date_returned_db2" name="DATE_RETURNED" value="<?=$nowFormattedDb2?>" type="hidden" required >

                    </div>
                  </div>



                  <input id='referenceRet' name='reference' value='' type='hidden' />
             	</form>
             </div>
             <div class='modal-footer'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton = $form->formButton('submit','Submit','confirmAssetReturned','enabled','Confirm','btn btn-success');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('user',$_SESSION['ssoEmail'],'user');
            ?>


             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
            </div>
        </div>
      </div>
    <?php
    }


    function exportResultsModal(){
        ?>
       <!-- Modal -->
    <div id="exportResultsModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Export Results</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
            </div>
        </div>
      </div>
    <?php
    }

    function editUidModal(){
        ?>
       <!-- Modal -->
    <div id="editUidModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Edit UID</h4>
            </div>
             <div class="modal-body" >
             	<form class="form-horizontal" role="form" id='editUidForm' onSubmit='return false;' >

				<div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="userid" >User</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='userid' name='userid'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="asset" >Asset</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='asset' name='asset'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>

                  <div class="form-group">
                  <label class="col-sm-2 control-label" id='primaryLabel'
                          for="asset" >Primary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='primaryUid' name='primaryUid'
                			value=''
                			type='text'
                			placeholder='Primary UID'
                			>
                    </div>
                  </div>
                 <div class="form-group" id='secondaryUidFormGroup' style='display:none' >
                 <label class="col-sm-2 control-label" id='secondaryLabel'
                          for="asset" >Secondary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='secondaryUid' name='secondaryUid'
                			value=''
                			type='text'
                			placeholder='Secondary UID'
                			>
                    </div>
                  </div>
                  <input id='reference' name='reference' value='' type='hidden' />
             	</form>
             </div>
             <div class='modal-footer'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton = $form->formButton('submit','Submit','saveEditUid','enabled','Save','btn btn-primary');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('user',$_SESSION['ssoEmail'],'user');
            ?>

             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
            </div>
        </div>
      </div>
    <?php
    }




    function mapVarbToOrderItModal(){
        ?>
       <!-- Modal -->
    <div id="mapVarbToOrderItModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
          <!-- Modal content-->
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Map vARB to OrderIt</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <div class='col-sm-1'>
             <button type="button" class="btn btn-xs btn-danger float-left" id='deVarb'  disabled >DeVarb Selected</button>
             </div>
             <div class='col-sm-8'>
             </div>
             <div class='col-sm-3'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton =   $form->formButton('submit','Submit','saveMapVarbToOrderIT','enabled','Save','btn btn-primary');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('mapper',$_SESSION['ssoEmail'],'mapper');
            ?>
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </div>
            </div>
        </div>
      </div>
    <?php
    }

    function mapVarbToOrderITForm(){
        $unmappedVarb = $this->getUnmappedVarb();
        $unmappedRef  = $this->getUnmappedRef();
        ?>
        <form id='mapVarbToOrderItForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Map VARB to LBG</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-3'>
                <select class='form-control select select2 '
                			  id='unmappedVarb'
                              name='unmappedVarb'
                      >
                    <option value=''></option>
                    <?php
                    foreach ($unmappedVarb as $varb){
                            ?><option value='<?=trim($varb);?>'><?=$varb?></option><?php
                        };
                        ?>
				</select>
            	</div>
            	<div class='col-sm-3'>
                <select class='form-control select select2 '
                			  id='unmappedRef'
                              name='unmappedRef'
                      >
                    <option value=''></option>
                    <?php
                    foreach ($unmappedRef as $ref){
                            ?><option value='<?=trim($ref);?>'><?=$ref?></option><?php
                        };
                        ?>
				</select>
            	</div>
        	</div>
        	<div class='form-group required'>
        	<div class='col-sm-12'>
        		<table class='table table-striped table-bordered '   style='width:90%' id='requestsWithinVarb'>
        		<thead><tr><th>Devarb</th><th>Ref</th><th>LBG</th><th>Requestee</th><th>Asset</th><th>Comment</th></tr></thead>
        		<tbody>
        		</tbody>
        		</table>
        	</div>
        </div>
        </div>
        <div class='panel-footer'>
        </div>
        </div>
        </form>
    	<?php
    }


    function amendOrderItModal(){
        ?>
        <!-- Modal -->
		<div id="amendOrderItModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Amend LBG</h4>
      			</div>

      			<div class="modal-body" >
          	<form class="form-horizontal" role="form" id='amendOrderItForm' onSubmit='return false;' >
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="amendOrderItRequestReference">Reference</label>
                    <div class="col-sm-10">
        				<input class='form-control' id='amendOrderItRequestReference' name='amendOrderItRequestReference'
                				value=''
                				type='text' disabled
                		>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="amendOrderItCurrent" >Current LBG</label>
                    <div class="col-sm-10">
       					<input class='form-control' id='amendOrderItCurrent'  name='amendOrderItCurrent'
                			   value=''
                			   type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="amendOrderItNewOrderIt" >New LBG</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='amendOrderItNewOrderIt' name='amendOrderItNewOrderIt'
                			value=''
                			type='text'
                			placeholder = 'Enter new LBG Number'
                			>
                    </div>
                  </div>
                </form>


      			</div>

      			<div class="modal-footer">
      				<button type="button" class="btn btn-success" id='confirmedSaveOrderIt'>Save</button>
      				<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>

      			</div>
    		</div>
  			</div>
		</div>
        <?php
    }


    function getUnmappedVarb(){
        $sql = " SELECT distinct ORDERIT_VARB_REF ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is null and STATUS in ('". assetRequestRecord::STATUS_EXPORTED . "','". assetRequestRecord::STATUS_RAISED_ORDERIT . "') ";
        $sql .= " ORDER BY ORDERIT_VARB_REF asc ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){
            $data[]=$row['ORDERIT_VARB_REF'];
        }
        return $data;
    }

    function getUnmappedref(){
        $sql = " SELECT distinct REQUEST_REFERENCE ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is null and STATUS in ('". assetRequestRecord::STATUS_EXPORTED . "','". assetRequestRecord::STATUS_RAISED_ORDERIT . "') ";
        $sql .= " ORDER BY REQUEST_REFERENCE asc ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){
            $data[]=$row['REQUEST_REFERENCE'];
        }
        return $data;
    }

    function getMappedVarb(){
        $sql = " SELECT distinct ORDERIT_VARB_REF ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is not null ";
        $sql .= " ORDER BY ORDERIT_VARB_REF asc ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){
            $data[]=$row['ORDERIT_VARB_REF'];
        }
        return $data;
    }

    function getMappedref(){
        $sql = " SELECT distinct REQUEST_REFERENCE ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_NUMBER is not null ";
        $sql .= " ORDER BY REQUEST_REFERENCE asc ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){
            $data[]=$row['REQUEST_REFERENCE'];
        }
        return $data;
    }

    function setOitStatusModal(){
        ?>
       <!-- Modal -->
    <div id="setOitStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Set OrderIt Status</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton =   $form->formButton('submit','Submit','saveOrderItStatus','enabled','Save','btn btn-primary');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('mapper',$_SESSION['ssoEmail'],'mapper');
            ?>
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
            </div>
        </div>
      </div>
    <?php
    }

    function setOrderItStatusForm(){
        $loader = new Loader();
        $restrictToActiveOrderIT = " ORDERIT_STATUS in ('" . assetRequestRecord::STATUS_ORDERIT_RAISED . "') ";
        $allOrderIt = $loader->load('ORDERIT_NUMBER',allTables::$ASSET_REQUESTS,$restrictToActiveOrderIT,true,'desc');
        $mappedVarb = $this->getMappedVarb();
        $mappedRef  = $this->getMappedRef();
        ?>
        <form id='setOrderItStatusForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Set LBG Status</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-3'>
                <select class='form-control select select2 '
                			  id='orderit'
                              name='orderit'
                      >
                    <option value=''></option>
                    <?php
                    foreach ($allOrderIt as $orderIt){
                            ?><option value='<?=trim($orderIt);?>'><?=$orderIt?></option><?php
                        };
                        ?>
				</select>
            	</div>
            	<div class='col-sm-3'>
                <select class='form-control select select2 '
                			  id='mappedVarb'
                              name='mappedVarb'
                      >
                    <option value=''></option>
                    <?php
                    foreach ($mappedVarb as $varb){
                            ?><option value='<?=trim($varb);?>'><?=$varb?></option><?php
                        };
                        ?>
				</select>
            	</div>

            	<div class='col-sm-3'>
                <select class='form-control select select2 '
                			  id='mappedRef'
                              name='mappedRef'
                      >
                    <option value=''></option>
                    <?php
                    foreach ($mappedRef as $ref){
                            ?><option value='<?=trim($ref);?>'><?=$ref?></option><?php
                        };
                        ?>
				</select>
            	</div>

         		<div class='col-sm-7'>
         		</div>
        	</div>

        	<div class='form-group required'>
        	<div class='col-sm-12'>
        		<table class='table table-striped table-bordered '   style='width:100%' id='requestsWithStatus'>
        		<thead><tr><th>Ref</th><th>Person</th><th>Asset</th><th>vbac<br/>Status</th><th>LBG<br/>Status</th><th>Primary UID</th><th>Comment</th><th>LBG Responded</th></tr></thead>
        		<tbody>
        		</tbody>
        		</table>
        	</div>
        </div>
        </div>
        <div class='panel-footer'>
        </div>
        </div>
        </form>
    	<?php
    }


    function getTracker(Spreadsheet $spreadsheet,$fullExtract = false){
        $recordsFound = false;
        $loader = new Loader();
        $fullStatus = $loader->load('ORDERIT_STATUS',allTables::$ASSET_REQUESTS," REQUEST_RETURN = 'No' or REQUEST_RETURN is null ");
        $lbgStatus = array(assetRequestRecord::STATUS_ORDERIT_RAISED=>assetRequestRecord::STATUS_ORDERIT_RAISED);
        $allStatus = $fullExtract ? $fullStatus : $lbgStatus;
        array_map('trim',$allStatus);
        $ctbOnly =  array(false,true);
        $sheet = 1;

        if(!empty($allStatus)){
            foreach ($ctbOnly as $isThisCtb){
                foreach ($allStatus as $key => $value) {
                    $sql = " SELECT AR.ORDERIT_NUMBER, AR.ORDERIT_STATUS,Ar.ORDERIT_VARB_REF, AR.REQUEST_REFERENCE, AR.ASSET_TITLE, AR.BUSINESS_JUSTIFICATION, AR.COMMENT ";
                    $sql.= " , AR.REQUESTOR_EMAIl, AR.REQUESTED, AR.APPROVER_EMAIL, AR.APPROVED, P.FIRST_NAME, P.LAST_NAME, P.EMAIL_ADDRESS, P.LBG_EMAIL, P.EMPLOYEE_TYPE ";
                    $sql.= " , P.CNUM, P.CT_ID, P.FM_CNUM as MGR_CNUM, FM.EMAIL_ADDRESS as MGR_EMAIL, FM.NOTES_ID as MGR_NOTESID, P.PES_STATUS, P.WORK_STREAM,P.CTB_RTB ";
                    $sql.= " ,P.TT_BAU, P.LOB, P.ROLE_ON_THE_ACCOUNT, P.CIO_ALIGNMENT,  AR.PRIMARY_UID, AR.SECONDARY_UID, AR.DATE_ISSUED_TO_IBM, AR. DATE_ISSUED_TO_USER  ";
                    $sql.= " ,AR.DATE_RETURNED  ";
                    $sql.= " FROM " . $GLOBALS['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as P ";
                    $sql.= " ON P.CNUM = AR.CNUM ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as FM ";
                    $sql.= " ON P.FM_CNUM = FM.CNUM ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$ORDER_IT_VARB_TRACKER . " as V ";
                    $sql.= " ON right(trim(AR.ORDERIT_VARB_REF),5) = right(concat('000000',V.VARB),5) ";
                    $sql.= " WHERE 1=1 ";
                    $sql.= " AND AR.ORDERIT_STATUS in ('" . assetRequestRecord::STATUS_ORDERIT_RAISED . "') ";
                    $sql.= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
                    $sql.= " AND ( ";
                    $sql.= "      ( USER_CREATED = 'No' AND AR.STATUS in ('" . assetRequestRecord::STATUS_RAISED_ORDERIT . "') )";
                    $sql.= "      OR ";
                    $sql.= "      ( USER_CREATED = 'Yes' AND (AR.APPROVED is not null AND AR.STATUS not in ('" . assetRequestRecord::STATUS_AWAITING_IAM . "') ) )";
                    $sql.= "    ) ";
                    $sql.= $isThisCtb ? " AND upper(P.CTB_RTB='CTB') " : " AND (upper(P.CTB_RTB != 'CTB') or P.CTB_RTB is null ) ";
                    $sql.= " ORDER BY AR.REQUESTED asc ";
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);

                    AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

                    if($rs){
                        $recordsFound = static::writeResultSetToXls($rs, $spreadsheet);

                        if($recordsFound){
                            static::autoFilter($spreadsheet);
                            static::autoSizeColumns($spreadsheet);
                            static::setRowColor($spreadsheet,'105abd19',1);
                        }
                    }

                    if(!$recordsFound){
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Warning");
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2,"No records found");
                    }
                    // Rename worksheet & create next.
                    $sheetTitle = $isThisCtb ? "CTB-$value" : "Non CTB-$value";
                    $spreadsheet->getActiveSheet()->setTitle($sheetTitle);
                    $spreadsheet->createSheet();
                    $spreadsheet->setActiveSheetIndex($sheet++);
                }
            }
        }
//         $nonPmo = self::countRequestsForNonPmoExport();

//         if($nonPmo > 0){
//         }

        return true;
    }


    function getFullExtract(Spreadsheet $spreadsheet,$fullExtract = false){
        $recordsFound = false;
        $loader = new Loader();
//         $fullStatus = $loader->load('ORDERIT_STATUS',allTables::$ASSET_REQUESTS," REQUEST_RETURN = 'No' or REQUEST_RETURN is null ");
//         $lbgStatus = array(assetRequestRecord::STATUS_ORDERIT_RAISED=>assetRequestRecord::STATUS_ORDERIT_RAISED);
//         $allStatus = $fullExtract ? $fullStatus : $lbgStatus;
//         array_map('trim',$allStatus);
//         $ctbOnly =  array(false,true);

        $age = array('Recent'=>' AR.REQUESTED > CURRENT TIMESTAMP - 3 MONTHS','>3 Months'=>' ( AR.REQUESTED <= CURRENT TIMESTAMP - 3 MONTHS )');

        $sheet = 1;

//         if(!empty($allStatus)){
//             foreach ($ctbOnly as $isThisCtb){
        foreach ($age as $ageTitle => $agePredicate) {
        $sql = " SELECT AR.*, P.FIRST_NAME, P.LAST_NAME, P.NOTES_ID, P.EMAIL_ADDRESS, P.LBG_EMAIL ";
        $sql.= ", CASE WHEN EM.DESCRIPTION IS NOT NULL THEN EM.DESCRIPTION ELSE P.EMPLOYEE_TYPE END AS EMPLOYEE_TYPE ";
        $sql.= ", P.CNUM, P.CT_ID, P.FM_CNUM as MGR_CNUM, FM.EMAIL_ADDRESS as MGR_EMAIL, FM.NOTES_ID as MGR_NOTESID, P.PES_STATUS, P.WORK_STREAM,P.CTB_RTB, P.TT_BAU, P.LOB, P.ROLE_ON_THE_ACCOUNT, P.CIO_ALIGNMENT, A.EMAIL_ADDRESS as APPROVER_EMAIL, A.NOTES_ID as APPROVER_NOTESID,A.WORK_STREAM as APPROVER_WORK_STREAM, A.TT_BAU as APPROVER_TT_BAU  ";
        $sql .= " FROM " . $GLOBALS['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as P ";
        $sql .= " ON P.CNUM = AR.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as FM ";
        $sql .= " ON P.FM_CNUM = FM.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as A ";
        $sql .= " ON lower(A.EMAIL_ADDRESS) = lower(AR.APPROVER_EMAIL) ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$EMPLOYEE_TYPE_MAPPING . " as EM ";
        $sql .= " ON upper(P.EMPLOYEE_TYPE) = upper(EM.CODE) ";



        $sql .= " WHERE 1=1 ";
        $sql .= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
        $sql .= " AND " . $agePredicate;
//         $sql .= "      ( AR.REQUESTED > CURRENT TIMESTAMP - 6 MONTHS )";
//         $sql .= "      OR ";
//         $sql .= "      ( AR.APPROVED > CURRENT TIMESTAMP - 6 MONTHS )";
//        $sql .= "    ) ";
        $sql .= " ORDER BY AR.REQUESTED desc ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);


        if($rs){
            $recordsFound = DbTable::writeResultSetToXls($rs, $spreadsheet);

            if($recordsFound){
                DbTable::autoFilter($spreadsheet);
                DbTable::autoSizeColumns($spreadsheet);
                DbTable::setRowColor($spreadsheet,'105abd19',1);
            }
        }

        if(!$recordsFound){
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Warning");
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2,"No records found");
        }
        // Rename worksheet & create next.
        $sheetTitle = $ageTitle;
        $spreadsheet->getActiveSheet()->setTitle($sheetTitle);
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet++);
        }
//             }
//         }
        //         $nonPmo = self::countRequestsForNonPmoExport();

        //         if($nonPmo > 0){
        //         }

        return true;
    }


    function getVarbTracker(Spreadsheet $spreadsheet,$fullExtract = false){
        $recordsFound = false;
        $loader = new Loader();
//        array_map('trim',$allStatus);
//        $ctbOnly =  array(false,true);
        $userCreated = array(false,true);
        $sheet = 1;

//         if(!empty($allStatus)){
        foreach ($userCreated as $uCreated  ) {
//            foreach ($ctbOnly as $isThisCtb){
//                 foreach ($allStatus as $key => $value) {
                    $sql = " SELECT AR.REQUEST_REFERENCE, AR.ORDERIT_NUMBER, AR.ORDERIT_VARB_REF, V.CREATED_DATE, V.CREATED_BY,AR.STATUS, AR.ORDERIT_STATUS ";
                    $sql.= ", AR.ASSET_TITLE, AR.BUSINESS_JUSTIFICATION, AR.COMMENT ";
                    $sql.= ", AR.REQUESTOR_EMAIl, AR.REQUESTED, AR.APPROVER_EMAIL, AR.APPROVED,  AR.PRIMARY_UID, AR.SECONDARY_UID ";
                    $sql.= ", ES.* ";
                    $sql.= " FROM " . $GLOBALS['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$PERSON . " as P ";
                    $sql.= " ON P.CNUM = AR.CNUM ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$ORDER_IT_VARB_TRACKER . " as V ";
                    $sql.= " ON right(trim(AR.ORDERIT_VARB_REF),5) = right(concat('000000',V.VARB),5) ";
                    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema']. "." . allTables::$ASSET_REQUESTS_EVENTS_SUMMARY . " as ES ";
                    $sql.= " ON AR.REQUEST_REFERENCE = ES.REF ";

                    $sql.= " WHERE 1=1 ";
//                    $sql.= $uCreated ? " AND AR.STATUS in ('" . assetRequestRecord::STATUS_APPROVED  . "' " : " AND AR.STATUS in ('" . assetRequestRecord::STATUS_EXPORTED . "','" . assetRequestRecord::STATUS_RAISED_ORDERIT . "') ";
//                    $sql.= " AND AR.ORDERIT_STATUS not in ('" . assetRequestRecord::STATUS_ORDERIT_APPROVED . "','" . assetRequestRecord::STATUS_ORDERIT_CANCELLED . "','" . assetRequestRecord::STATUS_ORDERIT_REJECTED. "') ";
                    $sql.= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
                    $sql.= $uCreated ? " AND USER_CREATED='" . assetRequestRecord::CREATED_USER . "' " : " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "'  ";
//                     $sql.= " AND ( ";
//                     $sql.= "      ( USER_CREATED = 'No' AND AR.STATUS in ('" . assetRequestRecord::STATUS_RAISED_ORDERIT . "') )";
//                     $sql.= "      OR ";
//                     $sql.= "      ( USER_CREATED = 'Yes' AND AR.APPROVED is not null )";
//                     $sql.= "    ) ";
//                    $sql.= $isThisCtb ? " AND upper(P.CTB_RTB='CTB') " : " AND (upper(P.CTB_RTB != 'CTB') or P.CTB_RTB is null ) ";
                    $sql.= " ORDER BY Ar.REQUEST_REFERENCE desc ";
                    // echo $sql;
                    // exit;
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);

                    AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);


                    if($rs){
                        $recordsFound = DbTable::writeResultSetToXls($rs, $spreadsheet);

                        if($recordsFound){
                            DbTable::autoFilter($spreadsheet);
                            DbTable::autoSizeColumns($spreadsheet);
                            DbTable::setRowColor($spreadsheet,'105abd19',1);
                        }
                    }

                    if(!$recordsFound){
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Warning");
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2,"No records found");
                    }
                    // Rename worksheet & create next.
                    $sheetTitle = $uCreated ? "User Created" : "PMO Created";
                    $spreadsheet->getActiveSheet()->setTitle($sheetTitle);
                    $spreadsheet->createSheet();
                    $spreadsheet->setActiveSheetIndex($sheet++);
//                 }
//            }
        }


        return true;
    }



    function getCnumAndAssetForReference($reference){

        $sql = " SELECT CNUM, ASSET_TITLE ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE REQUEST_REFERENCE= '" . htmlspecialchars($reference) . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        return array('cnum'=>$row['CNUM'],'assetTitle'=>$row['ASSET_TITLE']);

    }



    function getAssetRequestsForVarb($varb,$ref){

        $sql = " SELECT REQUEST_REFERENCE as REFERENCE, P.NOTES_ID as PERSON, AR.ASSET_TITLE as ASSET, AR.CNUM, PRIMARY_UID, ORDERIT_NUMBER, COMMENT ";
        $sql .= " ,ASSET_PRIMARY_UID_TITLE, ASSET_SECONDARY_UID_TITLE ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as AR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";

        $sql .=  " WHERE 1=1 " ;
        $sql .= !empty($varb) ? " AND ORDERIT_VARB_REF='" . htmlspecialchars($varb) . "' " : null;
        $sql .= !empty($ref) ? " AND REQUEST_REFERENCE='" . htmlspecialchars($ref) . "' " : null;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){
            $row['INCLUDED'] = "<input type='checkbox' name='request[]' value='" . $row['REFERENCE'] . "'  />";
            $row['ORDERIT_NUMBER'] = "<input type='text' name='orderit[" . $row['REFERENCE'] . "]' value='" . $row['ORDERIT_NUMBER'] . "'  min='999999' max='9999999' class='form-control'  /> " ;

            $comment =trim($row['COMMENT']);
            $row['COMMENT'] = "<input type='text' name='comment[" . $row['REFERENCE'] . "]' value='' class='form-control'  /><br/>$comment";

 //           $row['PRIMARY_UID'] = trim($row['ASSET'])=='CT ID' ?  "<input type='text' name='primaryUid[".$row['REFERENCE'] . "]' placeholder='" . $row['ASSET'] . "' value='' />" : null;
 //           $row['SECONDARY_UID'] = !empty($row['ASSET_SECONDARY_UID_TITLE']) ?  "<input type='text' name='secondaryUid[" .$row['REFERENCE'] . "]' placeholder='" . $row['ASSET_SECONDARY_UID_TITLE'] . "' value='" . $row['SECONDARY_UID'] . "'  />" : null;

            unset($row['CNUM']);
            $data[] = $row;
        }

        return $data;
    }

    function saveVarbToOrderItMapping($orderIt, $varb, array $request){

        $requestList = "'" . implode("','", $request) . "'";

        $autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_NUMBER='" . htmlspecialchars($orderIt) . "' ";
        $sql .= ",STATUS='" . assetRequestRecord::STATUS_RAISED_ORDERIT . "' ";
        $sql .= ",ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . htmlspecialchars($varb) . "' and STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";
        $sql .= " AND REQUEST_REFERENCE in (" . $requestList . ") " ;


        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        foreach ($request as $requestRef){
            $this->assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_ORDERIT_RAISED, $requestRef);
        }

        // Anything they didn't select gets reset for next time.

        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET STATUS='" . assetRequestRecord::STATUS_APPROVED . "' ";
        $sql .= ", ORDERIT_VARB_REF = null ";
        $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::STATUS_ORDERIT_YET . "' ";
        $sql .= ", ORDERIT_NUMBER = null ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . htmlspecialchars($varb) . "' and STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        // echo __METHOD__ . __LINE__ .  $sql;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        sqlsrv_commit($GLOBALS['conn']);

        db2_autocommit($GLOBALS['conn'],$autoCommit);

        return true;
    }

    function prepareRefToOrderItMapping(){

        if(!empty($this->preparedRefToOrderIt)){
            return $this->preparedRefToOrderIt;
        }

        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_NUMBER=? ";
        $sql .= ",STATUS='" . assetRequestRecord::STATUS_RAISED_ORDERIT . "' ";
        $sql .= ",ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' ";
        $sql .= " WHERE REQUEST_REFERENCE=? ";

        AuditTable::audit("Prepare SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedRefToOrderIt = $rs;

        return $this->preparedRefToOrderIt;
    }



    function saveRefToOrderItMapping($orderIt, $ref){

        $preparedStatement = $this->prepareRefToOrderItMapping();
        $data = array($orderIt,$ref);
        $rs = sqlsrv_execute($preparedStatement, $data);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_ORDERIT_RAISED, $ref);

//         // Anything they didn't select gets reset for next time.

//         $sql  = " UPDATE ";
//         $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
//         $sql .= " SET STATUS='" . assetRequestRecord::STATUS_APPROVED . "' ";
//         $sql .= ", ORDERIT_VARB_REF = null ";
//         $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::STATUS_ORDERIT_YET . "' ";
//         $sql .= ", ORDERIT_NUMBER = null ";
//         $sql .= " WHERE ORDERIT_VARB_REF='" . htmlspecialchars($varb) . "' and STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";

//         AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

//         // echo __METHOD__ . __LINE__ .  $sql;

//         $rs = sqlsrv_query($GLOBALS['conn'], $sql);

//         if(!$rs){
//             DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
//             return false;
//         }

//         sqlsrv_commit($GLOBALS['conn']);

//         db2_autocommit($GLOBALS['conn'],$autoCommit);

        return true;
    }


    function getAssetRequestsForOrderIt($orderIt,$varb,$ref){
        $sql = " SELECT REQUEST_REFERENCE as REFERENCE, P.NOTES_ID as PERSON, AR.ASSET_TITLE as ASSET,AR.STATUS as STATUS,  AR.ORDERIT_STATUS";
        $sql .=", '' as ACTION, COMMENT as COMMENT, ORDERIT_NUMBER, ORDERIT_VARB_REF, ASSET_PRIMARY_UID_TITLE, P.CT_ID, PRIMARY_UID, AR.ORDERIT_RESPONDED  ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as AR ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON AR.ASSET_TITLE = RAL.ASSET_TITLE ";


        $sql .= " WHERE 1=1 " ;
        $sql .= !empty($orderIt) ? " AND ORDERIT_NUMBER='" . htmlspecialchars($orderIt) . "' " : null;
        $sql .= !empty($varb) ? " AND ORDERIT_VARB_REF='" . htmlspecialchars($varb) . "' " : null;
        $sql .= !empty($ref) ? " AND REQUEST_REFERENCE='" . htmlspecialchars($ref) . "' " : null;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=sqlsrv_fetch_array($rs))==true){

            $status = trim($row['ORDERIT_STATUS']);
            switch ($status) {
                case assetRequestRecord::STATUS_ORDERIT_APPROVED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app" checked value="' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" value="' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::STATUS_ORDERIT_RAISED. '</label>
                                    </div>';
                     break;
                case assetRequestRecord::STATUS_ORDERIT_REJECTED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" checked value="' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::STATUS_ORDERIT_RAISED. '</label>
                                    </div>';

                    break;
                case assetRequestRecord::STATUS_ORDERIT_CANCELLED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej"  value="' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" checked value="' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::STATUS_ORDERIT_RAISED. '</label>
                                    </div>';

                    break;
                case assetRequestRecord::STATUS_ORDERIT_YET:
                    $row['ACTION'] = '<div class="form-check">
                                      <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'xxx" checked disabled value="' .  $status. '" >
                                      <label class="form-check-label text-primary" for="radio" id="radio' . $row['REFERENCE'] . 'xxx" >' . $status. '</label>
                                      </div>';

                    break;
                default:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" value="' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'xxx" checked value="' .  $status. '" >
                                        <label class="form-check-label text-primary" for="radio" id="radio' . $row['REFERENCE'] . 'xxx" >' . $status. '</label>
                                        </div>
';
                break;
            }


            $comment = $row['COMMENT'];
            $reference = trim($row['REFERENCE']);


            $row['COMMENT'] = '<div class="form-check"><textarea class="form-check-input" style="min-width: 100%" name=\'comment['. $row['REFERENCE'] . "]'  id=\'comment[". $row['REFERENCE'] . "]\'" . " ></textarea><br/>$comment</div>";


            $row['REFERENCE'] = "<small>" . trim($row['ORDERIT_NUMBER']) . ":" . $reference . "<br/>" . $row['ORDERIT_VARB_REF'] . "</small>";
            $row['PERSON'] = "<small>" . $row['PERSON'] . "</small>";



            if(trim($row['ASSET'])=='LBG Email' && empty(trim($row['PRIMARY_UID'])) && !empty(trim($row['CT_ID']))){
                $primaryUid = trim($row['CT_ID']) . "@lloydsbanking.com";
            } else {
                $primaryUid = trim($row['PRIMARY_UID']);
            }

            $row['PRIMARY_UID'] = !empty(trim($row['ASSET_PRIMARY_UID_TITLE'])) ?  "<input type='text' name='primaryUid[".$reference. "]' placeholder='" . trim($row['ASSET_PRIMARY_UID_TITLE']) . "' value='" . $primaryUid . "' />" : null;
            $row['STATUS'] = "<small>" . trim($row['STATUS']) . "</small>";


            $orderItResponded = \DateTime::createFromFormat('Y-m-d', $row['ORDERIT_RESPONDED']);
            $orderItRespondedDisplay = is_object($orderItResponded) ?  $orderItResponded->format('d M Y') : null;

            $row['ORDERIT_RESPONDED'] = "<div class='form-check'><input class='form-check' name='orderit_responded[" . $reference . "]' id='orderit_responded[". $reference ."]' value='$orderItRespondedDisplay' type='date' size='10' maxlength='10' placeholder='OrderIt Resp.' data-toggle='tooltip' title='LBG Responded'>";
            $row['ORDERIT_RESPONDED'].= "</div>";


            unset($row['ORDERIT_NUMBER']);
            unset($row['ORDERIT_VARB_REF']);
            unset($row['ORDERIT_STATUS']);
            unset($row['CT_ID']);

            $data[] = $row;
        }

        return $data;
    }



    function setRequestsOrderItStatus($reference, $orderItStatus, $comment=null){

//         $sql  = " UPDATE ";
//         $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
//         $sql .= " SET ORDERIT_STATUS='" . htmlspecialchars($orderItStatus) . "' ";
//         $sql .= " WHERE REQUEST_REFERENCE ='" . htmlspecialchars($reference) . "' " ;

        $data = array($orderItStatus,$reference);
        $preparedStmt = $this->prepareSetRequestsOrderItStatus();

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>Data:" . print_r($data,true),AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($preparedStmt,__CLASS__, __METHOD__, 'preparedStmt');
            return false;
        }
        $this->notifyRequestee($reference, $orderItStatus, $comment);
        $this->assetRequestEventsTable->logEventForRequest($orderItStatus, $reference);
        return true;
    }

    function updateCommentForOrderItStatus($requestReference, $comment){
        if(!empty($comment)){
            $now = new \DateTime();
            $existingComment = $this->GetCommentField($requestReference);

            $newComment = "<b>" . $now->format('Y-m-d H:i') . "</b>:" . trim($comment) . "<br/>";
            $newComment.= trim($existingComment);
            $newComment = substr($newComment, 0,512);

            $preparedStmt = $this->prepareUpdateCommentField();
            $data = array($newComment,$requestReference);

            $rs = sqlsrv_execute($preparedStmt,$data);

            if(!$rs){
                DbTable::displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, 'preparedStmt');
                return false;
            }
        }
        return true;
    }

    function updateOrderItResponded($requestReference, $orderitResponded){
        if(!empty($orderitResponded)){
            $now = new \DateTime();

            $preparedStmt = $this->prepareUpdateOrderitRespondedField();

            if(!$preparedStmt){
                echo "Prepare for LBG Responded has failed.";
                die('here');
            }


            $data = array($orderitResponded,$requestReference);

            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>Data:" . print_r($data,true),AuditTable::RECORD_TYPE_DETAILS);


            $rs = sqlsrv_execute($preparedStmt,$data);

            if(!$rs){
                DbTable::displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, 'preparedStmt');
                return false;
            }

            assetRequestsEventsTable::logEventForRequestWithDate(assetRequestsEventsTable::EVENT_ORDERIT_RESPONDED, $requestReference,$orderitResponded);
            return true;
        }
        return false;
    }


    function prepareSetRequestsOrderItStatus(){

        if(!empty($this->preparedSetRequestOrderItStatus)){
            return $this->preparedSetRequestOrderItStatus;
        }
        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_STATUS=? ";
        $sql .= " WHERE REQUEST_REFERENCE =? " ;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        $this->preparedSetRequestOrderItStatus = $rs;
        return $this->preparedSetRequestOrderItStatus;
    }


    function prepareUpdateCommentField(){
        if(!empty($this->preparedUpdateComment)){
            return $this->preparedUpdateComment;
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql.= " SET COMMENT = ? ";
        $sql.= " WHERE REQUEST_REFERENCE=? ";

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedUpdateComment = $rs;
        return $this->preparedUpdateComment;
    }

    function prepareUpdateOrderitRespondedField(){
        if(!empty($this->preparedOrderitResponded)){
            return $this->preparedOrderitResponded;
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql.= " SET ORDERIT_RESPONDED = ? ";
        $sql.= " WHERE REQUEST_REFERENCE=? ";

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedOrderitResponded = $rs;
        return $this->preparedOrderitResponded;
    }


    function preparepGetCommentField(){
        if(!empty($this->preparedGetComment)){
            return $this->preparedGetComment;
        }

        $sql = " SELECT COMMENT FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " WHERE REQUEST_REFERENCE=? ";

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }
        $this->preparedGetComment = $rs;
        return $this->preparedGetComment;
    }

    function GetCommentField($requestReference){
        $preparedStmt = $this->preparepGetCommentField();
        $data = array($requestReference);

        sqlsrv_execute($preparedStmt,$data);

        $row = sqlsrv_fetch_array($preparedStmt);
        return !empty($row['COMMENT']) ? $row['COMMENT'] : false;
    }


    function notifyRequestee($reference, $orderItStatus,$comment=null){
        $loader = new Loader();
        $cnum   = $loader->load('CNUM',$this->tableName," REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ");
        $asset   = $loader->load('ASSET_TITLE',$this->tableName," REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ");

        foreach ($cnum as $actualCnum){
        }

        foreach ($asset as $actualAsset){
        }


        $emailAddress = personTable::getEmailFromCnum($actualCnum);

        $message = "vBAC Requests for : $actualAsset ($reference) -  has been set to $orderItStatus status in LBG.<br/>";
        $message .= !empty($comment) ? "<b>Comment</b>&nbsp;" . $comment : "&nbsp;<b>No comment was provided</b>";
        $message .= "<br/>You can access the tool here  <a href='https://" . $_SERVER['HTTP_HOST'] . "/pa_assetPortal.php'  target='_blank' >vBAC Asset Portal</a>";

        BlueMail::send_mail(array($emailAddress), 'vBAC Request : ' . $orderItStatus , $message , personRecord::$vbacNoReplyId);
    }

    function setStatus($reference, $status, $comment=null,$dateReturned=null, $orderItStatus=null, $isPmo = false ){

        if(!empty($comment)){
            $now = new \DateTime();
            $sql = " SELECT COMMENT FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ";
            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
                return false;
            }

            $row = sqlsrv_fetch_array($rs);
            $existingComment = isset($row['COMMENT']) ?  trim($row['COMMENT']) : null;

            $newComment = "<b>" . $now->format('Y-m-d H:i') . "</b>:" . trim($comment) . "<br/>" . $existingComment;
        } else {
            $newComment = trim($comment);
        }

        switch (true) {
            case !empty($orderItStatus):
                // if they gave us a status - use it.
                $orderItStatus = trim($orderItStatus);
                break;
            case trim($status)==assetRequestRecord::STATUS_REJECTED:
                // else - if they are rejecting then go to "Not to be Raised"
                $orderItStatus = assetRequestRecord::STATUS_ORDERIT_NOT;
                break;
            case trim($status)==assetRequestRecord::STATUS_APPROVED:
                // else - if they are rejecting then go to "Not to be Raised"
                $orderItStatus = assetRequestRecord::STATUS_ORDERIT_YET;
                break;
            case trim($status)==assetRequestRecord::STATUS_AWAITING_IAM:
                // else - if they are rejecting then go to "Not to be Raised"
                $orderItStatus = assetRequestRecord::STATUS_ORDERIT_YET;
                break;
            default:
                $orderItStatus = null;
                break;
        }

        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " SET STATUS='" . htmlspecialchars($status) . "' ";
        $sql .= !empty($orderItStatus) ? " ,ORDERIT_STATUS='" . htmlspecialchars($orderItStatus) . "' " : null ;
        $sql .= !empty($newComment) ? ", COMMENT='" . htmlspecialchars(substr($newComment,0,500)) . "' " : null;
        $sql .= trim($status)==assetRequestRecord::STATUS_AWAITING_IAM ? ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' , APPROVED = current timestamp " : null;
        $sql .= $isPmo != true  && trim($status)==assetRequestRecord::STATUS_APPROVED ? ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' , APPROVED = current timestamp " : null;
        $sql .= trim($status)==assetRequestRecord::STATUS_RETURNED ? ", DATE_RETURNED = DATE('" . htmlspecialchars($dateReturned). "') " : null;
        $sql .= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ";
        $sql .= trim($status)==assetRequestRecord::STATUS_REJECTED ? " OR PRE_REQ_REQUEST='" . htmlspecialchars($reference) . "' " : null;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }

        $this->assetRequestEventsTable->logEventForRequest($status, $reference);

        // IF we're approving it - AND - it's NOT user raised - then set the ORDERIT_STATUS to 'Yet to be raised'
        if(trim($status)==assetRequestRecord::STATUS_APPROVED ){
            $sql  = " UPDATE ";
            $sql .= $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
            $sql .= " SET ORDERIT_STATUS = '" . assetRequestRecord::STATUS_ORDERIT_YET . "' " ;
            $sql .= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' ";

            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
                return false;
            }
        }

        self::notifyChangeOfStatus($reference, $status, $comment);

        return true;
    }

    function prepareUpdateUidsStmt(){

        if(empty($this->preparedUpdateUidsStmt)){
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET PRIMARY_UID = ? , SECONDARY_UID = ? ";
            $sql .= " WHERE REQUEST_REFERENCE = ? ";

            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $preparedUpdateUidsStmt = sqlsrv_prepare($GLOBALS['conn'], $sql);

            if(!$preparedUpdateUidsStmt){
                DbTable::displayErrorMessage($preparedUpdateUidsStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
            $this->preparedUpdateUidsStmt = $preparedUpdateUidsStmt;
        }

        return $this->preparedUpdateUidsStmt;
    }

    function updateUids($reference, $primaryUid,$secondaryUid=''){
        $stmt = $this->prepareUpdateUidsStmt();
        $data = array($primaryUid, $secondaryUid, $reference);

        $result = sqlsrv_execute($stmt,$data);

        if(!$result){
            echo sqlsrv_errors();
            echo sqlsrv_errors();
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, 'prepared stmt');
        }
        return true;
    }

    function setToProvisionedStatus($reference){
        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " SET STATUS='" . htmlspecialchars(assetRequestRecord::STATUS_PROVISIONED) . "' ";
        $sql .= " , ORDERIT_STATUS='" . htmlspecialchars(assetRequestRecord::STATUS_ORDERIT_APPROVED)  . "' ";
        $sql .= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ";
        $sql .= " AND STATUS not in ('" . assetRequestRecord::STATUS_REJECTED . "','" . assetRequestRecord::STATUS_RETURNED ."') ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }

        $this->assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_PROVISIONED, $reference);

        return true;
    }

    function saveJustification($reference, $justification){
        $justification = trim(substr($justification, 0, 500));

        $sql = " UPDATE ";
        $sql.= $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET BUSINESS_JUSTIFICATION='" . htmlspecialchars($justification) . "' ";
        $sql.= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }

    function saveAmendedOit($reference, $orderIt){

        $sql = " UPDATE ";
        $sql.= $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET ORDERIT_NUMBER='" . htmlspecialchars($orderIt) . "' ";
        $sql.= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }




    function extractForTracker(){

        $requestableAssetTable = new requestableAssetListTable(allTables::$REQUESTABLE_ASSET_LIST);
        $requestableAssets = $requestableAssetTable->returnAsArray(requestableAssetListTable::RETURN_EXCLUDE_DELETED,requestableAssetListTable::RETURN_WITHOUT_BUTTONS);

        foreach ($requestableAssets as $key => $record){
            $requestableAssets[$record['ASSET_TITLE']] = $record;
            unset($requestableAssets[$key]);
        }

        $dataAndSql = $this->returnForPortal(null,self::RETURN_WITHOUT_BUTTONS);
        list('data' => $data, 'sql' => $sql) = $dataAndSql;

        foreach ($data as $key => $record){
            $assetRequests[$record['EMAIL_ADDRESS']][$record['ASSET']] = $record;
        }

        ?>

        <div class='container-fluid'>

        <table class='table table-striped table-bordered compact' style='width:100%'>
        <thead>
        <tr>
        <th>IBMer</th>
        <th>CT ID</th>
        <th>Asset</th>
        <th>LBG Number</th>
        <th>LBG Status</th>
        <th>VBAC Status</th>
        <th>VBAC Approver</th>
        <th>VBAC Approved</th>
        <th>Location</th>
        <th>Justification</th>
        <th>Primary UID</th>
        <th>Secondary UID</th>
        <th>Date to IBM</th>
        <th>Date to User</th>
        <th>Date Rtnd</th>

        </tr>
        </thead>
        <tbody>
        <?php
            foreach ($assetRequests as $email => $assetRequests){
                foreach ($assetRequests as $asset => $record){
                ?>
                <tr>
                <td><?=$email?></td>
                <td><?=$record['CT_ID']?></td>
                <td><?=$record['ASSET']?></td>
                <td><?=$record['ORDERIT_NUMBER']?></td>
                <td><?=$record['ORDERIT_STATUS']?></td>
                <td><?=$record['STATUS']?></td>
                <td><?=$record['APPROVER_EMAIL']?></td>
                <td><?=$record['APPROVED_DATE']?></td>
                <td style="word-wrap: break-word"><?=$record['LOCATION']?></td>
                <td style="word-wrap: break-word"><?=$record['JUSTIFICATION']?></td>
                <td><?=$record['PRIMARY_UID']?></td>
                <td><?=$record['SECONDARY_UID']?></td>
                <td><?=$record['DATE_ISSUED_TO_IBM']?></td>
                <td><?=$record['DATE_ISSUED_TO_USER']?></td>
                <td><?=$record['DATE_RETURNED']?></td>
                </tr>
                <?php
                }
            }
        ?>
        </tbody>
        <tfoot>
        </tfoot>
        </table>

        </div>
        <?php
    }

    function getLastSql(){
        return $this->lastSql;
    }


    function notifyApprovingMgr(array $approvingMgr){
        $notifyApprovingMgr = "Requests have been created in vBAC that require your approval.<br/>You can access the tool here  <a href='https://" . $_SERVER['HTTP_HOST'] . "/pa_assetPortal.php'  target='_blank' >vBAC Asset Portal</a>";

        BlueMail::send_mail($approvingMgr, 'vBAC Approval Required', $notifyApprovingMgr, personRecord::$vbacNoReplyId);
    }


    function linkPrereqs($assetReferences){

        $assetRequestEventsTable = new assetRequestsEventsTable(allTables::$ASSET_REQUESTS_EVENTS);

        $autocommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

        $listOfAssetRefs = implode("','", $assetReferences);

        $sql  = "select ar.request_reference, ar.asset_title, ARL.asset_prerequisite, AR2.REQUEST_REFERENCE as pre_req ";
        $sql .= "from " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR  ";
        $sql .= "left join " . $GLOBALS['Db2Schema'] .  "." . allTables::$REQUESTABLE_ASSET_LIST . " as ARL  ";
        $sql .= "on AR.ASSET_TITLE = ARL.ASSET_TITLE  ";
        $sql .= "left join " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR2  ";
        $sql .= "on ARL.ASSET_PREREQUISITE = AR2.ASSET_TITLE  ";
        $sql .= "where AR.request_reference in ('" . $listOfAssetRefs . "')  ";
        $sql .= "and AR2.request_reference in ('" . $listOfAssetRefs  . "')  ";

        try {
            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $rs1 = sqlsrv_query($GLOBALS['conn'], $sql);

            if(!$rs1){
                DbTable::displayErrorMessage($rs1,__CLASS__, __METHOD__, $sql);
                return false;
            }

            while (($row=sqlsrv_fetch_array($rs1))==true) {
                $sql = " update " . $GLOBALS['Db2Schema'] . "." . $this->tableName ;
                $sql .= " SET PRE_REQ_REQUEST='" . htmlspecialchars(trim($row['PRE_REQ'])) . "' ";
                $sql .= " WHERE REQUEST_REFERENCE='" . htmlspecialchars(trim($row['REQUEST_REFERENCE']))  . "' " ;

                $rs2 = sqlsrv_query($GLOBALS['conn'], $sql);

                if(!$rs2){
                    DbTable::displayErrorMessage($rs2,__CLASS__, __METHOD__, $sql);
                    return false;
                }

                $assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_PRE_REQ_CREATED, $row['REQUEST_REFERENCE']);
            }



        } catch (Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
            echo $e->getTrace();
        }

        sqlsrv_commit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],$autocommit);
    }

    function prepareDevarb(){

        if(!empty($this->preparedDevarb)){
            return $this->preparedDevarb;
        }


        $sql  = " UPDATE ";
        $sql .= $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET STATUS='" . assetRequestRecord::STATUS_APPROVED . "' ";
        $sql .= ", ORDERIT_VARB_REF = null ";
        $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::STATUS_ORDERIT_YET . "' ";
        $sql .= ", ORDERIT_NUMBER = null ";
        $sql .= " WHERE REQUEST_REFERENCE= ? ";

        AuditTable::audit("Prepare SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>prepared sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedDevarb = $rs;
        return $this->preparedDevarb;

    }


    function deVarb($requestRef = null){
        if(empty($requestRef)){
            return false;
        }

        $preparedStmt = $this->prepareDevarb();
        $data = array($requestRef);

        AuditTable::audit("Devarb:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>Devarb:" . $requestRef,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return FALSE;
        }

        return true;
      }


     static function notifyChangeOfStatus($reference, $status, $comment=null){

         $sql = " SELECT P.EMAIL_ADDRESS, REQUESTOR_EMAIL, ASSET_TITLE, ORDERIT_VARB_REF, ORDERIT_NUMBER, STATUS, ORDERIT_STATUS, STATUS ";
         $sql.= " FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " AS AR ";
         $sql.= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
         $sql.= " ON P.CNUM = AR.CNUM ";
         $sql.= " WHERE REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' " ;

         $rs = sqlsrv_query($GLOBALS['conn'], $sql);

         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
             return false;
         }

         $row = sqlsrv_fetch_array($rs);

         $requestee  = array(trim($row['EMAIL_ADDRESS']));
         $requestor  = array(trim($row['REQUESTOR_EMAIL']));
         $assetTitle = trim($row['ASSET_TITLE']);
         $varbRef = !empty($row['ORDERIT_VARB_REF']) ? trim($row['ORDERIT_VARB_REF']) : "N/A";
         $orderIt = !empty($row['ORDERIT_NUMBER']) ? trim($row['ORDERIT_NUMBER']) : "N/A";
         $orderItStatus = trim($row['ORDERIT_STATUS']) ;
         $vStatus = trim($row['STATUS']) ;

         // $statusChangeEmailPattern = array('/&&requestReference&&/','/&&assetTitle&&/','/&&status&&/','/&&comment&&/','/&&varbNumber&&/','/&&orderItNumber&&/','/&&orderItStatus&&/','/&&vbacStatus&&/');
         $replacements = array($reference, $assetTitle, $status, $comment, $varbRef, $orderIt, $vStatus, $orderItStatus);
         $message = preg_replace(self::$statusChangeEmailPattern, $replacements, self::$statusChangeEmail);

         BlueMail::send_mail($requestee, "vBAC Request :$reference ($assetTitle ) - Status Change($status)", $message, personRecord::$vbacNoReplyId,$requestor);
     }


     function getOpenRequestsForCnum($cnum){
         $sql = " select distinct Asset_title ";
         $sql.= " from " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
         $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";
         $sql.= " AND ORDERIT_STATUS in ('" . assetRequestRecord::STATUS_ORDERIT_YET . "','" . assetRequestRecord::STATUS_ORDERIT_RAISED . "') ";
         $sql.= " AND ASSET_TITLE NOT LIKE 'Other%' ";
         $sql.= " AND ASSET_TITLE NOT LIKE 'MPW Renewal%' ";

         $rs = sqlsrv_query($GLOBALS['conn'], $sql);

         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
             return false;
         }
         $assetTitles=array();

         while (($row=sqlsrv_fetch_array($rs))==true) {
             $assetTitles[trim($row['ASSET_TITLE'])] = trim($row['ASSET_TITLE']);
         }

        return $assetTitles;

     }

}
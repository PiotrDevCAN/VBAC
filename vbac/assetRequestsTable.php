<?php
namespace vbac;

use itdq\DbTable;
use itdq\FormClass;
use itdq\Loader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\AuditTable;


class assetRequestsTable extends DbTable{

    const RETURN_WITH_BUTTONS = true;
    const RETURN_WITHOUT_BUTTONS = false;

    public $currentVarb;

    private $lastSql;

    private $preparedUpdateUidsStmt;
    private $preparedSetRequestOrderItStatus;
    private $preparedUpdateComment;
    private $preparedGetComment;

    private static $portalHeaderCells = array('REFERENCE','CT_ID','PERSON','ASSET','STATUS','JUSTIFICATION','REQUESTOR','APPROVER','FM',
        'LOCATION'
        ,'PRIMARY_UID','SECONDARY_UID','DATE_ISSUED_TO_IBM','DATE_ISSUED_TO_USER','DATE_RETURNED',
        'ORDERIT_VARB_REF','ORDERIT_NUMBER','ORDERIT_STATUS','ORDERIT_TYPE', 'COMMENT'
        ,'USER_CREATED','REQUESTEE_EMAIL','REQUESTEE_NOTES', 'APPROVER_EMAIL', 'FM_EMAIL','FM_NOTES',
        'CTB_RTB','TT_BAU','LOB', 'WORK_STREAM','PRE_REQ_REQUEST','REQUEST_RETURN'
    );

    private static $statusChangeEmail = "This is to inform you that your vBAC Request<b>&&requestReference&& (&&assetTitle&&)</b> has now moved to status :<b>&&status&&</b>.
<br/>The associated comment is :
<br/>&&comment&&
<br/>Reference Information : Request:<b>&&requestReference&&</b> Varb:<b>&&varbNumber&&</b> Order IT Number:<b>&&orderItNumber&&</b> Vbac Status:<b>&&vbacStatus&&</b> </b> Order IT Status:<b>&&orderItStatus&&";

    private static $statusChangeEmailPattern = array('/&&requestReference&&/','/&&assetTitle&&/','/&&status&&/','/&&comment&&/','/&&varbNumber&&/','/&&orderItNumber&&/','/&&vbacStatus&&/','/&&orderItStatus&&/');



    static function portalHeaderCells(){
        $headerCells = null;
       // $widths = array(5,5,5,5,10,10,10,10,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
         foreach (self::$portalHeaderCells as $key => $value) {
//                 $width = 'width="' . $widths[$key] . '"';
                $headerCells .= "<th>";
                $headerCells .= str_replace("_", " ", $value);
                $headerCells .= "</th>";
         }
         return $headerCells;
    }

    static function returnForPortal($predicate=null,$withButtons=true){
        $sql  = " SELECT ";
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
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON P.FM_CNUM = F.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON TRIM(RAL.ASSET_TITLE) = TRIM(AR.ASSET_TITLE) ";
        $sql .= " WHERE 1=1 ";
        $sql .=  $predicate;


        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);
        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($preTrimmed=db2_fetch_assoc($rs))==true){

            $row = array_map('trim', $preTrimmed);

            $userRaised = strtoupper($row['USER_CREATED'])=='YES';
            $approved   = $row['STATUS'] == assetRequestRecord::$STATUS_APPROVED;

            $reference = trim($row['REFERENCE']);
            $preReq = !empty(trim($row['PRE_REQ_REQUEST']))  ?  trim($row['PRE_REQ_REQUEST']): null;
            $sortableReference = substr('0000000'.$reference,-6);

            $startItalics = $row['USER_CREATED']==assetRequestRecord::$CREATED_USER ? "<i>" : null;
            $endItalics = $row['USER_CREATED']==assetRequestRecord::$CREATED_USER ? "</i>" : null;

            $row['REFERENCE'] =  $startItalics . trim($row['ORDERIT_NUMBER']) . ":" . $reference;
            $row['REFERENCE'] .= !empty($preReq) ? " <==" . $preReq : null;
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

            $pmoOrFm = ($_SESSION['isFm'] || $_SESSION['isPmo']);
            $notTheirOwnRecord = ( trim(strtolower($row['REQUESTEE_EMAIL'])) != trim(strtolower($_SESSION['ssoEmail'])));

            $allowedtoApproveReject = $withButtons && $pmoOrFm && $notTheirOwnRecord;
            $allowedtoReject = $withButtons && !$notTheirOwnRecord;

            switch (true) {
                case $status == assetRequestRecord::$STATUS_APPROVED:
                    $rejectable = true;
                    $approvable = false;
                break;
                case $status == assetRequestRecord::$STATUS_CREATED:
                    $rejectable = true;
                    $approvable = true;
                    break;
                case $status == assetRequestRecord::$STATUS_REJECTED:
                    $rejectable = false;
                    $approvable = true;
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
                case $status== assetRequestRecord::$STATUS_PROVISIONED:
                case $orderItStatus== assetRequestRecord::$STATUS_ORDERIT_APPROVED:
                    $returnable = true;
                    break;
                case $row['USER_CREATED'] == assetRequestRecord::$CREATED_USER && $status==assetRequestRecord::$STATUS_RAISED_ORDERIT && $orderItStatus != assetRequestRecord::$STATUS_ORDERIT_REJECTED:
                    $returnable = true;
                    break;
                default:
                    $returnable = false;
                break;
            }


            $returnedButton = $returnable && $status!= assetRequestRecord::$STATUS_RETURNED ? $returnedButton : null;


//             $row['ASSET'] =  ($returnable && $row['REQUEST_RETURN']!='Yes' ) ? $returnedButton . "&nbsp;<i>" .  $asset . "(Return/Remove)</i>" : $asset;

//             $row['ASSET'] .= $row['REQUEST_RETURN']=='Yes' ? "&nbsp;<small>(Return Request)</small>" : "";

            $row['JUSTIFICATION'] .= "<hr/>" . $row['COMMENT'];



            $data[] = $row;
        }

        return array('data'=>$data,'sql'=>$sql);

    }

    private function getNextVarb(){
        $sql  = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . allTables::$ORDER_IT_VARB_TRACKER;
        $sql .= " ( CREATED_BY ) VALUES ('" . db2_escape_string($_SESSION['ssoEmail']) . "' )" ;

        $rs = db2_exec($_SESSION['conn'], $sql);

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
         *   RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItGroup) . "'  - the ASSET_TITLE has a TYPE that matches the type we're processing
         *   AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "'  - It's approved for processing
         *
         *   '" . db2_escape_string($orderItType) . "' == 1  - It's a TYPE1 - ie it doesn't need a CT ID
         *   or P.CONTRACTOR_ID is not null                  - It's not a TYPE 1 - so it does need a CT ID, so CONTRACTOR ID can't be empty.
         *
         *
         */
        $predicate  = "";
        $predicate .= "   AND ORDERIT_VARB_REF is null and ORDERIT_NUMBER is null and RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItType) . "' AND AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $predicate .= "   AND ('" . db2_escape_string($orderItType) . "' = '1' or P.CT_ID is not null)";
        $predicate .= $this->predicateForPmoExportableRequest();

        return $predicate;
    }


    function getRequestsForOrderIt($orderItType, $first=false, $predicate = null ){


        $nextVarb = $this->getNextVarb();

        $commitState  = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

        $sql =  "UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql .= " SET ORDERIT_VARB_REF = '$nextVarb', STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " WHERE REQUEST_REFERENCE in ";
        $sql .= " (SELECT REQUEST_REFERENCE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= "  ON AR.CNUM = P.CNUM ";
        $sql .= "   WHERE 1=1 ";
        $sql .= !empty($predicate) ? $predicate : null;
        $sql .= $this->eligibleForOrderItPredicate($orderItType);
        $sql .= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql .= "   FETCH FIRST 20 ROWS ONLY) ";

        $rs = db2_exec($_SESSION['conn'],$sql);

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
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";


        $sql .= " WHERE ORDERIT_VARB_REF = '" . $nextVarb . "' ";
        $sql .= " ORDER BY ASSET_TITLE, REQUEST_REFERENCE desc";

        $data = array();
//         $data[] = "";
        $data[] = $first ? '"VARB","REQUEST","CT ID","CTB/RTB","TT/BAU","LOB","WORK STREAM","ASSET TITLE","REQUESTEE EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","FM EMAIL","EXPORTED"' : null;

        $rs2 = db2_exec($_SESSION['conn'],$sql);
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        while(($row=db2_fetch_assoc($rs2))==true){
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }

        $requestData = '';
        foreach ($data as $request){
            $requestData .= $request . "\n";
        }

//         $base64Encoded = base64_encode($requestData);

        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$commitState);

        return $requestData;
    }

    function getRequestsForNonPmo(){
        $commitState  = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

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
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";


        $sql .= " WHERE 1=1 ";
        $sql .= $this->predicateExportNonPmoRequests();

        $sql .= " ORDER BY ORDERIT_NUMBER desc, ASSET_TITLE, REQUEST_REFERENCE desc";

        $data = array();
        //         $data[] = "";
        $data[] = '"VARB","ORDER IT","REQUEST","CT ID","CTB/RTB","TT/BAU","LOB","WORK STREAM","ASSET TITLE","REQUESTEE EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","FM EMAIL","EXPORTED"';

        $rs2 = db2_exec($_SESSION['conn'],$sql);
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        while(($row=db2_fetch_assoc($rs2))==true){
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }

        $requestData = '';
        foreach ($data as $request){
            $requestData .= $request . "\n";
        }

        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$commitState);

        return $requestData;
    }


    function countRequestsForNonPmoExport(){
        $sql = " SELECT count(*) as tickets ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";

        $sql .= " WHERE 1=1 ";
        $sql .= $this->predicateExportNonPmoRequests();


        $rs2 = db2_exec($_SESSION['conn'],$sql);
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=db2_fetch_assoc($rs2);

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
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " ON F.CNUM = P.FM_CNUM ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";


        $sql .= " WHERE 1=1 ";
        $sql .= $bau ? " AND P.TT_BAU='BAU' " : null;
        $sql .= $nonBau ? " AND ( P.TT_BAU!='BAU' or P.TT_BAU is null) " : null;

        $pred = $this->predicateForPmoExportableRequest();

        $sql.= $pred;

        $rs2 = db2_exec($_SESSION['conn'],$sql);
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row=db2_fetch_assoc($rs2);

        return $row['TICKETS'];
    }



    function predicateExportNonPmoRequests(){
        $predicate = " AND STATUS IN('" . assetRequestRecord::$STATUS_APPROVED . "','" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "') AND ORDERIT_NUMBER is not NULL AND USER_CREATED='" . assetRequestRecord::$CREATED_USER . "' ";
        $predicate .= " AND ORDERIT_STATUS in ('" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "') ";
        $predicate .= " AND ORDERIT_VARB_REF is null  AND APPROVED is not null";

        return $predicate;

    }


    function exportForOrderIT($orderItGroup = 0){
        $rows = $this->getRequestsForOrderIt($orderItGroup);
        return $rows;
    }


    function predicateForPmoExportableRequest() {
        $predicate  = " AND  ( ";
        $predicate .= " STATUS IN('" . assetRequestRecord::$STATUS_APPROVED . "') AND ORDERIT_NUMBER is NULL AND USER_CREATED='" . assetRequestRecord::$CREATED_PMO . "' ";
        $predicate .= " AND ORDERIT_VARB_REF is null ";
        $predicate .= " AND (ORDER_IT_TYPE = '1' or P.CT_ID is not null)";
        $predicate .= " and ( pre_req_Request is null or pre_req_request  in (  select AR2.pre_req_request
						  	from " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR2
						  	left join " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR3
						  	on AR2.PRE_REQ_REQUEST = AR3.REQUEST_REFERENCE
						    where AR3.ORDERIT_STATUS in ('" . assetRequestRecord::$STATUS_ORDERIT_APPROVED . "')
							) )";
        $predicate .= " )";
        return $predicate;
    }




    function countApprovedForOrderItType($orderItType = 0, $predicate = null){
        $sql  = " SELECT COUNT(*) as REQUESTS ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " WHERE 1=1 ";
        $sql .= !empty($predicate) ? $predicate : null;
        $sql .= $this->eligibleForOrderItPredicate($orderItType);

        $rs = db2_exec($_SESSION['conn'],$sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->lastSql = $sql;

        $row = db2_fetch_assoc($rs);
        return $row['REQUESTS'];
    }

    function approveRejectModal(){
        ?>
       <!-- Modal -->
    <div id="approveRejectModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
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
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="<?=assetRequestRecord::$STATUS_APPROVED?>" data-off="<?=assetRequestRecord::$STATUS_REJECTED?>" id='assetRequestApprovalToggle' name='assetRequestApproval' value='Yes' data-onstyle='success' data-offstyle='warning'>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
						 <textarea class='form-control justification' rows='4' style='min-width: 100%' id='approveRejectRequestComment' name='approveRejectRequestComment' placeholder='Please provide comment if rejecting' min='0' max='500' ' ></textarea><span disabled>500 characters max</span>
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
                ?>
	      		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          	</div>
          	<input type=-'hidden' val='' id='approveRejectRequestOrderItStatus' name='approveRejectRequestOrderItStatus' />
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
                $form->formHiddenInput('user',$GLOBALS['ltcuser']['mail'],'user');
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
                $form->formHiddenInput('user',$GLOBALS['ltcuser']['mail'],'user');
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
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Map vARB to OrderIt</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <div class='col-sm-1'>
             <button type="button" class="btn btn-xs btn-danger float-left" id='deVarb'  disabled >DeVarb ALL</button>
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
                $form->formHiddenInput('mapper',$GLOBALS['ltcuser']['mail'],'mapper');
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
        ?>
        <form id='mapVarbToOrderItForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Map VARB to Order IT</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-5'>
                <select class='form-control select select2 '
                			  id='unmappedVarb'
                              name='unmappedVarb'
                              required
                      >
                    <option value=''></option>
                    <?php
                    foreach ($unmappedVarb as $varb){
                            ?><option value='<?=trim($varb);?>'><?=$varb?></option><?php
                        };
                        ?>
				</select>
            	</div>
         		<div class='col-sm-2 align-middle'>
         		<h4 class='text-center align-middle'>Maps to Order IT</h4>
         		</div>
<!--          		<div class='col-sm-5'> -->
<!--         			<input type="number" name='ORDERIT_NUMBER' id=orderItNumber' placeholder="Order IT Number" min="999999" max="9999999" class='form-control' required > -->
<!--          		</div> -->



        	</div>
        	<div class='form-group required'>
        	<div class='col-sm-12'>
        		<table class='table table-striped table-bordered ' cellspacing='0' width='90%' id='requestsWithinVarb'>
        		<thead><tr><th>Inc</th><th>Ref</th><th>Order IT</th><th>Requestee</th><th>Asset</th><th>Primary UID</th><th>Secondary UID</th></tr></thead>
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


    function getUnmappedVarb(){
        $sql = " SELECT distinct ORDERIT_VARB_REF ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is null and STATUS = '". assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " ORDER BY ORDERIT_VARB_REF asc ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }


        while(($row=db2_fetch_assoc($rs))==true){
            $data[]=$row['ORDERIT_VARB_REF'];
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
                $form->formHiddenInput('mapper',$GLOBALS['ltcuser']['mail'],'mapper');
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
        $restrictToActiveOrderIT = " ORDERIT_STATUS in ('" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "') ";
        $allOrderIt = $loader->load('ORDERIT_NUMBER',allTables::$ASSET_REQUESTS,$restrictToActiveOrderIT,true,'desc');
        ?>
        <form id='setOrderItStatusForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Set Order IT Status</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-5'>
                <select class='form-control select select2 '
                			  id='orderit'
                              name='orderit'
                              required
                      >
                    <option value=''></option>
                    <?php
                    foreach ($allOrderIt as $orderIt){
                            ?><option value='<?=trim($orderIt);?>'><?=$orderIt?></option><?php
                        };
                        ?>
				</select>
            	</div>
         		<div class='col-sm-7'>
         		</div>
        	</div>

        	<div class='form-group required'>
        	<div class='col-sm-12'>
        		<table class='table table-striped table-bordered ' cellspacing='0' width='90%' id='requestsWithStatus'>
        		<thead><tr><th>Ref</th><th>Person</th><th>Asset</th><th>OrderIT<br/>Status</th><th>Action</th><th>Comment</th></tr></thead>
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
        $lbgStatus = array(assetRequestRecord::$STATUS_ORDERIT_RAISED=>assetRequestRecord::$STATUS_ORDERIT_RAISED);
        $allStatus = $fullExtract ? $fullStatus : $lbgStatus;
        array_map('trim',$allStatus);
        $ctbOnly =  array(false,true);
        $sheet = 1;

        if(!empty($allStatus)){
            foreach ($ctbOnly as $isThisCtb){
                foreach ($allStatus as $key => $value) {
                    $sql = " SELECT AR.ORDERIT_NUMBER, AR.ORDERIT_STATUS,Ar.ORDERIT_VARB_REF, AR.REQUEST_REFERENCE, AR.ASSET_TITLE, AR.BUSINESS_JUSTIFICATION, AR.COMMENT, AR.REQUESTOR_EMAIl, AR.REQUESTED, AR.APPROVER_EMAIL, AR.APPROVED, P.FIRST_NAME, P.LAST_NAME, P.EMAIL_ADDRESS, P.LBG_EMAIL, P.EMPLOYEE_TYPE, P.CNUM, P.CT_ID, P.FM_CNUM as MGR_CNUM, FM.EMAIL_ADDRESS as MGR_EMAIL, FM.NOTES_ID as MGR_NOTESID, P.PES_STATUS, P.WORK_STREAM,P.CTB_RTB, P.TT_BAU, P.LOB, P.ROLE_ON_THE_ACCOUNT, P.CIO_ALIGNMENT,  AR.PRIMARY_UID, AR.SECONDARY_UID, AR.DATE_ISSUED_TO_IBM, AR. DATE_ISSUED_TO_USER, AR.DATE_RETURNED ";
                    $sql .= " FROM " . $_SESSION['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
                    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as P ";
                    $sql .= " ON P.CNUM = AR.CNUM ";
                    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as FM ";
                    $sql .= " ON P.FM_CNUM = FM.CNUM ";
                    $sql .= " WHERE 1=1 ";
                    $sql .= " AND AR.ORDERIT_STATUS in ('" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "') ";
                    $sql .= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
                    $sql .= " AND ( ";
                    $sql .= "      ( USER_CREATED = 'No' AND AR.STATUS in ('" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "') )";
                    $sql .= "      OR ";
                    $sql .= "      ( USER_CREATED = 'Yes' AND AR.APPROVED is not null )";
                    $sql .= "    ) ";
                    $sql .= $isThisCtb ? " AND upper(P.CTB_RTB='CTB') " : " AND (upper(P.CTB_RTB != 'CTB') or P.CTB_RTB is null ) ";
                    $sql .= " ORDER BY AR.REQUESTED asc ";
                    $rs = db2_exec($_SESSION['conn'], $sql);

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
//         $lbgStatus = array(assetRequestRecord::$STATUS_ORDERIT_RAISED=>assetRequestRecord::$STATUS_ORDERIT_RAISED);
//         $allStatus = $fullExtract ? $fullStatus : $lbgStatus;
//         array_map('trim',$allStatus);
//         $ctbOnly =  array(false,true);

        $age = array('Recent'=>' AR.REQUESTED > CURRENT TIMESTAMP - 3 MONTHS','>3 Months'=>' ( AR.REQUESTED <= CURRENT TIMESTAMP - 3 MONTHS )');

        $sheet = 1;

//         if(!empty($allStatus)){
//             foreach ($ctbOnly as $isThisCtb){
        foreach ($age as $ageTitle => $agePredicate) {
        $sql = " SELECT AR.*, P.FIRST_NAME, P.LAST_NAME, P.NOTES_ID, P.EMAIL_ADDRESS, P.LBG_EMAIL, P.EMPLOYEE_TYPE, P.CNUM, P.CT_ID, P.FM_CNUM as MGR_CNUM, FM.EMAIL_ADDRESS as MGR_EMAIL, FM.NOTES_ID as MGR_NOTESID, P.PES_STATUS, P.WORK_STREAM,P.CTB_RTB, P.TT_BAU, P.LOB, P.ROLE_ON_THE_ACCOUNT, P.CIO_ALIGNMENT, A.EMAIL_ADDRESS as APPROVER_EMAIL, A.NOTES_ID as APPROVER_NOTESID,A.WORK_STREAM as APPROVER_WORK_STREAM, A.TT_BAU as APPROVER_TT_BAU  ";
        $sql .= " FROM " . $_SESSION['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as P ";
        $sql .= " ON P.CNUM = AR.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as FM ";
        $sql .= " ON P.FM_CNUM = FM.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as A ";
        $sql .= " ON lower(A.EMAIL_ADDRESS) = lower(AR.APPROVER_EMAIL) ";


        $sql .= " WHERE 1=1 ";
        $sql .= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
        $sql .= " AND " . $agePredicate;
//         $sql .= "      ( AR.REQUESTED > CURRENT TIMESTAMP - 6 MONTHS )";
//         $sql .= "      OR ";
//         $sql .= "      ( AR.APPROVED > CURRENT TIMESTAMP - 6 MONTHS )";
//        $sql .= "    ) ";
        $sql .= " ORDER BY AR.REQUESTED desc ";
        $rs = db2_exec($_SESSION['conn'], $sql);

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
        array_map('trim',$allStatus);
        $ctbOnly =  array(false,true);
        $sheet = 1;

//         if(!empty($allStatus)){
            foreach ($ctbOnly as $isThisCtb){
//                 foreach ($allStatus as $key => $value) {
                    $sql = " SELECT AR.ORDERIT_VARB_REF, V.CREATED_DATE, V.CREATED_BY,AR.STATUS, AR.ORDERIT_STATUS, AR.ORDERIT_NUMBER, AR.REQUEST_REFERENCE, AR.ASSET_TITLE, AR.BUSINESS_JUSTIFICATION, AR.COMMENT, AR.REQUESTOR_EMAIl, AR.REQUESTED, AR.APPROVER_EMAIL, AR.APPROVED,  AR.PRIMARY_UID, AR.SECONDARY_UID ";
                    $sql .= " FROM " . $_SESSION['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
                    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as P ";
                    $sql .= " ON P.CNUM = AR.CNUM ";
                    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$ORDER_IT_VARB_TRACKER . " as V ";
                    $sql .= " ON right(trim(AR.ORDERIT_VARB_REF),5) = right(concat('000000',V.VARB),5) ";
                    $sql .= " WHERE 1=1 ";
                    $sql .= " AND AR.STATUS in ('" . assetRequestRecord::$STATUS_EXPORTED . "','" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "') ";
                    $sql .= " AND AR.ORDERIT_STATUS not in ('" . assetRequestRecord::$STATUS_ORDERIT_APPROVED . "','" . assetRequestRecord::$STATUS_ORDERIT_CANCELLED . "','" . assetRequestRecord::$STATUS_ORDERIT_REJECTED. "') ";
                    $sql .= " AND (AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ) ";
//                     $sql .= " AND ( ";
//                     $sql .= "      ( USER_CREATED = 'No' AND AR.STATUS in ('" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "') )";
//                     $sql .= "      OR ";
//                     $sql .= "      ( USER_CREATED = 'Yes' AND AR.APPROVED is not null )";
//                     $sql .= "    ) ";
                    $sql .= $isThisCtb ? " AND upper(P.CTB_RTB='CTB') " : " AND (upper(P.CTB_RTB != 'CTB') or P.CTB_RTB is null ) ";
                    $sql .= " ORDER BY V.CREATED_DATE desc ";
                    $rs = db2_exec($_SESSION['conn'], $sql);

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
                    $sheetTitle = $isThisCtb ? "CTB" : "Non CTB";
                    $spreadsheet->getActiveSheet()->setTitle($sheetTitle);
                    $spreadsheet->createSheet();
                    $spreadsheet->setActiveSheetIndex($sheet++);
//                 }
            }
//         }
        //         $nonPmo = self::countRequestsForNonPmoExport();

        //         if($nonPmo > 0){
        //         }

        return true;
    }



    function getCnumAndAssetForReference($reference){

        $sql = " SELECT CNUM, ASSET_TITLE ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE REQUEST_REFERENCE= '" . db2_escape_string($reference) . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($rs);
        return array('cnum'=>$row['CNUM'],'assetTitle'=>$row['ASSET_TITLE']);

    }



    function getAssetRequestsForVarb($varb){
        $sql = " SELECT REQUEST_REFERENCE as REFERENCE, P.NOTES_ID as PERSON, AR.ASSET_TITLE as ASSET, AR.CNUM, PRIMARY_UID, SECONDARY_UID, ORDERIT_NUMBER ";
        $sql .= " ASSET_PRIMARY_UID_TITLE, ASSET_SECONDARY_UID_TITLE ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";

        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $row['INCLUDED'] = "<input type='checkbox' name='request[]' value='" . $row['REFERENCE'] . "'  />";
            $row['ORDERIT_NUMBER'] = "<input type='text' name='orderit[]' value='" . $row['ORDERIT_NUMBER'] . "'  min='999999' max='9999999' class='form-control'  /> ";
            $row['PRIMARY_UID'] = !empty($row['ASSET_PRIMARY_UID_TITLE']) ?  "<input type='text' name='primaryUid[".$row['REFERENCE'] . "]' placeholder='" . $row['ASSET_PRIMARY_UID_TITLE'] . "' value='" . $row['PRIMARY_UID'] . "' />" : null;
            $row['SECONDARY_UID'] = !empty($row['ASSET_SECONDARY_UID_TITLE']) ?  "<input type='text' name='secondaryUid[" .$row['REFERENCE'] . "]' placeholder='" . $row['ASSET_SECONDARY_UID_TITLE'] . "' value='" . $row['SECONDARY_UID'] . "'  />" : null;

            unset($row['CNUM']);
            $data[] = $row;

        }

        return $data;
    }

    function saveVarbToOrderItMapping($orderIt, $varb, array $request){

        $requestList = "'" . implode("','", $request) . "'";

        $autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_NUMBER='" . db2_escape_string($orderIt) . "' ";
        $sql .= ",STATUS='" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "' ";
        $sql .= ",ORDERIT_STATUS='" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "' ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' and STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " AND REQUEST_REFERENCE in (" . $requestList . ") " ;


        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        // Anything they didn't select gets reset for next time.

        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $sql .= ", ORDERIT_VARB_REF = null ";
        $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::$STATUS_ORDERIT_YET . "' ";
        $sql .= ", ORDERIT_NUMBER = null ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' and STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        // echo __METHOD__ . __LINE__ .  $sql;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }

        db2_commit($_SESSION['conn']);

        db2_autocommit($_SESSION['conn'],$autoCommit);

        return true;
    }

    function getAssetRequestsForOrderIt($orderIt){
        $sql = " SELECT REQUEST_REFERENCE as REFERENCE, P.NOTES_ID as PERSON, AR.ASSET_TITLE as ASSET, AR.ORDERIT_STATUS, '' as ACTION, '' as COMMENT ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " WHERE ORDERIT_NUMBER='" . db2_escape_string($orderIt) . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $status = trim($row['ORDERIT_STATUS']);
            switch ($status) {
                case assetRequestRecord::$STATUS_ORDERIT_APPROVED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app" checked value="' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" value="' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '</label>
                                    </div>';
                     break;
                case assetRequestRecord::$STATUS_ORDERIT_REJECTED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" checked value="' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '</label>
                                    </div>';

                    break;
                case assetRequestRecord::$STATUS_ORDERIT_CANCELLED:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej"  value="' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" checked value="' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rai" value="' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'rai">' . assetRequestRecord::$STATUS_ORDERIT_RAISED. '</label>
                                    </div>';

                    break;
                default:
                    $row['ACTION'] = '<div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'app"  value="' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '" >
                                        <label class="form-check-label text-success" for="radio" id="radio' . $row['REFERENCE'] . 'app" >' . assetRequestRecord::$STATUS_ORDERIT_APPROVED. '</label>
                                        </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'rej" value="' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '" >
                                    <label class="form-check-label text-danger" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_REJECTED. '</label>
                                    </div>

                                    <div class="form-check">
                                    <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'can" value="' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '" >
                                    <label class="form-check-label text-warning" for="radio" id="radio' . $row['REFERENCE'] . 'app">' . assetRequestRecord::$STATUS_ORDERIT_CANCELLED. '</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" name=\'status['. $row['REFERENCE'] . ']\'  type="radio" id="radio' . $row['REFERENCE'] . 'xxx" checked value="' .  $status. '" >
                                        <label class="form-check-label text-primary" for="radio" id="radio' . $row['REFERENCE'] . 'xxx" >' . $status. '</label>
                                        </div>
';
                break;
            }

            $row['COMMENT'] = '<div class="form-check"><textarea class="form-check-input" style="min-width: 100%" name=\'comment['. $row['REFERENCE'] . "]'  id=\'comment[". $row['REFERENCE'] . "]'" . " ></textarea></div>";

            $data[] = $row;
        }

        return $data;
    }



    function setRequestsOrderItStatus($reference, $orderItStatus, $comment){

//         $sql  = " UPDATE ";
//         $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
//         $sql .= " SET ORDERIT_STATUS='" . db2_escape_string($orderItStatus) . "' ";
//         $sql .= " WHERE REQUEST_REFERENCE ='" . db2_escape_string($reference) . "' " ;

        $data = array($orderItStatus,$reference);
        $preparedStmt = $this->prepareSetRequestsOrderItStatus();

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>Data:" . print_r($data,true),AuditTable::RECORD_TYPE_DETAILS);

        db2_execute($preparedStmt,$data);

        if(!$preparedStmt){
            DbTable::displayErrorMessage($preparedStmt,__CLASS__, __METHOD__, 'preparedStmt');
            return false;
        }
        $this->notifyRequestee($reference, $orderItStatus, $comment);
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

            var_dump($data);


            $rs = db2_execute($preparedStmt,$data);

            if(!$rs){
                DbTable::displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, 'preparedStmt');
                return false;
            }
        }
        return true;
    }


    function prepareSetRequestsOrderItStatus(){

        if(!empty($this->preparedSetRequestOrderItStatus)){
            return $this->preparedSetRequestOrderItStatus;
        }
        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_STATUS=? ";
        $sql .= " WHERE REQUEST_REFERENCE =? " ;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_prepare($_SESSION['conn'], $sql);

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

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql.= " SET COMMENT = ? ";
        $sql.= " WHERE REQUEST_REFERENCE=? ";

        $rs = db2_prepare($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedUpdateComment = $rs;
        return $this->preparedUpdateComment;
    }

    function preparepGetCommentField(){
        if(!empty($this->preparedGetComment)){
            return $this->preparedGetComment;
        }

        $sql = " SELECT COMMENT FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " WHERE REQUEST_REFERENCE=? ";

        $rs = db2_prepare($_SESSION['conn'], $sql);
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

        db2_execute($preparedStmt,$data);

        $row = db2_fetch_assoc($preparedStmt);
        return !empty($row['COMMENT']) ? $row['COMMENT'] : false;
    }


    function notifyRequestee($reference, $orderItStatus,$comment=null){
        $loader = new Loader();
        $cnum   = $loader->load('CNUM',$this->tableName," REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ");
        $asset   = $loader->load('ASSET_TITLE',$this->tableName," REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ");

        foreach ($cnum as $actualCnum){
        }

        foreach ($asset as $actualAsset){
        }


        $emailAddress = personTable::getEmailFromCnum($actualCnum);

        $message = "vBAC Requests for : $actualAsset ($reference) -  has been set to $orderItStatus status in Order IT.<br/>";
        $message .= !empty($comment) ? "<b>Comment</b>&nbsp;" . $comment : "&nbsp;<b>No comment was provided</b>";
        $message .= "<br/>You can access the tool here  <a href='" . $_SERVER['HTTP_HOST'] . "/pa_assetPortal.php'  target='_blank' >vBAC Asset Portal</a>";

        \itdq\BlueMail::send_mail(array($emailAddress), 'vBAC Request : ' . $orderItStatus , $message , 'vbacNoReply@uk.ibm.com');
    }

    static function setStatus($reference, $status, $comment=null,$dateReturned=null, $orderItStatus=null){

        if(!empty($comment)){
            $now = new \DateTime();
            $sql = " SELECT COMMENT FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ";
            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
                return false;
            }

            $row = db2_fetch_assoc($rs);
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
            case trim($status)==assetRequestRecord::$STATUS_REJECTED:
                // else - if they are rejecting then go to "Not to be Raised"
                $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_NOT;
                break;
            case trim($status)==assetRequestRecord::$STATUS_APPROVED:
                // else - if they are rejecting then go to "Not to be Raised"
                $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_YET;
                break;
            default:
                $orderItStatus = null;
                break;
        }

        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " SET STATUS='" . db2_escape_string($status) . "' ";
        $sql .= !empty($newComment) ? ", COMMENT='" . db2_escape_string(substr($newComment,0,500)) . "' " : null;
        $sql .= trim($status)==assetRequestRecord::$STATUS_APPROVED ? ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' , APPROVED = current timestamp " : null;
        $sql .= trim($status)==assetRequestRecord::$STATUS_RETURNED ? ", DATE_RETURNED = DATE('" . db2_escape_string($dateReturned). "') " : null;
        $sql .= " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }

        // IF we're approving it - AND - it's NOT user raised - then set the ORDERIT_STATUS to 'Yet to be raised'
        if(trim($status)==assetRequestRecord::$STATUS_APPROVED ){
            $sql  = " UPDATE ";
            $sql .= $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
            $sql .= " SET ORDERIT_STATUS = '" . assetRequestRecord::$STATUS_ORDERIT_YET . "' " ;
            $sql .= " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' AND USER_CREATED='" . assetRequestRecord::$CREATED_PMO . "' ";

            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $rs = db2_exec($_SESSION['conn'], $sql);

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
            $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET PRIMARY_UID = ? , SECONDARY_UID = ? ";
            $sql .= " WHERE REQUEST_REFERENCE = ? ";

            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $preparedUpdateUidsStmt = db2_prepare($_SESSION['conn'], $sql);

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

        $result = db2_execute($stmt,$data);

        if(!$result){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, 'prepared stmt');
        }
        return true;
    }

    static function setToProvisionedStatus($reference){
        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " SET STATUS='" . db2_escape_string(assetRequestRecord::$STATUS_PROVISIONED) . "' ";
        $sql .= " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ";
        $sql .= " AND STATUS not in ('" . assetRequestRecord::$STATUS_REJECTED . "','" . assetRequestRecord::$STATUS_RETURNED ."') ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = db2_exec($_SESSION['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
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
        $data = $dataAndSql['data'];
        $sql  = $dataAndSql['sql'];


        foreach ($data as $key => $record){
            $assetRequests[$record['EMAIL_ADDRESS']][$record['ASSET']] = $record;
        }

        ?>

        <div class='container-fluid'>

        <table class='table table-striped table-bordered compact' '>
        <thead>
        <tr>
        <th>IBMer</th>
        <th>CT ID</th>
        <th>Asset</th>
        <th>Order IT Number</th>
        <th>Order IT Status</th>
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


    function notifyApprovingMgr($approvingMgr){
        $notifyApprovingMgr = "Requests have been created in vBAC that require your approval.<br/>You can access the tool here  <a href='" . $_SERVER['HTTP_HOST'] . "/pa_assetPortal.php'  target='_blank' >vBAC Asset Portal</a>";

        \itdq\BlueMail::send_mail($approvingMgr, 'vBAC Approval Required', $notifyApprovingMgr, 'vbacNoReply@uk.ibm.com');
    }


    function linkPrereqs($assetReferences){

        $autocommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

        $listOfAssetRefs = implode("','", $assetReferences);

        $sql  = "select ar.request_reference, ar.asset_title, ARL.asset_prerequisite, AR2.REQUEST_REFERENCE as pre_req ";
        $sql .= "from " . $_SESSION['Db2Schema'] . "." . \vbac\allTables::$ASSET_REQUESTS . " as AR  ";
        $sql .= "left join " . $_SESSION['Db2Schema'] .  "." . \vbac\allTables::$REQUESTABLE_ASSET_LIST . " as ARL  ";
        $sql .= "on AR.ASSET_TITLE = ARL.ASSET_TITLE  ";
        $sql .= "left join " . $_SESSION['Db2Schema'] . "." . \vbac\allTables::$ASSET_REQUESTS . " as AR2  ";
        $sql .= "on ARL.ASSET_PREREQUISITE = AR2.ASSET_TITLE  ";
        $sql .= "where AR.request_reference in ('" . $listOfAssetRefs . "')  ";
        $sql .= "and AR2.request_reference in ('" . $listOfAssetRefs  . "')  ";

        try {
            AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

            $rs1 = db2_exec($_SESSION['conn'], $sql);

            if(!$rs1){
                DbTable::displayErrorMessage($rs1,__CLASS__, __METHOD__, $sql);
                return false;
            }

            while (($row=db2_fetch_assoc($rs1))==true) {
                $sql = " update " . $_SESSION['Db2Schema'] . "." . $this->tableName ;
                $sql .= " SET PRE_REQ_REQUEST='" . db2_escape_string(trim($row['PRE_REQ'])) . "' ";
                $sql .= " WHERE REQUEST_REFERENCE='" . db2_escape_string(trim($row['REQUEST_REFERENCE']))  . "' " ;

                $rs2 = db2_exec($_SESSION['conn'], $sql);

                if(!$rs2){
                    DbTable::displayErrorMessage($rs2,__CLASS__, __METHOD__, $sql);
                    return false;
                }
            }



        } catch (Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
            echo $e->getTrace();
        }

        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$autocommit);
    }


    function deVarb($varbRef = null){
        if(empty($varbRef)){
            return false;
        }

        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $sql .= ", ORDERIT_VARB_REF = null ";
        $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::$STATUS_ORDERIT_YET . "' ";
        $sql .= ", ORDERIT_NUMBER = null ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varbRef) . "' and STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);
        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return FALSE;
        }

        return;

      }


     static function notifyChangeOfStatus($reference, $status, $comment=null){

         $sql = " SELECT P.EMAIL_ADDRESS, REQUESTOR_EMAIL, ASSET_TITLE, ORDERIT_VARB_REF, ORDERIT_NUMBER, STATUS, ORDERIT_STATUS, STATUS ";
         $sql.= " FROM ". $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " AS AR ";
         $sql.= " LEFT JOIN ". $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
         $sql.= " ON P.CNUM = AR.CNUM ";
         $sql.= " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' " ;

         $rs = db2_exec($_SESSION['conn'], $sql);

         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
             return false;
         }

         $row = db2_fetch_assoc($rs);

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

         \itdq\BlueMail::send_mail($requestee, "vBAC Request :$reference ($assetTitle ) - Status Change($status)", $message, 'vbacNoReply@uk.ibm.com',$requestor);
     }


     function getOpenRequestsForCnum($cnum){
         $sql = " select distinct Asset_title ";
         $sql.= " from " . $_SESSION['Db2Schema'] . "." . $this->tableName;
         $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
         $sql.= " AND ORDERIT_STATUS in ('" . assetRequestRecord::$STATUS_ORDERIT_YET . "','" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "') ";

         $rs = db2_exec($_SESSION['conn'], $sql);

         if(!$rs){
             DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
             return false;
         }
         $assetTitles=array();

         while (($row=db2_fetch_assoc($rs))==true) {
             $assetTitles[trim($row['ASSET_TITLE'])] = trim($row['ASSET_TITLE']);
         }

        return $assetTitles;

     }

}
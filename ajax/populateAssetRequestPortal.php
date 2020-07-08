<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;
use itdq\Loader;
use vbac\assetRequestRecord;
use vbac\personRecord;

set_time_limit(0);
ob_start();


$_SESSION['ssoEmail'] = $_SESSION['ssoEmail'];

$loader = new Loader();
// $GLOBALS['isFm']   = !empty($isFm)   ? true : false;
// $GLOBALS['isCdi']  = !empty($isCdi)  ? true : false;
// $GLOBALS['isPmo']  = !empty($isPmo)  ? true : false;
// $GLOBALS['isPes']  = !empty($isPes)  ? true : false;
// $GLOBALS['isUser'] = !empty($isUser) ? true : false;
$show = !empty($_POST['show']) ? $_POST['show'] : 'all';
$pmoRaised = !empty($_POST['pmoRaised']) ?$_POST['pmoRaised'] : false;

$pmoRaised = strtolower($pmoRaised)=='true';
// $showAll = $showAll==='true' ? true : false;

$withButtons = true;
$predicate = null;
switch (true) {
    case $GLOBALS['isFm']:
        echo "is FM";
        $myCnum = personTable::myCnum();

//         $amADelegateForRaw = $loader->load('EMAIL_ADDRESS',allTables::$DELEGATE," DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ");
//         $amADelegateFor = array_map('strtolower',$amADelegateForRaw);


        $myEmail = trim(strtolower($_SESSION['ssoEmail']));
        $myPeople = $loader->load("CNUM",allTables::$PERSON," FM_CNUM='" . trim($myCnum) . "' ");
        $myPeopleListed = "'";
        foreach ($myPeople as $personCnum){
            $myPeopleListed .= db2_escape_string($personCnum) . "','";
        }
        $myPeopleListed .= "'";
        $predicate .= " AND ( AR.CNUM in ('". db2_escape_string($myCnum) . "'," . $myPeopleListed . ") or lower(APPROVER_EMAIL) ='" . db2_escape_string($myEmail) . "' ";
        $predicate .= "       OR ";
        $predicate .= "       D.DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ) ";

        break;
    case $GLOBALS['isCdi']:
        echo "is CDI";
    case $GLOBALS['isPmo']:
        echo "is PMO";
        $assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

        switch ($show) {
            case 'awaitingIam':
                $predicate .= " AND STATUS='" . assetRequestRecord::STATUS_AWAITING_IAM . "' ";
            break;
            case 'exportable':
                $predicate .= " AND (( 1=1 " . $assetRequestTable->predicateForPmoExportableRequest() . " ) ";
                $predicate .= " OR ( 1=1 " . $assetRequestTable->predicateExportNonPmoRequests() . " )) ";
                $predicate .= $pmoRaised ?  " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " : " AND USER_CREATED='" . assetRequestRecord::CREATED_USER . "' ";
            break;
            case 'exported':
                $predicate .= " AND STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";
                break;
            case 'bauRaised':
                $predicate .= " AND ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' AND P.TT_BAU='BAU' " ;
                $predicate .= " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " ;
                break;
            case 'nonBauRaised':
                $predicate .= " AND ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' AND ( P.TT_BAU!='BAU' or P.TT_BAU is null )  " ;
                $predicate .= " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " ;
                break;
            default:
                ;
            break;
        }
        break;
    default:
        echo "is default";
        $myCnum = personTable::myCnum();
        $predicate .= " AND ( AR.CNUM = '". db2_escape_string($myCnum) . "' ";
        $predicate .= "       OR ";
        $predicate .= "       D.DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ) ";
        $withButtons = false;
    break;
}


$dataAndSql = assetRequestsTable::returnForPortal($predicate, $withButtons);
$data = $dataAndSql['data'];
$sql  = $dataAndSql['sql'];

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages,'sql'=>$sql,'post'=>print_r($_POST,true));

ob_clean();
echo json_encode($response);
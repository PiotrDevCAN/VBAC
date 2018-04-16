<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;
use itdq\Loader;
use vbac\assetRequestRecord;

set_time_limit(0);
ob_start();


$GLOBALS['ltcuser']['mail'] = $_SESSION['ssoEmail'];

$loader = new Loader();
// $_SESSION['isFm']   = !empty($isFm)   ? true : false;
// $_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
// $_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
// $_SESSION['isPes']  = !empty($isPes)  ? true : false;
// $_SESSION['isUser'] = !empty($isUser) ? true : false;
$showAll = !empty($_POST['showAll']) ? $_POST['showAll'] : false;

$showAll = $showAll==='true' ? true : false;

$predicate = null;
switch (true) {
    case $_SESSION['isFm']:
        echo "is FM";
        $myCnum = personTable::myCnum();
        $myEmail = strtolower($GLOBALS['ltcuser']['mail']);
        $myPeople = $loader->load("CNUM",allTables::$PERSON," FM_CNUM='" . trim($myCnum) . "' ");
        $myPeopleListed = "'";
        foreach ($myPeople as $personCnum){
            $myPeopleListed .= db2_escape_string($personCnum) . "','";
        }
        $myPeopleListed .= "'";
        $predicate .= " AND AR.CNUM in ('". db2_escape_string($myCnum) . "'," . $myPeopleListed . ") or lower(Approver_email='" . db2_escape_string($myEmail) . "') ";
        break;
    case $_SESSION['isCdi']:
        echo "is CDI";
    case $_SESSION['isPmo']:
        echo "is PMO";
        $predicate .= $showAll ? null : assetRequestsTable::predicateForPmoExportableRequest();
        break;
    default:
        echo "is default";
        $myCnum = personTable::myCnum();
        $predicate .= " AND AR.CNUM = '". db2_escape_string($myCnum) . "' ";
    break;
}

$data = assetRequestsTable::returnForPortal($predicate);
$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages, 'predicate'=>$predicate,'isFm'=>$_SESSION['isFm'],'isPmo'=>$_SESSION['isPmo'],'showAll'=>$showAll);

ob_clean();
echo json_encode($response);
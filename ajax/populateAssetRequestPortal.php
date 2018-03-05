<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;


set_time_limit(0);
ob_start();

session_start();

$loader = new Loader();

// $_SESSION['isFm']   = !empty($isFm)   ? true : false;
// $_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
// $_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
// $_SESSION['isPes']  = !empty($isPes)  ? true : false;
// $_SESSION['isUser'] = !empty($isUser) ? true : false;


$predicate = null;
switch (true) {
    case $_SESSION['isFm']:
        $myCnum = personTable::myCnum();
        $myEmail = strtolower($GLOBALS['ltcuser']['mail']);
        $myPeople = $loader->load("CNUM",allTables::$PERSON," FM_CNUM='" . trim($myCnum) . "' ");
        $myPeopleListed = "'";
        foreach ($myPeople as $personCnum){
            $myPeopleListed .= db2_escape_string($personCnum) . "','"; 
        }        
        $myPeopleListed .= "'";        
        $predicate .= " CNUM in ('". db2_escape($myCnum) . "'," . $myPeopleListed . ") or lower(Approver_email='" . db2_escape_string($myEmail) . "') ";    
    break;
    case $_SESSION['isCdi']:
    case $_SESSION['isPmo']:
    ;
    break;
    default:
        $myCnum = personTable::myCnum();
        $predicate .= " CNUM = '". db2_escape($myCnum) . "' ";
    break;
}

$data = assetRequestsTable::returnAsArray($predicate);

$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages, 'predicate'=>$predicate);

ob_clean();
echo json_encode($response);
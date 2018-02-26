<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;


set_time_limit(0);
ob_start();

session_start();


// $_SESSION['isFm']   = !empty($isFm)   ? true : false;
// $_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
// $_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
// $_SESSION['isPes']  = !empty($isPes)  ? true : false;
// $_SESSION['isUser'] = !empty($isUser) ? true : false;


$predicate = null;
switch (true) {
    case $_SESSION['isFm']:
    ;
    break;
    case $_SESSION['isCdi']:
    case $_SESSION['isPmo']:
    ;
    break;
    default:
        ;
    break;
}


$data = assetRequestsTable::returnAsArray($predicate);

$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);
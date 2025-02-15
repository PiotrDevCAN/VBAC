<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if($_REQUEST['token']!= $token){
    return;
}
ob_start();

$hostname = !empty($_GET['hostname']) ? trim($_GET['hostname']) : null;
$cnum     = !empty($_GET['cnum'])     ? trim($_GET['cnum']) : null;
$status   = !empty($_GET['status'])   ? trim($_GET['status']) : null;

$sql = " SELECT lower(D.STATUS) as STATUS, count(*) as RECORDS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DLP . " AS D ";
$sql.= " WHERE 1=1 AND lower(status) = '" . htmlspecialchars($status) . "' " ;
$sql.= !empty($cnum)     ? " AND D.CNUM = '" . htmlspecialchars($cnum) . "'  " : null;
$sql.= !empty($hostname) ? " AND lower(D.HOSTNAME) = '" . htmlspecialchars(strtolower($hostname)) . "'  " : null;
$sql.= " GROUP BY D.STATUS ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if($rs){
    $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
    if(! $row){
        $row=array('STATUS'=>$status,'RECORDS'=>0);
    }
    $row = array_map('trim', $row);
    ob_clean();
    echo json_encode($row);
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    ob_start();
    echo json_encode(array('status'=>$status,'records'=>0,'err'=>$errorMessage));
}




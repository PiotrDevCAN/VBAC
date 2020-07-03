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
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$DLP . " AS D ";
$sql.= " WHERE 1=1 AND lower(status) = '" . db2_escape_string($status) . "' " ;
$sql.= !empty($cnum)     ? " AND D.CNUM = '" . db2_escape_string($cnum) . "'  " : null;
$sql.= !empty($hostname) ? " AND lower(D.HOSTNAME) = '" . db2_escape_string(strtolower($hostname)) . "'  " : null;
$sql.= " GROUP BY D.STATUS ";

$rs = db2_exec($_SESSION['conn'], $sql);

if($rs){
    $row = db2_fetch_assoc($rs);
    if(!$row){
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




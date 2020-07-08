<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$sql = " SELECT CNUM, " . db2_escape_string($_REQUEST['fieldName']) ;
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON;
$sql.= " FOR SYSTEM_TIME as of '" . db2_escape_string($_REQUEST['systemTime']) . "' ";
$sql.= " ORDER BY CNUM ";

$rs = db2_exec($GLOBALS['conn'], $sql);
$data = array();

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $data[trim($row['CNUM'])] = array_map('trim', $row);
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    exit();
}

ob_clean();
$json = json_encode($data);
echo $json;
<?php

use vbac\allTables;

set_time_limit(0);
ob_start();

$uid = trim($_POST['amendUid']);
$table = trim($_POST['amendTable']);
$value = trim($_POST['amendedValue']);

$execute = false;

switch ($table){
    case allTables::$STATIC_DOMAINS:
        $valueField = 'DOMAIN';
        $valueId = 'DOMAIN_ID';
        break;
    case allTables::$STATIC_ROLES:
        $valueField = 'ROLE_TITLE';
        $valueId = 'ROLE_ID';
        break;
    default:
        throw new \Exception('Unexpected Tablename passed in');
}

if($uid != 'newEntry'){
    $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $table;
    $sql .= " SET " . $valueField  . "  =  ? " ;
    $sql .= " WHERE " . $valueId . " = ? ";
    $preparedStmt = db2_prepare($GLOBALS['conn'], $sql);
    $data = array($value,$uid);
} else {
    $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . $table;
    $sql .= " ( " . $valueField  . ") values (?) " ;
    $preparedStmt = db2_prepare($GLOBALS['conn'], $sql);
    $data = array($value);
}

if(!$preparedStmt){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
} else {
    $execute = db2_execute($preparedStmt,$data);
    if(!$execute){
        echo db2_stmt_error();
        echo db2_stmt_errormsg();
    }
}

$messages = ob_get_clean();
ob_start();

$response = array('result'=>$execute ? true : false,'Messages'=>$messages);

ob_clean();
echo json_encode($response);
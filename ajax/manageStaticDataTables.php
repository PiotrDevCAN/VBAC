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
    $data = array($value,$uid);
} else {
    $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . $table;
    $sql .= " ( " . $valueField  . ") values (?) " ;
    $data = array($value);
}

$rs = sqlsrv_query($GLOBALS['conn'], $sql, $data);

if(!$rs){
    echo json_encode(sqlsrv_errors());
}
/* Free the statement resources. */
sqlsrv_free_stmt($rs);

$messages = ob_get_clean();
ob_start();

$response = array('result'=>$execute ? true : false,'Messages'=>$messages);

ob_clean();
echo json_encode($response);
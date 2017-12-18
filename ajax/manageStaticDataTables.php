<?php

set_time_limit(0);
ob_start();

$uid = trim($_POST['amendUid']);
$table = trim($_POST['amendTable']);
$value = trim($_POST['amendValue']);


if($uid == 'newEntry'){
    $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $table;
    $sql .= " SET ROLE_TITLE =  ? " ;
    $sql .= " WHERE ROLE_ID = ? ";
    $staticUpdate = db2_prepare($_SESSION['conn'], $sql);
    $data = array($value);
} else {
    $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . $table;
    $sql .= " ( ROLE_TITLE) values (?) " ;
    $staticUpdate = db2_prepare($_SESSION['conn'], $sql);
    $data = array($value);
}


$staticUpdate = db2_prepare($_SESSION['conn'], $sql);

if(!$staticUpdate){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

var_dump($hoursUpdate);

foreach ($_POST as $key => $value){
    if(substr($key,0,15)== "ModalHRSForWeek"){
        $week = substr($key,15,10);
        $hours = $value;

        $data = array($hours,$resourceReference, $week);
        $result = db2_execute($hoursUpdate,$data);

        var_dump($result);
    }
}

$messages = ob_get_clean();

$response = array('Messages'=>$messages);

ob_clean();
echo json_encode($response);
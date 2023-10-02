<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$data = array();

if(!empty($_GET['cnum'])){
    $cnum = $_GET['cnum'];
    $workerAPI = new WorkerAPI();
    $data = $workerAPI->getworkerByCNUM($cnum);
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
header('Content-Type: application/json');
echo json_encode($response);

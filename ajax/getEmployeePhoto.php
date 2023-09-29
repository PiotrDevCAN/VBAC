<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$data = array();

if(!empty($_GET['query'])){
    $cnum = $_GET['query'];
    $workerAPI = new WorkerAPI();
    $workerData = $workerAPI->getPhoto($cnum);
    if ($workerData['count'] > 0) {
        foreach($workerData['results'] as $key => $employee) {
            $data[] = $employee;
        }
    }
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
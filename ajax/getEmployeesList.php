<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$data = array();

if(!empty($_GET['query'])){
    $search = $_GET['query'];
    $workerAPI = new WorkerAPI();
    $workerData = $workerAPI->typeaheadSearch($search);
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
<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$data = array();

if(!empty($_GET['query'])){
    $search = $_GET['query'];

    $redis = $GLOBALS['redis'];
    $key = 'getEmployeesList_'.$search;
    $redisKey = md5($key.'_key_'.$_ENV['environment']);
    if (!$redis->get($redisKey)) {
        $source = 'SQL Server';
            
        $workerAPI = new WorkerAPI();
        $workerData = $workerAPI->typeaheadSearch($search);
        if ($workerData['count'] > 0) {
            foreach($workerData['results'] as $key => $employee) {
                $data[] = $employee;
            }
        }

        $redis->set($redisKey, json_encode($data));
        $redis->expire($redisKey, REDIS_EXPIRE);
    } else {
        $source = 'Redis Server';
        $data = json_decode($redis->get($redisKey), true);
    }
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
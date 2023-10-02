<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$data = array();

if(!empty($_GET['cnum'])){
    $cnum = $_GET['cnum'];

    $redis = $GLOBALS['redis'];
    $key = 'workerAPI_'.$cnum;
    $redisKey = md5($key.'_key_'.$_ENV['environment']);
    if (!$redis->get($redisKey)) {
        $source = 'SQL Server';
        
        $workerAPI = new WorkerAPI();
        $data = $workerAPI->getworkerByCNUM($cnum);

        $redis->set($redisKey, json_encode($data));
        $redis->expire($redisKey, REDIS_EXPIRE);
    } else {
        $source = 'Redis Server';
        $data = json_decode($redis->get($redisKey), true);
    }
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'source'=>$source);

ob_clean();
header('Content-Type: application/json');
echo json_encode($response);

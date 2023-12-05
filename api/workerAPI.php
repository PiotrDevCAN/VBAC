<?php

use itdq\WorkerAPI;

set_time_limit(0);
ob_start();

$source = '';
$data = array();
$additionalKey = null;
$APIType = null;

$cnum       = !empty($_GET['cnum']) ? trim($_GET['cnum']) : null;
$workerID   = !empty($_GET['workerID']) ? trim($_GET['workerID']) : null;
$emailid    = !empty($_GET['emailid']) ? trim($_GET['emailid']) : null;

switch(true) {
    case !empty($cnum):
        $additionalKey = $cnum;
        $APIType = 'cnum'; 
        break;
    case !empty($workerID):
        $additionalKey = $workerID;
        $APIType = 'workerID';
        break;
    case !empty($emailid):
        $additionalKey = $emailid;
        $APIType = 'email';
        break;
    default:
        break;
}

// if(!empty($cnum) || !empty($workerID || !empty($emailid))){
if (!empty($additionalKey)) {

    $redis = $GLOBALS['redis'];
    $key = 'workerAPI_'.$additionalKey;
    $redisKey = md5($key.'_key_'.$_ENV['environment']);
    if (!$redis->get($redisKey)) {
        $source = 'SQL Server';
        $data = array();
        $workerAPI = new WorkerAPI();
        switch($APIType) {
            case 'cnum':
                $data = $workerAPI->getworkerByCNUM($cnum);
                break;
            case 'workerID':
                $data = $workerAPI->getworkerByWorkerId($workerID);
                break;
            case 'email':
                $data = $workerAPI->getworkerByEmail($emailid);
                break;
            default:            
                break;
        }
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

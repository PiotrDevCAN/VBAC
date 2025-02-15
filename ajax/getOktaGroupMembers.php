<?php

use itdq\OKTAGroups;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getOktaGroupMembers';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
    
    $OKTAGroups = $GLOBALS['OKTAGroups'];
    $membersData = $OKTAGroups->getGroupMembers($GLOBALS['site']['cdiBgAz']);
    list('users' => $data, 'source' => $source) = $membersData;
    
    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

ob_clean();
echo json_encode($response);
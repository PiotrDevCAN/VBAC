<?php

use itdq\Loader;
use vbac\allTables;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getKnownCNUMs';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
        
    $predicate=null;

    $loader = new Loader();
    $data = $loader->load('CNUM', allTables::$PERSON, null, false);

    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);
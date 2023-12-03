<?php

use itdq\Loader;
use vbac\allTables;
use vbac\personTable;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getKnownKyndrylEMails';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
        
    $predicate=null;

    $loader = new Loader();
    $data = $loader->load('KYN_EMAIL_ADDRESS', allTables::$PERSON, personTable::normalCNUMPredicate(), false);

    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

ob_clean();
echo json_encode($response);
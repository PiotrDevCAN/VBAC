<?php

use vbac\knownValues\knownKyndrylEmails;

set_time_limit(0);
ob_start();

$direcory = new knownKyndrylEmails();
$knownData = $direcory->getData();
list('data' => $data, 'source' => $source) = $knownData;

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

ob_clean();
echo json_encode($response);
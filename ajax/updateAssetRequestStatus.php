<?php
use vbac\assetRequestsTable;

ob_start();

$comment = !empty($_POST['comment']) ? trim($_POST['comment']) : null;
assetRequestsTable::setStatus($_POST['reference'], $_POST['status'],$comment);

$messages = ob_get_clean();

$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
<?php
ob_start();

$post = print_r($_POST,true);

$response = array('result'=>'success','post'=>$post);
ob_clean();
echo json_encode($response);
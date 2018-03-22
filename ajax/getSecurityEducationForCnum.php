<?php
use vbac\personTable;

ob_start();

$securityEducation = !empty($_GET['cnum']) ? personTable::getSecurityEducationForCnum($_GET['cnum']) : false ;

$messages = ob_get_clean();
$response = array('securityEducation'=>$securityEducation,'messages'=>$messages);
ob_clean();
echo json_encode($response);
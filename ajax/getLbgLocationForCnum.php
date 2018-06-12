<?php
use vbac\personTable;

ob_start();

$details = !empty($_GET['cnum']) ? personTable::getLbgLocationForCnum($_GET['cnum']) : false ;

$messages = ob_get_clean();
$response = array('lbgLocation'=>$details['location'],'fmCnum'=>$details['fmCnum'],'messages'=>$messages);
ob_clean();
echo json_encode($response);
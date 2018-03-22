<?php
use vbac\personTable;

ob_start();

$lbgLocation = !empty($_GET['cnum']) ? personTable::getLbgLocationForCnum($_GET['cnum']) : false ;

$messages = ob_get_clean();
$response = array('lbgLocation'=>$lbgLocation,'messages'=>$messages);
ob_clean();
echo json_encode($response);
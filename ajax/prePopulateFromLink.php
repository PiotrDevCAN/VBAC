<?php
use vbac\personTable;

ob_start();
$personData = personTable::dataFromPreBoarder($_POST['cnum']);

$success = $personData ? true : false;

$messages = ob_get_clean();
$response = array("success"=>$success, "data"=>$personData,'messages'=>$messages);

ob_clean();
echo json_encode($response);
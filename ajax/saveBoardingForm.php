<?php
use vbac\personRecord;

ob_start();

print_r($_POST);

$person = new personRecord();
$person->setFromArray($_POST);

echo "******";

$person->iterateVisible();

$messages = ob_get_clean();


$response = array("data"=>'','messages'=>$messages,'success'=>true);

ob_clean();
echo json_encode($response);
<?php

use vbac\AgileSquadTable;

ob_start();

$squadNumber = trim($_REQUEST['squadNumber']);
$squadDetails = AgileSquadTable::getSquadDetails($squadNumber);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('squadDetails'=>$squadDetails,'success'=>$success, 'messages'=>$messages);
ob_clean();
echo json_encode($response);
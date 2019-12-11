<?php

use itdq\Loader;
use vbac\allTables;
use vbac\AgileSquadTable;

ob_start();

$squadNumber = trim($_REQUEST['squadNumber']);
$squadDetails = AgileSquadTable::getSquadDetails($squadNumber);

$messages = ob_get_clean();
$success = empty($messages) && $squadDetails;

$response = array('squadDetails'=>$squadDetails,'success'=>$success, 'messages'=>$messages);
ob_clean();
echo json_encode($response);
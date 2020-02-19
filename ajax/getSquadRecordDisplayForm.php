<?php
use itdq\Trace;
use itdq\FormClass;
use vbac\AgileSquadRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();
$squadRecord = new AgileSquadRecord();
$squadRecord->setTribeOrganisation($_POST['version']);
$squadRecord->displayForm(FormClass::$modeDEFINE);
$displayForm = ob_get_clean();

$response = array('displayForm'=>$displayForm);

ob_clean();
echo json_encode($response);


Trace::pageLoadComplete($_SERVER['PHP_SELF']);
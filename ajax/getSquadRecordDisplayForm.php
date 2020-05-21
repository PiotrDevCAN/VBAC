<?php
use itdq\Trace;
use itdq\FormClass;
use vbac\AgileSquadRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();
$tribeRecord = new AgileSquadRecord();
$tribeRecord->setTribeOrganisation($_POST['version']);
$tribeRecord->displayForm(FormClass::$modeDEFINE);
$displayForm = ob_get_clean();
ob_start();

$response = array('displayForm'=>$displayForm);

ob_clean();
echo json_encode($response);


Trace::pageLoadComplete($_SERVER['PHP_SELF']);
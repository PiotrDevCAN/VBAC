<?php
use itdq\AuditTable;
use itdq\AllItdqTables;

echo "<pre>";

AuditTable::audit("User:" . $_SESSION['ssoEmail'] . " Opening" . __FILE__);

var_Dump($GLOBALS['ltcuser']['mail']);

$cdi = employee_bluegroups($GLOBALS['ltcuser']['mail']);


var_dump($cdi);

$isCdi = employee_in_group($_SESSION['cdiBg'], $GLOBALS['ltcuser']['mail']);
$isPmo = employee_in_group($_SESSION['pmoBg'], $GLOBALS['ltcuser']['mail']);

var_dump($isCdi);

var_dump($isPmo);


echo "</pre>";

phpinfo();
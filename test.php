<?php
echo "<pre>";

var_Dump($GLOBALS['ltcuser']['mail']);

$cdi = employee_bluegroups($GLOBALS['ltcuser']['mail']);


var_dump($cdi);

$isCdi = employee_in_group($_SESSION['cdiBg'], $GLOBALS['ltcuser']['mail']);
$isPmo = employee_in_group($_SESSION['pmoBg'], $GLOBALS['ltcuser']['mail']);

var_dump($isCdi);

var_dump($isPmo);

var_dump($_SESSION);

echo "</pre>";

phpinfo();
<?php
use vbac\personTable;

unset($_SESSION['isFm']);
unset($_SESSION['isCdi']);
unset($_SESSION['isPmo']);
unset($_SESSION['isUser']);

$isFm   = personTable::isManager($GLOBALS['ltcuser']['mail'])                 ? ".not('.accessFm')" : null;
$isCdi  = employee_in_group($_SESSION['cdiBg'],  $GLOBALS['ltcuser']['mail']) ? ".not('.accessCdi')" : null;
$isPmo  = employee_in_group($_SESSION['pmoBg'],  $GLOBALS['ltcuser']['mail']) ? ".not('.accessPmo')" : null;
$isUser = ".not('.accessUser')";

// $isCdi   = stripos($_SERVER['environment'], 'dev') ? ".not('.accessCdi')"  : $isCdi;
// $isPmo   = stripos($_SERVER['environment'], 'dev')  ? ".not('.accessPmo')" : $isPmo;

$_SESSION['isFm']   = !empty($isFm)   ? true : false;
$_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
$_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
$_SESSION['isUser'] = !empty($isUser) ? true : false;


?>
<pre>
<h3>Session</h3>
<?=print_r($_SESSION)?>
</pre>
<?php
use vbac\personTable;

var_dump((ini_get('memory_limit')));

ini_set('memory_limit','150M'); 

var_dump((ini_get('memory_limit')));

var_dump($_SERVER['HTTP_HOST']);


echo "<div class='container'>";


echo "<h3>cdi_diags</h3>";

unset($GLOBALS['isFm']);
unset($GLOBALS['isCdi']);
unset($GLOBALS['isPmo']);
unset($GLOBALS['isUser']);
unset($GLOBALS['isPes']);

$isFm   = personTable::isManager($_SESSION['ssoEmail'])                 ? ".not('.accessFm')" : null;
$isCdi  = employee_in_group($_SESSION['cdiBg'],  $_SESSION['ssoEmail']) ? ".not('.accessCdi')" : null;
$isPmo  = employee_in_group($_SESSION['pmoBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPmo')" : null;
$isPes  = employee_in_group($_SESSION['pesBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPes')" : null;
$isUser = ".not('.accessUser')";


$GLOBALS['isFm']   = !empty($isFm)   ? true : false;
$GLOBALS['isCdi']  = !empty($isCdi)  ? true : false;
$GLOBALS['isPmo']  = !empty($isPmo)  ? true : false;
$GLOBALS['isUser'] = !empty($isUser) ? true : false;
$GLOBALS['isPes']  = !empty($isPes)  ? true : false;

?>

<h3>Session</h3>
<pre>
<?=print_r($_SESSION)?>
</pre>


<h3>Session</h3>
<pre>
<?=print_r($GLOBALS)?>
</pre>

<?php ini_set('MEMORY_LIMIT','150M'); ?>


<h3>phpInfo</h3>
<pre>
<?=phpinfo()?>
</pre>
<?php
use vbac\personTable;

var_dump((ini_get('max_execution_time')));

ini_set('max_execution_time', 360);

var_dump((ini_get('max_execution_time')));

var_dump((ini_get('memory_limit')));

ini_set('memory_limit','256M'); 

var_dump((ini_get('memory_limit')));

var_dump($_SERVER['HTTP_HOST']);

echo "<div class='container'>";

echo "<h3>cdi_diags</h3>";

unset($_SESSION['isFm']);
unset($_SESSION['isCdi']);
unset($_SESSION['isPmo']);
unset($_SESSION['isUser']);
unset($_SESSION['isPes']);

$isFm   = personTable::isManager($_SESSION['ssoEmail'])                 ? ".not('.accessFm')" : null;
$isCdi  = employee_in_group($_SESSION['cdiBg'],  $_SESSION['ssoEmail']) ? ".not('.accessCdi')" : null;
$isPmo  = employee_in_group($_SESSION['pmoBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPmo')" : null;
$isPes  = employee_in_group($_SESSION['pesBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPes')" : null;
$isUser = ".not('.accessUser')";

$_SESSION['isFm']   = !empty($isFm)   ? true : false;
$_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
$_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
$_SESSION['isUser'] = !empty($isUser) ? true : false;
$_SESSION['isPes']  = !empty($isPes)  ? true : false;

?>

<h3>Session</h3>
<pre>
<?=print_r($_SESSION)?>
</pre>


<h3>Session</h3>
<pre>
<?=print_r($GLOBALS)?>
</pre>

<?php ini_set('MEMORY_LIMIT','512M'); ?>


<h3>phpInfo</h3>
<pre>
<?=phpinfo()?>
</pre>
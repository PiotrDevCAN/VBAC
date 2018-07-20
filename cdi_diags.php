<?php
use vbac\personTable;

var_dump((ini_get('memory_limit')));

ini_set('memory_limit','150M'); 

var_dump((ini_get('memory_limit')));


echo "<div class='container'>";


echo "<h3>cdi_diags</h3>";

unset($_SESSION['isFm']);
unset($_SESSION['isCdi']);
unset($_SESSION['isPmo']);
unset($_SESSION['isUser']);
unset($_SESSION['isPes']);

$isFm   = personTable::isManager($GLOBALS['ltcuser']['mail'])                 ? ".not('.accessFm')" : null;
$isCdi  = employee_in_group($_SESSION['cdiBg'],  $GLOBALS['ltcuser']['mail']) ? ".not('.accessCdi')" : null;
$isPmo  = employee_in_group($_SESSION['pmoBg'],  $GLOBALS['ltcuser']['mail']) ? ".not('.accessPmo')" : null;
$isPes  = employee_in_group($_SESSION['pesBg'],  $GLOBALS['ltcuser']['mail']) ? ".not('.accessPes')" : null;
$isUser = ".not('.accessUser')";


$_SESSION['isFm']   = !empty($isFm)   ? true : false;
$_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
$_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
$_SESSION['isUser'] = !empty($isUser) ? true : false;
$_SESSION['isPes']  = !empty($isPes)  ? true : false;

?>
<pre>
<h3>Session</h3>
<?=print_r($_SESSION)?>
</pre>

<pre>
<h3>Session</h3>
<?=print_r($GLOBALS)?>
</pre>

<?php ini_set('MEMORY_LIMIT','150M'); ?>

<pre>
<h3>phpInfo</h3>
<?=phpinfo()?>
</pre>


</div>

<?php
use vbac\allTables;

?>
<div class='container greyablePage'>

<?php 


$loader = new Loader();
$allStatus = $loader->load('ORDERIT_STATUS',allTables::$ASSET_REQUESTS," AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ");
array_map('trim',$allStatus);

var_dump($allStatus);
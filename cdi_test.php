<?php
use vbac\allTables;

?>
<div class='container greyablePage'>

<?php 


// $loader = new Loader();
// $allStatus = $loader->load('ORDERIT_STATUS',allTables::$ASSET_REQUESTS," AR.REQUEST_RETURN = 'No' or AR.REQUEST_RETURN is null ");
// array_map('trim',$allStatus);

// var_dump($allStatus);


$date = '2018-04-12 12:12:14.0';
$date = '2018-04-11 10:43:25.0';




$start = new DateTime($date);


echo $start->format('YmdHis');


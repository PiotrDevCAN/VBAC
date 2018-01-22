<?php
use itdq\Loader;
use itdq\BluePages;

$loader = new Loader();

$allCnum = $loader->load('CNUM',"CNUM4BP");
$chunkedCnum = array_chunk($allCnum, 100);

$detailsFromBp = "&manager&worklocation&employeetype";


foreach ($chunkedCnum as $cnumList){
    $jsonObject = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);

    echo "<pre>";
    print_r($jsonObject);
    echo "</pre>";

}
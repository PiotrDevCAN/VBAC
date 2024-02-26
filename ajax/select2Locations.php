<?php
use itdq\Loader;
use vbac\allTables;

ob_start();

$loader = new Loader();
$locationsByCity = $loader->loadIndexed('CITY','ADDRESS',allTables::$STATIC_LOCATIONS);
$children = array();
$results  = array();

$locationsByCityEncoded = array();
foreach ($locationsByCity as $key=>$value){
    $value = utf8_encode( trim($value) );
    $key   = utf8_encode( trim($key) );
    $locationsByCityEncoded[$key] = $value;
}

foreach ($locationsByCityEncoded as $location => $city){
    $object = new stdClass();
    $object->id = trim($location);
    $object->text = trim($location);
    $children[trim($city)][] = $object;
}

foreach ($children as $city => $locations){
    $group = new stdClass();
    $group->text = $city;
    $group->children = $locations;
    $results[] = $group;
}

$diags = ob_get_clean();
ob_start();
$selectDropDown  = array('results'=>$results);
ob_clean();
echo json_encode($selectDropDown);
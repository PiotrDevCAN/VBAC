<?php

use itdq\Bluepages;

$locations = array('d4t','wwk');

$json = Bluepages::lookupLocations($locations);


$allLocations = $json->search->entry;

$workloc = '';
$c = '';
$l = '';

$locationDetails = array();


foreach ($allLocations as $location){
    foreach ($location->attribute as $attribute){
        $name = $attribute->name;
        $$name = $attribute->value[0];

        echo $name .  $$name;



    }
    $allLocationDetails[$workloc] = array('country'=>$c,'city'=>$l);
}

echo "<pre>";

var_dump($allLocationDetails);

echo "</pre>";



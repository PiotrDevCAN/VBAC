<?php
use itdq\Loader;
use vbac\allTables;

ob_start();

// $results = '{
//   "results": [
//     {
//       "text": "Group 1",
//       "children" : [
//         {
//             "id": 1,
//             "text": "Option 1.1"
//         },
//         {
//             "id": 2,
//             "text": "Option 1.2"
//         }
//       ]
//     },
//     {
//       "text": "Group 2",
//       "children" : [
//         {
//             "id": 3,
//             "text": "Option 2.1"
//         },
//         {
//             "id": 4,
//             "text": "Option 2.2"
//         }
//       ]
//     }
//   ]
// }';

$loader = new Loader();
$locationsByCity = $loader->loadIndexed('CITY','ADDRESS',allTables::$STATIC_LOCATIONS);
$children = array();
$results  = array();

// $asString = print_r($locationsByCity,true);


// $encoding = mb_detect_encoding($asString);

// var_dump($encoding);

$locationsByCityEncoded = array();
foreach ($locationsByCity as $key=>$value){
    $value = utf8_encode( trim($value) );
    $key   = utf8_encode( trim($key) );
    $locationsByCityEncoded[$key] = $value;
}

// $encoding = mb_detect_encoding($asString);

// var_dump($encoding);


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
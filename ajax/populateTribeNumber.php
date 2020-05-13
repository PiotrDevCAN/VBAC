<?php
use itdq\Trace;
use itdq\Loader;
use vbac\allTables;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$tribeTable = $_GET['version']=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_OLD;
$loader = new Loader();

$allTribes = $loader->loadIndexed('TRIBE_NAME','TRIBE_NUMBER', $tribeTable, " ORGANISATION='" . $_GET['organisation'] . "' ");

$data = array();
$tribeSelected = !empty($_GET['tribeNumber']) ? $_GET['tribeNumber'] : null;
foreach ($allTribes as $tribeNumber => $tribeName) {
    $newOption = array('id'=>$tribeNumber,'text'=>$tribeName);
    if($tribeNumber==$tribeSelected){
        $newOption['selected'] = true;
    }
    $data[] = $newOption;
}

$messages = ob_get_clean();
$response = array("results"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
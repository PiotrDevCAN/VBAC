<?php

use vbac\allTables;
use vbac\requestableAssetListTable;

set_time_limit(0);
ob_start();

// session_start();

$requestableAssetList = new requestableAssetListTable(allTables::$REQUESTABLE_ASSET_LIST);
$data = $requestableAssetList->returnAsArray();

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
    $response = array("data"=>$data,'messages'=>$messages);
} else {
    $response = array("data"=>'error, data can\'t be JSON ENcoded, look for special chars','messages'=>$messages);

}
ob_clean();
echo json_encode($response);
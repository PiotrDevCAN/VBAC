<?php

use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;

$loader = new Loader();
$allOrderItGroups = $loader->load('ORDER_IT_GROUP',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);


foreach ($allOrderItGroups as $orderItGroup){
    $rows = $assetRequestTable->getRowsForOrderIt($orderItGroup);   
    echo "<h4>Order IT Group : $orderItGroup" . "</h4>";
    echo "<pre>";    
    var_dump($rows);
    echo "</pre>";
}
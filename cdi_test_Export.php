<?php

use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;

$loader = new Loader();
$allOrderItTypes = $loader->load('ORDER_IT_TYPE',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$tempFile = tmpfile();


foreach ($allOrderItTypes as $orderItType){    
    
   $totalRequestsForType = 0; 
   $outstandingRequestsForType = 0;

   while(($outstandingRequestsForType = $assetRequestTable->countApprovedForOrderItType($orderItType)) > 0){
        $totalRequestsForType += $outstandingRequestsForType;
        $fileOfRequests = $assetRequestTable->getRequestsForOrderIt($orderItType, $tempFile);       
   }
   echo "<h5>Total requests for Order IT Type " . $orderItType . " :" . $totalRequestsForType;
}

fseek($tempFile, 0);

while(($row=fgets($tempFile))){
    echo "<br/>" . $row;
}

fclose($tempFile);


<?php

use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;
use itdq\BlueMail;

$loader = new Loader();
$allOrderItTypes = $loader->load('ORDER_IT_TYPE',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

foreach ($allOrderItTypes as $orderItType){    
    
   $totalRequestsForType = 0; 
   $outstandingRequestsForType = 0;

   while(($outstandingRequestsForType = $assetRequestTable->countApprovedForOrderItType($orderItType)) > 0){
        $totalRequestsForType += $outstandingRequestsForType;
        $base64EncodedData = $assetRequestTable->getRequestsForOrderIt($orderItType);       
   }
   echo "<h5>Total requests for Order IT Type " . $orderItType . " :" . $totalRequestsForType;
}

var_dump($base64EncodedData);

$sendResponse = BlueMail::send_mail(array('robdaniel@uk.ibm.com'), 'orderit export', 'Find attached CSV not',
    'robdaniel@uk.ibm.com',array(),array(),true);

// array('filename'=>'export.csv','content_type'=>'text/csv','data'=>$base64EncodedData));

echo "<pre>";

var_dump($sendResponse);

echo "</pre>";

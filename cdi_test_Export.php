<?php

use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;
use itdq\BlueMail;

$loader = new Loader();
$allOrderItTypes = $loader->load('ORDER_IT_TYPE',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$base64EncodedData = '';

foreach ($allOrderItTypes as $orderItType){    
    
   $totalRequestsForType = 0; 
   $outstandingRequestsForType = 0;

   while(($outstandingRequestsForType = $assetRequestTable->countApprovedForOrderItType($orderItType)) > 0){
        $totalRequestsForType += $outstandingRequestsForType;
        $base64EncodedData = $assetRequestTable->getRequestsForOrderIt($orderItType);       
   }
   echo "<h5>Total requests for Order IT Type " . $orderItType . " :" . $totalRequestsForType;
}

if(empty($base64EncodedData)){
    echo "No requests found to export";
    exit('No requests');
}
$sendResponse = BlueMail::send_mail(array('Piotr.Tajanowicz@kyndryl.com'), 'orderit export', 'Find attached CSV',
    'Piotr.Tajanowicz@kyndryl.com',array(),array(),true,array(array('filename'=>'test.txt','content_type'=>'text/plain','data'=>$base64EncodedData)));

// array('filename'=>'export.csv','content_type'=>'text/csv','data'=>$base64EncodedData));

echo "<pre>";
var_dump($base64EncodedData);
var_dump($sendResponse);

echo "</pre>";

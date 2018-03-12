<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;
use itdq\BlueMail;


ob_start();
$now = new DateTime();
$loader = new Loader();
$allOrderItTypes = $loader->load('ORDER_IT_TYPE',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$requestData = '';
$varbsCovered = array();
$first = true;

foreach ($allOrderItTypes as $orderItType){
    
    $totalRequestsForType = 0;
    $outstandingRequestsForType = 0;
    
    while(($outstandingRequestsForType = $assetRequestTable->countApprovedForOrderItType($orderItType)) > 0){
        $totalRequestsForType += $outstandingRequestsForType;
        $requestData .= $assetRequestTable->getRequestsForOrderIt($orderItType,$first);
        $varbsCovered[] = $assetRequestTable->currentVarb;
        $first = false;
    }
    echo "<h5>Total requests for Order IT Type " . $orderItType . " :" . $totalRequestsForType;
}

// $decoded = base64_decode($base64EncodedData);
// var_dump($decoded);

// $dummy = 'VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ=';

$varbRange = $varbsCovered[0];
$varbRange .= count($varbsCovered) > 1 ? " => " . $varbsCovered[count($varbsCovered)-1] : null;
$csvName = "varbForOrderIt_" . $now->format('Y-m-d h:i:s') . ".csv";


$base64EncodedData = base64_encode($requestData);



if(empty($base64EncodedData)){
    $messages = ob_get_clean();
    $messages .= "<br/>No requests found to export";
    $response = array('success'=>false,'messages'=>$messages,'post'=>print_r($_POST,true));
    echo json_encode($response);
} else {
    $sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'vBac Orderit Export: ' . $varbRange, 'Find attached CSV or Asset Request Details ready for Order IT',
        'rob.daniel@uk.ibm.com',array(),array(),true,array(array('filename'=>$csvName,'content_type'=>'text/plain','data'=>$base64EncodedData)));

    $messages = ob_get_clean();
    $response = array('success'=>true,'messages'=>$messages,"sendResponse"=>$sendResponse,'post'=>print_r($_POST,true));
    echo json_encode($response);
}
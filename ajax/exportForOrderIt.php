<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\Loader;
use itdq\BlueMail;
use vbac\personRecord;


ob_start();

// $ctb = isset($_REQUEST['ctb']) ? $_REQUEST['ctb']=='true' : false;
// $predicate = $ctb ? " AND CTB_RTB='CTB' " : " AND (CTB_RTB is null or CTB_RTB != 'CTB' ) ";
// $pmoTaskid = $ctb ? personRecord::$orderITCtbTaskId : personRecord::$orderITNonCtbTaskId;

$bau = isset($_REQUEST['bau']) ? $_REQUEST['bau']=='true' : false;
$predicate = $bau ? " AND TT_BAU='BAU' " : " AND (TT_BAU is null or TT_BAU != 'BAU' ) ";
$pmoTaskid = $bau ? personRecord::$orderITBauTaskId : personRecord::$orderITNonBauTaskId;


$now = new DateTime();
$loader = new Loader();
$allOrderItTypes = $loader->load('ORDER_IT_TYPE',allTables::$REQUESTABLE_ASSET_LIST);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$requestData = '';
$varbsCovered = array();
$first = true;
$lastSql = array();
foreach ($allOrderItTypes as $orderItType){

    $totalRequestsForType = 0;
    $outstandingRequestsForType = 0;

    while(($outstandingRequestsForType = $assetRequestTable->countApprovedForOrderItType($orderItType, $predicate)) > 0){
        $lastSql[] = $assetRequestTable->getLastSql();

        $totalRequestsForType += $outstandingRequestsForType;

        $requestData .= $assetRequestTable->getRequestsForOrderIt($orderItType,$first, $predicate);
        $lastSql[] = $assetRequestTable->getLastSql();

        $varbsCovered[] = $assetRequestTable->currentVarb;
        $first = false;

        $lastSql[] = $assetRequestTable->getLastSql();
    }
    echo "<h5>Total requests for Order IT Type " . $orderItType . " :" . $totalRequestsForType;
    $lastSql[] = $assetRequestTable->getLastSql();

}

// $decoded = base64_decode($base64EncodedData);
// var_dump($decoded);

// $dummy = 'VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ=';



$varbRange = !empty($varbsCovered[0]) ? $varbsCovered[0] : null;
$varbRange .= count($varbsCovered) > 1 ? " => " . $varbsCovered[count($varbsCovered)-1] : null;
$csvName = "varbForOrderIt_" . $now->format('Y-m-d h:i:s') . ".csv";


$base64EncodedData = base64_encode($requestData);

if(empty($base64EncodedData)){
    $messages = ob_get_clean();
    $messages .= "<br/>No requests found to export";
    $response = array('success'=>false,'messages'=>$messages,'post'=>print_r($_REQUEST,true),'lastSql'=>print_r($lastSql,true));
    echo json_encode($response);
} else {
    $titlePrefix = $bau ? "(BAU)" : "(Non-BAU)";

    $sendResponse = BlueMail::send_mail($pmoTaskid, 'vBac Orderit Export' . $titlePrefix . ': ' . $varbRange, 'Find attached CSV of Asset Request Details ready for Order IT',
        'vbacNoReply@uk.ibm.com',array(),array(),true,array(array('filename'=>$csvName,'content_type'=>'text/plain','data'=>$base64EncodedData)));

    $messages = ob_get_clean();
    $response = array('success'=>true,'messages'=>$messages,"sendResponse"=>$sendResponse,'post'=>print_r($_REQUEST,true),'lastSql'=>print_r($lastSql,true));
    echo json_encode($response);
}
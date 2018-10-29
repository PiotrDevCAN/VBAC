<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\personTable;
use vbac\assetRequestRecord;
use itdq\Loader;
use vbac\assetRequestsEventsTable;
use itdq\AllItdqTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$personTable = new personTable(allTables::$PERSON);

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

$success = false;

foreach ($_POST['status'] as $reference => $statusIndicator){
    set_time_limit(60);
    $status = trim($statusIndicator);
    $ref = trim($reference);
    $comment = isset($_POST["comment"][$ref]) ? $_POST["comment"][$ref] : null ;
    $orderItResponded = isset($_POST["orderit_responded"][$ref]) ? $_POST["orderit_responded"][$ref] : null ;

    $success = $assetRequestTable->setRequestsOrderItStatus($reference,$status,$comment);
    echo "Ref:$reference Status:$status Comment:$comment LBG Responded : $orderItResponded Success:" . $success;
    if($success && !empty($comment)){
        $success = $assetRequestTable->updateCommentForOrderItStatus($reference, $comment);
    }
    if($success && !empty($orderItResponded)){
        $success = $assetRequestTable->updateOrderItResponded($reference, $orderItResponded);
    }
    
    // Now, if we're APPROVE and we have a PRIMAR_UID, save that too.
    if($success && trim($statusIndicator)==assetRequestRecord::STATUS_ORDERIT_APPROVED && !empty($_POST['primaryUid'][$reference])){
       $primaryUid = $_POST['primaryUid'][$reference];
        $assetRequestTable->updateUids($reference, trim($primaryUid));
        $assetRequestTable->setToProvisionedStatus($reference);
        $requestDetails = $assetRequestTable->getCnumAndAssetForReference($reference);
        if($requestDetails){
            $personTable->assetUpdate($requestDetails['cnum'], $requestDetails['assetTitle'], $primaryUid);
        }
    }
    // Now, if we're setting to approved - ?Has that released any of our pre-reqs, if so log that event
    if($success && trim($statusIndicator)==assetRequestRecord::STATUS_ORDERIT_APPROVED){
        $assetRequestEventsTable = new assetRequestsEventsTable(allTables::$ASSET_REQUESTS_EVENTS);
        $loader = new Loader();
        $postReqs = $loader->load('REQUEST_REFERENCE',allTables::$ASSET_REQUESTS," PRE_REQ_REQUEST='" . db2_escape_string($reference) . "' ");
        foreach ($postReqs as $postReqReference){
            $assetRequestEventsTable->logEventForRequest(assetRequestsEventsTable::EVENT_PRE_REQ_APPROVED, $postReqReference);
        }
    }
}

if($success){
    db2_commit($_SESSION['conn']);
} else {
    db2_rollback($_SESSION['conn']);
}

db2_autocommit($_SESSION['conn'],$autoCommit);

$messages = ob_get_clean();
$response = array('result'=>$success,'post'=>print_r($_POST,true),'messages'=>$messages);

ob_clean();
echo json_encode($response);
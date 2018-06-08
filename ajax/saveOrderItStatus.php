<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\personTable;
use vbac\assetRequestRecord;

function timestampNow($comment){
    $new = new DateTime();
    echo "<br/>" . $new->format('H:i:s.u') . ":" . $comment;
}


ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$personTable = new personTable(allTables::$PERSON);

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

$success = false;

timestampNow('Before Loop');

foreach ($_POST['status'] as $reference => $statusIndicator){
    set_time_limit(60);
    $status = trim($statusIndicator);
    $ref = trim($reference);
    $comment = isset($_POST["comment"][$ref]) ? $_POST["comment"][$ref] : null ;
    echo "Ref:$reference Status:$status Comment:$comment";
    $success = $assetRequestTable->setRequestsOrderItStatus($reference,$status,$comment);
    if($success && !empty($comment)){
        $success = $assetRequestTable->updateCommentForOrderItStatus($reference, $comment);
    }
    // Now, if we're APPROVE and we have a PRIMAR_UID, save that too.
    if($success && trim($statusIndicator)==assetRequestRecord::STATUS_ORDERIT_APPROVED && !empty($_POST['primaryUid'][$reference])){
       $primaryUid = $_POST['primaryUid'][$reference];
        $assetRequestTable->updateUids($reference, trim($primaryUid));
        $assetRequestsTable->setToProvisionedStatus($reference);
        $requestDetails = $assetRequestTable->getCnumAndAssetForReference($reference);
        if($requestDetails){
            $personTable->assetUpdate($requestDetails['cnum'], $requestDetails['assetTitle'], $primaryUid);
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
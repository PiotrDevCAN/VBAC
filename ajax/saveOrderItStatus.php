<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use itdq\AuditTable;

function timestampNow($comment){
    $new = new DateTime();
    echo "<br/>" . $new->format('H:i:s.u') . ":" . $comment;
}


ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

$success = false;

timestampNow('Before Loop');

foreach ($_POST['status'] as $reference => $statusIndicator){
    set_time_limit(60);
    $status = trim($statusIndicator);
    $ref = trim($reference);
    $comment = isset($_POST["comment"][$ref]) ? $_POST["comment"][$ref] : null ;
    echo "Ref:$reference Status:$status Comment:$comment";
    timestampNow('Before set Status');
    $success = $assetRequestTable->setRequestsOrderItStatus($reference,$status,$comment);
    timestampNow('After set status');
    if($success && !empty($comment)){
        timestampNow('before update comment');
        $success = $assetRequestTable->updateCommentForOrderItStatus($reference, $comment);
        timestampNow('after update comment');
    }
    if(!$success){
        db2_rollback($_SESSION['conn']);
        break;
    }
}

if($success){
    db2_commit($_SESSION['conn']);
}

db2_autocommit($_SESSION['conn'],$autoCommit);

timestampNow('about to return');

$messages = ob_get_clean();

$response = array('result'=>$success,'post'=>print_r($_POST,true),'messages'=>$messages);



ob_clean();
echo json_encode($response);
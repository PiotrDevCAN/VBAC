<?php
use vbac\personTable;
use vbac\assetRequestRecord;
use itdq\DbTable;
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personRecord;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$post = print_r($_POST,true);
$now = new DateTime();
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$records = array();
$assetRequests = array();
$assetReferences = array();

$requested = $now->format('Y-m-d h:i:s');

$approvingMgrEmail = personTable::getEmailFromCnum(trim($_POST['approvingManager']));

$autoApproved = strtoupper(trim($_POST['requestor'])) == strtoupper(trim($approvingMgrEmail)) || isset($_POST['REQUEST_RETURN']);
//$status = $autoApproved ? assetRequestRecord::STATUS_APPROVED : assetRequestRecord::STATUS_CREATED;
$approved = $autoApproved ? $requested : null;

$requestReturn = isset($_POST['REQUEST_RETURN']) ? 'Yes' : 'No';

$notifyApprovingMgr = false;

// $orderItStatus = empty($_POST['ORDERIT_NUMBER']) ? assetRequestRecord::STATUS_ORDERIT_YET : assetRequestRecord::STATUS_ORDERIT_RAISED;
// $userCreated   = empty($_POST['ORDERIT_NUMBER']) ? assetRequestRecord::CREATED_PMO : assetRequestRecord::CREATED_USER;



switch (true) {
    case isset($_POST['REQUEST_RETURN']) && !empty($_POST['ORDERIT_NUMBER']) :
        // Raising a Return request that has already been Raised with LBG.
        $approvingMgrEmail = $_SESSION['ssoEmail'];
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_RAISED;
        $status = assetRequestRecord::STATUS_RAISED_ORDERIT;
        $userCreated = assetRequestRecord::CREATED_USER;
        break;
    case isset($_POST['REQUEST_RETURN']) && empty($_POST['ORDERIT_NUMBER']) :
        // Raising a Return request that has NOT already been Raised with LBG.
        $approvingMgrEmail = $_SESSION['ssoEmail'];
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_YET;
        $status = assetRequestRecord::STATUS_APPROVED;
        $userCreated = assetRequestRecord::CREATED_PMO;
        break;
    case $autoApproved && !empty($_POST['ORDERIT_NUMBER']) :
        // This is a manager, entering details of a request that has already been Raised with LBG.
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_RAISED;
        $status = assetRequestRecord::STATUS_AWAITING_IAM;
        $userCreated = assetRequestRecord::CREATED_USER;
    break;
    case !$autoApproved && !empty($_POST['ORDERIT_NUMBER']) :
        // Someone (Not the approving mgr) raising a request that has already been Raised with LBG.
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_RAISED;
        $status = assetRequestRecord::STATUS_CREATED;
        $userCreated = assetRequestRecord::CREATED_USER;
        $notifyApprovingMgr = true;
    break;
    case $autoApproved && empty($_POST['ORDERIT_NUMBER']) :
        // Approving Mgr raising it - it's NOT in ORDER IT Yet,
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_YET;
        $status = assetRequestRecord::STATUS_APPROVED;
        $userCreated = assetRequestRecord::CREATED_PMO;
    break;
    case !$autoApproved && empty($_POST['ORDERIT_NUMBER']) :
        // NOY the Approving Mgr raising it - it's NOT in ORDER IT Yet,
        $orderItStatus = assetRequestRecord::STATUS_ORDERIT_YET;
        $status = assetRequestRecord::STATUS_CREATED;
        $userCreated = assetRequestRecord::CREATED_PMO;
        $notifyApprovingMgr = true;
    break;
    default:
        ;
    break;
}

$personTable = new personTable(allTables::$PERSON);


foreach ($_POST as $key => $value){
    $decodedKey = urldecode($key);
    $split=array();
    $isAssetRequest = preg_match('/person-([\d]+)-asset-([\d]+)-([\D0-9]+)/', $decodedKey, $split);
    if($isAssetRequest && $value=='on') {
        $personId = $split[1];
        $assetId  = $split[2];
        $assetTitle = $split[3];
        $justification = !isset($_POST['person-'.$personId.'-justification-'.$assetId]) ? null : $_POST['person-'.$personId.'-justification-'.$assetId];
        $location = $_POST['person-'.$personId.'-location'];
        $cnum = $_POST['requestee'];
        $educationConfirmed = personTable::getSecurityEducationForCnum($cnum);

//         $email = $_POST[$cnum];

        $email = $personTable->getEmailFromCnum($cnum);

        $assetRequest = array(
            'CNUM'=>$cnum
            ,'ASSET_TITLE'=>$assetTitle
            ,'USER_LOCATION'=>$location
            ,'BUSINESS_JUSTIFICATION' => $justification
            ,'REQUESTOR_EMAIL'=>$_POST['requestor']
            ,'REQUESTED'=>$requested
            ,'APPROVER_EMAIL'=>$approvingMgrEmail
            ,'APPROVED'=>$approved
            ,'EDUCATION_CONFIRMED'=>$educationConfirmed
            ,'STATUS'=>$status
            ,'ORDERIT_NUMBER'=>$_POST['ORDERIT_NUMBER']
            ,'ORDERIT_STATUS'=>$orderItStatus
            ,'USER_CREATED' => $userCreated
            ,'REQUEST_RETURN'=> $requestReturn
            );


        try {
            $assetRequestRecord = new assetRequestRecord();
            $assetRequestRecord->setFromArray($assetRequest);

            $records[] = print_r($assetRequestRecord,true);


            $assetRequestTable->saveRecord($assetRequestRecord);
            $assetReferences[] = $assetRequestTable->lastId();
            $requestDetails = ($status == assetRequestRecord::STATUS_RAISED_ORDERIT ) || ($status == assetRequestRecord::STATUS_APPROVED ) ? "<div class='bg-success'>" : "<div class='bg-warning'>" ;
            $requestDetails .= "<br/>Request :<strong>" .$assetRequestTable->lastId();
            $requestDetails .= "</strong><br/>Requestee: <strong>" .  $email . "</strong> Asset:<em>" . $assetTitle . "</em>";
            $requestDetails .= ' Status: <strong>' . $status . '</strong>';
            $assetRequests[] = $requestDetails;

            $rest = $personTable->updateLbgLocationForCnum($location, $cnum);

//            echo "<br/>Location:$location Cnum:$cnum Result:$rest Security: $educationConfirmed" ;


        } catch (Exception $e) {
            $messages = ob_get_clean();
            $response = array('result'=>'failed','post'=>$post,'messages'=>$messages);
            echo json_encode($response);
        }
    }
}

$notifyApprovingMgr && !empty($approvingMgrEmail) ? $assetRequestTable->notifyApprovingMgr(array($approvingMgrEmail)) : null;

$assetRequestTable->linkPrereqs($assetReferences);

$messages = ob_get_clean();

$result = empty($messages) ? 'success' : 'failed';

$response = array('result'=>$result,'messages'=>$messages,'requests'=>$assetRequests, 'post'=>$post,'approvingMgrEmail'=>$approvingMgrEmail, 'records'=>$records);
AuditTable::audit("Concluded:<b>" . __FILE__ . "</b>Response:<pre>" . print_r($response,true) ."</pre>",AuditTable::RECORD_TYPE_DETAILS);

echo json_encode($response);
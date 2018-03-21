<?php
use vbac\personTable;
use vbac\assetRequestRecord;
use itdq\DbTable;
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

$post = print_r($_POST,true);
$now = new DateTime();
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$records = array();
$assetRequests = array();

$requested = $now->format('Y-m-d h:i:s');

$approvingMgrEmail = personTable::getEmailFromCnum(trim($_POST['approvingManager']));
$autoApproved = strtoupper(trim($_POST['requestor'])) == strtoupper(trim($approvingMgrEmail));
//$status = $autoApproved ? assetRequestRecord::$STATUS_APPROVED : assetRequestRecord::$STATUS_CREATED;
$approved = $autoApproved ? $requested : null;

$educationConfirmed = !empty($_POST['EDUCATION_CONFIRMED']) ? $_POST['EDUCATION_CONFIRMED'] : 'No';

// $orderItStatus = empty($_POST['ORDERIT_NUMBER']) ? assetRequestRecord::$STATUS_ORDERIT_YET : assetRequestRecord::$STATUS_ORDERIT_RAISED;
// $userCreated   = empty($_POST['ORDERIT_NUMBER']) ? assetRequestRecord::$CREATED_PMO : assetRequestRecord::$CREATED_USER;



switch (true) {
    case $autoApproved && !empty($_POST['ORDERIT_NUMBER']) :
        // This is a manager, entering details of a request that has already been raised in ORDER IT.
        $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_RAISED;
        $status = assetRequestRecord::$STATUS_RAISED_ORDERIT;
        $userCreated = assetRequestRecord::$CREATED_USER;
    break;
    case !$autoApproved && !empty($_POST['ORDERIT_NUMBER']) :
        // Someone (Not the approving mgr) raising a request that has already been raised in ORDER IT.
        $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_RAISED;
        $status = assetRequestRecord::$STATUS_CREATED;
        $userCreated = assetRequestRecord::$CREATED_USER;
    break;
    case $autoApproved && empty($_POST['ORDERIT_NUMBER']) :
        // Approving Mgr raising it - it's NOT in ORDER IT Yet, 
        $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_YET;
        $status = assetRequestRecord::$STATUS_APPROVED;
        $userCreated = assetRequestRecord::$CREATED_PMO;
    break;
    case !$autoApproved && empty($_POST['ORDERIT_NUMBER']) :
        // NOY the Approving Mgr raising it - it's NOT in ORDER IT Yet,
        $orderItStatus = assetRequestRecord::$STATUS_ORDERIT_YET;
        $status = assetRequestRecord::$STATUS_CREATED;
        $userCreated = assetRequestRecord::$CREATED_PMO;
    break;
    default:
        ;
    break;
}

var_dump($autoApproved);
var_dump(empty($_POST['ORDERIT_NUMBER']));
var_dump($status);



foreach ($_POST as $key => $value){
    $decodedKey = urldecode($key);
    $split=array();
    $isAssetRequest = preg_match('/person-([\d]+)-asset-([\d]+)-([\D]+)/', $decodedKey, $split);
    if($isAssetRequest && $value=='on') {
        $personId = $split[1];
        $assetId  = $split[2];
        $assetTitle = $split[3];
        $justification = !isset($_POST['person-'.$personId.'-justification-'.$assetId]) ? null : $_POST['person-'.$personId.'-justification-'.$assetId];

        $email = $_POST[$_POST['requestee']];
        
        $assetRequest = array(
            'CNUM'=>$_POST['requestee']
            ,'ASSET_TITLE'=>$assetTitle
            ,'USER_LOCATION'=>$_POST['person-'.$personId.'-location']
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
            );

        
        try {
            $assetRequestRecord = new assetRequestRecord();
            $assetRequestRecord->setFromArray($assetRequest);
            
            $records[] = print_r($assetRequestRecord,true);
            
            
            $assetRequestTable->saveRecord($assetRequestRecord);
            $requestDetails = ($status == assetRequestRecord::$STATUS_RAISED_ORDERIT ) || ($status == assetRequestRecord::$STATUS_APPROVED ) ? "<div class='bg-success'>" : "<div class='bg-warning'>" ;
            $requestDetails .= "<br/>Request :<strong>" .$assetRequestTable->lastId();
            $requestDetails .= "</strong><br/>Requestee: <strong>" .  $email . "</strong> Asset:<em>" . $assetTitle . "</em>";
            $requestDetails .= ' Status: <strong>' . $status . '</strong>';
            $assetRequests[] = $requestDetails;
        } catch (Exception $e) {
            $messages = ob_get_clean();
            $response = array('result'=>'failed','post'=>$post,'messages'=>$messages);
            ob_clean();
            echo json_encode($response);
        }
    }
}

$messages = ob_get_clean();

$response = array('result'=>'success','messages'=>$messages,'requests'=>$assetRequests, 'post'=>$post,'approvingMgrEmail'=>$approvingMgrEmail, 'records'=>$records);
ob_clean();
echo json_encode($response);
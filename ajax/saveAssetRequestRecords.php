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
$status = $autoApproved ? assetRequestRecord::$STATUS_APPROVED : assetRequestRecord::$STATUS_CREATED;
$approved = $autoApproved ? $requested : null;

$educationConfirmed = !empty($_POST['EDUCATION_CONFIRMED']) ? $_POST['EDUCATION_CONFIRMED'] : 'No';

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
            );

        
        try {
            $assetRequestRecord = new assetRequestRecord();
            $assetRequestRecord->setFromArray($assetRequest);
            
            $records[] = print_r($assetRequestRecord,true);
            
            
            $assetRequestTable->saveRecord($assetRequestRecord);
            $requestDetails = $status == assetRequestRecord::$STATUS_APPROVED ? "<div class='bg-success'>" : "<div class='bg-warning'>" ;
            $requestDetails .= "<br/>Request :<strong>" .$assetRequestTable->lastId();
            $requestDetails .= "</strong> Requestee: <strong>" .  $email . "</strong> Asset:<em>" . $assetTitle . "</em>";
            $requestDetails .= $status==assetRequestRecord::$STATUS_APPROVED ? 'Status: <b>' . $status . "</b>" : ' Status: <b>Approval Required</b>';
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

$response = array('result'=>'success','requests'=>$assetRequests, 'post'=>$post,'approvingMgrEmail'=>$approvingMgrEmail, 'records'=>$records);
ob_clean();
echo json_encode($response);
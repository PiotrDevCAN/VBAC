<?php
use vbac\personTable;
use vbac\assetRequestRecord;
use itdq\DbTable;
use vbac\assetRequestsTable;
use vbac\allTables;

ob_start();

$post = print_r($_POST,true);
$now = new DateTime();
$requested = $now->format('Y-m-d h:i:s');

$approvingMgrEmail = personTable::getEmailFromCnum(trim($_POST['approvingManager']));
$autoApproved = strtoupper(trim($_POST['requestor'])) == strtoupper(trim($approvingMgrEmail));
$status = $autoApproved ? assetRequestRecord::$STATUS_APPROVED : assetRequestRecord::$STATUS_CREATED;
$educationConfirmed = !empty($_POST['EDUCATION_CONFIRMED']) ? $_POST['EDUCATION_CONFIRMED'] : 'No';
$approved = $autoApproved ? $requested : null;
$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$assetRequests = array();
foreach ($_POST as $key => $value){
    $split=array();
    $isAssetRequest = preg_match('/person-([\d]+)-asset-([\d]+)-([\D]+)/', $key, $split);
    if($isAssetRequest && $value=='on') {
        $personId = $split[1];
        $assetId  = $split[2];
        $assetTitle = $split[3];
        $justification = !isset($_POST['person-'.$personId.'-justification-'.$assetId]) ? null : $_POST['person-'.$personId.'-justification-'.$assetId];

        $assetRequest = array(
            'CNUM'=>$_POST['requestee']
            ,'ASSET_TITLE'=>$assetTitle
            ,'USER_LOCATION'=>$_POST['person-'.$personId.'-location']
            ,'BUSINESS_JUSTIFICATION' => $justification
            ,'REQUESTOR_EMAIL'=>$_POST['requestor']
            ,'REQUESTED'=>$now->format('Y-m-d h:i:s')
            ,'APPROVER_EMAIL'=>$approvingMgrEmail
            ,'APPROVED'=>$approved
            ,'EDUCATION_CONFIRMED'=>$educationConfirmed
            ,'STATUS'=>$status
            );
        try {
            $assetRequestRecord = new assetRequestRecord();
            $assetRequestRecord->setFromArray($assetRequest);
            $assetRequestTable->saveRecord($assetRequestRecord);
        } catch (Exception $e) {
            $messages = ob_get_clean();
            $response = array('result'=>'failed','post'=>$post,'messages'=>$messages);
            ob_clean();
            echo json_encode($response);
        }
    }
}

$response = array('result'=>'success','post'=>$post);
ob_clean();
echo json_encode($response);
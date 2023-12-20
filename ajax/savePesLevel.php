<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    $personTable = new personTable(allTables::$PERSON);
    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    
    // original record data
    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['plm_cnum'], 'WORKER_ID'=>$_POST['plm_worker_id']));

    $personData = $personTable->getRecord($person);
    $person->setFromArray($personData);

    // set new PES Level
    $updateRecordResult = $personTable->setPesLevel($_POST['plm_cnum'], $_POST['plm_worker_id'], $_POST['plm_level'], $_SESSION['ssoEmail']);

    // set new PES recheck date
    $currentPesLevel = $person->getValue('PES_LEVEL');
    $newPesLevel = $_POST['plm_level'];

    $now = new \DateTime();
    $nowDate = $now->format('Y-m-d');
    $pesRecheckDate = $person->getValue('PES_RECHECK_DATE');
    $pesRecheckDateObj = \DateTime::createFromFormat('Y-m-d', $pesRecheckDate);
    if ($pesRecheckDateObj){
        if (
            $currentPesLevel == personTable::PES_LEVEL_ONE 
            && $newPesLevel == personTable::PES_LEVEL_TWO
        ) {
            
            // Scenario 1
            // PES Level goes 1 -> 2
    
            // PES Recheck goes from Level 1 (Annual) to Level 2 (3 Years)  -  [ADD] +2 Years to the current recheck date
            $plusTwoYears = $pesRecheckDateObj->add(new \DateInterval('P2Y'));
            $plusTwoYearsDate = $plusTwoYears->format('Y-m-d');
    
            echo 'Current PES recheck date has been extended by 2 years!';
            $updateRecordResult2 = $personTable->setPesRescheckDate($_POST['plm_cnum'], $_POST['plm_worker_id'], $_SESSION['ssoEmail'], $plusTwoYearsDate, true);
        } elseif (
            $currentPesLevel == personTable::PES_LEVEL_TWO 
            && $newPesLevel == personTable::PES_LEVEL_ONE
        ) {
    
            // Scenario 2
            // PES Level goes 2 -> 1
    
            // PES Recheck goes from Level 2 (3 Years) to Level 1 (Annual) -  [SUBTRACT] -2 Years from the current recheck date
            // If -2 Years is < Today then make Recheck date = Today 
            
            $minuseTwoYears = $pesRecheckDateObj->sub(new \DateInterval('P2Y'));
            $minusTwoYearsDate = $minuseTwoYears->format('Y-m-d');
    
            // If -2 Years is < Today then make Recheck date = Today 
            if ($minuseTwoYears < $now) {
                echo 'Current PES recheck date is within two years from now - set today date!';
                $updateRecordResult2 = $personTable->setPesRescheckDate($_POST['plm_cnum'], $_POST['plm_worker_id'], $_SESSION['ssoEmail'], $nowDate, true);
            } else {
                echo 'Current PES recheck date is beyond two years from now - take away two years from current recheck date!';
                $updateRecordResult2 = $personTable->setPesRescheckDate($_POST['plm_cnum'], $_POST['plm_worker_id'], $_SESSION['ssoEmail'], $minusTwoYearsDate, true);
            }
        } else {
            $updateRecordResult2 = true;
        }

    } else {
        echo 'Current PES recheck date is blank so we need to update it!';
        $updateRecordResult2 = true;
    }

    // modified record data
    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['plm_cnum'], 'WORKER_ID'=>$_POST['plm_worker_id']));
    
    $personData = $personTable->getRecord($person);
    $person->setFromArray($personData);

    $formattedPesLevelField = personTable::getPesLevelWithButtons($personData);

    AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    if(!$updateRecordResult || !$updateRecordResult2){
        echo json_encode(sqlsrv_errors());
        echo json_encode(sqlsrv_errors());
        AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . json_encode(sqlsrv_errors()) . "</b> Msg:<b>" . json_encode(sqlsrv_errors()) . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        $success = false;
    } else {
        $comment = $pesTracker->savePesComment($_POST['plm_cnum'],$_POST['plm_worker_id'],"PES_LEVEL set to : " . $_POST['plm_level']);
        $success = true;

    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
}

$messages = ob_get_clean();
ob_start();
// $success = $success && empty($messages);
$response = array(
    'success'=>$success,
    'messages'=>$messages,
    "cnum"=>$_POST['plm_cnum'],
    'formattedPesLevelField'=>$formattedPesLevelField
);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));
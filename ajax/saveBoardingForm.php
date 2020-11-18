<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\AuditRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

$boardingIbmer = $_POST['boarding']== 'true' ? true:false;

try {
    if(!$boardingIbmer && $_POST['mode']!='Update'){
        $cnum = personTable::getNextVirtualCnum();
        $_POST['CNUM']= $cnum;
        // And put their name in the NOTES_ID as that's the field we display as their identity.
        // And copy over their fields into the standard fields.
        $_POST['NOTES_ID'] = $_POST['resFIRST_NAME'] . " " . $_POST['resLAST_NAME'];
        $_POST['FIRST_NAME'] = $_POST['resFIRST_NAME'];
        $_POST['LAST_NAME'] = $_POST['resLAST_NAME'];
        $_POST['COUNTRY'] = $_POST['resCOUNTRY'];
        $_POST['EMAIL_ADDRESS'] = $_POST['resEMAIL_ADDRESS'];
        $_POST['EMPLOYEE_TYPE'] = $_POST['resEMPLOYEE_TYPE'];

        if($_POST['EMPLOYEE_TYPE']=='vendor'){
            $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_VENDOR;
        } else {
            $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_PREBOARDER;
        }

        switch (trim($_POST['ROLE_ON_THE_ACCOUNT'])) {
            case 'Wipro':
            case 'Cognizant':
            case 'Densify':
                $_POST['PES_STATUS'] = personRecord::PES_STATUS_CLEARED;
            break;
            case 'Other':
                $_POST['PES_STATUS'] = personRecord::PES_STATUS_TBD;
            default:
            break;
        }
        AuditTable::audit("Pre boarding:<b>" . $cnum . "</b> Type:" .  $_POST['EMPLOYEE_TYPE'],AuditTable::RECORD_TYPE_AUDIT);
    }

    if($_POST['mode']!='Update'){
        $_POST['REVALIDATION_STATUS'] = isset($_POST['REVALIDATION_STATUS']) ? $_POST['REVALIDATION_STATUS'] : personRecord::REVALIDATED_FOUND;
    }
    $_POST['PRE_BOARDED'] = !empty($_POST['person_preboarded']) ? $_POST['person_preboarded']  : null;  // Save the link to the pre-boarded person

    $_POST['EMAIL_ADDRESS'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', trim($_POST['EMAIL_ADDRESS'])); // remove special characters

    $person->setFromArray($_POST);
    $person->convertCountryCodeToName();
    $saveRecordResult = $table->saveRecord($person,false,false);

    if($_POST['CTB_RTB'] != 'CTB'){
        $table->clearCioAlignment($_POST['CNUM']);
    }

    AuditTable::audit("Saved Boarding Record:<B>" . $_POST['CNUM'] .":" . $_POST['NOTES_ID'] .  "</b>Mode:<b>" . $_POST['mode'],AuditTable::RECORD_TYPE_AUDIT);
    AuditTable::audit("Saved Record:<pre>". print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
    AuditTable::audit("PROJECTED_END_DATE:<pre>". print_r($_POST['PROJECTED_END_DATE'],true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    $timeToWarnPmo = $person->checkIfTimeToWarnPmo();
    $timeToWarnPmo ? $person->sendOffboardingWarning() : null;

     if(($saveRecordResult && $_POST['mode']=='Save') || (!$saveRecordResult && $_POST['mode']=='Update')){
        if($_POST['mode']=='Save'){
            echo "<br/>Boarding Form Record - Saved.";
            echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
            $person->checkForCBC();
        }
        if($_POST['mode']=='Update'){
            echo "<br/>Boarding Form Record - Updated.";
        }
        $success = true;
        // Do we need to update a PRE-BOARDING record ?
        if(!empty($_POST['person_preboarded'])){
            $table->linkPreBoarderToIbmer($_POST['person_preboarded'], $_POST['CNUM']);
        }
    } else {
       AuditTable::audit("Db2 Error in " . __FILE__ . " POST:" . print_r($_POST,true) , AuditTable::RECORD_TYPE_DETAILS);
       if($saveRecordResult && $_POST['mode']=='Save'){
           echo "Expecting to create a new record, we ended up UPDATING a record that already existed.";
       }
       if((!$saveRecordResult && $_POST['mode']=='Update')){
           echo "Expecting to update an existing record, we ended up INSERTING a new one";
       }

       echo "saveRecordResult:";
       var_dump($saveRecordResult);
       $success = false;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
}

$messages = ob_get_clean();
ob_start();

$response = array('boarding'=>$_POST['boarding'], 'boardingIbmer'=>$boardingIbmer, 'employeetype'=>$_POST['EMPLOYEE_TYPE'],'pesstatus'=>$_POST['PES_STATUS'], 'success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult
                 ,'cnum'=>$_POST['CNUM']
                 ,'post'=>print_r($_POST,true)
                 ,'sendWarning'=>print_r($timeToWarnPmo,true)                 
);
ob_clean();
echo json_encode($response);

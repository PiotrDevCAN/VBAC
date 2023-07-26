<?php

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);

use vbac\allTables;
use vbac\cFIRST\APICaller;
use vbac\cFIRST\CandidateDetailsRecord;
use vbac\cFIRST\CandidateDetailsTable;
use vbac\cFIRST\CandidateDocumentRecord;
use vbac\cFIRST\CandidateDocumentTable;
use vbac\cFIRST\CandidateStatusRecord;
use vbac\cFIRST\CandidateStatusTable;
use vbac\cFIRST\CandidateStatusRequestRecord;
use vbac\cFIRST\CandidateStatusRequestTable;

echo '<pre>';
$api = new APICaller();

$api->getPackages();

// add candidate to cFIRST db
$candidateData = array(
    // "PackageName" => "Test package", // not required
    // "PackageId" => "111",
    "PackageName" => "Package 1",
    "PackageId" => "28000000000001",
    "BGCResponseEmailIds" => "piotr.tajanowicz@ocean.ibm.com",
    "EmployeeId" => "123456",
    "APIReferenceCode" => "123456",
    "RequesterId" => "piotr.tajanowicz@ocean.ibm.com",
    "FirstName" => "TEST",
    "MiddleName" => "C",
    "LastName" => "CANDIDATE",
    "Email" => "piotr.tajanowicz@ocean.ibm.com",
    "Phone" => "(425)+533-7745"
);
// $resopnse = $api->addCandidate($candidateData);
// var_dump($resopnse[0]);

// check candidate's status
$candidateId = '96000000001533';
$accessToken = null;
// $candidateId = null;
$date = null;
$resopnse2 = $api->getCandidateStatus($candidateId);
$dataObj = $resopnse2[0];
// var_dump($dataObj);

$BGVErrors = $dataObj->BGVErrors;
// var_dump($BGVErrors);

$candidateDetails = $dataObj->CandidateDetails;
// var_dump($candidateDetails);

$candidateStatus = $dataObj->CandidateStatus[0];
// var_dump($candidateStatus);

$documents = $dataObj->Documents;
// var_dump($documents);

$requestRecord = new CandidateStatusRequestRecord();
$requestTable = new CandidateStatusRequestTable(allTables::$CANDIDATE_STATUS_REQ);

$additionalFields = array();
$additionalFields['ACCESS_TOKEN'] = $api->getAccessToken();
$additionalFields['CANDIDATE_ID'] = $candidateId;
$additionalFields['DATE'] = $date;
if ($BGVErrors !== null) {
    $additionalFields['ERROR'] = $BGVErrors[0]->Error;
    $additionalFields['ERROR_DESCRIPTION'] = $BGVErrors[0]->Errordescription;
    $additionalFields['ERROR_CODES'] = $BGVErrors[0]->Errorcode;
    $additionalFields['TIMESTAMP'] = $BGVErrors[0]->Timestamp;   
} else {
    // Store Candidate Details
    $detailsRecord = new CandidateDetailsRecord();
    $detailsTable = new CandidateDetailsTable(allTables::$CANDIDATE_DETAILS);

var_dump($candidateDetails);

    $candidateDetailFields = array();
    $candidateDetailFields['API_REFERENCE_CODE'] = $candidateDetails->APIReferenceCode;
    $candidateDetailFields['CANDIDATE_ID'] = $candidateId;
    $candidateDetailFields['ADDED_ON'] = $candidateDetails->AddedOn;
    $candidateDetailFields['COMPLETED_CASE'] = $candidateDetails->CompletedCase;
    $candidateDetailFields['CURRENT_STATUS'] = $candidateDetails->CurrentStatus;
    $candidateDetailFields['DATE_OF_BIRTH'] = $candidateDetails->DateOfBirth;
    $candidateDetailFields['EMAIL'] = $candidateDetails->Email;
    $candidateDetailFields['FIRST_NAME'] = $candidateDetails->FirstName;
    $candidateDetailFields['LAST_NAME'] = $candidateDetails->LastName;
    $candidateDetailFields['MIDDLE_NAME'] = $candidateDetails->MiddleName;
    $candidateDetailFields['GOVERNMENT_ID'] = $candidateDetails->GovernmentId;
    $candidateDetailFields['NEED_ATTENTION_CASE'] = $candidateDetails->NeedAttentionCase;
    $candidateDetailFields['ORDER_ID'] = $candidateDetails->OrderId;
    $candidateDetailFields['PHONE'] = $candidateDetails->Phone;
    $candidateDetailFields['REPORT_BODY'] = $candidateDetails->Report->ReportBody;
    $candidateDetailFields['REPORT_NAME'] = $candidateDetails->Report->ReportName;
    $candidateDetailFields['REPORT_STATUS'] = $candidateDetails->ReportStatus;
    $candidateDetailFields['REVIEW_NEEDED_CASE'] = $candidateDetails->ReviewNeededCase;
    $candidateDetailFields['SSN'] = $candidateDetails->SSN;
    $candidateDetailFields['TOTAL_CASE'] = $candidateDetails->TotalCase;

    $detailsRecord->setFromArray($candidateDetailFields);
    // $detailsRecord->iterateVisible('full');
    $validDetails = $requestRecord->validateForTable($requestTable);
    if($validDetails===true){
        
        // Returns TRUE if it inserted a new record, false if it updated an existing record.
        $saveResponse = $detailsTable->saveRecord($detailsRecord);
        if ($saveResponse === true) {
            // new record

        } elseif ($saveResponse === false) {
            // existing record
            
        } else {

        }

    } else {
        $saveResponse = false;
        $messages = "Candidate Details Record has been not saved";
    }
    
    // Store Candidate Status    
    $statusRecord = new CandidateStatusRecord();
    $statusTable = new CandidateStatusTable(allTables::$CANDIDATE_STATUS);

// var_dump($candidateStatus);

    $candidateStatusFields = array();
    $candidateStatusFields['API_REFERENCE_CODE'] = $candidateDetails->APIReferenceCode;
    $candidateStatusFields['CANDIDATE_ID'] = $candidateId;
    $candidateStatusFields['ADDED_ON_DATE'] = $candidateStatus->AddedOnDate;
    $candidateStatusFields['CASE_DOCUMENTS'] = serialize($candidateStatus->CaseDocuments);
    $candidateStatusFields['CASE_ID'] = $candidateStatus->CaseId;
    $candidateStatusFields['CASE_STATUS'] = $candidateStatus->CaseStatus;
    $candidateStatusFields['COMPLETED_ON_DATE'] = $candidateStatus->CompletedOnDate;
    $candidateStatusFields['DELAY_ETA'] = $candidateStatus->DelayETA;
    $candidateStatusFields['ENTITY'] = $candidateStatus->Entity;
    $candidateStatusFields['INSUFF_REMARKS'] = $candidateStatus->InsuffRemarks;
    $candidateStatusFields['NOTES'] = serialize($candidateStatus->Notes);
    $candidateStatusFields['REVIEW_NEEDED'] = $candidateStatus->ReviewNeeded;
    $candidateStatusFields['SEARCH_NAME'] = $candidateStatus->SearchName;

    $statusRecord->setFromArray($candidateStatusFields);
    $statusRecord->iterateVisible('full');
    $validStatus = $requestRecord->validateForTable($requestTable);
    if($validStatus===true){
        
        // Returns TRUE if it inserted a new record, false if it updated an existing record.
        $saveResponse = $statusTable->saveRecord($statusRecord);
        if ($saveResponse === true) {
            // new record

        } elseif ($saveResponse === false) {
            // existing record
            
        } else {

        }

    } else {
        $saveResponse = false;
        $messages = "Candidate Status Record has been not saved";
    }

    // Store Candidate Documents
    // $documentRecord = new CandidateDocumentRecord();
    // $documentTable = new CandidateDocumentTable(allTables::$CANDIDATE_DOCUMENTS);

    // if (count($documents) > 0) {
    //     foreach($documents as $key =>$document) {

    //         $candidateDocumentFields = array();
    //         $candidateDocumentFields['API_REFERENCE_CODE'] = $candidateDetails->APIReferenceCode;
    //         $candidateDocumentFields['CANDIDATE_ID'] = $candidateId;
    //         $candidateDocumentFields['ADDED_ON'] = $document->AddedOn;
    //         // $candidateDocumentFields['DOCUMENT_BODY'] = $document->DocumentBody;
    //         $candidateDocumentFields['DOCUMENT_BODY'] = 'AAA';
    //         $candidateDocumentFields['DOCUMENT_ID'] = $document->DocumentId;
    //         $candidateDocumentFields['DOCUMENT_NAME'] = $document->DocumentName;

    //         $documentRecord->setFromArray($candidateDocumentFields);
    //         $documentRecord->iterateVisible('full');
    //         $validDocument = $requestRecord->validateForTable($requestTable);
    //         if($validDocument===true){
                
    //             // Returns TRUE if it inserted a new record, false if it updated an existing record.
    //             $saveResponse = $documentTable->saveRecord($documentRecord);
    //             if ($saveResponse === true) {
    //                 // new record

    //             } elseif ($saveResponse === false) {
    //                 // existing record
                    
    //             } else {

    //             }

    //         } else {
    //             $saveResponse = false;
    //             $messages = "Candidate Document Record has been not saved";
    //         }
    //     }
    // }
}
if (!empty($additionalFields)) {
    $requestRecord->setFromArray($additionalFields);
}

$requestRecord->iterateVisible('full');
$valid = $requestRecord->validateForTable($requestTable);
if($valid===true){
    
    // Returns TRUE if it inserted a new record, false if it updated an existing record.
    $saveResponse = $requestTable->saveRecord($requestRecord);
    if ($saveResponse === true) {
        // new record

    } elseif ($saveResponse === false) {
        // existing record
        
    } else {

    }

} else {
    $saveResponse = false;
    $messages = "Add Candidate Record has been not saved";
}

echo '</pre>';
?>
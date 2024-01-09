<?php
namespace vbac\cFIRST;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class CandidateStatusRecord extends DbRecord {

    protected $API_REFERENCE_CODE;
    protected $CANDIDATE_ID;
    protected $ADDED_ON_DATE;
    protected $CASE_DOCUMENTS;      // array
    protected $CASE_ID;
    protected $CASE_STATUS;
    protected $COMPLETED_ON_DATE;
    protected $DELAY_ETA;
    protected $ENTITY;
    protected $INSUFF_REMARKS;
    protected $NOTES;               // array
    protected $REVIEW_NEEDED;
    protected $SEARCH_NAME;

    //     ["CandidateStatus"]=>
    //     array(1) {
    //       [0]=>
    //       object(stdClass)#121 (11) {
    //         ["AddedOnDate"]=>
    //         string(19) "2022-04-06 12:22:44"
    //         ["CaseDocuments"]=>
    //         array(0) {
    //         }
    //         ["CaseId"]=>
    //         string(5) "78733"
    //         ["CaseStatus"]=>
    //         string(10) "InProgress"
    //         ["CompletedOnDate"]=>
    //         NULL
    //         ["DelayETA"]=>
    //         NULL
    //         ["Entity"]=>
    //         string(0) ""
    //         ["InsuffRemarks"]=>
    //         NULL
    //         ["Notes"]=>
    //         array(0) {
    //         }
    //         ["ReviewNeeded"]=>
    //         string(5) "false"
    //         ["SearchName"]=>
    //         string(28) "Global Watch Database Search"
    //       }
    //     }

}
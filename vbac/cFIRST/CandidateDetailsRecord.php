<?php
namespace vbac\cFIRST;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class CandidateDetailsRecord extends DbRecord {

    protected $API_REFERENCE_CODE;
    protected $CANDIDATE_ID;
    protected $ADDED_ON;
    protected $COMPLETED_CASE;
    protected $CURRENT_STATUS;
    protected $DATE_OF_BIRTH;
    protected $EMAIL;
    protected $FIRST_NAME;
    protected $LAST_NAME;
    protected $MIDDLE_NAME;
    protected $GOVERNMENT_ID;
    protected $NEED_ATTENTION_CASE;
    protected $ORDER_ID;
    protected $PHONE;
    protected $REPORT_BODY;
    protected $REPORT_NAME;
    protected $REPORT_STATUS;
    protected $REVIEW_NEEDED_CASE;
    protected $SSN;
    protected $TOTAL_CASE;

    // ["CandidateDetails"]=>
    //     object(stdClass)#123 (19) {
    //       ["APIReferenceCode"]=>
    //       string(6) "123456"
    //       ["AddedOn"]=>
    //       string(19) "2022-04-06 12:22:44"
    //       ["CandidateId"]=>
    //       string(14) "96000000001533"
    //       ["CompletedCase"]=>
    //       int(0)
    //       ["CurrentStatus"]=>
    //       string(11) "In Progress"
    //       ["DateOfBirth"]=>
    //       string(19) "1999-04-21 00:00:00"
    //       ["Email"]=>
    //       string(24) "twinkal.mavani@caypro.io"
    //       ["FirstName"]=>
    //       string(6) "Mallik"
    //       ["GovernmentId"]=>
    //       string(1) "3"
    //       ["LastName"]=>
    //       string(1) "L"
    //       ["MiddleName"]=>
    //       string(0) ""
    //       ["NeedAttentionCase"]=>
    //       bool(false)
    //       ["OrderId"]=>
    //       string(14) "31000000007024"
    //       ["Phone"]=>
    //       string(0) ""
    //       ["Report"]=>
    //       object(stdClass)#122 (2) {
    //         ["ReportBody"]=> ''
    //         ["ReportName"]=>
    //         string(25) "Report_96000000001533.pdf"
    //       }
    //       ["ReportStatus"]=>
    //       string(0) ""
    //       ["ReviewNeededCase"]=>
    //       bool(false)
    //       ["SSN"]=>
    //       string(0) ""
    //       ["TotalCase"]=>
    //       int(1)
    //     }
}
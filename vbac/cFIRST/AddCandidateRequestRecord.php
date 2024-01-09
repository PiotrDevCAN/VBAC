<?php
namespace vbac\cFIRST;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class AddCandidateRequestRecord extends DbRecord {

    protected $API_REFERENCE_CODE;
    protected $BGV_ERRORS;
    protected $CANDIDATE_ID;
    protected $SUCESS;
    protected $SUCESS_CODE;
    protected $SUCESS_DESCRIPTION;
    protected $TIMESTAMP;

    // object(stdClass)#125 (1) {
    //     ["AddCandidateResult"]=>
    //     array(1) {
    //       [0]=>
    //       object(stdClass)#60 (7) {
    //         ["APIReferenceCode"]=>
    //         string(6) "123456"
    //         ["BGVErrors"]=>
    //         NULL
    //         ["CandidateId"]=>
    //         string(14) "96000000001533"
    //         ["Sucess"]=>
    //         string(9) "Duplicate"
    //         ["Sucesscode"]=>
    //         string(3) "403"
    //         ["Sucessdescription"]=>
    //         string(38) "Candidate is already part of database."
    //         ["Timestamp"]=>
    //         int(1675264938)
    //       }
    //     }
    //   }

}
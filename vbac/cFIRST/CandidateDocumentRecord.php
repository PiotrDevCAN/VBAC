<?php
namespace vbac\cFIRST;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class CandidateDocumentRecord extends DbRecord {

    protected $API_REFERENCE_CODE;
    protected $CANDIDATE_ID;
    protected $ADDED_ON;
    protected $DOCUMENT_BODY;
    protected $DOCUMENT_ID;         // integer
    protected $DOCUMENT_NAME;

    //     ["Documents"]=>
    //     array(3) {
    //       [0]=>
    //       object(stdClass)#120 (4) {
    //         ["AddedOn"]=>
    //         string(19) "2022-04-06 12:20:12"
    //         ["DocumentBody"]=>
    //         NULL
    //         ["DocumentId"]=>
    //         int(45000000021377)
    //         ["DocumentName"]=>
    //         string(34) "Consent_20220406_122009_f038ad.pdf"
    //       }
    //       [1]=>
    //       object(stdClass)#119 (4) {
    //         ["AddedOn"]=>
    //         string(19) "2022-04-06 12:20:23"
    //         ["DocumentBody"]=>
    //         NULL
    //         ["DocumentId"]=>
    //         int(45000000021378)
    //         ["DocumentName"]=>
    //         string(37) "Disclosure_20220406_122022_2c9fe2.pdf"
    //       }
    //       [2]=>
    //       object(stdClass)#118 (4) {
    //         ["AddedOn"]=>
    //         string(19) "2022-04-06 12:20:37"
    //         ["DocumentBody"]=>
    //         NULL
    //         ["DocumentId"]=>
    //         int(45000000021379)
    //         ["DocumentName"]=>
    //         string(40) "Authorization_20220406_122037_ea0d5c.pdf"
    //       }
    //     }
    //   }

}
<?php
namespace vbac\cFIRST;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class CandidateStatusRequestRecord extends DbRecord {

    protected $ACCESS_TOKEN;
    protected $CANDIDATE_ID;
    protected $DATE;
    protected $ERROR;
    protected $ERROR_DESCRIPTION;
    protected $ERROR_CODES;
    protected $TIMESTAMP;
    
}
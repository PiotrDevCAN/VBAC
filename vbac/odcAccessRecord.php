<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;

/**
 *
 * @author gb001399
 *
 */
class odcAccessRecord extends DbRecord
{    
    protected $S_NO;
    protected $REQUEST_ID;
    protected $OWNER_CNUM_ID;
    protected $OWNER_NOTES_ID;
    protected $ACCESS_FOR;
    protected $SECURED_AREA_NAME;
    protected $REQUEST_TYPE;
    protected $START_DATE;
    protected $END_DATE;
    protected $REQUEST_STATUS;
    protected $WORK_FLOW_TYPE;
    protected $WORK_FLOW_STATUS;
    protected $CREATED_TMSP;
    protected $PEOPLE_MANAGERS_NOTES_ID;
    protected $SECURE_AREA_MANAGERS_NAME;
    protected $CREATED;
}
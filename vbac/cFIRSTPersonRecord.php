<?php
namespace vbac;

use itdq\DbRecord;

class cFIRSTPersonRecord extends DbRecord {

    protected $API_REFERENCE_CODE;
    protected $UNIQUE_REFERENCE_NO;
    protected $PROFILE_ID;
    protected $CANDIDATE_ID;
    protected $STATUS;
    protected $EMAIL_ADDRESS;
    protected $FIRST_NAME;
    protected $MIDDLE_NAME;
    protected $LAST_NAME;
    protected $PHONE;
    protected $ADDED_ON_DATE;
    protected $INFO_RECEIVED_ON_DATE;
    protected $INFO_REQUESTED_ON_DATE;
    protected $INVITED_ON_DATE;
    protected $SUBMITTED_ON_DATE;
    protected $COMPLETED_ON_DATE;

    const PROFILE_STATUS_CANCELLED            = 'Cancelled';
    const PROFILE_STATUS_COMPLETED            = 'Completed';
    const PROFILE_STATUS_CONSENT              = 'Consent'; 
    const PROFILE_STATUS_CONSENT_COMPLETED    = 'Consent Completed';
    const PROFILE_STATUS_IN_PROGRESS          = 'In Progress';
    const PROFILE_STATUS_INVITATION_CANCELLED = 'Invitation Cancelled';
    const PROFILE_STATUS_NEED_MORE_INFO       = 'Need more Information';
    const PROFILE_STATUS_PRIMARY_ADDRESS      = 'Primary Address';
    const PROFILE_STATUS_WELCOME              = 'Welcome';
    const PROFILE_STATUS_WITHDRAWN            = 'Withdrawn';
}
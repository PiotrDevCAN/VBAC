<?php
namespace vbac;

use itdq\DbRecord;

//   CNUM CHAR(9) NOT NULL WITH DEFAULT 'cnum'
// , Passport_First_Name CHAR(200)
// , Passport_Surname CHAR(200)
// , JML CHAR(20)
// ,  Consent CHAR(10)
// , Right_to_work CHAR(10)
// , Proof_of_Id CHAR(10)
// , Proof_of_Residency CHAR(10)
// , Credit_Check CHAR(10)
// , Financial_Sanctions CHAR(10)
// , Criminal_Records_Check CHAR(10)
// , Proof_of_Activity CHAR(10)
// , Processing_Status CHAR(20)
// , Processing_Status_Changed TIMESTAMP(6)
// , Date_Last_Chased DATE


class pesTrackerRecord extends DbRecord
{
    
    protected $CNUM;
    protected $PASSPORT_FIRST_NAME;
    protected $PASSPORT_SURNAME;
    protected $JML;
    
    protected $CONSENT;
    protected $RIGHT_TO_WORK;
    protected $PROOF_OF_ID;
    protected $PROOF_OF_RESIDENCY;
    protected $CREDIT_CHECK;
    protected $FINANCIAL_SANCTIONS;
    protected $CRIMINAL_RECORDS_CHECK;
    protected $PROOF_OF_ACTIVITY;
    protected $PROCESSING_STATUS;
    protected $PROCESSING_STATUS_CHANGED;
    protected $DATE_LAST_CHASED;
    protected $COMMENT;
    protected $PRIORITY;

}
    
    
    
    
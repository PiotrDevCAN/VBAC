<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;
use itdq\DbTable;
use itdq\AuditTable;
use itdq\BlueMail;
use vbac\allTables;
use vbac\personTable;
use DateTime;

/**
 *
 * @author gb001399
 *
 * ALTER TABLE "ROB_DEV"."PERSON" ADD COLUMN "SQUAD_NUMBER" NUMERIC(5);
 *
 */
class personRecord extends DbRecord
{
    protected $CNUM;
    protected $OPEN_SEAT_NUMBER;
    protected $FIRST_NAME;
    protected $LAST_NAME;
    protected $EMAIL_ADDRESS;
    protected $NOTES_ID;
    protected $LBG_EMAIL;
    protected $EMPLOYEE_TYPE;
    protected $FM_CNUM;
    protected $FM_MANAGER_FLAG;
    protected $CTB_RTB;
    // protected $TT_BAU;
    protected $LOB;
    protected $ROLE_ON_THE_ACCOUNT;
    protected $ROLE_TECHNOLOGY;
    protected $START_DATE;
    protected $PROJECTED_END_DATE;
    protected $COUNTRY;
    protected $IBM_BASE_LOCATION;
    protected $LBG_LOCATION;
    protected $OFFBOARDED_DATE;
    protected $PES_DATE_REQUESTED;
    protected $PES_REQUESTOR;
    protected $PES_DATE_RESPONDED;
    protected $PES_STATUS_DETAILS;
    protected $PES_STATUS;
    protected $REVALIDATION_DATE_FIELD;
    protected $REVALIDATION_STATUS;
    protected $CBN_DATE_FIELD;
    protected $CBN_STATUS;

    // protected $WORK_STREAM;
    protected $CT_ID_REQUIRED;
    protected $CT_ID;
    protected $CIO_ALIGNMENT;
    protected $PRE_BOARDED;
    protected $SECURITY_EDUCATION;
    protected $RF_Flag;
    protected $RF_Start;
    protected $RF_End;
    protected $PMO_STATUS;
    protected $PES_DATE_EVIDENCE;
    protected $RSA_TOKEN;
    protected $CALLSIGN_ID;
    protected $PROCESSING_STATUS;
    protected $PROCESSING_STATUS_CHANGED;
    protected $PES_LEVEL;
    protected $PES_RECHECK_DATE;
    protected $PES_CLEARED_DATE;
    protected $ACCT_ACC;
    protected $PES_API_STATUS;
    protected $SQUAD_NUMBER;
    protected $KYN_EMAIL_ADDRESS;
    protected $SKILLSET_ID;
    protected $PROPOSED_LEAVING_DATE;
    protected $person_bio;

    // Fields to be edited in the DataTables Reports. Need to know their position in the array $row;
    // const FIELD_CNUM = 0;
    // const FIELD_NOTES_ID = 5;
    // const FIELD_FM_MANAGER_FLAG = 9;
    // const FIELD_LOB = 12;
    // const FIELD_ROLE_ON_THE_ACCOUNT = 13;
    // const FIELD_COUNTRY = 17;
    // const FIELD_PES_DATE_REQUESTED = 21;
    // const FIELD_PES_REQUESTOR = 22;
    // const FIELD_PES_DATE_RESPONDED = 23;
    // const FIELD_PES_STATUS_DETAILS = 24;
    // const FIELD_PES_STATUS = 25;
    const COLUMN_PES_DATE_REQUESTED = 'PES_DATE_REQUESTED';
    const COLUMN_PES_DATE_EVIDENCE = 'PES_DATE_EVIDENCE';
    const COLUMN_PES_DATE_RESPONDED = 'PES_DATE_RESPONDED';
    const COLUMN_PES_RECHECK_DATE = 'PES_RECHECK_DATE';
    const COLUMN_PES_CLEARED_DATE = 'PES_CLEARED_DATE';
    const COLUMN_PES_STATUS_DETAILS = 'PES_STATUS_DETAILS';
    const COLUMN_PES_STATUS = 'PES_STATUS';
    const COLUMN_PES_LEVEL = 'PES_LEVEL';
    const COLUMN_PES_REQUESTOR = 'PES_REQUESTOR';
    static public $personFields = array(
        personRecord::COLUMN_PES_DATE_REQUESTED,
        personRecord::COLUMN_PES_DATE_RESPONDED,
        personRecord::COLUMN_PES_RECHECK_DATE,
        personRecord::COLUMN_PES_CLEARED_DATE,
        personRecord::COLUMN_PES_STATUS_DETAILS,
        personRecord::COLUMN_PES_STATUS,
        personRecord::COLUMN_PES_LEVEL,
        personRecord::COLUMN_PES_REQUESTOR
    );
    const COLUMN_PROCESSING_STATUS = 'PROCESSING_STATUS';
    const COLUMN_PROCESSING_STATUS_CHANGED = 'PROCESSING_STATUS_CHANGED';
    const COLUMN_COMMENT = 'COMMENT';
    static public $pesTrackerFields = array(
      personRecord::COLUMN_PROCESSING_STATUS,
      personRecord::COLUMN_PROCESSING_STATUS_CHANGED,
      personRecord::COLUMN_COMMENT
    );
    const PES_API_STATUS_FOUND = 'found';
    const PES_API_STATUS_NOT_FOUND = 'not found';
    const PES_API_STATUS_IGNORE = 'ignore';

    // const IBM_STATUS_NOT_IBMER = 'Not an IBMer';
    // const IBM_STATUS_CONTRACTOR = 'Contractor';
    // const IBM_STATUS_REGULAR = 'Regular';
    // const IBM_STATUS_PRE_HIRE = 'Pre-Hire';
    const REVALIDATED_FOUND = 'found';
    const REVALIDATED_VENDOR = 'vendor';
    const REVALIDATED_LEAVER = 'leaver';
    const REVALIDATED_POTENTIAL = 'potentialLeaver';
    const REVALIDATED_POTENTIAL_BEGINNING = 'potential';
    const REVALIDATED_PREBOARDER = 'preboarder';
    const REVALIDATED_OFFBOARD = 'offboard';
    const REVALIDATED_OFFBOARDING = 'offboarding'; // 12 
    const REVALIDATED_OFFBOARDED =  'offboarded'; // 11
    const REVALIDATED_OFFBOARDING_STOPPED = 'Offboarding Stopped';
    const REVALIDATED_OFFBOARDED_REVERSED =  'Offboarded Reversed'; // 11
    const SECURITY_EDUCATION_COMPLETED = 'Yes';
    const SECURITY_EDUCATION_NOT_COMPLETED = 'No';
    const PMO_STATUS_TBA       = 'To be assessed';
    const PMO_STATUS_CONFIRMED = 'Confirmed';
    const PMO_STATUS_AWARE     = 'Aware';
    const ROLE_ON_THE_ACCOUNT_WIPRO     = 'Wipro';
    const ROLE_ON_THE_ACCOUNT_COGNIZANT = 'Cognizant';
    const ROLE_ON_THE_ACCOUNT_DENSIFY   = 'Densify';
    const ROLE_ON_THE_ACCOUNT_OTHER     = 'Other';
    const EMPLOYEE_TYPE_REGULAR = 'Regular';
    const EMPLOYEE_TYPE_CONTRACTOR = 'Contractor';
    const EMPLOYEE_TYPE_PRE_HIRE = 'Pre-Hire';
    const EMPLOYEE_TYPE_VENDOR = 'vendor';
    const EMPLOYEE_TYPE_PREBOARDER = 'preboarder';
    const EMPLOYEE_TYPE_ADDITIONAL_IBMER = 'ibmer';
    const EMPLOYEE_TYPE_ADDITIONAL_OTHER = 'other';
    const CIO_ALIGNMENT_TYPE_CTB = 'CTB';
    const CIO_ALIGNMENT_TYPE_RTB = 'RTB';
    const CIO_ALIGNMENT_TYPE_OTHER = 'Other';
//    public static $employeeTypeMapping = array('A'=>'Regular','B'=>'Contractor','C'=>'Contractor','I'=>'Regular','J'=>'Pre-Hire','L'=>'Regular','O'=>'Regular','P'=>'Regular','V'=>'Contractor','X'=>'Regular');
    const AURORA_TRAINING_URL = 'https://kyndryl.percipio.com/track/26c2c517-29c2-4671-bfc1-024271d8fe6b';
    public static $cio = array('CTB Leadership','CTB Central BU','CTB PMO','Commercial & Business Banking','Insurance & Enterprise Programmes','Cyber & TRP','Enterprise Transformation','Retail & Community Banking Transformation','Cross Platform','Product & Engineering');

    // public static $pesTaskId = array('LBGVETPR@uk.ibm.com'); // Only first entry will be used as the "contact" in the PES status emails.
    // Only first entry will be used as the "contact" in the PES status emails.
    public static $pesTaskId = array(
      'Kyndryl' => 'Kyndryl.Lloyds.PES@ibm.com', 
      'IBM' => 'LBGVETPR@uk.ibm.com'
    );

    public static $pesKyndrylTaskIds = array(
      'CTB' => 'aurora.aurora.lbg.ctb.on-off.boarding@kyndryl.com',
      'RTB' => 'aurora.central.pmo@kyndryl.com'
    );

    public static $vbacNoReplyId = 'UKI.Business.Intelligence@kyndryl.com';

    public static $pmoTaskId = array('aurora.central.pmo@kyndryl.com');
    public static $orderITCtbTaskId = array('jeemohan@in.ibm.com');
    public static $orderITNonCtbTaskId = array('aurora.central.pmo@kyndryl.com');
    public static $orderITBauTaskId = array('aurora.central.pmo@kyndryl.com');
    public static $orderITNonBauTaskId = array('aurora.central.pmo@kyndryl.com');
    public static $smCdiAuditEmail = 'e3h3j0u9u6l2q3a3@ventusdelivery.slack.com';
    public static $securityOps = array('Kyndryl.LBG.IAM.Requests@Kyndryl.com');

    private static $pesEmailBody = 'Please initiate PES check for the following individual : Name : &&name&&, Email Address : &&email&&, Notes Id : &&notesid&&, Country working in : &&country&&, LoB : &&lob&&, Role on Project : &&role&&, Contract : &&contract&&, Open Seat : &&openSeat&&, Requested By : &&requestor&&, Requested Timestamp : &&requested&&, Functional Mgr (on CC) : &&functionalMgr&&, PES Level : &&level&&';
    
    private static $pesEmailPatterns = array(
        '/&&name&&/',
        '/&&email&&/',
        '/&&notesid&&/',
        '/&&country&&/',
        '/&&lob&&/',
        '/&&role&&/',
        '/&&contract&&/',
        '/&&openSeat&&/',
        '/&&requestor&&/',
        '/&&requested&&/',
        '/&&functionalMgr&&/',
        '/&&level&&/',
    );

    private static $preboarderStatusChangeEmailBody = '<table width="100%" border="0"   cellpadding="0">
      <tr><td align="center">
        <table width="50%">
            <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please Note the <b>PES STATUS</b> for a <b>Pre-Boarder</b> has changed :</td></tr>
            <tr><th style="background-color:silver;font-size:16px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
            <tr><th style="background-color:silver;font-size:16px">Notes Id</th><td style="font-size:20px">&&notesid&&</td></tr>
            <tr><th style="background-color:SkyBlue;font-size:18px">Status Is</th><td style="font-size:18px">&&StatusIs&&</td></tr>
            <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed By</th><td style="font-size:16px">&&changeor&&</td></tr>
            <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed Date</th><td style="font-size:16px">&&changed&&</td></tr>
        </table>
    </td></tr>
    </table>';

    private static $preboarderStatusChangeEmailPattern = array(
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&StatusIs&&/',
      '/&&changeor&&/',
      '/&&changed&&/'
    );

    private static $pesClearedPersonalEmail = 'Hello &&candidate&&,
      <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, with a personal reference, effective from &&effectiveDate&&
      <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.
      <br/>You are now required to successfully complete the mandatory Aurora Security Education.
      <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
      <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
      <br/>Many Thanks for your cooperation';

    private static $pesClearedPersonalEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

    private static $pesClearedEmail = 'Hello &&candidate&&,
      <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
      <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.

      <br/>If this is the first time you have been PES Cleared for Lloyds Bank, you are required to successfully complete the mandatory Aurora Security Education.
      <br/>If you have previously successfully completed the Aurora Security Education, then this requirement does not apply.
      <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
      <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
      <br/>Many Thanks for your cooperation,';

    private static $pesClearedEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

    private static $pesClearedAmberEmail = 'Hello &&candidate&&,
      <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
      <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.

      <br/>If this is the first time you have been PES Cleared for Lloyds Bank, you are required to successfully complete the mandatory Aurora Security Education.
      <br/>If you have previously successfully completed the Aurora Security Education, then this requirement does not apply.
      <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
      <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
      <br/>Many Thanks for your cooperation,';

    private static $pesClearedAmberEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

    private static $pesCancelPesEmail = 'PES Team,
      <br/>Please stop processing the PES Clearance for : &&candidateFirstName&& <b>&&candidateSurname&&</b> CNUM:( &&cnum&& )
      <br/>This action has been requested by  &&requestor&&.';
    private static $pesCancelPesEmailPattern = array('/&&candidateFirstName&&/','/&&candidateSurname&&/','/&&cnum&&/','/&&requestor&&/');

    private static $pesRestartPesEmail = 'PES Team,
      <br/>Please <b>restart</b> processing the PES Clearance for : &&candidateFirstName&& <b>&&candidateSurname&&</b> CNUM:( &&cnum&& )
      <br/>This action has been requested by  &&requestor&&.';
    private static $pesRestartPesEmailPattern = array('/&&candidateFirstName&&/','/&&candidateSurname&&/','/&&cnum&&/','/&&requestor&&/');

    private static $pesClearedProvisionalEmail = '<p>Hello &&candidate&&,</p>
      <p>Due to the recent situation we understand that many people will be unable to meet with fellow IBM\'ers to have their documents Certified.  We have implemented a \'provisional clearance\' process and will be accepting all documents without certification - however these documents will require to be certified as soon as the restrictions are lifted.</p>
      <p>Therefore I can confirm that you have provisionally passed  Lloyds Bank PES Screening.</p>
      <p>Please note that this will not give you full PES clearance, and your account may not recognise Provisional Clearance, therefore, if you can get your documents certified correctly (as per below) please do so.</p>
      <p>When sending your document please only send to the PES team.</p>
      <p><b>The Certification MUST be done by another IBM�er</b>, to confirm that they have seen the original document. The following statement should be handwritten on <b>each document</b>, on the <b>same side as the image</b>.</p>
      <p style=\'text-align:center;color:red\'>True & Certified Copy<br/>Name of certifier  in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date<br/>Signature of certifier</p>
      <p>If you need any more information regarding your PES clearance, please let me know.</p>
      <p>Many Thanks for your cooperation,</p>'
    ;

    private static $pesClearedProvisionalEmailPattern = array('/&&candidate&&/');

    private static $offboardingEmail = 'Please initiate OFFBOARDING for the following individual:\n
      Name : &&name&&
      Serial: &&cnum&&
      Email Address : &&email&&
      Notes Id : &&notesid&&

      Projected End Date : &&projectedEndDate&&

      Country working in : &&country&&
      LoB : &&lob&&
      Employee Type:&&type&&
      Functional Mgr: &&functionalMgr&&'
    ;

    private static $offboardingEmailPattern = array(
      '/&&name&&/',
      '/&&cnum&&/',
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&projectedEndDate&&/',
      '/&&country&&/',
      '/&&lob&&/',
      '/&&type&&/',
      '/&&functionalMgr&&/',
    );

    private static $warnPmoDateChange = 'Please consider OFFBOARDING the following individual:
      Name : &&name&&
      Serial: &&cnum&&
      Email Address : &&email&&
      Notes Id : &&notesid&&

      Projected End Date : &&projectedEndDate&&

      Country working in : &&country&&
      LoB : &&lob&&
      Employee Type:&&type&&
      Functional Mgr: &&functionalMgr&&'
    ;

    private static $warnPmoDateChangePattern = array(
      '/&&name&&/',
      '/&&cnum&&/',
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&projectedEndDate&&/',
      '/&&country&&/',
      '/&&lob&&/',
      '/&&type&&/',
      '/&&functionalMgr&&/',
    );
    
    private static $cbnEmailBody = "You are recorded in the <a href='&&host&&'>vBAC</a> tool, as a Functional Manager for one or more people.<h3>Please review the people assigned to you for continued business need and/or to correct any inaccuracies. <a href='&&host&&/pa_pmo.php'>Link here</a></h3>"
      . "<p>Select the <b><em>Mgrs CBN Report</em></b> and use the <b><em>Hide Offboarded/ing</em></b> option, both are buttons on the Person Portal page.</p>"
      . "<ul><li>If your reportee has moved to a new functional manager or changed roles, you can amend their details using the <b>Edit Icon</b> in the <em>Notes ID</em> column to do this. All mandatory information must be completed to save the person record. </li>"
      . "<li>If you have people who no longer work on the account  please initiate offboarding by amending their <b>Projected End Date</b>.  Use the <b>Edit Icon</b> in the <em>Notes ID</em> column to do this</li>"
      . "<li> If you are missing people who should report to you<br/>Ensure they have been boarded to the account using the vBAC <a href='&&host&&/pa_personFinder.php'>People Finder</a> screen<br/>You can transfer someone to yourself from another manager by clicking the <b>Transfer Icon</b> in the <em>FM Column</em></li>"
      . "<li>If the person needs to be boarded, then please use the <a href='&&host&&/pb_onboard.php'>Boarding</a> screen</li></ul>";

    private static $cbnEmailPattern = array('/&&host&&/');

    private static $cbcEmailBody = "<h3>The following person has been boarded with a location that may not have a CBC/DOU in place.</h3>"
      . "<table>"
      . "<tbody>"
      . "<tr><th>Notes Id</th><td>&&notesid&&</td></tr>"
      . "<tr><th>CNUM</th><td>&&cnum&&</td></tr>"
      . "<tr><th>Country</th><td>&&countryCode&&</td></tr>"
      . "<tr><th>LBG Location</th><td>&&lbgLocation&&</td></tr>"
      . "<tr><th>Role</th><td>&&role&&</td></tr>"
      . "<tbody>"
      . "<table>";

    private static $cbcEmailPattern = array('/&&notesid&&/','/&&cnum&&/','/&&countryCode&&/','/&&lbgLocation&&/','/&&role&&/');

    // protected static $lobValue = array('GTS','GBS','IMI','Cloud','Security','Other');
    protected static $lobValue = array('Kyndryl', 'IBM - GBS', 'IBM - TSS', 'IBM - Cloud', 'IBM - Security', 'Other');

    const PES_STATUS_NOT_REQUESTED        = 'Not Requested';
    const PES_STATUS_INITIATED            = 'Initiated';
    const PES_STATUS_PES_PROGRESSING      = 'PES Progressing'; 
    const PES_STATUS_CLEARED              = 'Cleared';
    const PES_STATUS_CLEARED_PERSONAL     = 'Cleared - Personal Reference';
    const PES_STATUS_CLEARED_AMBER        = 'Cleared - Amber';
    const PES_STATUS_PROVISIONAL          = 'Provisional Clearance';
    const PES_STATUS_REQUESTED            = 'Evidence Requested';
    const PES_STATUS_RESTART              = 'Restart Requested';
    const PES_STATUS_CANCEL_REQ           = 'Cancel Requested';
    const PES_STATUS_CANCEL_CONFIRMED     = 'Cancel Confirmed';
    const PES_STATUS_RECHECK_REQ          = 'Recheck Req';
    const PES_STATUS_RECHECK_PROGRESSING  = 'Recheck Progressing';
    const PES_STATUS_REMOVED              = 'Removed';
    const PES_STATUS_REVOKED              = 'Revoked';
    const PES_STATUS_DECLINED             = 'Declined';
    const PES_STATUS_EXCEPTION            = 'Exception';
    const PES_STATUS_FAILED               = 'Failed';
    const PES_STATUS_MOVER                = 'Mover';
    const PES_STATUS_LEFT_IBM             = 'Left IBM';
    const PES_STATUS_TBD                  = 'TBD';
    
    static public $pesStatus = array(
      personRecord::PES_STATUS_CLEARED,
      personRecord::PES_STATUS_CLEARED_AMBER,
      personRecord::PES_STATUS_DECLINED,
      personRecord::PES_STATUS_REQUESTED,
      personRecord::PES_STATUS_EXCEPTION,
      personRecord::PES_STATUS_FAILED,
      personRecord::PES_STATUS_INITIATED,
      personRecord::PES_STATUS_PROVISIONAL,
      personRecord::PES_STATUS_REMOVED,
      personRecord::PES_STATUS_REVOKED,
      personRecord::PES_STATUS_TBD,
      personRecord::PES_STATUS_RECHECK_REQ,
      personRecord::PES_STATUS_RECHECK_PROGRESSING,
      personRecord::PES_STATUS_CANCEL_CONFIRMED,
      personRecord::PES_STATUS_MOVER,
      personRecord::PES_STATUS_LEFT_IBM,
      personRecord::PES_STATUS_PES_PROGRESSING
    );

    const PES_STATUS_DETAILS_BOARDED_AS = 'Boarded as';

    const EMP_RESOURCE_REG = 'Resource Details - Kyndryl employees use Kyndryl IDs';
    const EMP_RESOURCE_EXT = 'Resource Details - Use external email addresses';

//     function htmlHeaderCells(){
//         $headerCols = array('Notesid','Fm Cnum','Pes Status','Revalidation Status','Email','CNUM');
//         foreach ($headerCols  as $column) {
//             $headerCells .= "<th>";
//             $headerCells .= $column;
//             $headerCells .= "</th>";
//         }

// //        $headerCells = "<th>Name</th><th>Position</th><th>Office</th><th>Extn.</th><th>Start date</th><th>Salary</th>";
//         return $headerCells;
//     }

    static function getPesTaskId() {
      return self::$pesTaskId['Kyndryl'];
    }

    static function getPesTaskIdByCIO($ctbRtb = '') {
      $pesTaskId = '';
      $pesKyndrylTaskIds = personRecord::$pesKyndrylTaskIds;  // array
      switch($ctbRtb) {
        case self::CIO_ALIGNMENT_TYPE_CTB:
          $pesTaskId = $pesKyndrylTaskIds[self::CIO_ALIGNMENT_TYPE_CTB];            
          break;
        case self::CIO_ALIGNMENT_TYPE_RTB:
          $pesTaskId = $pesKyndrylTaskIds[self::CIO_ALIGNMENT_TYPE_RTB];
          break;
        case self::CIO_ALIGNMENT_TYPE_OTHER:
          // if other choose RTB
          $pesTaskId = $pesKyndrylTaskIds[self::CIO_ALIGNMENT_TYPE_RTB];
          break;
        default:
          break;
      }
      return $pesTaskId;
    }

    function __construct($pwd=null){
        $this->headerTitles['FM_CNUM'] = 'FUNCTIONAL MGR';
        $this->headerTitles['SQUAD_NUMBER'] = 'SQUAD NAME';
        $this->headerTitles['OLD_SQUAD_NUMBER'] = 'OLD SQUAD NAME';
        parent::__construct();
    }
    
    function htmlHeaderCells(){
        $headerCells = parent::htmlHeaderCells();
        $headerCells.= "<th>Has Delegates</th>";
        return $headerCells;
    }
  
    static function loadKnownCnum($predicate=null){
      $sql = " SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$PERSON;

      $rs = sqlsrv_query($GLOBALS['conn'], $sql);

      if(!$rs){
          DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
          return false;
      }

      $allCnums = array();
      while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
          $cnum = trim($row['CNUM']);
          $allCnums[] = $cnum;
      }

      ?><script type="text/javascript">
      var knownCnum = <?=json_encode($allCnums)?>;
      // console.log(knownCnum);
      </script><?php
    }

    static function loadKnownExternalEmail($predicate=null){
      $sql = " SELECT EMAIL_ADDRESS FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$PERSON;
      $sql.= " WHERE CNUM like '%XXX' ";
      $sql.= " ORDER BY 1 ";

      $rs = sqlsrv_query($GLOBALS['conn'], $sql);

      if(!$rs){
          DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
          return false;
      }

      $allExternalEmails = array();
      while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
        $email = trim($row['EMAIL_ADDRESS']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $allExternalEmails[] = $email;
        }
      }

      ?><script type="text/javascript">
      var knownExternalEmail = <?=json_encode($allExternalEmails)?>;
      // console.log(knownExternalEmail);
      </script><?php
    }

    static function loadKnownIBMEmail($predicate=null){
      $sql = " SELECT EMAIL_ADDRESS FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$PERSON;
      $sql.= " WHERE CNUM not like '%XXX' ";
      $sql.= " ORDER BY 1 ";

      $rs = sqlsrv_query($GLOBALS['conn'], $sql);

      if(!$rs){
          DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
          return false;
      }

      $allIBMEmails = array();
      while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
        $email = trim($row['EMAIL_ADDRESS']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $allIBMEmails[] = $email;
        }
      }

      ?><script type="text/javascript">
      var knownIBMEmail = <?=json_encode($allIBMEmails)?>;
      // console.log(knownIBMEmail);
      </script><?php
    }

    static function loadKnownKyndrylEmail($predicate=null){
      $sql = " SELECT KYN_EMAIL_ADDRESS FROM " . $GLOBALS['Db2Schema'] . "." .  allTables::$PERSON;
      $sql.= " WHERE CNUM not like '%XXX' ";
      $sql.= " ORDER BY 1 ";

      $rs = sqlsrv_query($GLOBALS['conn'], $sql);

      if(!$rs){
          DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
          return false;
      }

      $allKyndrylEmails = array();
      while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
        $email = trim($row['KYN_EMAIL_ADDRESS']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $allKyndrylEmails[] = $email;
        }
      }

      ?><script type="text/javascript">
      var knownKyndrylEmail = <?=json_encode($allKyndrylEmails)?>;
      // console.log(knownKyndrylEmail);
      </script><?php
    }
  
    function checkIfTimeToWarnPmo(){
        if(!empty($this->PROJECTED_END_DATE)){
            $projectedEndDate = DateTime::createFromFormat('Y-m-d', $this->PROJECTED_END_DATE);

            $offboardingDate = new \DateTime();
       //    $offboardThreshold = new \DateInterval('P30D');
       //    $offboardingDate->add($offboardThreshold);

            return $projectedEndDate <= $offboardingDate;
        } else {
            return false;
        }
    }

    function checkForCBC(){
        $to = self::$pmoTaskId;
        $title = 'vBAC CBC Check Required -' . $this->CNUM ." (" . trim($this->FIRST_NAME) . " " . trim($this->LAST_NAME) . ")";
        $countryCode = strtoupper(substr(trim($this->CNUM),-3));
        switch ($countryCode) {
            case 'XXX':
                // Not an IBM'er
                break;
            case '709' :
                // India
            case '744' :
                // India
            case '866' :
                // UK
                break;
            default:
                AuditTable::audit('CBC Check Required for ' . $this->NOTES_ID . " ($countryCode)", AuditTable::RECORD_TYPE_AUDIT);
                // private static $cbcEmailPattern = array('/&&notesid&&/','/&&cnum&&/','/&&countryCode&&/','/&&lbgLocation&&/','/&&role&&/');
                $replacements = array($this->NOTES_ID, $this->CNUM, $this->COUNTRY, $this->LBG_LOCATION,  $this->ROLE_ON_THE_ACCOUNT );
                $message = preg_replace(self::$cbcEmailPattern, $replacements, self::$cbcEmailBody);
                BlueMail::send_mail($to, $title, $message, self::$vbacNoReplyId, self::$securityOps);
            break;
        }
    }

    function initiateOffboarding(){

        $mailAccount = empty($this->NOTES_ID) ? $this->EMAIL_ADDRESS : $this->NOTES_ID;

        AuditTable::audit("Prior to Offboarding for Cnum:" . $this->CNUM . " Revalidation Status:" . $this->REVALIDATION_STATUS . " Revalidation Date:" . $this->REVALIDATION_DATE_FIELD . " Updater:" . $_SESSION['ssoEmail'], AuditTable::RECORD_TYPE_DETAILS);
        AuditTable::audit("Initiated Offboarding for Cnum:" . $this->CNUM . " Id:" . $mailAccount . " Projected End Date:" . $this->PROJECTED_END_DATE . " Proposed Leaving Date:" . $this->PROPOSED_LEAVING_DATE, AuditTable::RECORD_TYPE_AUDIT);
        $personTable = new personTable(allTables::$PERSON);
        $personTable->flagOffboarding($this->CNUM, $this->REVALIDATION_STATUS, $this->NOTES_ID, $this->PROPOSED_LEAVING_DATE);
    }

    function displayBoardingForm($mode){

    }

    function displayRegularBoardingForm($mode){
      $loader = new Loader();
      $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
      $activePredicate = personTable::activePersonPredicate();

      $employeeTypeMapping = $loader->loadIndexed('DESCRIPTION','CODE',allTables::$EMPLOYEE_TYPE_MAPPING);

      if (isset($this->EMPLOYEE_TYPE)){
        $this->EMPLOYEE_TYPE = isset($employeeTypeMapping[strtoupper($this->EMPLOYEE_TYPE)]) ? $employeeTypeMapping[strtoupper($this->EMPLOYEE_TYPE)] : $this->EMPLOYEE_TYPE;
        $this->EMPLOYEE_TYPE = ucwords($this->EMPLOYEE_TYPE,' -');
      }

      /*
       * Functional Mgr can board to ANY Functional Mgr Ant Stark 16th Jan 2018
       */
      $userDetails = $loader->loadIndexed('CNUM','EMAIL_ADDRESS',allTables::$PERSON, " EMAIL_ADDRESS='" . htmlspecialchars($_SESSION['ssoEmail']) . "' ");
      $userCnum = isset($userDetails[$_SESSION['ssoEmail']]) ? $userDetails[$_SESSION['ssoEmail']] : false;
      $fmPredicate = " FM_MANAGER_FLAG='Yes' AND $activePredicate ";
      $selectedManagerId = $mode==FormClass::$modeEDIT ? $this->FM_CNUM : $userCnum;
      $hasActivePESStatus = $loader->loadIndexed('PES_STATUS','EMAIL_ADDRESS',allTables::$PERSON, " CNUM='" . $selectedManagerId . "' AND $activePredicate ");
      if (empty($hasActivePESStatus)) {
        $FmCnumNotActive = true;
        $inActiveFMCnum = $this->FM_CNUM;
      } else {
        $FmCnumNotActive = false;
        $inActiveFMCnum = null;
      }
      $allManagers = personTable::optionsForManagers($fmPredicate, $selectedManagerId, $inActiveFMCnum);
      // $countryCodes = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES);
      $skillSets = $loader->loadIndexed('SKILLSET','SKILLSET_ID',allTables::$STATIC_SKILLSETS);

      $allWorkstream = $workstreamTable->getallWorkstream();
      JavaScript::buildSelectArray($allWorkstream, 'workStream');

      $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;
      $displayForEdit = $notEditable ? 'none' : 'inline' ;
      $onlyEditable = $mode==FormClass::$modeEDIT ? 'text' : 'hidden'; // Some fields the user can edit - but not see/set the first time.
      $hideDivFromEdit = $mode==FormClass::$modeEDIT ? ' style="display: none;"  ' : null; // Some fields we don't show on the edit screen.
      $hideDivMgrChange =  ' style="display: none;" ' ; //Some fields we don't until they try to change the FM value

      $availableFromPreBoarding = personTable::optionsForPreBoarded($this->PRE_BOARDED);
      $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";

      $fmManagerFlag = empty($this->FM_MANAGER_FLAG) ? 'No' : $this->FM_MANAGER_FLAG;
      $employeeType = empty($this->EMPLOYEE_TYPE) ? personRecord::EMPLOYEE_TYPE_REGULAR : $this->EMPLOYEE_TYPE;

      $pesDateRequested = empty($this->PES_DATE_REQUESTED) ? '' : $this->PES_DATE_REQUESTED;
      $pesDateResponded = empty($this->PES_DATE_RESPONDED) ? '' : $this->PES_DATE_RESPONDED;
      $pesRequestor = empty($this->PES_REQUESTOR) ? $_SESSION['ssoEmail'] : $this->PES_REQUESTOR;

      $pesStatus = empty($this->PES_STATUS) ? personRecord::PES_STATUS_NOT_REQUESTED : $this->PES_STATUS;
      $pesStatusDetails = $this->PES_STATUS_DETAILS;

      $startDate = \DateTime::createFromFormat('Y-m-d', $this->START_DATE);
      $endDate = \DateTime::createFromFormat('Y-m-d', $this->PROJECTED_END_DATE);
      
      $pesClearedDate = \DateTime::createFromFormat('Y-m-d', $this->PES_CLEARED_DATE);
      $pesRecheckDate = \DateTime::createFromFormat('Y-m-d', $this->PES_RECHECK_DATE);
      $proposedLeavingDate = \DateTime::createFromFormat('Y-m-d', $this->PROPOSED_LEAVING_DATE);

      $formHeading = personRecord::EMP_RESOURCE_REG;
      ?>

      <form id='boardingFormIbmer' class="form-horizontal" onsubmit="return false;">
        <div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title" id='employeeResourceHeading'><?=$formHeading;?></h3>
          </div>
          <div class="panel-body">
          <div class="form-group">
            <div class="col-sm-6">
              <input class="form-control typeahead" id="person_name" name="person_name"
              value="<?=trim($this->FIRST_NAME . " " . $this->LAST_NAME)?>"
              type="text" placeholder='Start typing name/serial/email'
              <?=$notEditable?>>
            </div>
            <div class='col-sm-6'>
              <input class='form-control' id='person_serial' name='CNUM'
                value='<?=$this->CNUM?>' required type='text' disabled='disabled'
                placeholder='Serial Number' <?=$notEditable?>>
            </div>
          </div>

          <div class='form-group'>
            <div class='col-sm-6'>
            <input class='form-control' id='person_notesid' name='NOTES_ID'
              value='<?=$this->NOTES_ID?>'  type='text'
              disabled='disabled' placeholder="Notes Id" <?=$notEditable?>>
            </div>
            <div class='col-sm-6'>
            <input class='form-control' id='person_intranet' name='EMAIL_ADDRESS' 
              value='<?=$this->EMAIL_ADDRESS?>' type='text' 
              disabled='disabled' placeholder="Intranet Id" <?=$notEditable?>>
            </div>
          </div>

          <div class='form-group'>
            <div class='col-sm-6' <?=$hideDivFromEdit?>>
            <input id='person_bio'                   name='person_bio'             value=''                              type='text'   required disabled='disabled' class='form-control' placeholder="Bio">
            <input id='person_first_name'            name='FIRST_NAME'             value='<?=$this->FIRST_NAME?>'        type='hidden' <?=$notEditable?>>
            <input id='person_last_name'             name='LAST_NAME'              value='<?=$this->LAST_NAME?>'         type='hidden' <?=$notEditable?>>
            <input id='person_ibm_location'          name='IBM_BASE_LOCATION'      value='<?=$this->IBM_BASE_LOCATION?>' type='hidden' >
            <input id='person_uid'                   name='person_uid'             value=''                              type='hidden' required>
            <input id='person_is_mgr'                name='FM_MANAGER_FLAG'        value='<?=$fmManagerFlag?>'           type='hidden' >
            <input id='person_employee_type'         name='EMPLOYEE_TYPE'          value='<?=$employeeType?>'		         type='hidden' >
            <input id='person_country'               name='COUNTRY'                value='<?=$this->COUNTRY?>'           type='hidden' >
            <input id='person_pes_status'            name='PES_STATUS'             value='<?=$pesStatus?>'               type='hidden' <?=$notEditable?>>
            <input id='person_pes_status_details'    name='PES_STATUS_DETAILS'     value='<?=$pesStatusDetails?>'        type='hidden' >
            </div>
            <div class='col-sm-6'>
            <input class='form-control' id='person_kyn_intranet' name='KYN_EMAIL_ADDRESS' 
              value='<?=$this->KYN_EMAIL_ADDRESS?>' type='text' 
              disabled='disabled' placeholder="Kyndryl Intranet Id" >
            </div>
          </div>
          
          <div class='form-group'>
            <div class="col-sm-6" id='linkToPreBoarded'>
            <select class='form-control select select2' id='person_preboarded'
              name='PRE_BOARDED'
              <?=$preBoardersAvailable?>
              <?=$notEditable?>
              data-placeholder='Was pre-boarded as:' >
            <option value=''>Link to Pre-Boarded</option>
            <?php
              foreach ($availableFromPreBoarding as $option){
                echo $option;
              };
            ?>
            </select>
            </div>
            <?php $allowEditLocation = " style='display:block' "; ?>
            <div id='editLocationDiv' class='col-sm-6' <?=$allowEditLocation;?>>
            <select class='form-control select select2 locationFor '
              id='person_LBG_LOCATION'
              name='LBG_LOCATION' 
              data-placeholder='LBG Work Location'
            >
            <option value=''>LBG Work Location</option>
            <?php
              $options = assetRequestRecord::buildLocationOptions($this->LBG_LOCATION);
              echo $options;
            ?>
            </select>
            </div>
          </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">Functional Manager Details</h3>
          </div>
          <div class="panel-body" id='fmPanelBody' >
            <div class="form-group">
              <div class="col-sm-6">
              <select class='form-control select select2' 
                id='person_FM_CNUM'
                name='FM_CNUM'
                required='required'
                data-placeholder='Select functional manager' >
              <option value=''>Select Functional Mgr</option>
              <?php
                foreach ($allManagers as $option){
                  echo $option;
                };
              ?>
              </select>
              </div>
              <?php
              if ($FmCnumNotActive == true && $mode==FormClass::$modeEDIT && !empty($this->FM_CNUM)) {
              ?>
              <div class='col-sm-6'>
                <div class="alert alert-danger">
                  <b>Warning:</b> Assigned FM Manager is NOT active employee! 
                </div>
              </div>
              <?php
              }
              ?>
            </div>
          </div>
          <div class="panel-body bg-danger" id='personFmPanelBodyCheckMsg' <?=$hideDivMgrChange?> >
          <input type='hidden' id='person_original_fm' value='<?=$this->FM_CNUM ?>' />
          <p>Before submitting this change please ensure that all HR/Workday, Bluepages and Department Code (GUDA) updates have been completed as necessary. If moving to a new role please ensure the assignment reference number, JRSS and Squad/Tribe alignment are also correct.</p>
          <?php
            $buttons = null;
            $confirmButton = $this->formButton('button','Confirm','confirmFmChangePerson',null,'Confirm','btn btn-primary') ;
            $resetButton   = $this->formButton('button','Cancel','resetFmChangePerson',null,'Cancel','btn btn-primary ');
            $buttons[] = $confirmButton;
            $buttons[] = $resetButton;
            $this->formBlueButtons($buttons); 
          ?>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">Role Details</h3>
          </div>
          <div class="panel-body">
          <div class='form-group' >
            <div class='col-sm-6'>
            <input class="form-control" id="person_open_seat" name="OPEN_SEAT_NUMBER"  required maxlength='15' value="<?=$this->OPEN_SEAT_NUMBER?>" type="text" placeholder='Open Seat Number' data-toggle='tooltip' title='Open Seat' max=12 >
            </div>
            <div class='col-sm-6'>
            <select class='form-control select select2' 
              id='person_skill_set_id'
              name='SKILLSET_ID'
              required
            >
              <option value=''>Select Skillset</option>
              <?php
              foreach ($skillSets as $skillSetId => $skillSet) {
              ?><option value='<?=$skillSetId?>' <?=trim($this->SKILLSET_ID)==trim($skillSetId)? ' selected ' : null ?> ><?=$skillSet?></option>
              <?php
              }
              ?>
            </select>
            </div>
          </div>
          <div class='form-group' >
            <input class="form-control" id="person_lob" name="LOB" value="<?=$this->LOB?>" type="hidden" >
            <?php $allowEditCtid = empty($this->CT_ID) ? " style='display:none;' " : null; ?>
            <div id='editCtidDiv' class='col-sm-6' <?=$allowEditCtid;?>>
            <input class="form-control" id="person_ct_id" name="CT_ID" type="number" min='999999' max='9999999'  value="<?=$this->CT_ID?>" placeholder='7-digit Contractor Id(CT Id) (If known)' >
            <input class="form-control" id="person_ct_id_required" name="CT_ID_REQUIRED" value="<?=$this->CT_ID_REQUIRED?>" type="hidden" >
            </div>
          </div>
          <div class='form-group' id='selectCioAllignment'>
            <div class='col-sm-6'>
              <div class="radio">
                <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='CTB' required  <?=substr($this->CTB_RTB,0,3) == self::CIO_ALIGNMENT_TYPE_CTB ? 'checked' : null ?>    <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>CTB</label>
                <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='RTB' required <?=substr($this->CTB_RTB,0,3) == self::CIO_ALIGNMENT_TYPE_RTB ? 'checked' : null ?>     <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>RTB</label>
                <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='Other' required <?=substr($this->CTB_RTB,0,5) == self::CIO_ALIGNMENT_TYPE_OTHER ? 'checked' : null ?> <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>Other</label>
              </div>
            </div>
            <input class="form-control" id="person_cioAlignment" name="CIO_ALIGNMENT" value="<?=$this->CIO_ALIGNMENT?>" type="hidden" >
          </div>
          <div class='form-group required' >
            <div class='col-sm-6'>
            <input class="form-control required " required id="person_start_date" value="<?=is_object($startDate) ?  $startDate->format('d M Y') : null?>" type="text" placeholder='Start Date' data-toggle='tooltip' title='Start Date'>
            <input class="form-control" id="person_start_date_db2" name="START_DATE" value="<?=$this->START_DATE?>" type="hidden" >
            </div>
            <div class='col-sm-6'>
            <input class="form-control required " required id="person_end_date"  value="<?=is_object($endDate) ? $endDate->format('d M Y') : null?>"  type="text" placeholder='End Date (if known)' data-toggle='tooltip' title='End Date'>
            <input class="form-control" id="person_end_date_db2" name="PROJECTED_END_DATE" value="<?=$this->PROJECTED_END_DATE?>" type="hidden" >
            </div>
          </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">PES Details</h3>
          </div>
          <div class="panel-body">
          <div class='form-group' >
            <div class='col-sm-6'>
            <select class='form-control select select2' 
              id='person_pesLevel'
              required='required'
              name='PES_LEVEL'
              <?=!empty($this->PES_LEVEL) ? ' disabled ' : null;?>
              <?=empty($this->PES_LEVEL) ? " data-toggle='tooltip' title='Please select appropriate PES LEVEL'" : " data-toggle='tooltip' title='Contact PES Team to change PES LEVEL'";?>
            >
              <option value=''>Select PES Level</option>
              <option value='<?=personTable::PES_LEVEL_ONE;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_ONE ? ' selected ' : null;?>><?=personTable::PES_LEVEL_ONE . " (SPRH - Recheck Annually)";?></option>
              <option value='<?=personTable::PES_LEVEL_TWO;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_TWO ? ' selected ' : null;?>><?=personTable::PES_LEVEL_TWO . " (SRH or No Client Access - Recheck every 3 Yrs)"?></option>        
            </select>
            </div>
            <div class='col-sm-6'>
            <button class="btn btn-xs btn-success btnPesDescription" title="PES Details explanation">
              <span class="glyphicon glyphicon-tags"></span>
            </button>
            </div>
          </div>
          <input id="person_account_access" name="ACCT_ACC" value="<?=$this->ACCT_ACC?>" type="hidden" >
          <div class='form-group' >
            <div class='col-sm-6'>
            <input class="form-control" id="person_pes_cleared_date" value="<?=is_object($pesClearedDate) ?  $pesClearedDate->format('d M Y') . " (Cleared)" : null?>" type="text" placeholder='PES Cleared Date' data-toggle='tooltip' title='PES Cleared Date' disabled>
            <input class="form-control" id="person_pes_cleared_date_db2" name="pesClearedDate" value="<?=$this->PES_CLEARED_DATE . " (Cleared)"?>" type="hidden" >
            </div>
            <div class='col-sm-6'>
            <input class="form-control" id="person_pes_recheck_date"  value="<?=is_object($pesRecheckDate) ? $pesRecheckDate->format('d M Y')  . " (Recheck)" : null?>"  type="text" placeholder='PES Recheck Date' data-toggle='tooltip' title='PES Recheck Date' disabled>
            <input class="form-control" id="person_pes_recheck_date_db2" name="pesRecheckDate" value="<?=$this->PES_RECHECK_DATE?>" type="hidden" >
            </div>
          </div>
          </div>
          <input id='person_pes_date_requested'   name='PES_DATE_REQUESTED'     value='<?=$pesDateRequested?>'           type='hidden'  >
          <input id='person_pes_date_responded'   name='PES_DATE_RESPONDED'     value='<?=$pesDateResponded?>'           type='hidden'  >
          <input id='person_pes_requestor'        name='PES_REQUESTOR'          value='<?=$pesRequestor?>'               type='hidden'  >
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">Revalidation Details</h3>
          </div>
          <div class="panel-body">
          <div class='form-group' >
            <div class='col-sm-6'>
            <input class="form-control" id="person_proposed_leaving_date" value="<?=is_object($proposedLeavingDate) ? $proposedLeavingDate->format('d M Y') : null?>" type="text" placeholder='Proposed Leaving Date' data-toggle='tooltip' title='Proposed Leaving Date' disabled>
            <input class="form-control" id="person_proposed_leaving_date_db2" name="PROPOSED_LEAVING_DATE" value="<?=$this->PROPOSED_LEAVING_DATE?>" type="hidden" >
            </div>
          </div>
          </div>
        </div>
        <?php

        include_once 'includes/formMessageArea.html';

        $allButtons = null;
        $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRegularBoarding',null,'Update','btn btn-primary') :  $this->formButton('submit','Submit','saveRegularBoarding','disabled','Save','btn btn-primary');
        $pesButton    = $mode==FormClass::$modeEDIT ?  null :  $this->formButton('button','initiateRegularPes','initiateRegularPes','disabled','Initiate PES','btn btn-primary btnPesInitiate');
          $allButtons[] = $submitButton;
          $allButtons[] = $pesButton;
        $this->formBlueButtons($allButtons);
        $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
        ?>
      </form>
      <?php
    }

    function displayVendorBoardingForm($mode){
      $loader = new Loader();
      $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
      $activePredicate = personTable::activePersonPredicate();

      $employeeTypeMapping = $loader->loadIndexed('DESCRIPTION','CODE',allTables::$EMPLOYEE_TYPE_MAPPING);

      if (isset($this->EMPLOYEE_TYPE)){
        $this->EMPLOYEE_TYPE = isset($employeeTypeMapping[strtoupper($this->EMPLOYEE_TYPE)]) ? $employeeTypeMapping[strtoupper($this->EMPLOYEE_TYPE)] : $this->EMPLOYEE_TYPE;
        $this->EMPLOYEE_TYPE = ucwords($this->EMPLOYEE_TYPE,' -');
      }

      /*
      * Functional Mgr can board to ANY Functional Mgr Ant Stark 16th Jan 2018
      */
      $userDetails = $loader->loadIndexed('CNUM','EMAIL_ADDRESS',allTables::$PERSON, " EMAIL_ADDRESS='" . htmlspecialchars($_SESSION['ssoEmail']) . "' ");
      $userCnum = isset($userDetails[$_SESSION['ssoEmail']]) ? $userDetails[$_SESSION['ssoEmail']] : false;
      $fmPredicate = " FM_MANAGER_FLAG='Yes' AND $activePredicate ";
      $selectedManagerId = $mode==FormClass::$modeEDIT ? $this->FM_CNUM : $userCnum;
      $hasActivePESStatus = $loader->loadIndexed('PES_STATUS','EMAIL_ADDRESS',allTables::$PERSON, " CNUM='" . $selectedManagerId . "' AND $activePredicate ");
      if (empty($hasActivePESStatus)) {
        $FmCnumNotActive = true;
        $inActiveFMCnum = $this->FM_CNUM;
      } else {
        $FmCnumNotActive = false;
        $inActiveFMCnum = null;
      }
      $allManagers = personTable::optionsForManagers($fmPredicate, $selectedManagerId, $inActiveFMCnum);
      $countryCodes = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES);
      $skillSets = $loader->loadIndexed('SKILLSET','SKILLSET_ID',allTables::$STATIC_SKILLSETS);

      $allWorkstream = $workstreamTable->getallWorkstream();
      JavaScript::buildSelectArray($allWorkstream, 'workStream');

      $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;
      $displayForEdit = $notEditable ? 'none' : 'inline' ;
      $onlyEditable = $mode==FormClass::$modeEDIT ? 'text' : 'hidden'; // Some fields the user can edit - but not see/set the first time.
      $hideDivFromEdit = $mode==FormClass::$modeEDIT ? ' style="display: none;"  ' : null; // Some fields we don't show on the edit screen.
      $hideDivMgrChange =  ' style="display: none;" ' ; //Some fields we don't until they try to change the FM value

      $availableFromPreBoarding = personTable::optionsForPreBoarded($this->PRE_BOARDED);
      $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";

      $fmManagerFlag = empty($this->FM_MANAGER_FLAG) ? 'No' : $this->FM_MANAGER_FLAG;
      $employeeType = empty($this->EMPLOYEE_TYPE) ? personRecord::REVALIDATED_PREBOARDER : $this->EMPLOYEE_TYPE;

      $pesDateRequested = empty($this->PES_DATE_REQUESTED) ? '' : $this->PES_DATE_REQUESTED;
      $pesDateResponded = empty($this->PES_DATE_RESPONDED) ? '' : $this->PES_DATE_RESPONDED;
      $pesRequestor = empty($this->PES_REQUESTOR) ? $_SESSION['ssoEmail'] : $this->PES_REQUESTOR;

      $pesStatus = empty($this->PES_STATUS) ? personRecord::PES_STATUS_NOT_REQUESTED : $this->PES_STATUS;
      $pesStatusDetails = $this->PES_STATUS_DETAILS;

      $startDate = \DateTime::createFromFormat('Y-m-d', $this->START_DATE);
      $endDate = \DateTime::createFromFormat('Y-m-d', $this->PROJECTED_END_DATE);
      
      $pesClearedDate = \DateTime::createFromFormat('Y-m-d', $this->PES_CLEARED_DATE);
      $pesRecheckDate = \DateTime::createFromFormat('Y-m-d', $this->PES_RECHECK_DATE);
      $proposedLeavingDate = \DateTime::createFromFormat('Y-m-d', $this->PROPOSED_LEAVING_DATE);

      $formHeading = personRecord::EMP_RESOURCE_EXT;
      ?>

      <form id='boardingFormNotIbmer' class="form-horizontal" onsubmit="return false;">
        <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title" id='employeeResourceHeading'><?=$formHeading;?></h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
          <div class="col-sm-6">
            <input class="form-control" id="resource_first_name" name="FIRST_NAME"
            value="<?=$this->FIRST_NAME?>"
            type="text" placeholder='First Name'
            <?=$notEditable?>>
          </div>
          <div class="col-sm-6">
            <input class="form-control" id="resource_last_name" name="LAST_NAME"
            value="<?=$this->LAST_NAME?>"
            type="text" placeholder='Last Name'
            <?=$notEditable?>>
          </div>
          </div>

          <div class='form-group'>
          <div class='col-sm-6'>
            <input class='form-control' id='resource_email'
            name='EMAIL_ADDRESS' value='<?=$this->EMAIL_ADDRESS?>'
            type='text' placeholder="Email Address"
            required
            <?=$notEditable?>>
          </div>
          <div class='col-sm-6'>
            <select class='form-control select select2 ' 
            id='resource_country'
            name='COUNTRY'
            data-placeholder='Country working in:' >
            <option value=''>Country working in</option>
            <?php
            foreach ($countryCodes as $countryName){
              $selected = $this->COUNTRY == $countryName ? " selected " : null;
              echo "<option value='$countryName' ".$selected." >$countryName</option>";
            };
            ?>
            </select>
          </div>
          </div>
          <input id='resource_uid'                name='person_uid'          value='<?=$this->CNUM?>'   				    type='hidden' >
          <input id='resource_is_mgr'	            name='FM_MANAGER_FLAG'     value='<?=$fmManagerFlag?>'            type='hidden' >
          <input id='resource_ibm_location'       name='IBM_BASE_LOCATION'   value='<?=$this->IBM_BASE_LOCATION?>'	type='hidden' >
          <input id='resource_pes_status'         name='PES_STATUS'          value='<?=$pesStatus?>'                type='hidden' >
          <input id='resource_pes_status_details' name='PES_STATUS_DETAILS'  value='<?=$pesStatusDetails?>'         type='hidden' >
          <input id='resource_kyn_email'          name='KYN_EMAIL_ADDRESS'   value='<?=$this->KYN_EMAIL_ADDRESS?>'  type='hidden' >

          <div class='form-group'>
          <div class='col-sm-12'>
            <?php
              $checked = personRecord::EMPLOYEE_TYPE_PREBOARDER == strtolower($this->EMPLOYEE_TYPE) || personRecord::EMPLOYEE_TYPE_PRE_HIRE == $this->EMPLOYEE_TYPE ? ' checked ' : null;
            ?>
            <label class="radio-inline employeeTypeRadioBtn" data-toggle='tooltip' data-placement='auto top' title='IBM Regular and IBM Contractors'>
            <input type="radio" name="EMPLOYEE_TYPE" value='<?=personRecord::EMPLOYEE_TYPE_PREBOARDER?>' data-type='<?=personRecord::EMPLOYEE_TYPE_ADDITIONAL_IBMER?>' <?=$checked?> required>
            Kyndryl Pre-Hire (Regular or Contractor)
            </label>
            <?php
              $checked = personRecord::EMPLOYEE_TYPE_VENDOR == strtolower($this->EMPLOYEE_TYPE) ? ' checked ' : null;
            ?>
            <label class="radio-inline employeeTypeRadioBtn" data-toggle='tooltip' data-placement='auto top' title='3rd Party Vendors'>
            <input type="radio" name="EMPLOYEE_TYPE" value='<?=personRecord::EMPLOYEE_TYPE_VENDOR?>' data-type='<?=personRecord::EMPLOYEE_TYPE_ADDITIONAL_OTHER?>' <?=$checked?> required>
            Other (ie.3rd Party Vendor)
            </label>
          </div>
          </div>

          <input id='resource_preboarded' name='PRE_BOARDED' value='' type='hidden'>

          <div class='form-group'>
          <?php $allowEditLocation = " style='display:block' "; ?>
          <div id='editLocationDiv' class='col-sm-6' <?=$allowEditLocation;?>>
            <select class='form-control select select2 locationFor '
            id='resource_LBG_LOCATION'
            name='LBG_LOCATION' 
            data-placeholder='LBG Work Location'
            >
            <option value=''>LBG Work Location</option>
            <?php
            $options = assetRequestRecord::buildLocationOptions($this->LBG_LOCATION);
            echo $options;
            ?>
            </select>
          </div>
          </div>
        </div>
        </div>

        <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Functional Manager Details</h3>
        </div>
        <div class="panel-body" id='fmPanelBody' >
            <div class="form-group">
              <div class="col-sm-6">
              <select class='form-control select select2' 
                id='person_FM_CNUM'
                name='FM_CNUM'
                required='required'
                data-placeholder='Select functional manager' >
              <option value=''>Select Functional Mgr</option>
              <?php
                foreach ($allManagers as $option){
                  echo $option;
                };
              ?>
              </select>
              </div>
              <?php
              if ($FmCnumNotActive == true && $mode==FormClass::$modeEDIT && !empty($this->FM_CNUM)) {
              ?>
              <div class='col-sm-6'>
                <div class="alert alert-danger">
                  <b>Warning:</b> Assigned FM Manager is NOT active employee! 
                </div>
              </div>
              <?php
              }
              ?>
            </div>
          </div>
        <div class="panel-body bg-danger" id='resourceFmPanelBodyCheckMsg' <?=$hideDivMgrChange?> >
          <input type='hidden' id='resource_original_fm' value='<?=$this->FM_CNUM ?>' />
          <p>Before submitting this change please ensure that all HR/Workday, Bluepages and Department Code (GUDA) updates have been completed as necessary. If moving to a new role please ensure the assignment reference number, JRSS and Squad/Tribe alignment are also correct.</p>
          <?php
          $buttons = null;
          $confirmButton = $this->formButton('button','Confirm','confirmFmChangeResource',null,'Confirm','btn btn-primary') ;
          $resetButton   = $this->formButton('button','Cancel','resetFmChangeResource',null,'Cancel','btn btn-primary ');
          $buttons[] = $confirmButton;
          $buttons[] = $resetButton;
          $this->formBlueButtons($buttons); 
          ?>
        </div>
        </div>

        <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Role Details</h3>
        </div>
        <div class="panel-body">
          <div class='form-group' >
          <div class='col-sm-6'>
            <input class="form-control" id="resource_open_seat" name="OPEN_SEAT_NUMBER"  required maxlength='15' value="<?=$this->OPEN_SEAT_NUMBER?>" type="text" placeholder='Open Seat Number' data-toggle='tooltip' title='Open Seat' max=12 >
          </div>
          <div class='col-sm-6'>
            <select class='form-control select select2' 
            id='resource_skill_set_id'
            name='SKILLSET_ID'
            required
            >
            <option value=''>Select Skillset</option>
            <?php
            foreach ($skillSets as $skillSetId => $skillSet) {
            ?><option value='<?=$skillSetId?>' <?=trim($this->SKILLSET_ID)==trim($skillSetId)? ' selected ' : null ?> ><?=$skillSet?></option>
            <?php
            }
            ?>
            </select>
          </div>
          </div>
          <div class='form-group' >
          <input class="form-control" id="resource_lob" name="LOB" value="<?=$this->LOB?>" type="hidden" >
          <?php $allowEditCtid = empty($this->CT_ID) ? " style='display:none;' " : null; ?>
          <div id='editCtidDiv' class='col-sm-6' <?=$allowEditCtid;?>>
            <input class="form-control" id="resource_ct_id" name="CT_ID" type="number" min='999999' max='9999999'  value="<?=$this->CT_ID?>" placeholder='7-digit Contractor Id(CT Id) (If known)' >
            <input class="form-control" id="resource_ct_id_required" name="CT_ID_REQUIRED" value="<?=$this->CT_ID_REQUIRED?>" type="hidden" >
          </div>
          </div>
          <div class='form-group' id='selectCioAllignment'>
          <div class='col-sm-6'>
            <div class="radio">
            <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='CTB' required  <?=substr($this->CTB_RTB,0,3) == self::CIO_ALIGNMENT_TYPE_CTB ? 'checked' : null ?>    <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>CTB</label>
            <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='RTB' required <?=substr($this->CTB_RTB,0,3) == self::CIO_ALIGNMENT_TYPE_RTB ? 'checked' : null ?>     <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>RTB</label>
            <label><input type="radio" name="CTB_RTB" class='ctbRtb' value='Other' required <?=substr($this->CTB_RTB,0,5) == self::CIO_ALIGNMENT_TYPE_OTHER ? 'checked' : null ?> <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>Other</label>
            </div>
          </div>
          <input class="form-control" id="resource_cioAlignment" name="CIO_ALIGNMENT" value="<?=$this->CIO_ALIGNMENT?>" type="hidden" >
          </div>
          <div class='form-group required' >
          <div class='col-sm-6'>
            <input class="form-control required " required id="resource_start_date" value="<?=is_object($startDate) ?  $startDate->format('d M Y') : null?>" type="text" placeholder='Start Date' data-toggle='tooltip' title='Start Date'>
            <input class="form-control" id="resource_start_date_db2" name="START_DATE" value="<?=$this->START_DATE?>" type="hidden" >
          </div>
          <div class='col-sm-6'>
            <input class="form-control required " required id="resource_end_date"  value="<?=is_object($endDate) ? $endDate->format('d M Y') : null?>"  type="text" placeholder='End Date (if known)' data-toggle='tooltip' title='End Date'>
            <input class="form-control" id="resource_end_date_db2" name="PROJECTED_END_DATE" value="<?=$this->PROJECTED_END_DATE?>" type="hidden" >
          </div>
          </div>
        </div>
        </div>

        <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">PES Details</h3>
        </div>
        <div class="panel-body">
          <div class='form-group' >
          <div class='col-sm-6'>
            <select class='form-control select select2' 
            id='resource_pesLevel'
            required='required'
            name='PES_LEVEL'
            <?=!empty($this->PES_LEVEL) ? ' disabled ' : null;?>
            <?=empty($this->PES_LEVEL) ? " data-toggle='tooltip' title='Please select appropriate PES LEVEL'" : " data-toggle='tooltip' title='Contact PES Team to change PES LEVEL'";?>
            >
            <option value=''>Select PES Level</option>
            <option value='<?=personTable::PES_LEVEL_ONE;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_ONE ? ' selected ' : null;?>><?=personTable::PES_LEVEL_ONE . " (SPRH - Recheck Annually)";?></option>
            <option value='<?=personTable::PES_LEVEL_TWO;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_TWO ? ' selected ' : null;?>><?=personTable::PES_LEVEL_TWO . " (SRH or No Client Access - Recheck every 3 Yrs)"?></option>        
            </select>
          </div>
          <div class='col-sm-6'>
            <button class="btn btn-xs btn-success btnPesDescription" title="PES Details explanation">
            <span class="glyphicon glyphicon-tags"></span>
            </button>
          </div>
          </div>
          <input id="resource_account_access" name="ACCT_ACC" value="<?=$this->ACCT_ACC?>" type="hidden" >
          <div class='form-group' >
          <div class='col-sm-6'>
            <input class="form-control" id="resource_pes_cleared_date" value="<?=is_object($pesClearedDate) ?  $pesClearedDate->format('d M Y') . " (Cleared)" : null?>" type="text" placeholder='PES Cleared Date' data-toggle='tooltip' title='PES Cleared Date' disabled>
            <input class="form-control" id="resource_pes_cleared_date_db2" name="pesClearedDate" value="<?=$this->PES_CLEARED_DATE . " (Cleared)"?>" type="hidden" >
          </div>
          <div class='col-sm-6'>
            <input class="form-control" id="resource_pes_recheck_date"  value="<?=is_object($pesRecheckDate) ? $pesRecheckDate->format('d M Y')  . " (Recheck)" : null?>"  type="text" placeholder='PES Recheck Date' data-toggle='tooltip' title='PES Recheck Date' disabled>
            <input class="form-control" id="resource_pes_recheck_date_db2" name="pesRecheckDate" value="<?=$this->PES_RECHECK_DATE?>" type="hidden" >
          </div>
          </div>
        </div>
        <input id='resource_pes_date_requested'   name='PES_DATE_REQUESTED'     value='<?=$pesDateRequested?>'           type='hidden'  >
        <input id='resource_pes_date_responded'   name='PES_DATE_RESPONDED'     value='<?=$pesDateResponded?>'           type='hidden'  >
        <input id='resource_pes_requestor'        name='PES_REQUESTOR'          value='<?=$pesRequestor?>'               type='hidden'  >
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Revalidation Details</h3>
          </div>
          <div class="panel-body">
            <div class='form-group' >
            <div class='col-sm-6'>
              <input class="form-control" id="resource_proposed_leaving_date" value="<?=is_object($proposedLeavingDate) ? $proposedLeavingDate->format('d M Y') : null?>" type="text" placeholder='Proposed Leaving Date' data-toggle='tooltip' title='Proposed Leaving Date' disabled>
              <input class="form-control" id="resource_proposed_leaving_date_db2" name="PROPOSED_LEAVING_DATE" value="<?=$this->PROPOSED_LEAVING_DATE?>" type="hidden" >
            </div>
            </div>
          </div>
        </div>
        <?php
        
        include_once 'includes/formMessageArea.html';

        $allButtons = null;
        $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateVendorBoarding',null,'Update','btn btn-primary') :  $this->formButton('submit','Submit','saveVendorBoarding','disabled','Save','btn btn-primary');
        $pesButton    = $mode==FormClass::$modeEDIT ?  null :  $this->formButton('button','initiateVendorPes','initiateVendorPes','disabled','Initiate PES','btn btn-primary btnPesInitiate');
        $allButtons[] = $submitButton;
        $allButtons[] = $pesButton;
        $this->formBlueButtons($allButtons);
        $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
        ?>
      </form>
    <?php
    }

    function displayLinkForm($mode){
      $loader = new Loader();
      $availableFromPreBoarding = personTable::optionsForPreBoarded();
      $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";
      $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;

      $availableForLinking = " PRE_BOARDED is null and CNUM not like '%XXX' ";
      $allNonLinkedKyndrylEmployees = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON, $availableForLinking);
      ?>
      <form id='linkingForm'  class="form-horizontal" onsubmit="return false;">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title" id='employeeResourceHeading'>Employee Details</h3>
          </div>
          <div class="panel-body">
            <div class='form-group' id='ibmerForLinking'>
              <div class="col-sm-6" id='ibmerSelect'>
                <select class='form-control select select2' 
                  id='ibmer_preboarded'
                  name='ibmer_preboarded'
                  data-placeholder='Select Kyndryl employee:' >
                <option value=''>Reg to Link</option>
                <?php
                  foreach ($allNonLinkedKyndrylEmployees as $cnum => $emailAddress){
                      if (empty($emailAddress)) {
                        $emailAddress = 'Unknown email address';
                      }
                      ?><option value='<?=$cnum?>'><?="(" . $cnum . ") " . $emailAddress?></option><?php
                  };
                  ?>
                </select>
              </div>
            </div>

            <div class='form-group' id='linkToPreBoardedFormgroupDiv'>
              <div class="col-sm-6" id='linkToPreBoarded'>
                <select class='form-control select select2' id='person_preboarded'
                  name='person_preboarded'
                  <?=$preBoardersAvailable?>
                  <?=$notEditable?>
                  data-placeholder='Was pre-boarded as:' >
                <option value=''>Link to Pre-Boarded</option>
                <?php
                    foreach ($availableFromPreBoarding as $option){
                      echo $option;
                    };
                ?>
                </select>
              </div>
            </div>
		      </div>
	      </div>
        <?php
        $allButtons = null;
        $submitButton =  $this->formButton('submit','Submit','saveLinking',null,'Save','btn btn-primary');
        $allButtons[] = $submitButton;
        $this->formBlueButtons($allButtons);
        $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
      ?>
      </form>
      <?php
    }

    function displayRfFlagForm(){
      $loader = new Loader();
      $activePredicate = personTable::activePersonPredicate();
      $notAlreadyFlagged = " AND RF_FLAG = '0' ";
      $availableForRfFlag = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON, $activePredicate . $notAlreadyFlagged);

      $rfStartDate = \DateTime::createFromFormat('Y-m-d', $this->RF_Start);
      $rfEndDate = \DateTime::createFromFormat('Y-m-d', $this->RF_End);
      ?>
      <form id='rfFlagForm'  class="form-horizontal" onsubmit="return false;">
        <div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title" id='ringFencing'>Ring Fencing</h3>
            </div>
          <div class="panel-body">
          <div class='form-group' id='ibmerForRfFlag'>
            <div class="col-sm-3" id='ibmerSelect'>
              <select class='form-control select select2' id='personForRfFlag'
                name=personForRfFlag
                placeholder='Select Person:' >
              <option value=''>Person to Flag</option>
              <?php
              foreach ($availableForRfFlag as $cnum => $notesId){
                    ?><option value='<?=$cnum?>'><?=$notesId . "(" . $cnum . ")" ?></option><?php
                };
                ?>
              </select>
            </div>
            <div class="col-sm-3" id='rfStartDate'>
                    <input class="form-control" id="rfStart_Date" value="<?=is_object($rfStartDate) ?  $rfStartDate->format('d M Y') : null?>" type="text" placeholder='RF Start Date' data-toggle='tooltip' title='RF Start Date'>
                  <input class="form-control" id="rfStart_Date_Db2" name="RF_Start" value="<?=$this->RF_Start?>" type="hidden" >
            </div>
            <div class="col-sm-3" id='rfEndDate'>
                    <input class="form-control" id="rfEnd_Date" value="<?=is_object($rfEndDate) ?  $rfEndDate->format('d M Y') : null?>" type="text" placeholder='RF End Date' data-toggle='tooltip' title='RF End Date'>
                  <input class="form-control" id="rfEnd_Date_Db2" name="RF_End" value="<?=$this->RF_End?>" type="hidden" >
            </div>


          </div>
      </div>
    </div>
    <?php
    $allButtons = null;
    $submitButton =  $this->formButton('submit','Submit','saveRfFlag',null,'Ring Fence','btn btn-primary');
    $allButtons[] = $submitButton;
    $this->formBlueButtons($allButtons);
    $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
    ?>
    </form>
    <?php
    }

    function editPersonModalBody(){
        ?>
        <div class='container-fluid'>
        <?php
        $loader = new Loader();
        $employeeType = $this->EMPLOYEE_TYPE;
        $employeeTypeMapping = $loader->loadIndexed('DESCRIPTION', 'CODE', allTables::$EMPLOYEE_TYPE_MAPPING);

        $employeeTypeFinal = isset($employeeTypeMapping[strtoupper($employeeType)]) ? $employeeTypeMapping[strtoupper($employeeType)] : $employeeType;
        // $employeeTypeFinal = ucwords($employeeTypeFinal, ' -');

        switch($employeeTypeFinal) {
          case self::EMPLOYEE_TYPE_REGULAR:
          case self::EMPLOYEE_TYPE_CONTRACTOR:
            $this->displayRegularBoardingForm(FormClass::$modeEDIT);
            break;
          case self::EMPLOYEE_TYPE_PRE_HIRE:
          case self::EMPLOYEE_TYPE_VENDOR:     // lowercased
          case self::EMPLOYEE_TYPE_PREBOARDER: // lowercased
          case 'Vendor':
          case 'Preboarder':
            $this->displayVendorBoardingForm(FormClass::$modeEDIT);
            break;
          default:
            break;
        }
        ?>
        </div>
        <?php
    }

    function convertCountryCodeToName(){
        if(strlen($this->COUNTRY)== 2){
            $loader = new Loader();
            $countryName = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES, " COUNTRY_CODE='" . htmlspecialchars(trim($this->COUNTRY)) . "' ");
            $this->COUNTRY = isset($countryName[$this->COUNTRY]) ? $countryName[$this->COUNTRY] : $this->COUNTRY;
        }
    }

    function sendPesRequest(){
        $loader = new Loader();

        if(!empty($this->FM_CNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($this->FM_CNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($this->FM_CNUM)]) ? $fmEmailArray[trim($this->FM_CNUM)] : $this->FM_CNUM;
        } else {
            $fmEmail = 'Unknown';
        }
        $firstName = !empty($this->FIRST_NAME) ? $this->FIRST_NAME : "firstName";
        $lastName  = !empty($this->LAST_NAME) ? $this->LAST_NAME : "lastName";
        $emailAddress = !empty($this->EMAIL_ADDRESS) ? $this->EMAIL_ADDRESS : "emailAddress";
        $notesId = !empty($this->NOTES_ID) ? $this->NOTES_ID : "notesId";
        $country = !empty($this->COUNTRY) ? $this->COUNTRY : "country";
        $openSeat = !empty($this->OPEN_SEAT_NUMBER) ? $this->OPEN_SEAT_NUMBER : "open seat/hiring";
        $lob = !empty($this->LOB) ? $this->LOB : "lob";
        $role = !empty($this->ROLE_ON_THE_ACCOUNT) ? $this->ROLE_ON_THE_ACCOUNT : "role";
        $level = !empty($this->PES_LEVEL) ? $this->PES_LEVEL : personTable::PES_LEVEL_TWO;

        $ctbRtb = !empty($this->CTB_RTB) ? trim($this->CTB_RTB) : null;
        $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);

        $now = new \DateTime();
        $replacements = array($firstName . " " . $lastName,
          $emailAddress,
          $notesId,
          $country,
          $lob,
          $role,
          'Ventus',
          $openSeat,
          $_SESSION['ssoEmail'],
          $now->format('Y-m-d H:i:s'),
          $fmEmail,
          $level
        );
        $to[] = $pesTaskId;
        $title = 'vBAC PES Request - ' . $this->CNUM ." (" . trim($this->FIRST_NAME) . " " . trim($this->LAST_NAME) . ")";
        $message = preg_replace(self::$pesEmailPatterns, $replacements, self::$pesEmailBody);
        
        BlueMail::send_mail($to, $title, $message, self::$vbacNoReplyId);

    }

    function sendPesStatusChangedEmail($isPesSuppressable = true){

        $fmIsIbmEmail = false;

        if(!empty($this->FM_CNUM)){
            $loader = new Loader();
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($this->FM_CNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($this->FM_CNUM)]) ? $fmEmailArray[trim($this->FM_CNUM)] : null;
            $fmIsIbmEmail = strtolower(substr($fmEmail,-7))=='ibm.com';
        }

        $fmEmail = $fmIsIbmEmail ? $fmEmail : null;

        $ctbRtb = !empty($this->CTB_RTB) ? trim($this->CTB_RTB) : null;
        $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);

        $emailAddress = trim($this->EMAIL_ADDRESS);
        $isIbmEmail = strtolower(substr($emailAddress,-7))=='ibm.com';

        if(!$isIbmEmail && !$fmIsIbmEmail){
            throw new \Exception('No IBM Email Address for individual or Functional Manager');
        }

        $to = array();
        $cc = array();
        $bcc = array();
        $noAttachments = array();

        switch ($this->PES_STATUS) {
            case self::PES_STATUS_CLEARED_PERSONAL:
                $pattern   = self::$pesClearedPersonalEmailPattern;
                $emailBody = self::$pesClearedPersonalEmail;
                $replacements = array(
                  $this->FIRST_NAME, 
                  $this->PES_DATE_RESPONDED, 
                  $pesTaskId
                );
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_CLEARED_AMBER:
                $pattern   = self::$pesClearedAmberEmailPattern;
                $emailBody = self::$pesClearedAmberEmail;
                $replacements = array(
                  $this->FIRST_NAME, 
                  $this->PES_CLEARED_DATE, 
                  $pesTaskId
                );
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_CLEARED:
                $pattern   = self::$pesClearedEmailPattern;
                $emailBody = self::$pesClearedEmail;
                $replacements = array(
                  $this->FIRST_NAME, 
                  $this->PES_CLEARED_DATE, 
                  $pesTaskId
                );
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_PROVISIONAL: // For Covid
                $pattern   = self::$pesClearedProvisionalEmailPattern;
                $emailBody = self::$pesClearedProvisionalEmail;
                $replacements = array(
                  $this->FIRST_NAME
                );
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_CANCEL_REQ:
                $pattern   = self::$pesCancelPesEmailPattern;
                $emailBody = self::$pesCancelPesEmail;
                $title = 'vBAC Cancel Request';
                $replacements = array(
                  $this->FIRST_NAME, 
                  $this->LAST_NAME, 
                  $this->CNUM, 
                  $_SESSION['ssoEmail']
                );
                foreach (personRecord::$pesKyndrylTaskIds as $key => $taskId){
                  $to[] = $taskId;
                }
                !empty($fmEmail) ? $cc[] = $fmEmail : null;
                $cc[] = $_SESSION['ssoEmail'];
                break;
            case self::PES_STATUS_RESTART:
                $pattern   = self::$pesRestartPesEmailPattern;
                $emailBody = self::$pesRestartPesEmail;
                $title = 'vBAC Restart Request';
                $replacements = array(
                  $this->FIRST_NAME, 
                  $this->LAST_NAME, 
                  $this->CNUM, 
                  $_SESSION['ssoEmail']
                );
                foreach (personRecord::$pesKyndrylTaskIds as $key => $taskId){
                  $to[] = $taskId;
                }
                !empty($fmEmail) ? $cc[] = $fmEmail : null;
                $cc[] = $_SESSION['ssoEmail'];
            default:
              break;
        }

        AuditTable::audit(print_r($pattern,true),AuditTable::RECORD_TYPE_DETAILS);
        AuditTable::audit(print_r($replacements,true),AuditTable::RECORD_TYPE_DETAILS);
        AuditTable::audit(print_r($emailBody,true),AuditTable::RECORD_TYPE_DETAILS);

        $message = preg_replace($pattern, $replacements, $emailBody);

        AuditTable::audit(print_r($message,true),AuditTable::RECORD_TYPE_DETAILS);
        
        // $responseData = BlueMail::send_mail($to, $title ,$message, $pesTaskId, $cc, $bcc, true, $noAttachments, pesEmail::EMAIL_PES_SUPRESSABLE );
        $responseData = BlueMail::send_mail($to, $title ,$message, $pesTaskId, $cc, $bcc, true, $noAttachments, $isPesSuppressable );
        list(
            'sendResponse' => $response, 
            'Status' => $status
        ) = $responseData;

        return array(
          'response' => $response,
          'to' => $to,
          'message' => $message,
          'pesTaskId' => $pesTaskId
        );
    }

    function setPesRequested(){
        $table = new personTable(allTables::$PERSON);
        $success = $table->setPesRequested($this->CNUM, $_SESSION['ssoEmail']);
        return $success;
    }

    function sendOffboardingWarning(){
        $loader = new Loader();

        if(!empty($this->FM_CNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($this->FM_CNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($this->FM_CNUM)]) ? $fmEmailArray[trim($this->FM_CNUM)] : $this->FM_CNUM;
        } else {
            $fmEmail = 'Unknown';
        }
        $firstName = !empty($this->FIRST_NAME) ? $this->FIRST_NAME : "firstName";
        $lastName  = !empty($this->LAST_NAME) ? $this->LAST_NAME : "lastName";
        $cnum  = !empty($this->CNUM) ? $this->CNUM : "serial  missing from vBAC";
        $emailAddress = !empty($this->EMAIL_ADDRESS) ? $this->EMAIL_ADDRESS : "emailAddress  missing from vBAC";
        $notesId = !empty($this->NOTES_ID) ? $this->NOTES_ID : "notesId  missing from vBAC";
        $country = !empty($this->COUNTRY) ? $this->COUNTRY : "county  missing from vBAC";
        $lob     = !empty($this->LOB) ? $this->LOB : "lob  missing from vBAC";
        $type    = !empty($this->EMPLOYEE_TYPE) ? $this->EMPLOYEE_TYPE : "employee type missing from vBAC";
        $projectedEndDate = !empty($this->PROJECTED_END_DATE) ? $this->PROJECTED_END_DATE : "projected_end_date";

        $now = new \DateTime();
        $replacements = array($firstName . " " . $lastName,
          $cnum,
          $emailAddress,
          $notesId,
          $projectedEndDate,
          $country,
          $lob,
          $type,
          $fmEmail
        );
        $to = self::$pmoTaskId;
        $title = 'vBAC Projected End Date Change - ' . $this->CNUM ." (" . trim($this->FIRST_NAME) . " " . trim($this->LAST_NAME) . ")";
        $message = preg_replace(self::$warnPmoDateChangePattern, $replacements, self::$warnPmoDateChange);

        BlueMail::send_mail($to, $title, $message, self::$vbacNoReplyId);
    }

    function sendCbnEmail(){
//         $loader = new Loader();
        $personTable = new personTable(allTables::$PERSON);
//        $allFm = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON, " UPPER(FM_MANAGER_FLAG) like 'Y%' ");

        $allFm = $personTable->activeFmEmailAddressesByCnum();
        $emailableFmLists = array_chunk($allFm, 49);
        $replacements = array("https://" . $_SERVER['HTTP_HOST']);
        $to = self::$pmoTaskId;
        $title = 'CBN Initiation Request';
        $emailMessage = preg_replace(self::$cbnEmailPattern, $replacements, self::$cbnEmailBody);
        foreach ($emailableFmLists as $groupOfFmEmail){
            $cc = array();
            $bcc = $groupOfFmEmail;
            /*
             *
             * Next few lines - use for Testing. Comment out for Live.
             *
             */
//             $to  = array('Piotr.Tajanowicz@ocean.ibm.com');
//             $cc  = array('jayhunter@uk.ibm.com');
//             $bcc = array('Piotr.Tajanowicz@ocean.ibm.com','antstark@uk.ibm.com');
            /*
             *
             * Lines above - use for testing. Comment out for Live.
             *
             */
            $bcc[] = self::$smCdiAuditEmail;  // Always copy the slack channel.
            set_time_limit(60);
            BlueMail::send_mail($to, $title, $emailMessage, self::$vbacNoReplyId, $cc, $bcc);
        }
   }

   function informPmoOfPesStatusChange($newPesStatus, $ctbRtb){
      $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);
      $to = array($pesTaskId);
      $cc = array();
      $bcc = array();
      $title = 'Pre-Boarder PES Status Change';
      $noAttachments = array();
      
      $emailAddress = !empty($this->EMAIL_ADDRESS) ? $this->EMAIL_ADDRESS : "emailAddress  missing from vBAC";
      $notesId = !empty($this->NOTES_ID) ? $this->NOTES_ID : "NotesId  missing from vBAC";

      $now = new \DateTime();
      $replacements = array($emailAddress,$notesId,$newPesStatus,$_SESSION['ssoEmail'],$now->format('d-m-Y'));
      $emailMessage = preg_replace(self::$preboarderStatusChangeEmailPattern, $replacements, self::$preboarderStatusChangeEmailBody);

      set_time_limit(60);
      $responseData = BlueMail::send_mail($to, $title, $emailMessage, self::$vbacNoReplyId, $cc, $bcc, true, $noAttachments, pesEmail::EMAIL_NOT_PES_SUPRESSABLE );
      list(
        'sendResponse' => $response, 
        'Status' => $status
      ) = $responseData;
   }

//    static function employeeTypeMappingToDb2(){
//        $sqlDrop  = "DROP TABLE SESSION.EMPLOYEE_TYPE_MAPPING;";
//        $sqlCreate = "Create global temporary table SESSION.EMPLOYEE_TYPE_MAPPING  (code char(1) not null, description char(20) not null) ON COMMIT PRESERVE ROWS;";
//        $sqlInsert = array();

//        foreach (self::$employeeTypeMapping as $code => $description){
//            $sqlInsert[] = "INSERT into SESSION.EMPLOYEE_TYPE_MAPPING  ( code, description ) values ('$code','$description') ";
//        }

//         sqlsrv_query($GLOBALS['conn'], $sqlDrop);
//         $created = sqlsrv_query($GLOBALS['conn'], $sqlCreate);

//         if(!$created){
//             throw new \Exception('Unable to create EmployeeTypeMapping Temp Table');
//         }

//         foreach ($sqlInsert as $insertStatement){
//             $inserted = sqlsrv_query($GLOBALS['conn'], $insertStatement);
//             if(!$inserted){
//                 throw new \Exception('Unable to populate EmployeeTypeMapping Temp Table');
//             }
//         }
//    }
  
function amendPesStatusModal(){
  $now = new \DateTime();
?>
<!-- Modal -->
<div id="amendPesStatusModal" class="modal fade" role="dialog">
<div class="modal-dialog">

  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Amend PES Status</h4>
      </div>
    <div class="modal-body" >
    <form id='psmForm' class="form-horizontal"  method='post'>

    <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Employee Details</h3>
    </div>
  <div class="panel-body">
      <div class='row'>
        <div class='form-group' >
          <div class='col-sm-6'>
              <input class="form-control" id="psm_notesid" name="psm_notesid" value="" type="text" disabled>
          </div>
          <div class='col-sm-6'>
              <input class="form-control" id="psm_cnum" name="psm_cnum" value="" type="text" disabled>
          </div>
       </div>
     </div>
     <div class='row' id='passportNameDetails' >
        <div class='form-group' >
          <div class='col-sm-6'>
              <input class="form-control" id="psm_passportFirst" name="psm_passportFirst" value="" type="text" placeholder='Passport First Name' disabled >
          </div>
          <div class='col-sm-6'>
              <input class="form-control" id="psm_passportSurname" name="psm_passportSurname" value="" type="text" placeholder='Passport Surname'>
         </div>
       </div>
     </div>

   </div>
    </div>

    <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">PES Status</h3>
    </div>
  <div class="panel-body">
     <div class='row'>
        <div class='form-group required' >
          <label for='psm_status' class='col-md-2 control-label '>Status</label>
          <div class='col-md-4'>
              <select class='form-control select' id='psm_status'
                name='psm_status'
                required='required'
                data-tags="true" data-placeholder="Status" data-allow-clear="true"
              >
                <option value=''>Status</option>
                <?php
                foreach (self::$pesStatus as $key => $status) {
                    ?><option value='<?=$status;?>'><?=$status;?></option>
                    <?php
                }
                ?>
              </select>
           </div>
           <label for='pes_date' class='col-md-1 control-label '>Date</label>
           <div class='col-md-3'>
            <input class="form-control" id="pes_date" value="<?=$now->format('d M Y')?>" type="text" placeholder='Pes Status Changed' data-toggle='tooltip' title='PES Date Responded'>
            <input class="form-control" id="pes_date_db2"  value="<?=$now->format('Y-m-d')?>" name="PES_DATE_RESPONDED" type='hidden' >
           </div>
        </div>
      </div>

     <div class='row'>
        <div class='form-group required' >
          <label for='psm_detail' class='col-md-2 control-label '>Detail</label>
            <div class='col-md-8' id='pesDateDiv'>
            <input class="form-control" id="psm_detail" name="psm_detail" value="" type="text"  >
               </div>
          </div>
       </div>
     </div>
    </div>
    <div class="modal-footer">
    <?php
    $allButtons = null;
    $submitButton = $this->formButton('submit','Submit','savePesStatus',null,'Submit','btn-primary');
    $allButtons[] = $submitButton;
    $this->formBlueButtons($allButtons);
    ?>

     <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
    </div>
    <input id='psm_revalidationstatus' name='psm_revalidationstatus' type='hidden' />
    </form>
  </div>
</div>
</div>
</div>
  <?php
}

function amendPesLevelModal(){
  $now = new \DateTime();
  ?>
  <!-- Modal -->
  <div id="amendPesLevelModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Amend PES Level</h4>
        </div>
        <div class="modal-body" >
          <form id='plmForm' class="form-horizontal"  method='post'>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Employee Details</h3>
              </div>
              <div class="panel-body">
                <div class='row'>
                  <div class='form-group' >
                    <div class='col-sm-6'>
                      <input class="form-control" id="plm_notesid" name="plm_notesid" value="" type="text" disabled>
                    </div>
                    <div class='col-sm-6'>
                      <input class="form-control" id="plm_cnum" name="plm_cnum" value="" type="text" disabled>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">PES Details</h3>
              </div>
              <div class="panel-body">
                <div class='form-group' >
                  <div class='col-sm-6'>
                    <select class='form-control select select2' id='plm_level'
                      required='required'
                      name='plm_level'
                      <?=!empty($this->PES_LEVEL) ? ' disabled ' : null;?>
                      <?=empty($this->PES_LEVEL) ? " data-toggle='tooltip' title='Please select appropriate PES LEVEL'" : " data-toggle='tooltip' title='Contact PES Team to change PES LEVEL'";?>
                    >
                      <option value=''>Select PES Level</option>
                      <option value='<?=personTable::PES_LEVEL_ONE;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_ONE ? ' selected ' : null;?>><?=personTable::PES_LEVEL_ONE . " (SPRH - Recheck Annually)";?></option>
                      <option value='<?=personTable::PES_LEVEL_TWO;?>' <?=$this->PES_LEVEL==personTable::PES_LEVEL_TWO ? ' selected ' : null;?>><?=personTable::PES_LEVEL_TWO . " (SRH or No Client Access - Recheck every 3 Yrs)"?></option>        
                    </select>
                  </div>
                  <div class='col-sm-6'>
                    <button class="btn btn-xs btn-success btnPesDescription" title="PES Details explanation">
                      <span class="glyphicon glyphicon-tags"></span>
                    </button>
                  </div>
                </div>
                <div class='form-group' >
                  <div class='col-sm-12'>
                    <input class="form-control" id="pes_cleared_date" value="" type="text" placeholder='PES Cleared Date' data-toggle='tooltip' title='PES Cleared Date' disabled>
                    <input class="form-control" id="pes_cleareD_date_db2" name="pesClearedDate" value="" type="hidden" >
                  </div>
                </div>
                <div class='form-group' >
                  <div class='col-sm-12'>
                    <input class="form-control" id="pes_old_recheck_date"  value=""  type="text" placeholder='Current PES Recheck Date' data-toggle='tooltip' title='Current PES Recheck Date' disabled>
                    <input class="form-control" id="pes_old_recheck_date_db2" name="pesOldRecheckDate" value="" type="hidden" >
                  </div>
                </div>
              </div>
            </div>
              <div class="modal-footer">
              <?php
                $allButtons = null;
                $submitButton = $this->formButton('submit','Submit','savePesLevel',null,'Submit','btn-primary');
                $allButtons[] = $submitButton;
                $this->formBlueButtons($allButtons);
              ?>
              <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
}

function confirmChangeFmFlagModal(){
  ?>
  <!-- Modal -->
  <div id="confirmChangeFmFlagModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirm change of FM Flag</h4>
        </div>

        <form id='confirmFmFlagChangeForm' class="form-horizontal"  method='post' >
        <div class="modal-body" >
        </div>
        <div class='modal-footer'>
        <?php
        $allButtons = null;
        $submitButton = $this->formButton('submit','Submit','confirmFmStatusChange',null,'Submit','btn-primary');
        $allButtons[] = $submitButton;
        $this->formBlueButtons($allButtons);
        ?>
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
        </div>
        </form>
        </div>
    </div>
  </div>
  <?php
}

function confirmSendPesEmailModal(){
  ?>
 <!-- Modal -->
<div id="confirmSendPesEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm PES Email Details</h4>
      </div>
       <div class="modal-body" >
       <div class="panel panel-default">
        <div class="panel-heading">Applicant Details</div>
        <div class="panel-body">
        <form>
        <input type='hidden' id='pesEmailCnum' name='pesEmailCnum' >
        <div class="form-group">
        <label for="pesEmailFirstName">First Name</label>
        <input type="text" class="form-control" id="pesEmailFirstName" name="pesEmailFirstName" disabled >
        </div>
        <div class="form-group">
        <label for="pesEmailLastName">Last Name</label>
        <input type="text" class="form-control" id="pesEmailLastName" name="pesEmailLastName" disabled >
        </div>
        <div class="form-group">
        <label for="pesEmailOpenSeat">Open Seat</label>
        <input type="text" class="form-control" id="pesEmailOpenSeat" name="pesEmailOpenSeat" disabled >
        </div>
        <div class="form-group">
        <label for="pesEmailAddress">Email address</label>
        <input type="text" class="form-control" id="pesEmailAddress" name="pesEmailAddress" disabled >
        </div>
        <div class="form-group">
        <label for="pesEmailCountry">Country</label>
        <input type="text" class="form-control" id="pesEmailCountry" name="pesEmailCountry" disabled >
        </div>
        <div class="form-group">
        <label for="pesEmailFilename">Filename</label>
        <input type="text" class="form-control" id="pesEmailFilename" name="pesEmailFilename" disabled >
        </div>

        <div class="form-group">
        <label for="pesEmailAttachments">Attachments</label>
        <textarea class="form-control" id="pesEmailAttachments" name="pesEmailAttachments" disabled ></textarea>
        </div>
        <input type="hidden" id="pesEmailRecheck" name="pesEmailRecheck" >

        </form>
        </div>
        </div>
      </div>
       <div class='modal-footer'>
              <?php
              $allButtons = null;
              $submitButton = $this->formButton('submit','confirmSendPesEmail','confirmSendPesEmail',null,'Confirm','btn-primary');
              $allButtons[] = $submitButton;
              $this->formBlueButtons($allButtons);
              ?>
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
       </div>
      </div>
  </div>
</div>
<?php
}


function confirmTransferModal(){
  $myCnum = personTable::myCnum();
  $myEmail = trim($_SESSION['ssoEmail']);
  $myNotesid = personTable::getNotesidFromCnum($myCnum);
  ?>
 <!-- Modal -->
<div id="confirmTransferModal" class="modal fade" role="dialog">
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirm Transfer</h4>
        </div>
        <div class="modal-body" >
<form id='confirmTransferForm' >
<div class="form-group">
<label for="notes_id">Notes ID</label>
<input type="text" class="form-control" id="transferNotes_id" disabled placeholder="Notes Id">
<input type="hidden" class="form-control" id="transferCnum" name="transferCnum" >
</div>
<div class="form-group">
<label for="from_notes_id">From Manager:</label>
<input type="text" class="form-control" id="transferFromNotesId"  disabled placeholder="From Manager">
<input type="hidden" class="form-control" id="transferFromCnum" >
</div>
<div class="form-group">
<label for="to_notes_id">To Manager:</label>
<input type="text" class="form-control" id="transferToNotesId" disabled placeholder="To Manager" value='<?=$myNotesid;?>'>
<input type="hidden" class="form-control" id="transferToCnum" name="transferToCnum" value='<?=$myCnum;?>' >
</div>
</form>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-success btnConfirmTransfer" >Confirm</button>
         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
       </div>
</div>
</div>
</div>
  <?php
}

function editAgileSquadModal($version='original'){

$squadTable = $version=='original'  ? allTables::$AGILE_SQUAD : allTables::$AGILE_SQUAD_OLD;

$loader = new Loader();
$allSquadNames = $loader->loadIndexed('SQUAD_NAME','SQUAD_NUMBER',$squadTable);
$allSquadLeaders = $loader->loadIndexed('SQUAD_LEADER','SQUAD_NUMBER',$squadTable);

$squadNumber = $version=='original'  ? $this->SQUAD_NUMBER : $this->OLD_SQUAD_NUMBER;
$title = $version=='original' ? "Edit Agile Squad" : "Edit Old Agile Squad" ;

$squadDetails = !empty($squadNumber) ?  AgileSquadTable::getSquadDetails($this->SQUAD_NUMBER, $version) : array();
 ?>
<!-- Modal -->
<div id="editAgileSquadModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?=$title?></h4>
      </div>
      <div class="modal-body" >
         <form id='editAgileSquadForm' class="form-horizontal"  method='post' >

         <div class="form-group">
         <label for="agileSquad">Squad</label>
              <select name='agileSquad' id='agileSquad' class='form-control' required='required'>
              <option value=''></option>
              <?php
              foreach ($allSquadNames as $squadNumber => $squadName) {
                  ?><option value='<?=$squadNumber;?>'
                  <?=(int)$squadNumber==(int)$this->SQUAD_NUMBER ? " selected " : null;?>
                  >
                  <?=$squadName;?>
                  <?=isset($allSquadLeaders[$squadNumber]) ? " (" . $allSquadLeaders[$squadNumber] . ")" : null;?>
                  </option>
                  <?php
              }
              ?>
         </select>
           </div>
         <div class="form-group">
         <label for="agileSquadType">Squad Type</label>
         <input type="text" class="form-control" id="agileSquadType" name="agileSquadType"
                value='<?=isset($squadDetails['SQUAD_TYPE'])? $squadDetails['SQUAD_TYPE'] : null ;?>'
                disabled >
         </div>
         <div class="form-group">
         <label for="agileTribeNumber">Tribe Number</label>
         <input type="text" class="form-control" id="agileTribeNumber" name="agileTribeNumber"
                value='<?=isset($squadDetails['TRIBE_NUMBER'])? $squadDetails['TRIBE_NUMBER'] : null ;?>'
                disabled >
         </div>
         <div class="form-group">
         <label for="agileTribeName">Tribe Name</label>
         <input type="text" class="form-control" id="agileTribeName" name="agileTribeName"
                value='<?=isset($squadDetails['TRIBE_NAME'])? $squadDetails['TRIBE_NAME'] : null ;?>'
                disabled >
         </div>
         <div class="form-group">
         <label for="agileTribeLeader">Tribe Leader</label>
         <input type="text" class="form-control" id="agileTribeLeader" name="agileTribeLeader"
                value='<?=isset($squadDetails['TRIBE_LEADER'])? $squadDetails['TRIBE_LEADER'] : null ;?>'
                disabled >
         </div>
          <input type="hidden" class="form-control" id="agileCnum" name="agileCnum"
                value='<?=$this->CNUM;?>'
          >
          <input type="hidden" class="form-control" id="version" name="version"
                value='<?=$version;?>'
          >

         <?php

           $allButtons = array();
              $submitButton = $this->formButton('submit','Submit','updateSquad',null,'Update');
              $resetButton  = $this->formButton('reset','Reset','resetRfs',null,'Reset','btn-warning');
              $allButtons[] = $submitButton;
              $allButtons[] = $resetButton;
              $this->formBlueButtons($allButtons);
          ?>
          </form>
      </div>
      <div class='modal-footer'>
      </div>
    </div>
  </div>
</div>
<?php
}

function portalReportSaveModal(){
  ?>
 <!-- Modal -->
<div id="saveReportModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Save Report</h4>
      </div>
       <div class="modal-body" >
        <form id='reportSaveForm' class="form-horizontal"  method='post'>

        <div class='row'>
        <div class='form-group required' >
          <label for='psm_detail' class='col-md-2 control-label '>Report Name</label>
            <div class='col-md-8' id='reportNameDiv'>
            <input class="form-control" id="reportName" name="REPORT_NAME" value="" type="text" >
            </div>

            <input type='hidden' id='reportSettings' name='SETTINGS'>
            <input type='hidden' id='reportCreator' name='EMAIL_ADDRESS' value='<?=$_SESSION['ssoEmail']?>'>

          </div>
       </div>
       </form>
       </div>
       <div class='modal-footer'>
       <?php
      $allButtons = null;
      $submitButton = $this->formButton('submit','Submit','reportSaveConfirm',null,'Submit','btn-primary');
      $allButtons[] = $submitButton;
      $this->formBlueButtons($allButtons);
      ?>
     <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
       </div>
      </div>
  </div>
</div>
<?php
}

function confirmOffboardingModal() {
?>
<!-- Modal -->
<div id="confirmOffboardingModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
       <!-- Modal content-->
       <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
           <h4 class="modal-title">Offboarding</h4>
         </div>
         <form id='plmForm' class="form-horizontal"  method='post'>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Additional Offboarding Details</h3>
            </div>
            <div class="panel-body">
              <div class='form-group'>
                <div class='col-sm-6'>
                  <input class="form-control" id="offboarding_cnum" name="offboarding_cnum" value="" type="text" 
                    placeholder="CNUM" readonly="">
                </div>
                <div class='col-sm-6'>
                  <input class="form-control" id="offboarding_proposed_leaving_date" value="" type="text" placeholder='Proposed Leaving Date' title='Proposed Leaving Date'>
                  <input class="form-control" id="offboarding_proposed_leaving_date_db2" name="offboardingProposedLeavingDate" value="" type="hidden" >
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <?php
              $allButtons = null;
              $submitButton = $this->formButton('submit','Submit','saveOffboarding',null,'Submit','btn-primary');
              $allButtons[] = $submitButton;
              $this->formBlueButtons($allButtons);
            ?>
            <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          </div>
        </form>
       </div>
     </div>
   </div>
<?php
}

function confirmOffboardingInfoModal() {
  ?>
  <!-- Modal -->
  <div id="confirmOffboardingInfoModal" class="modal fade" role="dialog">
       <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
           <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
             <h4 class="modal-title">Offboarding</h4>
           </div>
           <div class="modal-body">
            <div class="panel">
            </div>
          </div>
          <div class='modal-footer'>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
         </div>
       </div>
     </div>
  <?php
  }
}
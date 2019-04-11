<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;
use itdq\DbTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\personTable;
use DateTime;
use DateInterval;

/**
 *
 * @author gb001399
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
    protected $TT_BAU;
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

    protected $WORK_STREAM;
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

    protected $person_bio;




    // Fields to be edited in the DataTables Reports. Need to know their position in the array $row;
    const FIELD_CNUM = 0;
    const FIELD_NOTES_ID = 5;
    const FIELD_FM_MANAGER_FLAG = 9;
    const FIELD_LOB = 12;
    const FIELD_ROLE_ON_THE_ACCOUNT = 13;
    const FIELD_COUNTRY = 17;
    const FIELD_PES_DATE_REQUESTED = 21;
    const FIELD_PES_REQUESTOR = 22;
    const FIELD_PES_DATE_RESPONDED = 23;
    const FIELD_PES_STATUS_DETAILS = 24;
    const FIELD_PES_STATUS = 25;

    const REVALIDATED_FOUND = 'found';
    const REVALIDATED_VENDOR = 'vendor';
    const REVALIDATED_LEAVER = 'leaver';
    const REVALIDATED_POTENTIAL = 'potentialLeaver';
    const REVALIDATED_PREBOARDER = 'preboarder';
    const REVALIDATED_OFFBOARDING = 'offboarding';
    const REVALIDATED_OFFBOARDED = 'offboarded';

    const SECURITY_EDUCATION_COMPLETED = 'Yes';
    const SECURITY_EDUCATION_NOT_COMPLETED = 'No';

    const PMO_STATUS_CONFIRMED = 'Confirmed';
    const PMO_STATUS_AWARE     = 'Aware';

//    public static $employeeTypeMapping = array('A'=>'Regular','B'=>'Contractor','C'=>'Contractor','I'=>'Regular','J'=>'Pre-Hire','L'=>'Regular','O'=>'Regular','P'=>'Regular','V'=>'Contractor','X'=>'Regular');

    public static $cio = array('Commercial','Cross CIO Leadership','Cyber', 'Digital','Divestment','GOFE','IT 4 IT','Insurance','Retail','Sandbox','TRP','tbc');

    public static $pesTaskId = array('lbgvetpr@uk.ibm.com'); // Only first entry will be used as the "contact" in the PES status emails.
    public static $pmoTaskId = array('Aurora.On.and.Off.Boarding.support@uk.ibm.com');
    public static $orderITCtbTaskId = array('jeemohan@in.ibm.com');
    public static $orderITNonCtbTaskId = array('Aurora.On.and.Off.Boarding.support@uk.ibm.com');
    public static $orderITBauTaskId = array('Aurora.On.and.Off.Boarding.support@uk.ibm.com');
    public static $orderITNonBauTaskId = array('Aurora.On.and.Off.Boarding.support@uk.ibm.com');
    public static $smCdiAuditEmail = 'e3h3j0u9u6l2q3a3@ventusdelivery.slack.com';
    //private static $pesTaskId = 'rob.daniel@uk.ibm.com';
    //private static $pesTaskId    = array('rob.daniel@uk.ibm.com', 'carrabooth@uk.ibm.com');
//     private static $pesEmailBody = '<table width="100%" border="0"   cellpadding="0">
//                              <tr><td align="center">
//                                 <table width="50%">
//                                     <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please initiate PES check for the following individual:</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Name</th><td style="font-size:20px">&&name&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Notes Id</th><td style="font-size:20px">&&notesid&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Country working in </th><td style="font-size:20px">&&country&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">LoB</th><td style="font-size:20px">&&lob&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Role on Project</th><td style="font-size:20px">&&role&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Contract</th><td style="font-size:20px">&&contract&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:20px">Open Seat/Hiring</th><td style="font-size:20px">&&openSeat&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested By</th><td style="font-size:16px">&&requestor&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested Timestamp</th><td style="font-size:16px">&&requested&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Functional Mgr (on CC)</th><td style="font-size:16px">&&functionalMgr&&</td></tr>
//                                 </table>
//                             </td></tr>
//                             </table>';
    private static $pesEmailBody = 'Please initiate PES check for the following individual : Name : &&name&&, Email Address : &&email&&, Notes Id : &&notesid&&, Country working in : &&country&&, LoB : &&lob&&, Role on Project : &&role&&, Contract : &&contract&&, Open Seat : &&openSeat&&, Requested By : &&requestor&&, Requested Timestamp : &&requested&&, Functional Mgr (on CC) : &&functionalMgr&&';
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
    );


//     private static $pesStatusChangeEmailBody = '<table width="100%" border="0"   cellpadding="0">
//                              <tr><td align="center">
//                                 <table width="50%">
//                                     <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please Note the<b>PES STATUS</b> has changed for the following individual:</td></tr>
//                                     <tr><th style="background-color:silver;font-size:16px">Name</th><td style="font-size:20px">&&name&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:16px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
//                                     <tr><th style="background-color:silver;font-size:16px">Notes Id</th><td style="font-size:20px">&&notesid&&</td></tr>

//                                     <tr><th style="background-color:SkyBlue;font-size:18px">Status Is</th><td style="font-size:18px">&&statusIs&&</td></tr>

//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed By</th><td style="font-size:16px">&&changeor&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed Timestamp</th><td style="font-size:16px">&&changed&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Functional Mgr</th><td style="font-size:16px">&&functionalMgr&&</td></tr>
//                                 </table>
//                             </td></tr>
//                             </table>';
//     private static $pesStatusChangeEmailPatterns = array(
//         '/&&name&&/',
//         '/&&email&&/',
//         '/&&notesid&&/',
//         '/&&StatusIs&&/',
//         '/&&changeor&&/',
//         '/&&changed&&/',
//         '/&&functionalMgr&&/',
//     );


    private static $pesClearedPersonalEmail = 'Hello &&candidate&&,
                                              <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, with a personal reference, effective from &&effectiveDate&&
                                              <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.
                                              <br/>You are now required to successfully complete the mandatory Aurora Security Education for IBMers.
                                              <br/>Please contact <a href="mailto:Aurora.Central.PMO@uk.ibm.com">Aurora Central PMO/UK/IBM</a> to gain access to these self-paced online courses which are available here:- <a href="https://lt.be.ibm.com/aurora">https://lt.be.ibm.com/aurora</a>
                                              <br/>Many Thanks for your cooperation';



    private static $pesClearedPersonalEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

    private static $pesClearedEmail = 'Hello &&candidate&&,
                                              <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
                                              <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.
                                              <br/>You are now required to successfully complete the mandatory Aurora Security Education for IBMers.
                                              <br/>Please contact <a href="mailto:Aurora.Central.PMO@uk.ibm.com">Aurora Central PMO/UK/IBM</a> to gain access to these self-paced online courses which are available here:- <a href="https://lt.be.ibm.com/aurora">https://lt.be.ibm.com/aurora</a>

                                              <br/>Many Thanks for your cooperation,';
    private static $pesClearedEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');


    private static $pesCancelPesEmail = 'PES Team,
                                              <br/>Please stop processing the PES Clearance for : &&candidateFirstName&& <b>&&candidateSurname&&</b> CNUM:( &&cnum&& )
                                              <br/>This action has been requested by  &&requestor&&.';
    private static $pesCancelPesEmailPattern = array('/&&candidateFirstName&&/','/&&candidateSurname&&/','/&&cnum&&/','/&&requestor&&/');

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



    private static  $lobValue = array('GTS','GBS','IMI','Cloud','Security','Other');




    const PES_STATUS_NOT_REQUESTED = 'Not Requested';
    const PES_STATUS_CLEARED       = 'Cleared';
    const PES_STATUS_CLEARED_PERSONAL   = 'Cleared - Personal Reference';
    const PES_STATUS_DECLINED      = 'Declined';
    const PES_STATUS_EXCEPTION     = 'Exception';
    const PES_STATUS_PROVISIONAL   = 'Provisional Clearance';
    const PES_STATUS_FAILED        = 'Failed';
    const PES_STATUS_INITIATED     = 'Initiated';
    const PES_STATUS_REQUESTED     = 'Evidence Requested';
    const PES_STATUS_REMOVED       = 'Removed';
    const PES_STATUS_CANCEL_REQ     = 'Cancel Requested';
    const PES_STATUS_CANCEL_CONFIRMED = 'Cancel Confirmed';
    const PES_STATUS_TBD           = 'TBD';


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


    function __construct($pwd=null){
        $this->headerTitles['FM_CNUM'] = 'FUNCTIONAL MGR';
        parent::__construct();
    }




    static function loadKnownCnum($predicate=null){
        $sql = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." .  allTables::$PERSON;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        ?><script type="text/javascript">
        var knownCnum = [];
        <?php

        while(($row=db2_fetch_assoc($rs))==true){
            ?>knownCnum.push("<?=trim($row['CNUM']);?>");
            <?php
        }
        ?></script><?php

    }

    static function loadKnownEmail($predicate=null){
        $sql = " SELECT EMAIL_ADDRESS FROM " . $_SESSION['Db2Schema'] . "." .  allTables::$PERSON;
        $sql.= " WHERE CNUM like '%XXX' ";
        $sql.= " ORDER BY 1 ";


        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        ?><script type="text/javascript">
        var knownEmail = [];
        <?php

        while(($row=db2_fetch_assoc($rs))==true){
            ?>knownEmail.push("<?=trim($row['EMAIL_ADDRESS']);?>");
            <?php
        }
        ?>console.log(knownEmail);<?php
        ?></script><?php

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

    function initiateOffboarding(){

        $mailAccount = empty($this->NOTES_ID) ? $this->EMAIL_ADDRESS : $this->NOTES_ID;

        AuditTable::audit("Prior to Offboarding for Cnum:" . $this->CNUM . " Revalidation Status:" . $this->REVALIDATION_STATUS . " Revalidation Date:" . $this->REVALIDATION_DATE_FIELD . " Updater:" . $_SESSION['ssoEmail'],AuditTable::RECORD_TYPE_DETAILS);
        AuditTable::audit("Initiated Offboarding for Cnum:" . $this->CNUM . " Id:" . $mailAccount . " Projected End Date:" . $this->PROJECTED_END_DATE,AuditTable::RECORD_TYPE_AUDIT);
        $personTable = new personTable(allTables::$PERSON);
        $personTable->flagOffboarding($this->CNUM);
    }

    function displayBoardingForm($mode){
        $loader = new Loader();
        $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
        $activePredicate = personTable::activePersonPredicate();
        //$allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');

        /*
         * Functional Mgr can board to ANY Functional Mgr Ant Start 16th Jan 2018
         */

       // $isFM = personTable::isManager($_SESSION['ssoEmail']);
       // $fmPredicate = $isFM ? " UPPER(EMAIL_ADDRESS)='" . db2_escape_string(trim(strtoupper($_SESSION['ssoEmail']))) . "'  AND UPPER(LEFT(FM_MANAGER_FLAG,1))='Y'  " : " UPPER(LEFT(FM_MANAGER_FLAG,1))='Y' "; // FM Can only board people to themselves.
       // $fmPredicate = $mode==FormClass::$modeEDIT ? "( " . $fmPredicate . " ) OR ( CNUM='" . db2_escape_string($this->FM_CNUM) . "' ) " : $fmPredicate;
        $fmPredicate = " UPPER(LEFT(FM_MANAGER_FLAG,1))='Y' AND $activePredicate ";
        $allManagers =  $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON, $fmPredicate);
        $countryCodes = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES);

       //  $allManagers = empty($allManagers)? array('VBAC00001'=>'Dummy Fm') : $allManagers;
        $userDetails = $loader->loadIndexed('CNUM','EMAIL_ADDRESS',allTables::$PERSON, " EMAIL_ADDRESS='" . db2_escape_string($_SESSION['ssoEmail']) . "' ");
        $userCnum = isset($userDetails[$_SESSION['ssoEmail']]) ? $userDetails[$_SESSION['ssoEmail']] : false;
        //$allWorkStream = array('Work Stream 1'=>'ws001','Work Stream 2'=>'ws002','Work Stream 3'=>'ws003','Work Stream 4'=>'ws004');
        $allWorkstream = $workstreamTable->getallWorkstream();
        JavaScript::buildSelectArray($allWorkstream, 'workStream');

        $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;
        $displayForEdit = $notEditable ? 'hidden' : 'inline' ;
        $onlyEditable = $mode==FormClass::$modeEDIT ? 'text' : 'hidden'; // Some fields the user can edit - but not see/set the first time.
        $hideDivFromEdit = $mode==FormClass::$modeEDIT ? ' style="display: none;"  ' : null; //Some fields we don't show on the edit screen.

//         $availPreBoPredicate  = " CNUM LIKE '%xxx' AND PES_STATUS not like '%xxx' AND PES_STATUS not in (";
//         $availPreBoPredicate .= " '" . personRecord::PES_STATUS_REMOVED . "' "; // Pre-boarded who haven't been boarded
//         $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_DECLINED ."' ";
//         $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_FAILED ."' ";
//         $availPreBoPredicate .= " )";
//         $availableFromPreBoarding = $loader->loadIndexed("EMAIL_ADDRESS","CNUM", allTables::$PERSON, $availPreBoPredicate);
        $availableFromPreBoarding = personTable::optionsForPreBoarded($this->PRE_BOARDED);
        $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";
        $pesStatus = empty($this->PES_STATUS) ? personRecord::PES_STATUS_NOT_REQUESTED : $this->PES_STATUS;
        $pesStatusDetails = $this->PES_STATUS_DETAILS;

        $startDate = \DateTime::createFromFormat('Y-m-d', $this->START_DATE);
        $endDate = \DateTime::createFromFormat('Y-m-d', $this->PROJECTED_END_DATE);

        ?>
        <form id='boardingForm'  class="form-horizontal" onsubmit="return false;">
    	<div class="panel panel-default">
      		<div class="panel-heading">
        	<h3 class="panel-title" id='employeeResourceHeading'>Employee Details</h3>
      		</div>
	    	<div class="panel-body">
    			<div id='existingIbmer'>
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

	        	<div id='personDetails' display='<?=$displayForEdit?>'>
	          		<div class='form-group'>
	            		<div class='col-sm-6'>
	              		<input class='form-control' id='person_notesid' name='NOTES_ID'
	                		   value='<?=$this->NOTES_ID?>'  type='text'
	                		   disabled='disabled' placeholder="Notesid" <?=$notEditable?>>
	            		</div>
		            	<div class='col-sm-6'>
    	        		<input class='form-control' id='person_intranet'
        	           		   name='EMAIL_ADDRESS' value='<?=$this->EMAIL_ADDRESS?>'
                	   		   type='text' disabled='disabled' placeholder="Intranet" >
            			</div>
          			</div>

          			<div class='form-group'>
            			<div class='col-sm-12' <?=$hideDivFromEdit?>>
		              	<input class='form-control' id='person_bio' name='person_bio'  value='' required type='text' disabled='disabled' placeholder="Bio">
                		<input id='person_uid'           name='person_uid'        value='' type='hidden' required>
                		<input id='person_is_mgr'	     name='FM_MANAGER_FLAG'   value='<?=$this->FM_MANAGER_FLAG?>'   type='hidden'  >
                		<input id='person_employee_type' name='EMPLOYEE_TYPE'     value='<?=$this->EMPLOYEE_TYPE?>'		type='Hidden'  >
                		<input id='person_first_name'    name='FIRST_NAME'        value='<?=$this->FIRST_NAME?>'        type='hidden'   <?=$notEditable?>>
                		<input id='person_last_name'     name='LAST_NAME'         value='<?=$this->LAST_NAME?>'         type='hidden'   <?=$notEditable?>>
                		<input id='person_ibm_location'  name='IBM_BASE_LOCATION' value='<?=$this->IBM_BASE_LOCATION?>'	type='hidden'  >
                		<input id='person_country'       name='COUNTRY'           value='<?=$this->COUNTRY?>'           type='hidden'  >
                		<input id='person_pes_status'    name='PES_STATUS'        value='<?=$pesStatus?>'               type='hidden'   <?=$notEditable?>>
			            </div>
          			</div>
        		</div>
      		</div>


      		<div id='notAnIbmer' style='display:none'>
        		<div class="form-group">
          			<div class="col-sm-6">
		            <input class="form-control" id="resource_first_name" name="resFIRST_NAME"
              			   value="<?=$this->LAST_NAME?>"
                           type="text" placeholder='First Name'
                           <?=$notEditable?>>
          			</div>
          			<div class="col-sm-6">
            		<input class="form-control" id="resource_last_name" name="resLAST_NAME"
              			   value="<?=$this->LAST_NAME?>"
              			   type="text" placeholder='Last Name'
                           <?=$notEditable?>>
          			</div>
        		</div>

        		<div id='resourceDetails' style="display:<?=$displayForEdit?>">
          			<div class='form-group'>
            			<div class='col-sm-6'>
              			<input class='form-control' id='resource_email'
                			   name='resEMAIL_ADDRESS' value='<?=$this->EMAIL_ADDRESS?>'
                			   type='text' placeholder="Email Address"
                			   disabled	>
		            	</div>
        		    	<div class='col-sm-6'>
		                <select class='form-control select select2 ' id='resource_country'
                        		name='resCOUNTRY'
                              	placeholder='Country working in:' >

                   		<option value=''>Country working in</option>
                   		<?php
                            foreach ($countryCodes as $countryName){
                                echo "<option value='$countryName'>$countryName</option>";
                            };
                        ?>
              			</select>
        				</div>
        			</div>
				<input id='resource_uid'           name='resperson_uid'        value='<?=$this->CNUM?>'   				type='hidden' >
        		<input id='resource_is_mgr'	       name='resFM_MANAGER_FLAG'   value='N'               			    	type='hidden' >
        		<input id='resource_employee_type' name='resEMPLOYEE_TYPE'     value='setByRadioButtons'	    		type='hidden' >
        		<input id='resource_ibm_location'  name='resIBM_BASE_LOCATION' value='<?=$this->IBM_BASE_LOCATION?>'	type='hidden' >
        		<input id='resource_pes_status'    name='resPES_STATUS'        value='<?=$pesStatus?>'                  type='hidden' >
        		<input id='resource_pes_status_details'    name='resPES_STATUS_DETAILS'        value='<?=$pesStatusDetails?>'                 type='hidden' >
				</div>

				<div class='form-group'>
            		<div class='col-sm-12'>
						<label class="radio-inline employeeTypeRadioBtn"><input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_PREBOARDER ?>' data-type='ibmer'>IBMer Pre-Hire </label>
						<label class="radio-inline employeeTypeRadioBtn"><input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_VENDOR?>' data-type='cognizant' >Cognizant </label>
						<label class="radio-inline employeeTypeRadioBtn"><input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_VENDOR?>' data-type='densify'   >Densify </label>
						<label class="radio-inline employeeTypeRadioBtn"><input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_VENDOR?>' data-type='wipro'     >Wipro </label>
						<label class="radio-inline employeeTypeRadioBtn"><input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_VENDOR?>' data-type='other'     >Other (Vendor)</label>
		        	</div>
        		</div>
			</div>
        	<div class='form-group' id='linkToPreBoardedFormgroupDiv'>
	            <div class="col-sm-6" id='linkToPreBoarded'>
                <select class='form-control select select2' id='person_preboarded'
                        name='person_preboarded'
                        <?=$preBoardersAvailable?>
                        <?=$notEditable?>
                        placeholder='Was pre-boarded as:' >
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
                			  id='LBG_LOCATION'
                              name='LBG_LOCATION'
                >
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
	<div class="panel-body">
    	<div class="form-group">
        	<div class="col-sm-6">
          	<select class='form-control select select2' id='FM_CNUM'
                    name='FM_CNUM'
                    required='required'
                    placeholder='Select functional manager' >
            <option value=''>Select Functional Mgr</option>
            <?php
                foreach ($allManagers as $mgrCnum => $mgrNotesid){
                    echo"<option value='" . $mgrCnum . "' ";
                    echo (($userCnum==$mgrCnum) && empty($this->FM_CNUM)) ? " selected " : null;        // The person using the tool is a Manager - and this is their entry.
                    echo $mgrCnum==$this->FM_CNUM ? " selected " : null;                                // This is the entry for the person already declared to be the Func Mgr
                    echo ">" . $mgrNotesid . "</option>";
                };
            ?>
          	</select>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Role Details</h3>
  </div>
  <div class="panel-body">
    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="open_seat" name="OPEN_SEAT_NUMBER"  required maxlength='15' value="<?=$this->OPEN_SEAT_NUMBER?>" type="text" placeholder='Open Seat' data-toggle='tooltip' title='Open Seat' max=12 >
        </div>
        <div class='col-sm-6'>
          <input class="form-control" id="role_on_account" name="ROLE_ON_THE_ACCOUNT" maxlength='120' value="<?=$this->ROLE_ON_THE_ACCOUNT?>" type="text" placeholder='Role on account' >
       </div>
    </div>



    <div class='form-group' >

        <div class='col-sm-6 form-required'>
               <select class='form-control select select2' id='lob'
                              name='LOB'
                              required="true"
              >
                <option value=''>Select Lob</option>
                <?php
                foreach (self::$lobValue as $lob) {
                    ?><option value='<?=$lob?>'  <?=trim($this->LOB)==trim($lob)? ' selected ' : null ?>   ><?=$lob?></option><?php
                }
                ?>
            </select>
    	</div>

    	    <?php $allowEditCtid = empty($this->CT_ID) ? " style='display:none;' " : null; ?>

            <div id='editCtidDiv' class='col-sm-6' <?=$allowEditCtid;?>>
          		<input class="form-control" id="ct_id" name="CT_ID" type="number" min='999999' max='9999999'  value="<?=$this->CT_ID?>" placeholder='7-digit Contractor Id(CT Id) (If known)' >
       		</div>


     </div>

         <div class='form-group' id='selectCioAllignment'>
         <div class='col-sm-6'>
             <div class="radio">
          <label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='CTB' required  <?=substr($this->CTB_RTB,0,3)=='CTB'? 'checked' : null ?>>CTB</label>
          <label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='RTB' required <?=substr($this->CTB_RTB,0,3)=='RTB'? 'checked' : null ?>>RTB</label>
          <label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='Other' required <?=substr($this->CTB_RTB,0,5)=='Other'? 'checked' : null ?>>Other</label>
      </div>
        </div>
        <div class='col-sm-6'>
             <select class='form-control select select2' id='cioAlignment'
                              name='CIO_ALIGNMENT'
                              disabled
                              placeholder='Select CTB/RTB/Other'
              >
                <option value=''>Select CTB/RTB/Other</option>
                <?php
                foreach (self::$cio as $cioValue) {
                    ?><option value='<?=$cioValue?>'  <?=trim($this->CIO_ALIGNMENT)==trim($cioValue)? ' selected ' : null ?>   ><?=$cioValue?></option><?php
                }
                ?>
            </select>
            </div>
  </div>




    <div class='form-group' >
        <div class='col-sm-6'>
            <div class="radio">
        <label><input type="radio" name="TT_BAU"  class='accountOrganisation' value='T&T' required <?=substr($this->TT_BAU,0,3)=='T&T'? 'checked' : null ?>>T&amp;T</label>
        <label><input type="radio" name="TT_BAU"  class='accountOrganisation' value='BAU' required <?=substr($this->TT_BAU,0,3)=='BAU'? 'checked' : null ?>>BAU</label>
      </div>
        </div>

        <?php
        if(substr($this->TT_BAU,0,3)=='T&T'){
            ?>
            <script>
            $(document).on('ready', function(){
              $(document).click($('.accountOrganisation')[0]);
            });
            </script>
            <?php
        }
        ?>

        <div class='col-sm-6'>
          <input id='currentWorkstream' value='<?=$this->WORK_STREAM?>' type='hidden'>
          <select class='form-control select select2' id='work_stream'
                              name='WORK_STREAM'
                              disabled
                              placeholder='Select T&T/BAU First'
            >
                <option value=''>Select T&amp;T/BAU First</option>
            </select>

    </div>
    </div>

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="start_date" value="<?=is_object($startDate) ?  $startDate->format('d M Y') : null?>" type="text" placeholder='Start Date' data-toggle='tooltip' title='Start Date'>
          <input class="form-control" id="start_date_db2" name="START_DATE" value="<?=$this->START_DATE?>" type="hidden" >
           </div>

        <div class='col-sm-6'>
          <input class="form-control" id="end_date"  value="<?=is_object($endDate) ? $endDate->format('d M Y') : null?>"  type="text" placeholder='End Date (if known)' data-toggle='tooltip' title='End Date'>
		  <input class="form-control" id="end_date_db2" name="PROJECTED_END_DATE" value="<?=$this->PROJECTED_END_DATE?>" type="hidden" >
           </div>
     </div>
     </div>
     </div>

   <input id='pes_date_requested'   name='PES_DATE_REQUESTED'     value='<?=$this->PES_DATE_REQUESTED?>'		type='Hidden'  >
   <input id='pes_date_responded'   name='PES_DATE_RESPONDED'     value='<?=$this->PES_DATE_RESPONDED?>'      type='hidden'  >
   <input id='pes_requestor'        name='PES_REQUESTOR'          value='<?=$this->PES_REQUESTOR?>'           type='hidden'  >
   <input id='pes_status_details'   name='PES_STATUS_DETAILS'     value='<?=$this->PES_STATUS_DETAILS?>'      type='hidden'  >


    <?php
  $allButtons = null;
  $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBoarding',null,'Update','btn btn-primary') :  $this->formButton('submit','Submit','saveBoarding','disabled','Save','btn btn-primary');
  $pesButton    = $mode==FormClass::$modeEDIT ?  null :  $this->formButton('button','initiatePes','initiatePes','disabled','Initiate PES','btn btn-primary btnPesInitiate');
    $allButtons[] = $submitButton;
    $allButtons[] = $pesButton;
  $this->formBlueButtons($allButtons);
  $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
  ?>

  </form>
    <?php
    }

    function displayLinkForm(){
        $loader = new Loader();
        $availableFromPreBoarding = personTable::optionsForPreBoarded();
        $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";

        $availableForLinking = " PRE_BOARDED is null and CNUM not like '%XXX' ";
        $allNonLinkedIbmers = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON, $availableForLinking);

        ?>
        <form id='linkingForm'  class="form-horizontal" onsubmit="return false;">
    	<div class="panel panel-default">
      		<div class="panel-heading">
        	<h3 class="panel-title" id='employeeResourceHeading'>Employee Details</h3>
      		</div>
	    	<div class="panel-body">
				<div class='form-group' id='ibmerForLinking'>
	           		<div class="col-sm-6" id='ibmerSelect'>
                	<select class='form-control select select2' id='ibmer_preboarded'
                        name='ibmer_preboarded'
                        placeholder='Select IBMer:' >
                	<option value=''>IBMer to Link</option>
                	<?php
                    foreach ($allNonLinkedIbmers as $cnum => $notesId){
                        ?><option value='<?=$cnum?>'><?=$notesId . "(" . $cnum . ")" ?></option><?php
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
                        placeholder='Was pre-boarded as:' >
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



    function confirmTransferModal(){
        $myCnum = personTable::myCnum();
        $myEmail = trim($_SESSION['ssoEmail']);
        $myNotesid = personTable::getNotesidFromCnum($myCnum);
        ?>
       <!-- Modal -->
    <div id="confirmTransferModal" class="modal fade" role="dialog">
    	<div class="modal-dialog">
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


    function savingBoardingDetailsModal(){
        ?>
       <!-- Modal -->
    <div id="savingBoardingDetailsModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

            <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Saving Boarding Details</h4>
          </div>
          <div class="modal-body" >
            <div class="panel"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
        <?php
    }


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
                    <option value='<?=personRecord::PES_STATUS_CLEARED_PERSONAL;?>'><?=personRecord::PES_STATUS_CLEARED_PERSONAL?></option>
                    <option value='<?=personRecord::PES_STATUS_CLEARED;?>'><?=personRecord::PES_STATUS_CLEARED?></option>
                    <option value='<?=personRecord::PES_STATUS_DECLINED;?>'><?=personRecord::PES_STATUS_DECLINED?></option>
                    <option value='<?=personRecord::PES_STATUS_REQUESTED;?>'><?=personRecord::PES_STATUS_REQUESTED?></option>
                    <option value='<?=personRecord::PES_STATUS_EXCEPTION;?>'><?=personRecord::PES_STATUS_EXCEPTION?></option>
                    <option value='<?=personRecord::PES_STATUS_FAILED;?>'><?=personRecord::PES_STATUS_FAILED?></option>
                    <option value='<?=personRecord::PES_STATUS_INITIATED;?>'><?=personRecord::PES_STATUS_INITIATED?></option>
                    <option value='<?=personRecord::PES_STATUS_PROVISIONAL;?>'><?=personRecord::PES_STATUS_PROVISIONAL?></option>
                    <option value='<?=personRecord::PES_STATUS_REMOVED;?>'><?=personRecord::PES_STATUS_REMOVED?></option>
                    <option value='<?=personRecord::PES_STATUS_TBD;?>'><?=personRecord::PES_STATUS_TBD?></option>

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
          </form>
        </div>
      </div>
    </div>
    </div>
        <?php


    }

    function confirmOffboardingModal(){
        ?>
       <!-- Modal -->
    <div id="confirmOffboardingModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Offboarding</h4>
            </div>
             <div class="modal-body" >
               <div class="panel"></div>
             </div>
             <div class='modal-footer'>
              <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>

             </div>
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



    function portalReportSaveModal(){
        ?>
       <!-- Modal -->
    <div id="saveReportModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
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

    function editPersonModal(){
        ?>
       <!-- Modal -->
    <div id="editPersonModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Edit Person Record</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             </div>
            </div>
        </div>
      </div>
    <?php
    }


    function editPersonModalBody(){
        ?>
        <div class='container-fluid'>
        <?php
        $this->displayBoardingForm(FormClass::$modeEDIT);
        ?>
        </div>
        <?php
    }

    function confirmChangeFmFlagModal(){
        ?>
       <!-- Modal -->
    <div id="confirmChangeFmFlagModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
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

    function selectOffboarderModal(){
        ?>
       <!-- Modal -->
    <div id="selectOffboarderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Select Offboarder Modal</h4>
            </div>

             <form id='selectOffboarderForm' class="form-horizontal"  method='post' >
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <?php
             $allButtons = null;
             $submitButton = $this->formButton('submit','Submit','initiateOffboarding',null,'Initiate Offboarding','btn-primary');
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




    function convertCountryCodeToName(){
        if(strlen($this->COUNTRY)== 2){
            $loader = new Loader();
            $countryName = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES, " COUNTRY_CODE='" . db2_escape_string(trim($this->COUNTRY)) . "' ");
            $this->COUNTRY = isset($countryName[$this->COUNTRY]) ? $countryName[$this->COUNTRY] : $this->COUNTRY;
        }
    }




    function sendPesRequest(){
        $loader = new Loader();

        if(!empty($this->FM_CNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . db2_escape_string(trim($this->FM_CNUM)) . "' ");
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
                              $fmEmail);
        $message = preg_replace(self::$pesEmailPatterns, $replacements, self::$pesEmailBody);

        \itdq\BlueMail::send_mail(self::$pesTaskId, 'vBAC PES Request - ' . $this->CNUM ." (" . trim($this->FIRST_NAME) . " " . trim($this->LAST_NAME) . ")", $message, 'vbacNoReply@uk.ibm.com');

    }

    function sendPesStatusChangedEmail(){

        $fmIsIbmEmail = false;

        if(!empty($this->FM_CNUM)){
            $loader = new Loader();
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . db2_escape_string(trim($this->FM_CNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($this->FM_CNUM)]) ? $fmEmailArray[trim($this->FM_CNUM)] : null;
            $fmIsIbmEmail = strtolower(substr($fmEmail,-7))=='ibm.com';
        }

        $fmEmail = $fmIsIbmEmail ? $fmEmail : null;

        $emailAddress = trim($this->EMAIL_ADDRESS);
        $isIbmEmail = strtolower(substr($emailAddress,-7))=='ibm.com';

        if(!$isIbmEmail && !$fmIsIbmEmail){
            throw new \Exception('No IBM Email Address for individual or Functional Manager');
        }

        $to = array();
        $cc = array();

        switch ($this->PES_STATUS) {
            case self::PES_STATUS_CLEARED_PERSONAL:
                $pattern   = self::$pesClearedPersonalEmailPattern;
                $emailBody = self::$pesClearedPersonalEmail;
                $replacements = array($this->FIRST_NAME,$this->PES_DATE_RESPONDED,personRecord::$pesTaskId[0]);
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_CLEARED:
                $pattern   = self::$pesClearedEmailPattern;
                $emailBody = self::$pesClearedEmail;
                $replacements = array($this->FIRST_NAME,$this->PES_DATE_RESPONDED,personRecord::$pesTaskId[0]);
                $title = 'vBAC PES Status Change';
                !empty($emailAddress) ? $to[] = $emailAddress : null;
                !empty($fmEmail)      ? $to[] = $fmEmail : null;
                break;
            case self::PES_STATUS_CANCEL_REQ:
                $pattern   = self::$pesCancelPesEmailPattern;
                $emailBody = self::$pesCancelPesEmail;
                $title = 'vBAC Cancel Request';
                $replacements = array($this->FIRST_NAME,$this->LAST_NAME,$this->CNUM, $_SESSION['ssoEmail']);
                $to[] = personRecord::$pesTaskId[0];
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

        $response = \itdq\BlueMail::send_mail($to, $title ,$message, self::$pesTaskId[0], $cc);


        return $response;
    }



    function setPesRequested(){
        $table = new personTable(allTables::$PERSON);
        $success = $table->setPesRequested($this->CNUM, $_SESSION['ssoEmail']);
        return $success;
    }


    function sendOffboardingWarning(){
        $loader = new Loader();

        if(!empty($this->FM_CNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . db2_escape_string(trim($this->FM_CNUM)) . "' ");
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
            $fmEmail);
        $message = preg_replace(self::$warnPmoDateChangePattern, $replacements, self::$warnPmoDateChange);

        \itdq\BlueMail::send_mail(self::$pmoTaskId, 'vBAC Projected End Date Change - ' . $this->CNUM ." (" . trim($this->FIRST_NAME) . " " . trim($this->LAST_NAME) . ")", $message, 'vbacNoReply@uk.ibm.com');
    }


    function sendCbnEmail(){
//         $loader = new Loader();
        $personTable = new personTable(allTables::$PERSON);
//        $allFm = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON, " UPPER(FM_MANAGER_FLAG) like 'Y%' ");

        $allFm = $personTable->activeFmEmailAddressesByCnum();
        $emailableFmLists = array_chunk($allFm, 49);
        $replacements = array($_SERVER['HTTP_HOST']);
        $emailMessage = preg_replace(self::$cbnEmailPattern, $replacements, self::$cbnEmailBody);
        foreach ($emailableFmLists as $groupOfFmEmail){
            $to = self::$pmoTaskId;
            $cc = array();
            $bcc = $groupOfFmEmail;
            /*
             *
             * Next few lines - use for Testing. Comment out for Live.
             *
             */
//             $to  = array('rob.daniel@uk.ibm.com');
//             $cc  = array('jayhunter@uk.ibm.com');
//             $bcc = array('daniero@uk.ibm.com','antstark@uk.ibm.com');
            /*
             *
             * Lines above - use for testing. Comment out for Live.
             *
             */
            $bcc[] = self::$smCdiAuditEmail;  // Always copy the slack channel.
            set_time_limit(60);
            \itdq\BlueMail::send_mail($to, 'CBN Initiation Request' , $emailMessage, 'vbacNoReply@uk.ibm.com',$cc,$bcc);
        }
   }

//    static function employeeTypeMappingToDb2(){
//        $sqlDrop  = "DROP TABLE SESSION.EMPLOYEE_TYPE_MAPPING;";
//        $sqlCreate = "Create global temporary table SESSION.EMPLOYEE_TYPE_MAPPING  (code char(1) not null, description char(20) not null) ON COMMIT PRESERVE ROWS;";
//        $sqlInsert = array();

//        foreach (self::$employeeTypeMapping as $code => $description){
//            $sqlInsert[] = "INSERT into SESSION.EMPLOYEE_TYPE_MAPPING  ( code, description ) values ('$code','$description') ";
//        }

//         db2_exec($_SESSION['conn'], $sqlDrop);
//         $created = db2_exec($_SESSION['conn'], $sqlCreate);

//         if(!$created){
//             throw new \Exception('Unable to create EmployeeTypeMapping Temp Table');
//         }

//         foreach ($sqlInsert as $insertStatement){
//             $inserted = db2_exec($_SESSION['conn'], $insertStatement);
//             if(!$inserted){
//                 throw new \Exception('Unable to populate EmployeeTypeMapping Temp Table');
//             }
//         }
//    }


}

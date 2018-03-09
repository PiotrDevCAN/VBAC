<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;
use itdq\DbTable;
use vbac\allTables;
use itdq\AuditTable;
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
    protected $CONTRACTOR_ID_REQUIRED;
    protected $CONTRACTOR_ID;
    protected $CIO_ALIGNMENT;
    protected $PRE_BOARDED;


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
    const REVALIDATED_LEAVER = 'leaver';
    const REVALIDATED_PREBOARDER = 'preboarder';
    const REVALIDATED_OFFBOARDING = 'offboarding';
    const REVALIDATED_OFFBOARDED = 'offboarded';

    public static $employeeTypeMapping = array('A'=>'Regular','B'=>'Contractor','C'=>'Contractor','I'=>'Regular','L'=>'Regular','O'=>'Regular','P'=>'Regular','V'=>'Contractor','X'=>'Regular');

    public static $cio = array('Commercial','Cross CIO Leadership','Cyber', 'Digital','Divestment','GOFE','IT 4 IT','Insurance','Retail','Sandbox','TRP','tbc');

    private static $pesTaskId = array('lbgvetpr@uk.ibm.com','rob.daniel@uk.ibm.com'); // Only first entry will be used as the "contact" in the PES status emails.
    private static $pmoTaskId = array('rob.daniel@uk.ibm.com');
    //private static $pesTaskId = 'rob.daniel@uk.ibm.com';
    //private static $pesTaskId    = array('rob.daniel@uk.ibm.com', 'carrabooth@uk.ibm.com');
//     private static $pesEmailBody = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested By</th><td style="font-size:16px">&&requestor&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested Timestamp</th><td style="font-size:16px">&&requested&&</td></tr>
//                                     <tr><th style="background-color:WhiteSmoke;font-size:16px">Functional Mgr (on CC)</th><td style="font-size:16px">&&functionalMgr&&</td></tr>
//                                 </table>
//                             </td></tr>
//                             </table>';
    private static $pesEmailBody = 'Please initiate PES check for the following individual:\n
                                    Name : &&name&&
                                    Email Address : &&email&&
                                    Notes Id : &&notesid&&
                                    Country working in : &&country&&
                                    LoB : &&lob&&
                                    Role on Project : &&role&&

                                    Contract : &&contract&&
                                    Requested By : &&requestor&&
                                    Requested Timestamp : &&requested&&
                                    Functional Mgr (on CC) : &&functionalMgr&&';
    private static $pesEmailPatterns = array(
        '/&&name&&/',
        '/&&email&&/',
        '/&&notesid&&/',
        '/&&country&&/',
        '/&&lob&&/',
        '/&&role&&/',
        '/&&contract&&/',
        '/&&requestor&&/',
        '/&&requested&&/',
        '/&&functionalMgr&&/',
    );


//     private static $pesStatusChangeEmailBody = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                                              <br/>Many Thanks for your cooperation,';
    private static $pesClearedPersonalEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

    private static $pesClearedEmail = 'Hello &&candidate&&,
                                              <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
                                              <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.
                                              <br/>Many Thanks for your cooperation,';
    private static $pesClearedEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

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
    
    
    
    
    

    private static  $lobValue = array('GTS','GBS','IMI','Cloud','Security','Other');




    const PES_STATUS_NOT_REQUESTED = 'Not Requested';
    const PES_STATUS_CLEARED   = 'Cleared';
    const PES_STATUS_CLEARED_PERSONAL   = 'Cleared - Personal Reference';
    const PES_STATUS_DECLINED  = 'Declined';
    const PES_STATUS_EXCEPTION = 'Exception';
    const PES_STATUS_FAILED    = 'Failed';
    const PES_STATUS_INITIATED = 'Initiated';
    const PES_STATUS_REMOVED   = 'Removed';


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
        ?>console.log(knownCnum);<?php
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
        //$allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');

        /*
         * Functional Mgr can board to ANY Functional Mgr Ant Start 16th Jan 2018
         */

       // $isFM = personTable::isManager($_SESSION['ssoEmail']);
       // $fmPredicate = $isFM ? " UPPER(EMAIL_ADDRESS)='" . db2_escape_string(trim(strtoupper($_SESSION['ssoEmail']))) . "'  AND UPPER(LEFT(FM_MANAGER_FLAG,1))='Y'  " : " UPPER(LEFT(FM_MANAGER_FLAG,1))='Y' "; // FM Can only board people to themselves.
       // $fmPredicate = $mode==FormClass::$modeEDIT ? "( " . $fmPredicate . " ) OR ( CNUM='" . db2_escape_string($this->FM_CNUM) . "' ) " : $fmPredicate;
        $fmPredicate = " UPPER(LEFT(FM_MANAGER_FLAG,1))='Y' AND PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED . "','" . personRecord::PES_STATUS_CLEARED_PERSONAL . "''" . personRecord::PES_STATUS_EXCEPTION . "')  ";
        $allManagers =  $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON, $fmPredicate);
        $countryCodes = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES);

       //  $allManagers = empty($allManagers)? array('VBAC00001'=>'Dummy Fm') : $allManagers;
        $userDetails = $loader->loadIndexed('CNUM','EMAIL_ADDRESS',allTables::$PERSON, " EMAIL_ADDRESS='" . db2_escape_string($GLOBALS['ltcuser']['mail']) . "' ");
        $userCnum = isset($userDetails[$GLOBALS['ltcuser']['mail']]) ? $userDetails[$GLOBALS['ltcuser']['mail']] : false;
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
            <input class="form-control" id="person_name" name="person_name"
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
                type='text' disabled='disabled' placeholder="Intranet"
                >
            </div>
          </div>

          <div class='form-group'>
            <div class='col-sm-12' <?=$hideDivFromEdit?>>
              <input class='form-control' id='person_bio' name='person_bio'
                value='' required type='text' disabled='disabled' placeholder="Bio">
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
                >
            </div>
            <div class='col-sm-6'>
                <select class='form-control select select2 ' id='resource_country'
                              name='resCOUNTRY'
                              placeholder='Country working in:'
                      >
                    <option value=''>Country working in</option>
                    <?php
                        foreach ($countryCodes as $countryName){
                            echo "<option value='$countryName'>$countryName</option>";
                        };
                        ?>
                  </select>

            </div>
          </div>

          <div class='form-group' style='display:none'>
                <input id='resource_uid'           name='resperson_uid'        value='<?=$this->CNUM?>'   				type='hidden' >
                <input id='resource_is_mgr'	       name='resFM_MANAGER_FLAG'   value='N'               				type='hidden' >
                <input id='resource_employee_type' name='resEMPLOYEE_TYPE'     value='Pre-Hire'						type='hidden' >
                <input id='resource_ibm_location'  name='resIBM_BASE_LOCATION' value='<?=$this->IBM_BASE_LOCATION?>'	type='hidden' >
                <input id='resource_pes_status'    name='resPES_STATUS'        value='<?=$pesStatus?>'                 type='hidden'  <?$notEditable?>>
                <input id='resource_pes_status_details'    name='resPES_STATUS_DETAILS'        value='<?=$pesStatusDetails?>'                 type='hidden'  <?$notEditable?>>


          </div>
        </div>
      </div>


        <div class='form-group'>

              <div class="col-sm-6" id='linkToPreBoarded'>
                <select class='form-control select select2' id='person_preboarded'
                              name='person_preboarded'
                              <?=$preBoardersAvailable?>
                              <?=$notEditable?>
                              placeholder='Was pre-boarded as:'
                      >
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
                              placeholder='Select functional manager'
                >
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
          <input class="form-control" id="open_seat" name="OPEN_SEAT_NUMBER"  required maxlength='12' value="<?=$this->OPEN_SEAT_NUMBER?>" type="text" placeholder='Open Seat' data-toggle='tooltip' title='Open Seat' max=12 >
        </div>
        <div class='col-sm-6'>
          <input class="form-control" id="role_on_account" name="ROLE_ON_THE_ACCOUNT" maxlength='120' value="<?=$this->ROLE_ON_THE_ACCOUNT?>" type="text" placeholder='Role on account' >
       </div>
    </div>



    <div class='form-group' >

        <div class='col-sm-6'>
               <select class='form-control select select2' id='lob'
                              name='LOB'
                              placeholder='Select Lob'
              >
                <option value=''>Select Lob</option>
                <?php
                foreach (self::$lobValue as $lob) {
                    ?><option value='<?=$lob?>'  <?=trim($this->LOB)==trim($lob)? ' selected ' : null ?>   ><?=$lob?></option><?php
                }
                ?>
            </select>
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
        <label><input type="radio" name="TT_BAU"  class='accountOrganisation' value='T&T' required <?=substr($this->TT_BAU,0,3)=='T&T'? 'checked' : null ?>>T&T</label>
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
                <option value=''>Select T&T/BAU First</option>
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
   <input id='pes_status'           name='PES_STATUS'             value='<?=$this->PES_STATUS?>'              type='hidden'  >
   <input id='pes_status_details'   name='PES_STATUS_DETAILS'     value='<?=$this->PES_STATUS_DETAILS?>'      type='hidden'  >


    <?php
  $allButtons = null;
  $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBoarding',null,'Update','btn btn-primary') :  $this->formButton('submit','Submit','saveBoarding','disabled','Save','btn btn-primary');
  $pesButton    = $mode==FormClass::$modeEDIT ?  null :  $this->formButton('button','initiatePes','initiatePes','disabled','Initiate PES','btn btn-primary btnPesInitiate');
    $allButtons[] = $submitButton;
    $allButtons[] = $pesButton;
  $this->formBlueButtons($allButtons);
  $this->formHiddenInput('requestor',$GLOBALS['ltcuser']['mail'],'requestor');
  ?>

  </form>
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
                    <option value='<?=personRecord::PES_STATUS_EXCEPTION;?>'><?=personRecord::PES_STATUS_EXCEPTION?></option>
                    <option value='<?=personRecord::PES_STATUS_FAILED;?>'><?=personRecord::PES_STATUS_FAILED?></option>
                    <option value='<?=personRecord::PES_STATUS_INITIATED;?>'><?=personRecord::PES_STATUS_INITIATED?></option>
                    <option value='<?=personRecord::PES_STATUS_REMOVED;?>'><?=personRecord::PES_STATUS_REMOVED?></option>
                       </select>
                 </div>
                 <label for='ped_dsate' class='col-md-1 control-label '>Date</label>
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
        !empty($emailAddress) ? $to[] = $emailAddress : null;
        !empty($fmEmail)      ? $to[] = $fmEmail : null;

        $replacements = array($this->FIRST_NAME,$this->PES_DATE_RESPONDED,personRecord::$pesTaskId[0]);

        switch ($this->PES_STATUS) {
            case self::PES_STATUS_CLEARED_PERSONAL:
                $pattern   = self::$pesClearedPersonalEmailPattern;
                $emailBody = self::$pesClearedPersonalEmail;
            break;
            case self::PES_STATUS_CLEARED:
                $pattern   = self::$pesClearedEmailPattern;
                $emailBody = self::$pesClearedEmail;
                break;
            default:

            break;
        }
        $message = preg_replace($pattern, $replacements, $emailBody);
        $response = \itdq\BlueMail::send_mail($to, 'vBAC PES Status Change',$message, self::$pesTaskId[0]);
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
}

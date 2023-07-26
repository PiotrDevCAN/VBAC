<?php
namespace vbac;

use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;
use vbac\allTables;
use vbac\personTable;
use vbac\personRecord;
use DateTime;

/**
 *
 * @author gb001399
 *
 */
class personWithSubPRecord extends personRecord
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
    protected $SUBPLATFORM;
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

    protected $person_bio;

    const EMP_RESOURCE_REG = 'Resource Details - Kyndryl employees use Ocean IDs';
    const EMP_RESOURCE_EXT = 'Resource Details - Use external email addresses';

    function displayBoardingForm($mode){
        $loader = new Loader();
        $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
        $activePredicate = personTable::activePersonPredicate();
        //$allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');

        /*
         * Functional Mgr can board to ANY Functional Mgr Ant Stark 16th Jan 2018
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
        $displayForEdit = $notEditable ? 'none' : 'inline' ;
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

	        	<div id='personDetails' style='display:<?=$displayForEdit?>'>
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
                			   <?=$notEditable?>>
		            	</div>
        		    	<div class='col-sm-6'>
		                <select class='form-control select select2 ' id='resource_country'
                        		name='resCOUNTRY'
                              	data-placeholder='Country working in:' >

                   		<option value=''>Country working in</option>
                   		<?php
                            foreach ($countryCodes as $countryName){
                                echo "<option value='$countryName'>$countryName</option>";
                            };
                        ?>
              			</select>
        				</div>
        			</div>
				    <input id='resource_uid'                  name='resperson_uid'          value='<?=$this->CNUM?>'   				    type='hidden' >
        		<input id='resource_is_mgr'	              name='resFM_MANAGER_FLAG'     value='No'              			    	  type='hidden' >
        		<input id='resource_employee_type'        name='resEMPLOYEE_TYPE'       value='setByRadioButtons'	    		    type='hidden' >
        		<input id='resource_ibm_location'         name='resIBM_BASE_LOCATION'   value='<?=$this->IBM_BASE_LOCATION?>'	type='hidden' >
        		<input id='resource_pes_status'           name='resPES_STATUS'          value='<?=$pesStatus?>'               type='hidden' >
        		<input id='resource_pes_status_details'   name='resPES_STATUS_DETAILS'  value='<?=$pesStatusDetails?>'        type='hidden' >
				</div>

				<div class='form-group'>
          <div class='col-sm-12'>
						<label class="radio-inline employeeTypeRadioBtn" data-toggle='tooltip' data-placement='auto top' title='IBM Regular and IBM Contractors'>
              <input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_PREBOARDER ?>' data-type='ibmer' checked>
              Kyndryl Pre-Hire (Regular or Contractor)
						</label>
						<label class="radio-inline employeeTypeRadioBtn" data-toggle='tooltip' data-placement='auto top' title='3rd Party Vendors'>
              <input  type="radio" name="employeeType"  value='<?=personRecord::REVALIDATED_VENDOR?>' data-type='other'>
              Other (ie.3rd Party Vendor)
						</label>
          </div>
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
				<?php $allowEditLocation = " style='display:block' "; ?>
				<div id='editLocationDiv' class='col-sm-6' <?=$allowEditLocation;?>>
           	   	<select class='form-control select select2 locationFor '
                			  id='LBG_LOCATION'
                              name='LBG_LOCATION',
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

        <div class='col-sm-6'>
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
         <div class='col-sm-4'>
             <div class="radio">
          		<label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='CTB' required  <?=substr($this->CTB_RTB,0,3)=='CTB'? 'checked' : null ?>    <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>CTB</label>
          		<label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='RTB' required <?=substr($this->CTB_RTB,0,3)=='RTB'? 'checked' : null ?>     <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>RTB</label>
          		<label><input type="radio" name="CTB_RTB"  class='ctbRtb' value='Other' required <?=substr($this->CTB_RTB,0,5)=='Other'? 'checked' : null ?> <?=$_SESSION['isPmo'] || $_SESSION['isCdi'] ? null :  $notEditable;?>>Other</label>
         	</div>
         </div>
         <div class='col-sm-4'>
             <select class='form-control select select2' id='cioAlignment'
                              name='CIO_ALIGNMENT'
                              disabled
                              data-placeholder='Select CTB/RTB/Other'
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
        <div class='col-sm-4'>
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

        <div class='col-sm-4'>
          <input id='currentWorkstream' value='<?=$this->WORK_STREAM?>' type='hidden'>
          <select class='form-control select select2' id='work_stream'
                              name='WORK_STREAM'
                              disabled
                              data-placeholder='Select T&T/BAU First'
            >
                <option value=''>Select T&amp;T/BAU First</option>
          </select>

        </div>
        <div class='col-sm-4'>
             <select class='form-control select select2' id='subPlatform'
                              name='subPlatform[]'
                              disabled
                              data-placeholder='Select SubPlatform(s)'
                              multiple="multiple">
                <option value=''></option>
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

    function displayLinkForm($mode){
        $loader = new Loader();
        $availableFromPreBoarding = personTable::optionsForPreBoarded();
        $preBoardersAvailable = count($availableFromPreBoarding) > 1 ? null : " disabled='disabled' ";
        $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;

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
                        data-placeholder='Select IBMer:' >
                	<option value=''>Reg to Link</option>
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
}

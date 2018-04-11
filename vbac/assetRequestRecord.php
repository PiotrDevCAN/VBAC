<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;


class assetRequestRecord extends DbRecord {

    protected $REQUEST_REFERENCE;
    protected $CNUM;
    protected $ASSET_TITLE;
    protected $USER_LOCATION;
    protected $PRIMARY_UID;
    protected $SECONDARY_UID;
    protected $DATE_ISSUED_TO_IBM;
    protected $DATE_ISSUED_TO_USER;
    protected $DATE_RETURNED;
    protected $BUSINESS_JUSTIFICATION;
    protected $PRE_REQ_REQUEST;
    protected $REQUESTOR_EMAIL;
    protected $REQUESTED;
    protected $APPROVER_EMAIL;
    protected $APPROVED;
    protected $EDUCATION_CONFIRMED;
    protected $STATUS;
    protected $ORDERIT_VARB_REF;
    protected $ORDERIT_NUMBER;
    protected $ORDERIT_STATUS;
    protected $USER_CREATED;
    protected $COMMENT;
    protected $REQUEST_RETURN;

    public static $STATUS_CREATED           = 'Created in vBAC';
    public static $STATUS_APPROVED          = 'Approved for Order IT';
    public static $STATUS_EXPORTED          = 'Exported for Order IT';
    public static $STATUS_RAISED_ORDERIT    = 'Raised in Order IT';
    public static $STATUS_PROVISIONED       = 'Provisioned by Order IT';
    public static $STATUS_RETURNED          = 'Returned to LBG';
    public static $STATUS_REJECTED          = 'Rejected in vBAC';
    
    public static $STATUS_ORDERIT_YET       = 'Yet to be raised';
    public static $STATUS_ORDERIT_RAISED    = 'Raised in Order IT';
    public static $STATUS_ORDERIT_APPROVED  = 'Approved in Order IT';
    public static $STATUS_ORDERIT_CANCELLED = 'Cancelled in Order IT';
    public static $STATUS_ORDERIT_REJECTED  = 'Rejected in Order IT';
    
    public static $CREATED_USER             = 'Yes';
    public static $CREATED_PMO              = 'No';
     
    
    function displayForm(){
        $loader = new Loader();
        $myCnum = personTable::myCnum();
        $myManagersCnum = personTable::myManagersCnum();
        $isFm   = personTable::isManager($GLOBALS['ltcuser']['mail']);
        $isPmo  = $_SESSION['isPmo'];

        switch (true){
            case $isPmo:
                $predicate = " 1=1 ";
                break;
            case $isFm:
                $predicate = " FM_CNUM='" . db2_escape_string($myCnum) . "' or CNUM='" . db2_escape_string($myCnum) . "' ";
            break;
            default:
                $predicate = " CNUM='" . db2_escape_string($myCnum) . "' ";
            break;
        }

        $predicate .= " and (( REVALIDATION_STATUS = '" . personRecord::REVALIDATED_FOUND . "' and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_EXCEPTION. "') ) ";
        $predicate .= " or  ( REVALIDATION_STATUS = '" . personRecord::REVALIDATED_PREBOARDER . "' and ( PES_STATUS_DETAILS not like 'Boarded%' or PES_STATUS_DETAILS is null) and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_EXCEPTION. "') ) )";
        $selectableNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$predicate);
        $selectableEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON,$predicate);

        $approvingMgrPredicate = " FM_MANAGER_FLAG ='Yes' ";
        $approvingMgrs = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$approvingMgrPredicate)

        ?>
        <form id='assetRequestForm'  class="form-horizontal"
        	onsubmit="return false;">
        <div id='hideTillEducationConfirmed' style='display: inline'>

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Asset Request</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>      	
        		<div class='col-sm-4'>
                <select class='form-control select select2 '
                			  id='requestees'
                              name='requestee'
                              required

                      >
                    <option value=''></option>
                    <?php
                    foreach ($selectableNotesId as $cnum => $notesId){
                            $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
                            //$selected = !$isFm && trim($cnum)==trim($myCnum) ? ' selected ' : null    // If they don't select the user - we don't fire the CT ID & Education prompts.
                            $selected = null;
                            ?><option value='<?=trim($cnum);?>'<?=$selected?>><?=$displayedName?></option><?php
                        };
                        ?>
            	</select>
            	</div>
            	<div class='col-sm-3'>
        		<input name='REQUEST_RETURN' class='toggle' type='checkbox' data-toggle='toggle' data-on='Return/Remove existing' data-off='Request New' data-onstyle='danger' data-offstyle='success' data-width='250' >
        		</div> 
            	
            	
            	
        	</div>
		<div id='requestDetailsDiv'>
        	<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Request Details</h3>
				</div>
				<div class='panel-body'>
				<div class='form-group '>
				  	<div class='col-sm-8' id='allCtidHereDiv'>
            	  	<input class="form-control input-sm" id='ctidConfirmation' name='ctidConfirmation' value=''  type='hidden' disabled required >
            	   	<select class='form-control select select2 locationFor '
                			  id='person-1-location'
                              name='person-1-location'
                              disabled=true
                              required
                     >
                     <?php
                     $options = $this->buildLocationOptions();
                     echo $options;
                     ?>
                    </select>
            		</div>
            		<div class='form-group required'>
        			<div class='col-sm-4'>
        			<label for='educationConfirmed'>Security Education</label>
        			<input type='checkbox' id='person-1-educationConfirmed' name='EDUCATION_CONFIRMED' value='Yes' disabled class='educationConfirmedCheckbox' >
        			</div>
        			</div>
            	</div>
            	<div id='requestableAssetDetailsDiv' style='display:none'>
				<?php
				$this->addRequestableAssetDetails();
				?>
				</div>


			</div>
		</div>
       	</div>
        	<div class='form-group required'>
        		<div class='col-sm-4'>
        		<label for='approvingManager'>Approving Manager</label>
                <select class='form-control select select2 '
                			  id='approvingManager'
                              name='approvingManager'
                              required
                      >
                    <option value=''>                                     
                    </option>
                    <?php
                    foreach ($approvingMgrs as $cnum => $notesId){
                            $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
                            $selected = null;
                            
                            if(!$isFm && (trim($cnum)== trim($myManagersCnum))){
                                /*
                                 * The user is NOT a manager, and this entry is their Mgr
                                 *
                                 * Stops users who are managers having the drop down default to THEIR mgr, when it should default to them.
                                 * JS code will remove the entry in this list, if they pick themselves as the Requestee.
                                 *
                                 */
                                $selected = " selected ";
                            } elseif ($isFm && (trim($cnum)==trim($myCnum))){
                                /*
                                 * They ARE an FM and this is their entry, so select it by default.
                                 * If the requestee becomes themselves, we'll remove the entry from the dropdown.
                                 */
                                $selected = " selected ";
                            }
                            ?><option value='<?=trim($cnum);?>'<?=$selected?>><?=$displayedName?></option><?php
                        };
                        ?>
            	</select>
            	</div>
            	<div class='col-sm-4' >
            	<label for='orderItNumber'>Order IT Number (If already created)</label>
            	<input type="number" name='ORDERIT_NUMBER' id=orderItNumber' placeholder="Order IT Number" min="999999" max="9999999" class='form-control' >
            	
            	</div>
            	<div class='col-sm-4' >
            	
            	</div>
            	
            	
        		</div>


        <div class='panel-footer'>
        	<?php
            $allButtons = null;
            $submitButton =   $this->formButton('submit','Submit','saveAssetRequest','disabled','Save','btn btn-primary');
            $allButtons[] = $submitButton;
            $this->formBlueButtons($allButtons);
            $this->formHiddenInput('requestor',$GLOBALS['ltcuser']['mail'],'requestor');
            ?>
        </div>
        </div> <!--  Panel     -->
        </div> <!--  Container -->
        <?php 
		foreach ($selectableNotesId as $cnum => $notesId){
		    $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
		    ?><input type='hidden' name='<?=trim($cnum);?>' value='<?=$displayedName?>'/><?php
        };
        ?>
        </div>
        </form>
		<?php 
    }

    function createJsCtidLookup(){
        $loader = new Loader();
        $ctId = $loader->loadIndexed('CT_ID','CNUM',allTables::$PERSON);
        JavaScript::buildObjectFromLoadIndexedPair($ctId,'cnum2ctid');
    }
    
    function saveFeedbackModal(){
        ?>
        <!-- Modal -->
		<div id="saveFeedbackModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Asset Request Creation</h4>
      			</div>
      			<div class="modal-body" >
      			</div>
      			<div class="modal-footer">
      				<p class="text-center">When you close this modal, please allow time for the page to refresh before attempting to create another request</p>      				
      				<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      				
      			</div>
    		</div>
  			</div>
		</div>
        <?php
    }
    

    function confirmEducationModal(){
        ?>
        <!-- Modal -->
		<div id="confirmEducationModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Security Education Confirmation</h4>
      			</div>
      			<div class="modal-body" >
      				<p><center>Please confirm <b><span id='educationNotesid'></span></b> has successfully completed the mandatory Aurora Security Education Modules for IBMers before continuing</center></p>
      				<p><center>Contact Aurora Central PMO/UK/IBM for details of these self paced online courses.</center></p>
					<p><center>Please note a false declaration of completion constitutes a breach of IBM Business Conduct Guidelines and may lead to disciplinary action.</center></p>
      			</div>
      			<div class="modal-footer">
      		  		<button type="button" class="btn btn-success" id='confirmedEducation'>Education Completed</button>
      		  		<button type="button" class="btn btn-danger" id='noEducation'>Education NOT Completed</button>
      			</div>
      			<input id='cnumForSecurityModal' val='' type='hidden' />
    		</div>
  			</div>
		</div>
        <?php
    }

    function unknownUser(){
        ?>
        <div class="panel panel-danger">
        <div class="panel-heading">
        <h3 class="panel-title" id='requestableAssetListTitle'>Asset Request</h3>
        </div>
        <div class="panel-body">
        <p>User : <?=$GLOBALS['ltcuser']['mail']?> is not known to this tool. Please contact support</p>
        </div>
        <div class='panel-footer'>
        </div>
        </div>
        <?php
    }

    function doTheEducationModal(){
        ?>
        <div id='doTheEducation' style='display: none;'>
        <div class="panel panel-danger">
        <div class="panel-heading">
        <h3 class="panel-title" id='requestableAssetListTitle'>Education</h3>
        </div>
        <div class="panel-body">
        <p>Please complete the required <b>Aurora Security Education</b>, contact the <b>Aurora Central PMO/UK/IBM</b> for details.</p>
        </div>
        <div class='panel-footer'>
        </div>
        </div>
        </div>
        <?php
    }

    function ctIdRequiredModal(){
        ?>
        <!-- Modal -->
		<div id="obtainCtid" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Contractor ID (CT ID)</h4>
      			</div>
      			<div class="modal-body" >
        		<p>Before requests can be made on Order IT, the individual needs to have a Contractor ID (CT ID).</p>
        		<p>We do not have a record of the CT ID for:</p>
        		<input id='requesteeName' value='' disabled></p>
        		<label for='requesteeCtid'><b>Either</b> enter it here</label>
        		<input id='requesteeCtid' value=''></p>
        		<p><b>Or</b> simply close this Modal to have the form request a CT ID.</p>
        		<p class='bg-warning'>Please do not simply close this Modal if the individual already has a CT ID</p>
        		</div>
        		<div class='modal-footer'>
      		  		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      			</div>
        </div>
        </div>
        </div>
        <?php
    }

    function missingPrereqModal(){
        ?>
        <!-- Modal -->
		<div id="missingPrereqModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Missing Pre-Req</h4>
      			</div>
      			<div class="modal-body" >
      				<p><center>You have requested Asset : <b><span id='requestedAssetTitle'></span></b></center></p>
      				<p><center>However, this asset has a pre-req of : <b><span id='prereqAssetTitle'></span></b> which you have <b>NOT</b> selected.</p>
      				<p><center>If <b><span id='requesteeNotesid'></span></b> already has that asset, then simply close this window, and continue</center>

      			</div>
      			<div class="modal-footer">
      		  		<button type="button" class="btn btn-success" id='addPreReq'>Add Asset to request</button>
      		  		<button type="button" class="btn btn-warning" id='ignorePreReq'>Asset already acquired</button>
      			</div>
    		</div>
  			</div>
		</div>
        <?php
    }
    
    
    function exportResponseModal(){
        ?>
        <!-- Modal -->
		<div id="exportResponse" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Export Responde</h4>
      			</div>
      			<div class="modal-body" >
        		<div class='modal-footer'>
      		  		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      			</div>
        </div>
        </div>
        </div>
        <?php
    }



    function buildLocationOptions(){
        $loader = new Loader();
        $locationsByCity = $loader->loadIndexed('CITY','ADDRESS',allTables::$STATIC_LOCATIONS);
        $children = array();


        foreach ($locationsByCity as $location => $city){
            $children[trim($city)] = empty($children[trim($city)]) ? "" : $children[trim($city)];
            $children[trim($city)] .= "<option id='$location'>$location,$city</option>";
        }

        $options = "<option id=''></option>";
        foreach ($children as $city => $cityOptions){
            $options .= "<optgroup label='$city'>";
            $options .= $cityOptions;
            $options .= "</optgroup>";

        }
        return $options;
    }

    function addRequestableAssetDetails(){
        $requestableAssetListTable = new requestableAssetListTable(allTables::$REQUESTABLE_ASSET_LIST);
        $requestableAssetDetails = $requestableAssetListTable->returnAsArray(true,false);
        $assetId=0;
        $personId=1;
        foreach ($requestableAssetDetails as $requestableAsset){

            if($assetId++ % 4==0){
                ?><div class='form-group bg-info row' ><?php
            }

            $assetHtmlName = urlencode(trim($requestableAsset['ASSET_TITLE']));
            ?>
            <div class='col-sm-3 selectableThing'>
            <input class='form-check-input requestableAsset' type='checkbox'
                id='person-<?=$personId?>-asset-<?=$assetId?>'
                name='person-<?=$personId?>-asset-<?=$assetId?>-<?=$assetHtmlName?>'
                data-asset='<?=trim($requestableAsset['ASSET_TITLE'])?>'
            	data-prereq='<?=trim($requestableAsset['ASSET_PREREQUISITE'])?>'
            	data-onshore='<?=trim($requestableAsset['APPLICABLE_ONSHORE'])?>'
            	data-offshore='<?=trim($requestableAsset['APPLICABLE_OFFSHORE'])?>'
            	data-default='<?=trim($requestableAsset['APPLICABLE_ONSHORE'])?>'
            	data-ignore='<?=empty(trim($requestableAsset['ASSET_PREREQUISITE'])) ? 'Yes': 'No'?>'
            >
            <label class='form-check-label' for='person-<?=$personId?>-asset-<?=$assetId?>'><?=trim($requestableAsset['ASSET_TITLE'])?></label>
        	<?php
        	if($requestableAsset['BUSINESS_JUSTIFICATION_REQUIRED']=='Yes'){
        	   ?><div class='justificationDiv'  id='person-<?=$personId?>-justification-div-<?=$assetId?>' style='display:none'><textarea class='form-control justification' rows='2' style='min-width: 100%' id='person-<?=$personId?>-justification-<?=$assetId?>' name='person-<?=$personId?>-justification-<?=$assetId?>' placeholder='<?=trim(urldecode($requestableAsset['PROMPT']));?>' min=1  max='255' ' ></textarea><span disabled>255 chars max</span></div><?php
        	}
        	?>
        	</div>
        	<?php
        	if($assetId % 4==0){
                ?></div><?php
            }
        }
    }

}
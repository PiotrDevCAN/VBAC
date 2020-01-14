<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;


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
    protected $ORDERIT_RESPONDED;

    const STATUS_CREATED           = 'Created in vBAC';
    const STATUS_AWAITING_IAM      = 'Awaiting IAM Approval';
    const STATUS_APPROVED          = 'Approved for LBG';
    const STATUS_EXPORTED          = 'Exported for LBG';
    const STATUS_RAISED_ORDERIT    = 'Raised with LBG';
    const STATUS_PROVISIONED       = 'Provisioned by LBG';
    const STATUS_RETURNED          = 'Returned to LBG';
    const STATUS_REJECTED          = 'Rejected in vBAC';

    const STATUS_ORDERIT_YET       = 'Yet to be raised';
    const STATUS_ORDERIT_NOT       = 'Not to be raised';
    const STATUS_ORDERIT_RAISED    = 'Raised with LBG';
    const STATUS_ORDERIT_APPROVED  = 'Approved in LBG';
    const STATUS_ORDERIT_CANCELLED = 'Cancelled in LBG';
    const STATUS_ORDERIT_REJECTED  = 'Rejected in LBG';

    const CREATED_USER             = 'Yes';
    const CREATED_PMO              = 'No';


    static function ableToOwnAssets(){
//         $predicate  = " and ((( REVALIDATION_STATUS = '" . personRecord::REVALIDATED_FOUND . "' or (REVALIDATION_STATUS is null or (REVALIDATION_STATUS is null ) or (REVALIDATION_STATUS= '" . personRecord::REVALIDATED_POTENTIAL . "') ) and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_EXCEPTION. "') ) ";
//         $predicate .= " or  ( REVALIDATION_STATUS IN ('" . personRecord::REVALIDATED_VENDOR . "') and ( PES_STATUS_DETAILS not like 'Boarded%' or PES_STATUS_DETAILS is null) and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_EXCEPTION. "') )";
//         $predicate .= " or (REVALIDATION_STATUS like 'offboarding%' ) ";
//         $predicate .= " ) ";
        $predicate = " and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_EXCEPTION. "','" . personRecord::PES_STATUS_RECHECK_REQ. "') ";  // They must be PES Cleared.
        $predicate.= " and ((REVALIDATION_STATUS = '" . personRecord::REVALIDATED_FOUND . "' or REVALIDATION_STATUS is null or REVALIDATION_STATUS= '" . personRecord::REVALIDATED_POTENTIAL . "') "; // They are ACTIVE IBMer
        $predicate.= "        or ( REVALIDATION_STATUS IN ('" . personRecord::REVALIDATED_VENDOR . "') and ( PES_STATUS_DETAILS not like 'Boarded%' or PES_STATUS_DETAILS is null) )  "; // They are a vendor - who has not subsequently been boarded as an IBMer
	    $predicate.= "        or REVALIDATION_STATUS like 'offboarding%' ) ";  // OR they are in the process of offboarding so need to be able to request things be returned.
        return $predicate;
    }


    function displayForm(){
        $loader = new Loader();
        $myCnum = personTable::myCnum();
        $myManagersCnum = personTable::myManagersCnum();
        $isFm   = personTable::isManager($_SESSION['ssoEmail']);
        $isPmo  = $_SESSION['isPmo'];
        $isRequestor = employee_in_group('vbac_requestor', $_SESSION['ssoEmail']);

        $iAmDelegateForArray = $loader->load('CNUM',allTables::$DELEGATE," AND DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ");
        $iAmDelegateForTrimmed = array_map('trim', $iAmDelegateForArray);
        $iAmDelegateForString = !empty($iAmDelegateForTrimmed) ? implode("','", $iAmDelegateForTrimmed) : null;
        $iAmDelegateForPredicate = !empty($iAmDelegateForTrimmed) ? " OR FM_CNUM in ('" . $iAmDelegateForString . "' )" : null;

        switch (true){
            case $isRequestor:
            case $isPmo:
                $predicate = " 1=1 ";
                break;
            case $isFm:
                $predicate = " ( FM_CNUM='" . db2_escape_string($myCnum) . "' or CNUM='" . db2_escape_string($myCnum) . "' $iAmDelegateForPredicate ) ";
            break;
            default:
                $predicate = " ( CNUM='" . db2_escape_string($myCnum) . "' $iAmDelegateForPredicate  ) ";
            break;
        }

        $predicate .= self::ableToOwnAssets();

        $selectableNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$predicate);
        $selectableEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON,$predicate);
        $selectableRevalidationStatus = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);

        $approvingMgrPredicate = " upper(FM_MANAGER_FLAG) like 'Y%' ";
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
                              data-toggle="tooltip" title="Only PES Cleared IBMers & Vendors will appear in this list. If you feel someone is missing, please ensure they have a FULL Boarded record in the system."


                      >
                    <option value=''></option>
                    <?php
                    foreach ($selectableNotesId as $cnum => $notesId){
                            $isOffboarding = substr($selectableRevalidationStatus[$cnum],0,11)=='offboarding';

                            $dataOffboarding = " data-revalidationstatus" . "='" . $selectableRevalidationStatus[$cnum] . "' ";
                           // $dataOffboarding.= $isOffboarding ? "='true' " : "='false'";
                            $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
                            //$selected = !$isFm && trim($cnum)==trim($myCnum) ? ' selected ' : null    // If they don't select the user - we don't fire the CT ID & Education prompts.
                            $selected = null;
                            ?><option value='<?=trim($cnum);?>'<?=$selected?><?=$dataOffboarding?>><?=$displayedName?></option><?php
                        };
                        ?>
            	</select>
            	</div>
            	<div class='col-sm-4'>
<!--         		<input name='REQUEST_RETURN' class='toggle' type='checkbox' data-toggle='toggle' data-on='Return/Remove existing' data-off='Request New' data-onstyle='danger' data-offstyle='success' data-width='250' id='returnRequest' > -->
<!-- 				<p class='bg-warning'><small>The ability to initiate the return of an asset has been suspended. Please contact PMO direct to arrange</small></p> -->
        		</div>



        	</div>
		<div id='requestDetailsDiv'>
        	<div class="panel panel-primary">
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
                     $options = self::buildLocationOptions();
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
            	<label for='orderItNumber'>LBG Ref Number (If pre-raised)</label>
            	<input name='ORDERIT_NUMBER' id='orderItNumber' placeholder="LBG Ref Number" maxlength=20 class='form-control' >

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
            $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
            ?>
        </div>
        </div> <!--  Panel     -->
        </div> <!--  Container -->

        </div>

        <input id='revalidationStatus' value='' type='hidden'>
        </form>
		<?php
    }

    function createJsCtidLookup(){
        $loader = new Loader();
        $ctId = $loader->loadIndexed('CT_ID','CNUM',allTables::$PERSON);
        $ctbFlag = $loader->loadIndexed('CTB_RTB','CNUM',allTables::$PERSON);
        JavaScript::buildObjectFromLoadIndexedPair($ctId,'cnum2ctid');
        JavaScript::buildObjectFromLoadIndexedPair($ctbFlag,'cnum2ctbflag');
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
      				<p style="text-align:center">Please confirm <b><span id='educationNotesid'></span></b> has successfully completed the mandatory Aurora Security Education Modules for IBMers before continuing</p>
      				<p style="text-align:center">Access to these self paced online courses is via the following link <a href='https://lt.be.ibm.com/aurora' target='_blank'>https://lt.be.ibm.com/aurora</a></p>
      				<p style="text-align:center">Please contact <a href='mailto:Aurora.Central.PMO@uk.ibm.com'>Aurora Central PMO/UK/IBM</a> if you do not have access.</p>
					<p style="text-align:center">Please note a false declaration of completion constitutes a breach of IBM Business Conduct Guidelines and may lead to disciplinary action.</p>
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
      	<p>User <?=$_SESSION['ssoEmail']?> is not known to this tool</p>
       	<p>Your Functional Manager needs to Onboard you onto vBAC. </p>
       	<p>Boarding education for your manager can be found at this URL: http://w3.tap.ibm.com/medialibrary/media_set_view?id=47864</p>
       	<p>If you already have a Preboarder record in vBAC your manager will need to onboard you as an IBMer AND link it to your Preboarder Record.</p>
       	<p>They SHOULD NOT Initiate PES in vBAC if you are already PES cleared. Your manager should contact the PES Team (LBG Vetting Process/UK/IBM) asking them to update your PES Status in vBAC if necessary. </p>
      	</div>
        <div class='panel-footer'>
        </div>
        </div>
        <?php
    }

    function helpModal(){
        ?>
        <!-- Modal -->
		<div id="assetHelpModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Help/Guidance</h4>
      			</div>
      			<div class="modal-body" >
			        <p>For a new CT ID : </p>
			        <ul>
			        <li>Ask your line manager to create a<b>"New Starter"</b> request in <b>IT@LBG</b>.</li>
			        <li>Raise a vBAC request ONLY for a CT ID and enter the <b>IT@LBG Request reference in the LBG Ref Number field</b></li>
			        </ul>
        			<p>Once your CT ID is recorded in vBAC you may raise all other access requests..</p>
        		</div>
        		<div class='modal-footer'>
      		  		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      			</div>
        </div>
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
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
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
        		   <h4 class="modal-title">Contractor ID (CT ID) - LBG ID(7 Digits)</h4>
      			</div>
      			<div class="modal-body" >
      			<h1 class='text-center' style='font-size:56px;background-color:red;color:white'>STOP</h1>
        		<p>Before requests can be made on LBG, the individual needs to have a Contractor ID (CT ID/LBG ID).</p>
        		<p>We do not have a record of the CT ID/LBG ID for:</p>
        		<input id='requesteeName' value='' disabled>
        		<input id='ctbflag' value='' type='hidden'></p></p>
        		<label for='requesteeCtid'><b>Either</b> enter it here</label>
        		<input id='requesteeCtid' value='' type="number" min='999999' max='9999999'></p>
        		<p><b>Or</b> simply close this Modal to generate a new CT ID/LBG ID request.</p>
        		<p class='text-center'>Closing this modal without entering a CT ID/LBG ID will cause a request for a CT ID/LBG ID to be generated.</p>
        		<h4 class='bg-warning text-center'>Please do not close this Modal without entering a valid CTID (LBG ID) if the individual already has one. If you are unsure please check now before proceeding</h4>
        		<p>Creating duplicate requests introduces significant delay to the process of obtaining Digital Assets</p>
        		<p><small>The terms CT ID and LBG ID are interchangable and refer to the same thing</small></p>
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
      				<p style="text-align:center">You have requested Asset : <b><span id='requestedAssetTitle'></span></b></p>
      				<p style="text-align:center">However, this asset has a pre-req of : <b><span id='prereqAssetTitle'></span></b> which you have <b>NOT</b> selected.</p>
      				<p style="text-align:center">If <b><span id='requesteeNotesid'></span></b> already has that asset, then simply close this window, and continue</p>

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
        </div>
        <?php
    }



    static function buildLocationOptions($currentLocation=null){
        $loader = new Loader();
        $locationsByCity = $loader->loadIndexed('CITY','ADDRESS',allTables::$STATIC_LOCATIONS);
        $countryByCity = $loader->loadIndexed('COUNTRY','CITY', allTables::$STATIC_LOCATIONS);
        $children = array();


        foreach ($locationsByCity as $location => $city){
            $children[trim($countryByCity[$city]) . " - " . trim($city)] = empty($children[trim($countryByCity[$city]) . " - " . trim($city)]) ? "" : $children[trim($countryByCity[$city]) . " - " . trim($city)];

            $preparedLocation = str_replace(array(','), array(''), strtolower(trim($location . $city)));
            $preparedCurrLocation = str_replace(array(','), array(''), strtolower(trim($currentLocation)));

            $selected = $preparedLocation == $preparedCurrLocation ? ' selected ' : null;

            $children[trim($countryByCity[$city]) . " - " . trim($city)] .= "<option id='$location' $selected >$location,$city</option>";
        }

        ksort($children);

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

            $assetName = trim($requestableAsset['ASSET_TITLE']);
            $returnable = strpos($assetName,'Return/Deactivation') !== false;
            $renewable  = strpos($assetName,'Renewal') !== false;

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
            	data-return='<?=$returnable ? "yes" : "no";?>'
            	data-renewable='<?=$renewable ? "yes" : "no";?>'
            	data-ignore='<?=empty(trim($requestableAsset['ASSET_PREREQUISITE'])) ? 'Yes': 'No'?>'
            	data-orderitreq='<?=trim($requestableAsset['ORDER_IT_REQUIRED'])?>'
            >
            <label class='form-check-label' for='person-<?=$personId?>-asset-<?=$assetId?>'><?=trim($requestableAsset['ASSET_TITLE'])?></label>
        	<?php
        	if($requestableAsset['BUSINESS_JUSTIFICATION_REQUIRED']=='Yes'){
        	    $rowsRequired = (int)strlen(trim(urldecode($requestableAsset['PROMPT'])))/30;
        	    $rowsRequired = $rowsRequired < 2 ? 2 : $rowsRequired;
        	    ?><div class='justificationDiv'  id='person-<?=$personId?>-justification-div-<?=$assetId?>' style='display:none'><textarea class='form-control justification' rows='<?=$rowsRequired?>' style='min-width: 100%' id='person-<?=$personId?>-justification-<?=$assetId?>' name='person-<?=$personId?>-justification-<?=$assetId?>' placeholder='<?=trim(urldecode($requestableAsset['PROMPT']));?>' min=1  max='255' ></textarea><span disabled>255 chars max</span></div><?php
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
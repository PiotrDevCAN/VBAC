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

    static function ableToOwnAssets($includeProvisionallyCleared =true){
        $predicate = " and PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED. "',";
        $predicate.= $includeProvisionallyCleared ? "'" .personRecord::PES_STATUS_PROVISIONAL . "'," : null;
        $predicate.= "'" . personRecord::PES_STATUS_CLEARED_PERSONAL. "','" . personRecord::PES_STATUS_CLEARED_AMBER. "','" . personRecord::PES_STATUS_EXCEPTION. "','" . personRecord::PES_STATUS_RECHECK_REQ. "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "','" . personRecord::PES_STATUS_MOVER. "') ";  // They must be PES Cleared.
        $predicate.= " and ((trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "' or REVALIDATION_STATUS is null or trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_POTENTIAL . "') "; // They are ACTIVE IBMer
        $predicate.= " or ( trim(REVALIDATION_STATUS) IN ('" . personRecord::REVALIDATED_VENDOR . "') and ( PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PES_STATUS_DETAILS is null) )  "; // They are a vendor - who has not subsequently been boarded as an IBMer
	    $predicate.= " or REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDING . "%' ) ";  // OR they are in the process of offboarding so need to be able to request things be returned.
        return $predicate;
    }

    function displayForm(){
        $loader = new Loader();
        $myCnum = personTable::myCnum();
        $myManagersCnum = personTable::myManagersCnum();
        $isFm   = personTable::isManager($_SESSION['ssoEmail']);
        $isPmo  = $_SESSION['isPmo'];
        $isRequestor = employee_in_group($_SESSION['reqBg'], $_SESSION['ssoEmail']);

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
        
        $allNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON);
        $allEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON);

        $selectableRevalidationStatus = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);

        $approvingMgrPredicate = " upper(FM_MANAGER_FLAG) like 'Y%' ";
        $approvingMgrs = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$approvingMgrPredicate)

        ?>
        <form id='assetRequestForm'  class="form-horizontal"
        	onsubmit="return false;">
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
                        $isOffboarding = substr($selectableRevalidationStatus[$cnum],0,11)==personRecord::REVALIDATED_OFFBOARDING;
                        $dataOffboarding = " data-revalidationstatus" . "='" . $selectableRevalidationStatus[$cnum] . "' ";
                        // $dataOffboarding.= $isOffboarding ? "='true' " : "='false'";
                        $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $allEmailAddress[$cnum];
                        //$selected = !$isFm && trim($cnum)==trim($myCnum) ? ' selected ' : null    // If they don't select the user - we don't fire the CT ID & Education prompts.
                        $selected = null;
                        if (!empty(trim($displayedName))) {
                            $disabled = false;
                        } else {
                            // $disabled = " disabled ";
                            $disabled = null;
                            $displayedName = 'Missing Email Address or Notes Id for '.$cnum;
                        }
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
                            $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $allEmailAddress[$cnum];
                            if (!empty(trim($displayedName))) {
                                $selected = null;
                                $disabled = null;
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
                            } else {
                                $selected = null;
                                // $disabled = " disabled ";
                                $disabled = null;
                                $displayedName = 'Missing Email Address or Notes Id for '.$cnum;
                            }
                            ?><option value='<?=trim($cnum);?>'<?=$selected?><?=$disabled?>><?=$displayedName?></option><?php
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
            $submitButton =   $this->formButton('submit','Submit','saveAssetRequest','enabled','Save','btn btn-primary');
            $allButtons[] = $submitButton;
            $this->formBlueButtons($allButtons);
            $this->formHiddenInput('requestor',$_SESSION['ssoEmail'],'requestor');
            ?>
        </div>
        </div> <!--  Panel     -->
        </div> <!--  Container -->

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

    function unknownUser(){
        $pesTaskId = personRecord::getPesTaskId();
        ?>
        <div class="panel panel-danger">
        <div class="panel-heading">
        <h3 class="panel-title" id='requestableAssetListTitle'>Asset Request</h3>
        </div>
		<div class="panel-body">
      	<p>User <?=$_SESSION['ssoEmail']?> is not known to this tool</p>
       	<p>Your Functional Manager needs to Onboard you onto vBAC. </p>
       	<p>If you already have a Preboarder record in vBAC your manager will need to onboard you as an IBMer AND link it to your Preboarder Record.</p>
       	<p>They SHOULD NOT Initiate PES in vBAC if you are already PES cleared. Your manager should contact the PES Team (<?=$pesTaskId?>) asking them to update your PES Status in vBAC if necessary. </p>
      	</div>
        <div class='panel-footer'>
        </div>
        </div>
        <?php
    }

    static function buildLocationOptions($currentLocation=null){
        $loader = new Loader();
        $locationsByCity = $loader->loadIndexed('CITY','ADDRESS',allTables::$STATIC_LOCATIONS);
        $countryByCity = $loader->loadIndexed('COUNTRY','CITY', allTables::$STATIC_LOCATIONS);
        $children = array();

        $preparedCurrLocation = str_replace(array(','), array(''), strtolower(trim($currentLocation)));

        foreach ($locationsByCity as $location => $city){
            $city = trim($city);
            $country = trim($countryByCity[$city]);
            $childrenKey = $country . " - " . $city;
            $children[$childrenKey] = empty($children[$childrenKey]) ? "" : $children[$childrenKey];

            $preparedLocation = str_replace(array(','), array(''), strtolower(trim($location)));
            if ($preparedLocation == $preparedCurrLocation) {
                $selected = ' selected ';
            } else {
                $preparedLocation = str_replace(array(','), array(''), strtolower(trim($location . $city)));
                if ($preparedLocation == $preparedCurrLocation) {
                    $selected = ' selected ';
                } else {
                    $preparedLocation = str_replace(array(','), array(''), strtolower(trim($location . $city . $country)));
                    if ($preparedLocation == $preparedCurrLocation) {
                        $selected = ' selected ';
                    } else {
                        $selected = null;
                    }
                }
            }
            $children[$childrenKey] .= "<option id='$location' $selected >$location,$city,$country</option>";
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
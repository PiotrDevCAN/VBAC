<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

class requestableAssetListRecord extends DbRecord
{
    protected $ASSET_TITLE;
    protected $ASSET_PREREQUISITE;
    protected $ASSET_PRIMARY_UID_TITLE;
    protected $ASSET_SECONDARY_UID_TITLE;
    protected $APPLICABLE_ONSHORE;
    protected $APPLICABLE_OFFSHORE;
    protected $BUSINESS_JUSTIFICATION_REQUIRED;
    protected $REQUEST_BY_DEFAULT;
    protected $RECORD_DATE_ISSUED_TO_IBM;
    protected $RECORD_DATE_ISSUED_TO_USER;
    protected $RECORD_DATE_RETURNED;
    protected $LISTING_ENTRY_CREATED;
    protected $LISTING_ENTRY_CREATED_BY;
    protected $LISTING_ENTRY_REMOVED;
    protected $LISTING_ENTRY_REMOVED_BY;
    protected $PROMPT;
    protected $ORDER_IT_TYPE;
    protected $ORDER_IT_REQUIRED;



    function displayForm($mode){
        $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;

        $loader = new Loader();
        $predicate = empty($this->ASSET_TITLE) ? " LISTING_ENTRY_REMOVED is null " : " ASSET_TITLE != '" . db2_escape_string($this->ASSET_TITLE) . "' AND LISTING_ENTRY_REMOVED is null ";
        $possiblePrerequsites = $loader->load('ASSET_TITLE',allTables::$REQUESTABLE_ASSET_LIST,$predicate);
       
        $now = new \DateTime();
        $created = $now->format('Y-m-d h:i:s');
        ?>
		<form id='requestableAssetListForm' class="form-horizontal"
			onsubmit="return false;">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title" id='requestableAssetListTitle'>Requestable Asset</h3>
				</div>
				<div class="panel-body">
<!-- Title & Prerequisite -->
					<div class="form-group required">
						<div class="col-sm-6">
							<input class="form-control" id="asset_title" name="ASSET_TITLE"
								value="<?=trim($this->ASSET_TITLE)?>"
								type="text" placeholder='Unique title for Asset'
								required
						        <?=$notEditable?>>
						</div>
						<div class='col-sm-6'>
						<select class='form-control select select2' id='asset_prerequisite'
                              name='ASSET_PREREQUISITE'
                              placeholder='Prerequisite Asset Required:'
                      	>
                    	<option value=''></option>
                    	<?php
                            foreach ($possiblePrerequsites as $option){
                                ?><option value='<?=$option?>'
                                <?php
                                echo (trim($option)==$this->ASSET_PREREQUISITE) ? ' selected ' : null;
                                ?>
                            	><?=$option?></option><?php
                            };
                            ?>
                  		</select>
						</div>
					</div>

<!-- Primary & Secondary UID -->
					<div class="form-group">
						<div class="col-sm-6">
							<input class="form-control" id="asset_primary_uid_title" name="ASSET_PRIMARY_UID_TITLE"
								value="<?=trim($this->ASSET_PRIMARY_UID_TITLE)?>"
								type="text" placeholder='Primary UID title for Asset'
						        >
						</div>
						<div class='col-sm-6'>
							<input class="form-control" id="asset_secondary_uid_title" name="ASSET_SECONDARY_UID_TITLE"
								value="<?=trim($this->ASSET_SECONDARY_UID_TITLE)?>"
								type="text" placeholder='Secondary UID title for Asset'
						        >
						</div>
					</div>

<!-- Order IT Group -->
					<div class="form-group">
						<div class="col-sm-6">
							<input class="form-control" id="ORDER_IT_TYPE" name="ORDER_IT_TYPE"
								value="<?=trim($this->ORDER_IT_TYPE)?>"
								type="number" placeholder='Order IT Grouping Number'
								data-toggle="tooltip" 
								data-placement="right"
								title='Requests for Assets with the Same Order IT Grouping Number may be grouped together, in the same "batch", when creating Order IT requests'
								min=1
						        >
						</div>
						<div class='col-sm-6'></div>
					</div>
<!-- Applicable On Shore/Off Shore Business Justification  -->
					<div class="form-group">
						<div class="col-sm-3">
						<input checked='checked' data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Applicable Onshore" data-off="Not applicable Onshore" id='applicableOnShore' name='APPLICABLE_ONSHORE' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input checked='checked' data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Applicable Offshore" data-off="Not applicable Offshore" id='applicableOffShore' name='APPLICABLE_OFFSHORE' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Further Details Required" data-off="No Further Details Required" id='businessJustification' name='BUSINESS_JUSTIFICATION_REQUIRED' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Request by Default" data-off="Do not request by default" id='requestByDefault' name='REQUEST_BY_DEFAULT' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
					</div>

<!-- Business Justification Prompt -->
					<div class="form-group required" style='display: none' id='promptDiv'>
						<div class="col-sm-6 col-sm-offset-6">
						<textarea class='form-control' rows='2' style='min-width: 100%' id='prompt' name='PROMPT' placeholder='User prompt when further details are requested' min='50' max='255' ></textarea>
						</div>
					</div>



<!-- Record Dates  -->
					<div class="form-group">
						<div class="col-sm-3">
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Record Date Issued to IBM" data-off="Do Not Record Date to IBM" id='RecordDateToIbm' name='RECORD_DATE_ISSUED_TO_IBM' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Record Date Issued to User" data-off="Do Not Record Date to User" id='RecordDateToUser' name='RECORD_DATE_ISSUED_TO_USER' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Record Date Returned" data-off="Do Not Record Date Returned" id='RecordDateReturned' name='RECORD_DATE_RETURNED' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
						<div class='col-sm-3'>
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="Order It Required" data-off="Order It Optional" id='OrderItRequired' name='ORDER_IT_REQUIRED' value='1' data-onstyle='success' data-offstyle='warning'>
						</div>
					</div>

				</div>
			</div>
			<input type='hidden' name='LISTING_ENTRY_CREATED_BY' id='listingEntryCreatedBy' value='<?=$GLOBALS['ltcuser']['mail']?>'>
			<input type='hidden' name='LISTING_ENTRY_CREATED' id='listingEntryCreated' value='<?=$created?>'>
		</form>


		 <?php
            $allButtons = null;
            $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRequestableAsset',null,'Update','btn btn-primary') :  $this->formButton('submit','Submit','saveRequestableAsset',null,'Save','btn btn-primary');
            $allButtons[] = $submitButton;
            $this->formBlueButtons($allButtons);
            $this->formHiddenInput('requestor',$GLOBALS['ltcuser']['mail'],'requestor');
     }

}
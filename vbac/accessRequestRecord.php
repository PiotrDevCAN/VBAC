<?php
namespace vbac;

use itdq\DbRecord;


/**
 *
 * @author gb001399
 *
 */
class accessRequestRecord extends DbRecord
{

    protected $NAME;
    protected $CNUM;
    protected $FUNCTIONAL_MGR_FLAG;
    protected $FUNCTIONAL_MGR_CNUM;

    protected $TT_BAU;
    protected $OPEN_SEAT;
    protected $ACCOUNT_ORGANISATION;
    protected $START_DATE;
    protected $END_DATE;

    protected $person_NOTES_ID;
    protected $person_INTRANET_ID;
    protected $person_PHONE;
    protected $person_UID;


    function displayForm($mode){
        $allRoles = staticRolesTable::getallRoles();
        $sampleDropDown = array('DD001'=>"Drop Down One",'DD002'=>"Drop Down Two",'DD003'=>"Drop Down Three");
        ?>

		<div class='col-sm-2'></div>
		<div class='col-sm-8'>
        <form id='displayForm'  class="form-horizontal">

        <!-- //REQUEST TYPE -->
		<div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Request Type</h3>
  		</div>
  		<div class="panel-body">

		<div class="form-group">
		<label class='col-sm-3 label-centre' for='purpose_of_request'>Purpose of Request</label>
        <div class="col-sm-3">
        <input class="form-control" id="purpose_of_request" name="purpose_of_request" required="required" type="text" placeholder='' >
        </div>

		<label class='col-sm-3 label-centre' for='request_type'>Request Type</label>
        <div class='col-sm-3'>
        <input class='form-control' id='request_type' name='request_type' value='' required='required' type='text' placeholder='' >
        </div>
        </div>

        <div class='form-group' >
		<label class='col-sm-2 label-centre' for='account_type_in_request_type'>Account Type</label>
        <div class='col-sm-3'>
               <select class='form-control select select2 input-lg' id='account_type'
                  	          name='ACCOUNT_TYPE'
                  	          required='required'
                  	          placeHolder = 'Select Account Type'
                >
                <option value=''></option>
                <?php
                foreach ($sampleDropDown as $ddId => $ddValue){
                    ?><option value='<?=$ddId; ?>><?=$ddValue;?></option><?php
                }
                ?>
            	</select>
        </div>

		<label class='col-sm-3 label-centre' for='order_it_request_number'>Order IT Request Number</label>
        <div class='col-sm-3'>
        <input class='form-control' id='order_it_request_number' name='order_it_request_number' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <div class='form-group' >
		<label class='col-sm-3 label-centre' for='lbg_contractor_id'>LBG Contractor ID <br> (CT ID / LBG ID)</label>

        <div class='col-sm-3'>
        <input class='form-control' id='lbg_contractor_id' name='lbg_contractor_id' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-3 label-centre' for='domain_in_request_type'>Domain</label>
        <div class='col-sm-3'>
        <input class='form-control' id='domain_in_request_type' name='domain_in_request_type' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>


<!-- // END USER DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">End User Details</h3>
  </div>
  <div class="panel-body">


        <form id='displayBpDetails'  class="form-horizontal">



				<div class="form-group">
        <div class="col-sm-9">
        <input class="form-control" id="NAME" name="NAME" value="<?=$this->NAME?>" required="required" type="text" placeholder='Start typing a name to perform a lookup if request for someone else' >
        </div>

</div>
</div>

</div>




<!-- // AD GROUP DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">AD Group Details</h3>
  </div>
  <div class="panel-body">


        <form id='displayBpDetails'  class="form-horizontal">
        <div class="form-group" id="displayBpDetails">


		<div class="form-group">
		<label class='col-sm-2 label-centre' for='role_technology_on_lbg_account'>Role/Technology on LBG Account</label>
        <div class="col-sm-3">
        <input class="form-control" id="role_technology_on_lbg_account" name="role_technology_on_lbg_account" required="required" type="text" placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_role_technology'>If Other, please specify</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_role_technology' name='other_role_technology' value='' required='required' type='text' placeholder='' >
        </div>
        </div>

        <div class='form-group' >
		<label class='col-sm-2 label-centre' for='domain_1_in_ad_group_details'>Domain</label>

        <div class='col-sm-3'>
        <input class='form-control' id='domain_1_in_ad_group_details' name='domain_1_in_ad_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='standard_group_1_in_ad_group_details'>Standard Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='standard_group_1_in_ad_group_details' name='standard_group_1_in_ad_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <div class='form-group' >
        <label class='col-sm-2 label-centre' for='domain_2_in_ad_group_details'>Domain</label>

        <div class='col-sm-3'>
        <input class='form-control' id='domain_2_in_ad_group_details' name='domain_2_in_ad_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='standard_group_2_in_ad_group_details'>Standard Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='standard_group_2_in_ad_group_details' name='standard_group_2_in_ad_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <div class='form-group' >
		<label class='col-sm-2 label-centre' for='domain_6_in_ad_group_details'>Domain</label>

        <div class='col-sm-3'>
        <input class='form-control' id='domain_6_in_ad_group_details' name='domain_6_in_ad_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='privileged_group_1_in_ad_group_details'>Privileged Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='privileged_group_1_in_ad_group_details' name='privileged_group_1_in_ad_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <div class='form-group' >
        <label class='col-sm-2 label-centre' for='domain_7_in_ad_group_details'>Domain</label>

        <div class='col-sm-3'>
        <input class='form-control' id='domain_7_in_ad_group_details' name='domain_7_in_ad_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='privileged_group_2_in_ad_group_details'>Privileged Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='privileged_group_2_in_ad_group_details' name='privileged_group_2_in_ad_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <div class='form-group' >
        <label class='col-sm-2 label-centre' for='business_justification_in_ad_group_details'>Business Justification</label>

        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_ad_group_details' name='business_justification_in_ad_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_ad_group_details'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_ad_group_details' name='other_comments_details_in_ad_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>
</div>



<!-- // ASSET REQUEST -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Asset Request</h3>
  </div>
  <div class="panel-body">


        <form id='displayBpDetails'  class="form-horizontal">
        <div class="form-group" id="displayBpDetails">


		<div class="form-group">
		<label class='col-sm-2 label-centre' for='laptop_asset_request'>Laptop Asset Request</label>
        <div class="col-sm-3">
        <input class="form-control" id="laptop_asset_request" name="laptop_asset_request" value="<?=$this->NAME?>" required="required" type="text" placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='full_vpn_required'>Full VPN Required</label>
        <div class='col-sm-3'>
        <input class='form-control' id='full_vpn_required' name='full_vpn_required' value='' required='required' type='text' placeholder='' >
        </div>
        </div>

		<label class='col-sm-2 label-centre' for='vdi_required'>VDI Required</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='vdi_required' name='vdi_required' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='sdid_required'>SDID Required</label>
        <div class='col-sm-3'>
        <input class='form-control' id='sdid_required' name='sdid_required' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='rsa_access_required'>RSA Access Required</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='rsa_access_required' name='rsa_access_required' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='third_party_vdi_access_required'>3rd Party VDI Access Required</label>
        <div class='col-sm-3'>
        <input class='form-control' id='third_party_vdi_access_required' name='third_party_vdi_access_required' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

		<label class='col-sm-2 label-centre' for='lbg_email_required'>LBG Email Required</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='lbg_email_required' name='lbg_email_required' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='ocs_required'>OCS Required</label>
        <div class='col-sm-3'>
        <input class='form-control' id='ocs_required' name='ocs_required' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='vpn_lite_required'>VPN Lite Required</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='vpn_lite_required' name='vpn_lite_required' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='cyberark_required'>CyberArk Required</label>
        <div class='col-sm-3'>
        <input class='form-control' id='cyberark_required' name='cyberark_required' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

       	<label class='col-sm-2 label-centre' for='other'>Other</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other' name='other' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='business_justification_in_asset_request'>Business Justification</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_asset_request' name='business_justification_in_asset_request' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_asset_request'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_asset_request' name='other_comments_details_in_asset_request' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>


<!-- // USER ID CREATE DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">User ID Create Details</h3>
  </div>
  <div class="panel-body">


        <form id='displayBpDetails'  class="form-horizontal">
        <div class="form-group" id="displayBpDetails">


		<div class="form-group">
		<label class='col-sm-2 label-centre' for='type_of_uid_to_be_created'>Type of UID to be Created</label>
        <div class="col-sm-3">
        <input class="form-control" id="type_of_uid_to_be_created" name="type_of_uid_to_be_created" value="<?=$this->NAME?>" required="required" type="text" placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='account_type_in_user_id_create_details'>Account Type</label>
        <div class='col-sm-3'>
        <input class='form-control' id='account_type_in_user_id_create_details' name='account_type_in_user_id_create_details' value='' required='required' type='text' placeholder='' >
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='business_justification_in_user_id_create_details'>Business Justification</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_user_id_create_details' name='business_justification_in_user_id_create_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_user_id_create_details'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_user_id_create_details' name='other_comments_details_in_user_id_create_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>
</div>


<!-- // USER ID MODIFY GROUP DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">User ID Modify Details</h3>
  </div>
  <div class="panel-body">
        <div class="form-group" id="displayBpDetails">
		<div class="form-group">
		<label class='col-sm-2 label-centre' for='type_of_uid_to_be_modified'>Type of UID to be Modified</label>
        <div class="col-sm-3">
        <input class="form-control" id="type_of_uid_to_be_modified" name="type_of_uid_to_be_modified" value="<?=$this->NAME?>" required="required" type="text" placeholder='' >
        </div>
        </div>

		<label class='col-sm-2 label-centre' for='current_role'>Current Role</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='current_role' name='current_role' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='new_role'>New Role</label>
        <div class='col-sm-3'>
        <input class='form-control' id='new_role' name='new_role' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='current_domain'>Current Domain</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='current_domain' name='current_domain' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='new_domain'>New Domain</label>
        <div class='col-sm-3'>
        <input class='form-control' id='new_domain' name='new_domain' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        		<label class='col-sm-2 label-centre' for='domain_1_in_user_id_modify_group_details'>Domain</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='domain_1_in_user_id_modify_group_details' name='domain_1_in_user_id_modify_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='standard_group_1_in_user_id_modify_group_details'>Standard Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='standard_group_1_in_user_id_modify_group_details' name='standard_group_1_in_user_id_modify_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='domain_2_in_user_id_modify_group_details'>Domain</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='domain_2_in_user_id_modify_group_details' name='domain_2_in_user_id_modify_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='standard_group_2_in_user_id_modify_group_details'>Standard Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='standard_group_2_in_user_id_modify_group_details' name='standard_group_2_in_user_id_modify_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

		<label class='col-sm-2 label-centre' for='domain_6_in_user_id_modify_group_details'>Domain</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='domain_6_in_user_id_modify_group_details' name='domain_6_in_user_id_modify_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='privileged_group_1_in_user_id_modify_group_details'>Privileged Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='privileged_group_1_in_user_id_modify_group_details' name='privileged_group_1_in_user_id_modify_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='domain_7_in_user_id_modify_group_details'>Domain</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='domain_7_in_user_id_modify_group_details' name='domain_7_in_user_id_modify_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='privileged_group_2_in_user_id_modify_group_details'>Privileged Group</label>
        <div class='col-sm-3'>
        <input class='form-control' id='privileged_group_2_in_user_id_modify_group_details' name='privileged_group_2_in_user_id_modify_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='business_justification_in_user_id_modify_group_details'>Business Justification</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_user_id_modify_group_details' name='business_justification_in_user_id_modify_group_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_user_id_modify_group_details'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_user_id_modify_group_details' name='other_comments_details_in_user_id_modify_group_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>
</div>


<!-- // USER ID DELETE DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">User ID Delete Details</h3>
  </div>
  <div class="panel-body">
        <div class="form-group" id="displayBpDetails">


		<div class="form-group">
		<label class='col-sm-2 label-centre' for='type_of_uid_to_be_deleted'>Type of UID to be Deleted</label>
        <div class="col-sm-3">
        <input class="form-control" id="type_of_uid_to_be_deleted" name="type_of_uid_to_be_deleted" value="<?=$this->NAME?>" required="required" type="text" placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='account_type_in_user_id_delete_details'>Account Type</label>
        <div class='col-sm-3'>
        <input class='form-control' id='account_type_in_user_id_delete_details' name='account_type_in_user_id_delete_details' value='' required='required' type='text' placeholder='' >
        </div>
        </div>

        <label class='col-sm-2 label-centre' for='business_justification_in_user_id_delete_details'>Business Justification</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_user_id_delete_details' name='business_justification_in_user_id_delete_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_user_id_delete_details'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_user_id_delete_details' name='other_comments_details_in_user_id_delete_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>
</div>


<!-- USER ID RENEW DETAILS -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">User ID Renew Details</h3>
  </div>
  		<div class="panel-body">
        <div class="form-group" id="displayBpDetails">
        <label class='col-sm-2 label-centre' for='business_justification_in_user_id_renew_details'>Business Justification</label>
        <div class='form-group' >
        <div class='col-sm-3'>
        <input class='form-control' id='business_justification_in_user_id_renew_details' name='business_justification_in_user_id_renew_details' value='' required='required' type='text' placeholder='' >
        </div>

		<label class='col-sm-2 label-centre' for='other_comments_details_in_user_id_renew_details'>Other Comments/Details</label>
        <div class='col-sm-3'>
        <input class='form-control' id='other_comments_details_in_user_id_renew_details' name='other_comments_details_in_user_id_renew_details' value='' required='required' type='text' placeholder=''>
        </div>
        </div>
</div>
</div>
</form>
</div>


        <?php

    }
}
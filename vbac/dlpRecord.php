<?php
namespace vbac;

use itdq\BlueMail;
use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;

/**
 *
 * @author gb001399
 *
 */
class dlpRecord extends DbRecord
{    
    protected $CNUM;
    protected $APPROVER_EMAIL;
    protected $APPROVED_DATE;
    protected $HOSTNAME;
    protected $CREATION_DATE;
    protected $TRANSFERRED_TO_HOSTNAME;
    protected $TRANSFERRED_DATE;
    protected $TRANSFERRED_EMAIL;
    protected $EXCEPTION_CODE;
    protected $STATUS;
    
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_TRANSFERRED = 'transferred';
    
    private static $dlpEmailBody = "A DLP (DG&CB) License record has been created in vBAC for:<br/>
                                   <br/>Licensee : &&licensee&&
                                   <br/>Hostname : &&hostname&&
                                   <br/><b>Please Approve/Reject as appropriate</b>
                                   <br/>Access vBAC DLP Licence page <a href='&&server&&/pc_dlpRecord.php'>Here</a>";
    
    private static $dlpEmailPatterns = array(
        '/&&licensee&&/',
        '/&&hostname&&/',
        '/&&server&&/',
    );
    
    function displayForm($mode){
        $loader = new Loader();
        $predicate = " 1=1 " . assetRequestRecord::ableToOwnAssets();
        $myManagersCnum = personTable::myManagersCnum();
        
        // $selectableNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$predicate);
        $selectableEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON,$predicate);

        // $allNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON);
        $allEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON);

        $selectableRevalidationStatus = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);   
        
        $currentLicences = $loader->loadIndexed('HOSTNAME','CNUM', allTables::$DLP," TRANSFERRED_TO_HOSTNAME is null ");   
        JavaScript::buildObjectFromLoadIndexedPair($currentLicences,'licences');
        
        $approvingMgrPredicate = " upper(FM_MANAGER_FLAG) like 'Y%' ";
        // $approvingMgrsNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$approvingMgrPredicate);
        $approvingMgrsEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON,$approvingMgrPredicate);
        
        $cnumFm = $loader->loadIndexed('FM_CNUM','CNUM', allTables::$PERSON,$predicate);
        JavaScript::buildObjectFromLoadIndexedPair($cnumFm,'cnumfm');
        
        ?>
        <form id='dlpRecordingForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">DLP Register/Transfer Licence</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-4'>
				<label for='licencee'>Licence Holder</label>
                <select class='form-control select select2 '
                    id='licencee'
                    name='licencee'
                    required
                    data-toggle="tooltip" title="Only PES Cleared Kyndryl employees & Vendors will appear in this list. If you feel someone is missing, please ensure they have a FULL Boarded record in the system."
                    >
                    <option value=''></option>
                <?php                
                foreach ($selectableEmailAddress as $cnum => $emailAddress){
                    $isOffboarding = personRecord::checkIsOffboarding($selectableRevalidationStatus[$cnum]);
                    // $dataOffboarding = " data-revalidationstatus" . "='" . $selectableRevalidationStatus[$cnum] . "' ";
                    $displayedName = !empty(trim($emailAddress)) ?  trim($emailAddress) : $allEmailAddress[$cnum];
                    // $hostname = isset($currentLicences[trim($cnum)]) ? " (" .  $currentLicences[trim($cnum)] . ")" : " (no licence)";
                    if(!$isOffboarding){
                        if (!empty(trim($displayedName))) {
                            $disabled = false;
                        } else {
                            // $disabled = " disabled ";
                            $disabled = null;
                            $displayedName = 'Missing Email Address or Notes Id for '.$cnum;
                        }
                        ?><option value='<?=trim($cnum)?>'<?=$disabled?>><?=$displayedName?></option><?php                    
                    }
                };
                ?>    
            	</select>
            	</div>
            	<div class='col-sm-4'>
            	<label for='hostname'>New Hostname</label>
            	<input class="form-control " id='hostname' name='hostname' value=''  type='text' required placeholder='New Hostname' style="text-transform:uppercase">
				</div>
				
            	<div class='col-sm-4'>
            	<label for='currentHostname'>Current Hostname</label>  
        		<input class="form-control"  id='currentHostname' name='currentHostname' value='' type='text'  placeholder='Current Hostname' style="text-transform:uppercase" disabled>
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
                    $isFm = $_SESSION['isFm'];
                    $myCnum = personTable::myCnum();
                    foreach ($approvingMgrsEmailAddress as $cnum => $emailAddress){
                        $displayedName = !empty(trim($emailAddress)) ?  trim($emailAddress) : $allEmailAddress[$cnum];
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
        		</div>
		</div>

        <div class='panel-footer'>
        	<?php
            $allButtons = null;
            $submitButton =   $this->formButton('submit','Submit','saveDlpLicence',null, 'Save','btn btn-primary');
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

    static function notifyApprover($licensee, $hostname, $approvingMgr){
        $replacements = array($licensee, $hostname, "https://" . $_SERVER['HTTP_HOST']);
        $message = preg_replace(self::$dlpEmailPatterns, $replacements, self::$dlpEmailBody);  
        
        $delegates = delegateTable::delegatesFromEmail($approvingMgr);
        $delegates = $delegates ? $delegates : array();
        
        BlueMail::send_mail(array($approvingMgr), 'DLP(BG&CB) License Approval Request ', $message, personRecord::$vbacNoReplyId, $delegates);
    }
}
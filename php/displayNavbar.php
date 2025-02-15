<?php

use itdq\PlannedOutages;
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;
use itdq\NavbarDivider;
use itdq\OKTAGroups;
use vbac\personTable;

$beginNavBar = microtime(true);

include ('itdq/PlannedOutages.php');
$plannedOutagesLabel = "Planned Outages";
$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(lcfirst(strtoupper($_ENV['environment'])),"index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$OKTAGroups = $GLOBALS['OKTAGroups'];
$navbar = new Navbar($navBarImage, $navBarBrand, $navBarSearch);

$cdiAdmin       		= new NavbarMenu("CDI Admin");
$trace          		= new NavbarOption('View Trace','pi_trace.php','accessCdi');
$traceControl   		= new NavbarOption('Trace Control','pi_traceControl.php','accessCdi');
$traceDelete    		= new NavbarOption('Trace Deletion', 'pi_traceDelete.php','accessCdi');
$revalidation   		= new NavbarOption('Batch Reval','batchJobs/revalidate.php','accessCdi');
$cbn            		= new NavbarOption('Initiate CBN','batchJobs/sendCbnEmail.php','accessCdi');
$recheck       			= new NavbarOption('Batch ReCheck','batchJobs/recheckPotentialLeavers.php','accessCdi');
$emailDlp       		= new NavbarOption('Email DLP','batchJobs/emailWorkflowTracker.php','accessCdi');
$employeeData			= new NavbarOption('Email Employee Data','batchJobs/sendEmployeeData.php','accessCdi');
$employeeCompleteData	= new NavbarOption('Email Employee Complete Data','batchJobs/sendEmployeeCompleteData.php','accessCdi');
$headcountReport		= new NavbarOption('Email Headcount Report','batchJobs/sendHeadcountReport.php','accessCdi');
$link           		= new NavbarOption('Link Reg to PreB','pi_linkIbmerToPreboarder.php','accessCdi');
$workLocation   		= new NavbarOption('Work Location','pi_manageLocation.php','accessCdi');
$skillSet       		= new NavbarOption('Skillset','pi_manageSkillset.php','accessCdi');
$band 		      		= new NavbarOption('Business Title to Band','pi_manageBandMapping.php','accessCdi');
$manageGroups    		= new NavbarOption('Manage Okta Groups', 'pi_manageGroups.php' ,'accessCdi');
$workerAPIlookup    	= new NavbarOption('Worker API lookup', 'pi_workerAPIlookup.php' ,'accessCdi');
$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);
$cdiAdmin->addOption( new NavbarDivider('accessCdi'));
$cdiAdmin->addOption($revalidation);
$cdiAdmin->addOption($cbn);
$cdiAdmin->addOption($recheck);
$cdiAdmin->addOption($emailDlp);
$cdiAdmin->addOption($employeeData);
$cdiAdmin->addOption($employeeCompleteData);
$cdiAdmin->addOption($headcountReport);
$cdiAdmin->addOption( new NavbarDivider('accessCdi'));
$cdiAdmin->addOption($link);
$cdiAdmin->addOption($workLocation);
$cdiAdmin->addOption($skillSet);
$cdiAdmin->addOption($band);
$cdiAdmin->addOption( new NavbarDivider('accessCdi'));
$cdiAdmin->addOption($manageGroups);
$cdiAdmin->addOption( new NavbarDivider('accessCdi'));
$cdiAdmin->addOption($workerAPIlookup);

$adminMenu      = new NavbarMenu('vBac Admin');
$pmo            = new NavbarOption('Person Portal', 'pa_pmo.php','accessCdi accessPmo accessFm accessUser');
$pmoLite        = new NavbarOption('Person Portal (Lite)', 'pa_pmo_lite.php','accessCdi accessPmo accessFm accessUser');
$pmoArchive     = new NavbarOption('Person Portal (Archive)', 'pa_pmo_archive.php','accessCdi accessPmo');
$pmoCFirst      = new NavbarOption('cFIRST Reflection <b>NEW!</b>', 'pa_cfirst.php','accessCdi accessPmo');
$statusCrosscheck = new NavbarOption('Status Crosscheck <b>NEW!</b>', 'pa_statusCrosscheck.php','accessCdi accessPmo accessRepFullPerson');
$personFinder   = new NavbarOption('Person Finder','pa_personFinder.php','accessCdi accessFm');
$revalidation   = new NavbarOption('Revalidation Portal','pa_revalidation.php','accessCdi accessPmo');
$linkedReport   = new NavbarOption('Linked Portal','pa_pmoLinked.php','accessCdi accessPmo');
// $initiateCBN    = new NavbarOption('Iniate CBN','pa_sendCbnEmail.php','accessCdi accessPmo');
// $control        = new NavbarOption('Control', 'pa_control.php','accessCdi accessPmo');
// $audit          = new NavbarOption('Audit Report', 'pa_auditListing.php','accessCdi accessPmo');
$requestableAssets  = new NavbarOption('Requestable Assets', 'pa_requestableAssets.php','accessCdi accessPmo');
//$ringFenced     = new NavbarOption('Ring Fencing', 'pa_ringFencing.php','accessRes');
$delegate       = new NavbarOption('Delegates', 'pc_delegate.php','accessCdi accessPmo accessFm accessUser');
$pesTracker     = new NavbarOption('PES Tracker', 'pc_pesTracker.php','accessCdi accessPes');
$pesStatusChange= new NavbarOption('PES Status Changes', 'pr_pesStatusChangeReport.php','accessCdi accessPes');
$pesStatusUpdate= new NavbarOption('PES Status Manual Update', 'pa_statusUpdate.php','accessCdi accessPes');
$odcDataUpload  = new NavbarOption('ODC Access Upload', 'pc_odcAccessUpload.php','accessCdi accessPmo');
$CTIDDataUpdate= new NavbarOption('CT ID Update', 'pc_CTIDUpdate.php','accessCdi accessPmo');
// $email          = new NavbarOption('Email Log', 'pi_emailLog.php','accessCdi');
$tribes         = new NavbarOption('Tribes','pa_agileTribes.php','accessCdi accessPmo ');
$squads         = new NavbarOption('Squads (Current)','pa_agileSquads.php','accessCdi accessPmo ');
$squadALog      = new NavbarOption('Squadalog', 'pa_squadalog.php','accessCdi accessPmo accessFm');
$squadAssign	= new NavbarOption('Squad Assignment <b>NEW!</b>', 'pa_squadAssignment.php','accessCdi accessPmo accessFm');
$squadCrosscheck = new NavbarOption('Squad Crosscheck <b>NEW!</b>', 'pa_squadCrosscheck.php','accessCdi accessPmo accessFm');
$adminMenu->addOption($pmo);
$adminMenu->addOption($pmoLite);
$adminMenu->addOption($pmoArchive);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($pmoCFirst);
$adminMenu->addOption($statusCrosscheck);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($personFinder);
$adminMenu->addOption($revalidation);
$adminMenu->addOption($linkedReport);
// $adminMenu->addOption($initiateCBN);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
// $adminMenu->addOption($control);
$adminMenu->addOption($requestableAssets);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi accessUser accessFm'));
$adminMenu->addOption($delegate);
// $adminMenu->addOption( new NavbarDivider('accessRes'));
// $adminMenu->addOption($ringFenced);
//$adminMenu->addOption( new NavbarDivider('accessCdi'));
// $adminMenu->addOption($audit);
// $adminMenu->addOption($email);
$adminMenu->addOption( new NavbarDivider('accessPes accessCdi'));
$adminMenu->addOption($pesTracker);
$adminMenu->addOption($pesStatusChange);
$adminMenu->addOption($pesStatusUpdate);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($odcDataUpload);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($CTIDDataUpdate);
// $adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
// $adminMenu->addOption($tribes);
// $adminMenu->addOption($squads);
// $adminMenu->addOption($squadALog);
// $adminMenu->addOption($squadCrosscheck);

$agileMenu      = new NavbarMenu('Agile Admin');
$agileMenu->addOption($tribes);
$agileMenu->addOption($squads);
$agileMenu->addOption($squadALog);
$agileMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$agileMenu->addOption($squadAssign);
$agileMenu->addOption($squadCrosscheck);

$boarding       = new NavbarMenu('Boarding');
$onBoarding     = new NavbarOption('OnBoard','pb_onboard.php','accessCdi accessPmo accessFm');
// $offBoarding    = new NavbarOption('OffBoard', 'pb_offboard.php','accessCdi accessPmo accessFm');
$boarding->addOption($onBoarding);
// $boarding->addOption($offBoarding);

$access         = new NavbarMenu('Access/Assets');
$assetPortal    = new NavbarOption('Asset Portal', 'pa_assetPortal.php','accessCdi accessPmo accessFm accessUser');
$requestAssets  = new NavbarOption('Request Digital Assets', 'pc_assetRequest.php','accessCdi accessPmo accessFm accessUser');
$returnAsset    = new NavbarOption('Return Digital Assets', 'pc_assetReturn.php','accessCdi accessPmo accessFm accessUser');
// $requestAccess  = new NavbarOption('Request/Return AD Group Access', 'pc_accessRequest.php','accessCdi accessPmo accessFm accessUser');
$dlpRecord      = new NavbarOption('DLP Licences', 'pc_dlpRecord.php','accessCdi accessPmo accessFm accessUser');
//$iamAdmin       = new NavbarOption('IAM Admin', 'pc_iamAdmin.php','accessCdi accessPmo');
// $softRsaToken   =new NavbarOption('Soft RSA Token', 'https://w3.ibm.com/tools/cio/forms/landing/org/app/5e4da88f-3c98-4daf-8a76-4031d661db21/launch/index.html?form=F_SoftRSARequestForm','accessCdi accessPmo accessFm accessUser');
// https://w3.ibm.com/tools/cio/forms/landing/org/app/5e4da88f-3c98-4daf-8a76-4031d661db21/launch/index.html?form=F_SoftRSARequestForm
// $assetPortal    = new NavbarOption('Asset Portal', 'pa_assetPortal.php','accessCdi accessPmo  ');
// $requestAssets  = new NavbarOption('Request/Return Digital Assets', 'pc_assetRequest.php','accessCdi accessPmo  ');
// $requestAccess  = new NavbarOption('Request/Return AD Group Access', 'pc_accessRequest.php','accessCdi accessPmo  ');
// $iamAdmin       = new NavbarOption('IAM Admin', 'pc_iamAdmin.php','accessCdi accessPmo');
$access->addOption($assetPortal);
$access->addOption($requestAssets);
$access->addOption($returnAsset);
// $access->addOption($requestAccess);
$access->addOption($dlpRecord);
// $access->addOption($iamAdmin);
// $access->addOption($softRsaToken    );

$reports         = new NavbarMenu('Downloadable Reports');
$original        = new NavbarOption('Person Details - Original', 'pr_personDetailsEmail.php','accessCdi accessPmo accessRepFullPerson');
$fullExtract     = new NavbarOption('Person Details - Full', 'pr_personDetailsFullEmail.php','accessCdi accessPmo accessRepFullPerson');
$active          = new NavbarOption('Person Details - Active', 'pr_personDetailsActiveEmail.php','accessCdi accessPmo accessRepFullPerson');
$activeOdc       = new NavbarOption('Person Details - Active(ODC)<span id="odcPopulation" class="badge">**</span>', 'pr_personDetailsActiveOdcEmail.php','accessCdi accessPmo accessRepFullPerson');
$bauReport       = new NavbarOption('Person Details - BAU Report', 'pr_bauEmail.php','accessCdi accessPmo accessRepFullPerson');
$inactive        = new NavbarOption('Person Details - Inactive', 'pr_personDetailsInactiveEmail.php','accessCdi accessPmo accessRepFullPerson');
$locMismatch     = new NavbarOption('Location Mismatch', 'pr_odcMismatchReport.php','accessCdi accessPmo accessRepFullPerson');
$odcNotInVbac    = new NavbarOption('ODC Access but no Vbac Record', 'pr_odcAccessMissingFromVbac.php','accessCdi accessPmo accessRepFullPerson');
$assetRemoval    = new NavbarOption('Asset Removal', 'pr_assetRemovalReport.php','accessCdi accessPmo');
$reports->addOption($original);
$reports->addOption($fullExtract);
$reports->addOption($active);
$reports->addOption($activeOdc);
$reports->addOption($bauReport);
$reports->addOption($inactive);
$reports->addOption(new NavbarDivider('accessCdi accessPmo accessRepFullPerson'));
$reports->addOption($locMismatch);
$reports->addOption($odcNotInVbac);
$reports->addOption($assetRemoval);

$outages = new NavbarOption($plannedOutagesLabel, 'ppo_PlannedOutages.php','accessCdi accessPmo accessFm accessUser ');

// $privacy = new NavbarOption('Privacy','https://w3.ibm.com/w3publisher/w3-privacy-notice','accessCdi accessPmo accessFm accessUser ');

// bind all menus together
$navbar->addMenu($cdiAdmin);
$navbar->addMenu($adminMenu);
$navbar->addMenu($agileMenu);
$navbar->addMenu($boarding);
$navbar->addMenu($access);
$navbar->addMenu($reports);
$navbar->addOption($outages);
// $navbar->addOption($privacy);

$navbar->createNavbar($page);
$start = microtime(true);

error_log("to isFm:" . (float)($beginNavBar-$start));

$isFm   = personTable::isManager($_SESSION['ssoEmail']) ? ".not('.accessFm')" : null;

$elapsed = microtime(true);

error_log("isFm:" . (float)($elapsed-$start));

$isCdi  = $OKTAGroups->inAGroup($_SESSION['cdiBgAz'],  $_SESSION['ssoEmail']) ? ".not('.accessCdi')" : null;
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("CDI:" . (float)($elapsed-$start));

$isPmo  = $OKTAGroups->inAGroup($_SESSION['pmoBgAz'],  $_SESSION['ssoEmail']) ? ".not('.accessPmo')" : null;
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("Pmo:" . (float)($elapsed-$start));

$isPes  = $OKTAGroups->inAGroup($_SESSION['pesBgAz'],  $_SESSION['ssoEmail']) ? ".not('.accessPes')" : null;
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("Pes:" . (float)($elapsed-$start));

$isRep1  = $OKTAGroups->inAGroup($_SESSION['rfpBgAz'],  $_SESSION['ssoEmail']) ? ".not('.accessRepFullPerson')" : null;
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("Rep1:" . (float)($elapsed-$start));

$isRes   = $OKTAGroups->inAGroup($_SESSION['rsBgAz'],  $_SESSION['ssoEmail'],3) ? ".not('.accessRes')" : null;
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("vent:" . (float)($elapsed-$start));

$isUser = ".not('.accessUser')";
$isRequestor = $OKTAGroups->inAGroup($_SESSION['reqBgAz'], $_SESSION['ssoEmail']);
$elapsed = microtime(true);

$elapsed = microtime(true);
error_log("vbac:" . (float)($elapsed-$start));

$isCdi   = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessCdi')" : $isCdi;
$isFm    = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessFm')"  : $isFm;
$isPmo   = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessPmo')" : $isPmo;
$isPes   = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessPes')" : $isPes;
$isRep1  = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessRepFullPerson')" : $isRep1;
$isRes   = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? ".not('.accessRes')" : $isRes;

$isFm = $isPmo ? null : $isFm; // If they are PMO it don't matter if they are FM
$isFm = $isCdi ? null : $isFm; // If they are CDI it don't matter if they are FM

$_SESSION['isFm']   = !empty($isFm)   ? true : false;
$_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
$_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
$_SESSION['isPes']  = !empty($isPes)  ? true : false;
$_SESSION['isUser'] = !empty($isUser) ? true : false;
$_SESSION['isRep1'] = !empty($isRep1) ? true : false;
$_SESSION['isRes']  = !empty($isRes)  ? true : false;

$plannedOutagesId = str_replace(" ","_",$plannedOutagesLabel);
$odcStaff = personTable::countOdcStaff();
// $odcStaff = '';
?>
<script>

$('.navbarMenuOption')<?=$isFm?><?=$isPmo?><?=$isPes?><?=$isCdi?><?=$isUser?><?=$isRep1?><?=$isRes?>.remove();
$('.navbarMenu').not(':has(li)').remove();

$('li[data-pagename="<?=$page;?>"]').addClass('active').closest('li.dropdown').addClass('active');
<?php

if($page != "index.php" && substr($page,0,3)!='cdi'){
    ?>
	var pageAllowed = $('li[data-pagename="<?=$page;?>"]').length;
	if(pageAllowed==0 ){
		window.location.replace('index.php');
		alert("You do not have access to:<?=$page?>");
	}
	<?php
}

$rep = null;
!empty($isRep1) ? $rep .= '<small>(R1)</small>' : '';

$requestor= $isRequestor ? "+" : null;
// var_dump($requestor);
?>

$(document).ready(function () {

    $('button.accessRestrict')<?=$isFm?><?=$isPmo?><?=$isPes?><?=$isCdi?><?=$isUser?><?=$isRep1?><?=$isRes?>.remove();

	<?php 
	
	$userLevel = '';
	$userLevel.= !empty($isUser)? 'User:'. $requestor : null;
	$userLevel.= !empty($isFm)  ? 'Fm:'  . $requestor : null;
	$userLevel.= !empty($isRes) ? 'Res:' . $requestor : null;
	$userLevel.= !empty($isPmo) ? 'Pmo:' . $requestor : null;
	$userLevel.= !empty($isCdi) ? 'Cdi:' . $requestor : null;
	$userLevel.= !empty($isPes) ? 'Pes:' . $requestor : null;
	$userLevel.= $rep;
	?>

	$('#userLevel').html('<?=$userLevel?>');

	console.log($('#userLevel').html());

    var poContent = $('#<?=$plannedOutagesId?> a').html();
	var badgedContent = poContent + "&nbsp;" + "<?=$plannedOutages->getBadge();?>";
	$('#<?=$plannedOutagesId?> a').html(badgedContent);
	$('#odcPopulation').html(<?=$odcStaff?>);
});
</script>

<?php 
	$elapsed = microtime(true);
	error_log("Navbar ended:" . (float)($elapsed-$beginNavBar));
?>
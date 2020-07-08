<?php
use itdq\PlannedOutages;
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;
use vbac\personTable;
use itdq\NavbarDivider;
include ('itdq/PlannedOutages.php');
include ('itdq/DbTable.php');
$plannedOutagesLabel = "Planned Outages";
$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(lcfirst(strtoupper($_ENV['environment'])),"index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$navbar = new Navbar($navBarImage, $navBarBrand,$navBarSearch);

$cdiAdmin       = new NavbarMenu("CDI Admin");
$trace          = new NavbarOption('View Trace','pi_trace.php','accessCdi');
$traceControl   = new NavbarOption('Trace Control','pi_traceControl.php','accessCdi');
$traceDelete    = new NavbarOption('Trace Deletion', 'pi_traceDelete.php','accessCdi');
$revalidation   = new NavbarOption('Batch Reval','batchJobs/revalidate.php','accessCdi');
$recheck        = new NavbarOption('Batch ReCheck','batchJobs/recheckPotentialLeavers.php','accessCdi');
$cbn            = new NavbarOption('Initiate CBN','batchJobs/sendCbnEmail.php','accessCdi');
$link           = new NavbarOption('Link IBMer to PreB','pi_linkIbmerToPreboarder.php','accessCdi');
$emailDlp       = new NavbarOption('Email DLP','batchJobs/emailWorkflowTracker.php','accessCdi');
$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);
$cdiAdmin->addOption($revalidation);
$cdiAdmin->addOption($cbn);
$cdiAdmin->addOption($recheck);
$cdiAdmin->addOption($link);
$cdiAdmin->addOption($emailDlp);


$adminMenu      = new NavbarMenu('vBac Admin');
$pmo            = new NavbarOption('Person Portal', 'pa_pmo.php','accessCdi accessPmo accessFm accessUser');
$pmoLite        = new NavbarOption('Person Portal (Lite)', 'pa_pmo_lite.php','accessCdi accessPmo accessFm accessUser');
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
$odcDataUpload  = new NavbarOption('ODC Access Upload', 'pc_odcAccessUpload.php','accessCdi accessPmo');
// $email          = new NavbarOption('Email Log', 'pi_emailLog.php','accessCdi');
$tribes         = new NavbarOption('Tribes','pa_agileTribes.php','accessCdi accessPmo ');
$squads         = new NavbarOption('Squad(Current)','pa_agileSquads.php','accessCdi accessPmo ');
// $squadsOld         = new NavbarOption('Squad(Old)','pa_agileSquadsOld.php','accessCdi accessPmo ');
$squadALog       = new NavbarOption('Squadalog', 'pa_squadalog.php','accessCdi accessPmo accessFm');

$adminMenu->addOption($pmo);
$adminMenu->addOption($pmoLite);
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
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($odcDataUpload);
$adminMenu->addOption( new NavbarDivider('accessPmo accessCdi'));
$adminMenu->addOption($tribes);
$adminMenu->addOption($squads);
// $adminMenu->addOption($squadsOld);
$adminMenu->addOption($squadALog);





// $agileMenu      = new NavbarMenu('Agile');


// $agileMenu->addOption($tribes);
// $agileMenu->addOption($squads);




$boarding       = new NavbarMenu('Boarding');
$onBoarding     = new NavbarOption('OnBoard','pb_onboard.php','accessCdi accessPmo accessFm');
$offBoarding    = new NavbarOption('OffBoard', 'pb_offboard.php','accessCdi accessPmo accessFm');
$boarding->addOption($onBoarding);
$boarding->addOption($offBoarding);


$access         = new NavbarMenu('Access/Assets');
$assetPortal    = new NavbarOption('Asset Portal', 'pa_assetPortal.php','accessCdi accessPmo accessFm accessUser');
$requestAssets  = new NavbarOption('Request Digital Assets', 'pc_assetRequest.php','accessCdi accessPmo accessFm accessUser');
$returnAsset    = new NavbarOption('Return Digital Assets', 'pc_assetReturn.php','accessCdi accessPmo accessFm accessUser');
$requestAccess  = new NavbarOption('Request/Return AD Group Access', 'pc_accessRequest.php','accessCdi accessPmo accessFm accessUser');
$dlpRecord      = new NavbarOption('DLP Licences', 'pc_dlpRecord.php','accessCdi accessPmo accessFm accessUser');
$iamAdmin       = new NavbarOption('IAM Admin', 'pc_iamAdmin.php','accessCdi accessPmo');
$softRsaToken   =new NavbarOption('Soft RSA Token', 'https://w3.ibm.com/tools/cio/forms/landing/org/app/5e4da88f-3c98-4daf-8a76-4031d661db21/launch/index.html?form=F_SoftRSARequestForm','accessCdi accessPmo accessFm accessUser');
// https://w3.ibm.com/tools/cio/forms/landing/org/app/5e4da88f-3c98-4daf-8a76-4031d661db21/launch/index.html?form=F_SoftRSARequestForm
// $assetPortal    = new NavbarOption('Asset Portal', 'pa_assetPortal.php','accessCdi accessPmo  ');
// $requestAssets  = new NavbarOption('Request/Return Digital Assets', 'pc_assetRequest.php','accessCdi accessPmo  ');
// $requestAccess  = new NavbarOption('Request/Return AD Group Access', 'pc_accessRequest.php','accessCdi accessPmo  ');
// $iamAdmin       = new NavbarOption('IAM Admin', 'pc_iamAdmin.php','accessCdi accessPmo');
$access->addOption($assetPortal);
$access->addOption($requestAssets);
$access->addOption($returnAsset);
$access->addOption($requestAccess);
$access->addOption($dlpRecord);
$access->addOption($iamAdmin);
$access->addOption($softRsaToken    );


$reports         = new NavbarMenu('Downloadable Reports');
// $original        = new NavbarOption('Person Details - Original', 'pr_personDetails.php','accessCdi accessPmo accessRepFullPerson');
$fullExtract     = new NavbarOption('Person Details - Full', 'pr_personDetailsFull.php','accessCdi accessPmo accessRepFullPerson');
$active          = new NavbarOption('Person Details - Active', 'pr_personDetailsActive.php','accessCdi accessPmo accessRepFullPerson');
$activeOdc       = new NavbarOption('Person Details - Active(ODC)<span id="odcPopulation" class="badge">**</span>', 'pr_personDetailsActiveOdc.php','accessCdi accessPmo accessRepFullPerson');
$bauReport       = new NavbarOption('Person Details - BAU Report', 'pr_bau.php','accessCdi accessPmo accessRepFullPerson');
$inactive        = new NavbarOption('Person Details - Inactive', 'pr_personDetailsInactive.php','accessCdi accessPmo accessRepFullPerson');
$locMismatch     = new NavbarOption('Location Mismatch', 'pr_odcMismatchReport.php','accessCdi accessPmo accessRepFullPerson');
$odcNotInVbac    = new NavbarOption('ODC Access but no Vbac Record', 'pr_odcAccessMissingFromVbac.php','accessCdi accessPmo accessRepFullPerson');
$assetRemoval    = new NavbarOption('Asset Removal', 'pr_assetRemovalReport.php','accessCdi accessPmo');
$reports->addOption($fullExtract);
$reports->addOption($active);
$reports->addOption($activeOdc);
$reports->addOption($bauReport);
$reports->addOption($inactive);
$reports->addOption(new NavbarDivider('accessCdi accessPmo accessRepFullPerson'));
$reports->addOption($locMismatch);
$reports->addOption($odcNotInVbac);
$reports->addOption($assetRemoval);


$navbar->addMenu($cdiAdmin);
$navbar->addMenu($adminMenu);
// $navbar->addMenu($agileMenu);
$navbar->addMenu($boarding);
$navbar->addMenu($access);
$navbar->addMenu($reports);

$outages = new NavbarOption($plannedOutagesLabel, 'ppo_PlannedOutages.php','accessCdi accessPmo accessFm accessUser ');
$navbar->addOption($outages);

$privacy = new NavbarOption('Privacy','https://w3.ibm.com/w3publisher/w3-privacy-notice','accessCdi accessPmo accessFm accessUser ');
$navbar->addOption($privacy);


$navbar->createNavbar($page);

$isFm   = personTable::isManager($_SESSION['ssoEmail'])                 ? ".not('.accessFm')" : null;
$isCdi  = employee_in_group($_SESSION['cdiBg'],  $_SESSION['ssoEmail']) ? ".not('.accessCdi')" : null;
$isPmo  = employee_in_group($_SESSION['pmoBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPmo')" : null;
$isPes  = employee_in_group($_SESSION['pesBg'],  $_SESSION['ssoEmail']) ? ".not('.accessPes')" : null;
$isRep1  = employee_in_group('vbac_Reports_Full_Person',  $_SESSION['ssoEmail']) ? ".not('.accessRepFullPerson')" : null;
$isRes   = employee_in_group('ventus_resource_strategy',  $_SESSION['ssoEmail'],3) ? ".not('.accessRes')" : null;

$isUser = ".not('.accessUser')";
$isRequestor = employee_in_group('vbac_requestor', $_SESSION['ssoEmail']);

$isCdi   = stripos($_ENV['environment'], 'dev') ? ".not('.accessCdi')"  : $isCdi;
$isPmo   = stripos($_ENV['environment'], 'dev')  ? ".not('.accessPmo')" : $isPmo;
$isPes   = stripos($_ENV['environment'], 'dev')  ? ".not('.accessPes')" : $isPes;
$isRep1   = stripos($_ENV['environment'], 'dev')  ? ".not('.accessRepFullPerson')" : $isRep1;
$isRes   = stripos($_ENV['environment'], 'dev')  ? ".not('.accessRes')" : $isRes;

$isFm = $isPmo ? null : $isFm; // If they are PMO it don't matter if they are FM



// // Test PES Cancel
// $isPmo = false;
// $isCdi = false;
// $isPes = ".not('.accessPes')" ;
// $isRep1 = false;
// $isRes = false;
// $isFm = false;



$GLOBALS['isFm']   = !empty($isFm)   ? true : false;
$GLOBALS['isCdi']  = !empty($isCdi)  ? true : false;
$GLOBALS['isPmo']  = !empty($isPmo)  ? true : false;
$GLOBALS['isPes']  = !empty($isPes)  ? true : false;
$GLOBALS['isUser'] = !empty($isUser) ? true : false;
$GLOBALS['isRep1'] = !empty($isRep1) ? true : false;
$GLOBALS['isRes']  = !empty($isRes) ? true : false;


$plannedOutagesId = str_replace(" ","_",$plannedOutagesLabel);
$odcStaff = personTable::countOdcStaff();
?>
<script>

console.log('in navbar');

$('.navbarMenuOption')<?=$isFm?><?=$isPmo?><?=$isPes?><?=$isCdi?><?=$isUser?><?=$isRep1?><?=$isRes?>.remove();
$('.navbarMenu').not(':has(li)').remove();

$('li[data-pagename="<?=$page;?>"]').addClass('active').closest('li.dropdown').addClass('active');
<?php



if($page != "index.php" && substr($page,0,3)!='cdi'){
    ?>

    console.log('<?=$page;?>');

	var pageAllowed = $('li[data-pagename="<?=$page;?>"]').length;

    console.log('li[data-pagename="<?=$page;?>"]');
    console.log($('li[data-pagename="<?=$page;?>"]'));



	if(pageAllowed==0 ){
		window.location.replace('index.php');
		alert("You do not have access to:<?=$page?>");
	}
	<?php
}

$rep = null;
!empty($isRep1) ? $rep .= '<small>(R1)</small>' : '';

$requestor= $isRequestor ? "+" : null;
?>

$(document).ready(function () {

    $('button.accessRestrict')<?=$isFm?><?=$isPmo?><?=$isPes?><?=$isCdi?><?=$isUser?><?=$isRep1?><?=$isRes?>.remove();

    <?=!empty($isUser) ? '$("#userLevel").html("User' . $requestor . '&nbsp;' . $rep . '");console.log("user");' : null;?>

    <?=!empty($isFm)   ? '$("#userLevel").html("Func.Mgr.' . $requestor . '&nbsp;' . $rep . '");console.log("fm");' : null;?>
    <?=!empty($isRes)  ? '$("#userLevel").html("Req' . $requestor . '&nbsp;' . $rep . '");console.log("req");' : null;?>
    <?=!empty($isPmo)  ? '$("#userLevel").html("PMO' . $requestor . '&nbsp;' . $rep . '");console.log("pmo");' : null;?>
    <?=!empty($isCdi)  ? '$("#userLevel").html("CDI' . $requestor . '&nbsp;' . $rep . '");console.log("cdi");' : null;?>
    <?=!empty($isPes)  ? '$("#userLevel").html("PES' . $requestor . '&nbsp;' . $rep . '");console.log("pes");' : null;?>


    var poContent = $('#<?=$plannedOutagesId?> a').html();
	var badgedContent = poContent + "&nbsp;" + "<?=$plannedOutages->getBadge();?>";
	$('#<?=$plannedOutagesId?> a').html(badgedContent);
	$('#odcPopulation').html(<?=$odcStaff?>);
});
</script>


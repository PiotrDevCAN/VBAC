<?php
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(lcfirst(strtoupper($_SERVER['environment'])),"index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$navbar = new Navbar($navBarImage, $navBarBrand,$navBarSearch);

$cdiAdmin       = new NavbarMenu("CDI Admin");
$trace          = new NavbarOption('View Trace','pi_trace.php','accessCdi');
$traceControl   = new NavbarOption('Trace Control','pi_traceControl.php','accessCdi');
$traceDelete    = new NavbarOption('Trace Deletion', 'pi_traceDelete.php','accessCdi');
$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);


$adminMenu      = new NavbarMenu('vBac Admin');
$pmo            = new NavbarOption('Portal', 'pa_pmo.php','accessCdi accessPmo accessFm');
$revalidation   = new NavbarOption('Revalidation','pa_revalidation.php','accessCdi accessPmo');
$control        = new NavbarOption('Control', 'pa_control.php','accessCdi accessPmo');
$emailLog       = new NavbarOption('Email Log', 'pi_emailLog','accessCdi');
$adminMenu->addOption($pmo);
$adminMenu->addOption($revalidation);
$adminMenu->addOption($control);
$adminMenu->addOption($emailLog);

$boarding       = new NavbarMenu('Boarding');
$onBoarding     = new NavbarOption('OnBoard','pb_onboard.php','accessCdi accessPmo accessFm');
$offBoarding    = new NavbarOption('OffBoard', 'pb_offboard.php','accessCdi accessPmo accessFm');
$boarding->addOption($onBoarding);
$boarding->addOption($offBoarding);


$access         = new NavbarMenu('Access');
$request        = new NavbarOption('Request', 'pc_accesssRequest.php','accessCdi accessPmo accessFm accessUser');
$iamAdmin       = new NavbarOption('IAM Admin', 'pc_iamAdmin.php','accessCdi accessPmo accessFm accessUser');
$access->addOption($request);
$access->addOption($iamAdmin);


$navbar->addMenu($cdiAdmin);
$navbar->addMenu($adminMenu);
$navbar->addMenu($boarding);
$navbar->addMenu($access);

$outages = new NavbarOption('Planned Outages', 'ppo_PlannedOutages.php');

$navbar->addOption($outages);


$navbar->createNavbar($page);

?>




<script type="text/javascript">
  $('li[data-pagename="<?=$page;?>"]').addClass('active').closest('li.dropdown').addClass('active');
</script>

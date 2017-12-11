<?php
$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(lcfirst(strtoupper($_SERVER['environment'])),"index.php");
$navBarSearch = false;


$navBar_data = array(
    array("ITDQ Admin",'dropDown'),
    array("View Trace", "pi_trace.php"),
    array("Trace Control", "pi_traceControl.php"),
    array("Trace Deletion", "pi_traceDelete.php"),
    array("",'endOfDropDown'),

    array("vBac Admin",'dropDown'),
    array('PMO','pa_pmo.php'),
    array('Revalidation','pa_revalidation.php'),
    array('Control','pa_control.php'),
    array("",'endOfDropDown'),

    array("Boarding",'dropDown'),
    array('On Board','pb_onboard.php'),
    array('Off Board','pb_offboard.php'),
    array("",'endOfDropDown'),

    array("Access",'dropDown'),
    array('Access Request','pc_accessRequest.php'),
    array("",'endOfDropDown'),

    array('Planned Outages','ppo_PlannedOutages.php')

);

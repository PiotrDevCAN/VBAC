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
    array("",'endOfDropDown'),

    array("On Boarding",'dropDown'),
    array('Board Individual','pon_individual.php'),
    array("",'endOfDropDown'),

    array("Off Boarding",'dropDown'),
    array('Off Board Individual','poff_individual.php'),
    array("",'endOfDropDown'),

    array('Planned Outages','ppo_PlannedOutages.php')

);

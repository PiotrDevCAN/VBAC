<?php
$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(strtoupper($_SERVER['environment']),"index.php");
$navBarSearch = false;


$navBar_data = array(
    array("ITDQ Admin",'dropDown'),
    array("View Trace", "pi_trace.php"),
    array("Trace Control", "pi_traceControl.php"),
    array("Trace Deletion", "pi_traceDelete.php"),
    array("",'endOfDropDown'),

    array("vBac Admin",'dropDown'),
    array('Upload','pa_upload.php'),
    array("",'endOfDropDown'),

    array("Define",'dropDown'),
    array("",'endOfDropDown'),

    array("Manage",'dropDown'),
    array("",'endOfDropDown'),

    array('Planned Outages','ppo_PlannedOutages.php')

);

<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We have recently made changes to our LBG contracts, this includes the requirement for PES revalidation.  It is now within 8 weeks of your revalidation date.</p>';
$pesEmail.= '<p>If you would like to retain your LBG PES, please see below the requirements.</p>';
$pesEmail.= '<p>Please return the following documents:</p>';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully completed Global Application Form <Global Application Form>(attached)</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p>If you have any questions please contact the PES Team.';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
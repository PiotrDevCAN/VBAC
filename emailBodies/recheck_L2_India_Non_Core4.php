<?php
$pesEmail = 'Hello &&firstName&&,';

$pesEmail.= '<p>We have recently made changes to our LBG contracts, this includes the requirement for PES revalidation.  It is now within 8 weeks of your revalidation date.';
$pesEmail.= '<p>If you would like to retain your LBG PES, please see below the requirements.';
$pesEmail.= '<p>Please return the following documents:';
$pesEmail.= '<ul>';
$pesEmail.= '<li>All - Fully completed global application form - <global application form>(attached)';
$pesEmail.= '<li>Staff Living and working in India please complete the attached ODC form';
$pesEmail.= '<li>Staff Living and working in any country outside of UK, Poland, Korea, or India,  please complete the attached Owens form';
$pesEmail.= '</ul>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.';

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
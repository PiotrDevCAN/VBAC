<?php

$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We believe you are about to engage on the IBM/Lloyds account.  IBM are contractually obliged to ensure that anyone engaging on the Lloyds account is PES cleared.  To allow us to process this requirement, please return the below required documents to us <b>as one attachment</b> at your earliest convenience.</p>';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully Completed Application Form <b>(Attached)</b>';
$pesEmail.= '<br/><span style=\'color:red\'>Omissions & inaccuracies in your application form may prevent your PES clearance.</span></li>';
$pesEmail.= '<li>An email from your current manager confirming the start date of your current employment.<br/><b>*Further information and evidence</b> will be required if it is <b>less</b> than 5 years ago*</li>';
$pesEmail.= '<li>A Certified copy of the photo page of your Passport along with your VISA/Work Permit if required</li>';
$pesEmail.= '<li>A Certified copy of your current full Driving Licence photocard or utility bill (not mobile)/Bank statement (less than 3 months old) - as evidence of your current address</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p><b>The Certification MUST be done by an IBM\'er,</b> to confirm that they have seen the original document.  The following statement should be <b>handwritten</b< on <b>each document</b>, on the <b>same side as the image</b>.</p>';
$pesEmail.= '<p style="text-align:center;color:red"><b>True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date<br/>Signature of certifier</b></p>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team on <a href=\'mailto:LBGVETPR@uk.ibm.com\'>LBGVETPR@uk.ibm.com</a></p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
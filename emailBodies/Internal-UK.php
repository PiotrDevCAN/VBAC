<?php

$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We believe you are about to engage on the IBM/Lloyds account.  IBM are contractually obliged to ensure that anyone engaging on the Lloyds account is PES cleared.  To allow us to process this requirement, please return the below required documents to us <b>as one attachment</b> at your earliest convenience.</p>';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully completed Application Form <b>(Attached)</b><br/><span style="color:red">Omissions & inaccuracies in your application form may prevent your PES clearance.</span></li>';
$pesEmail.= '<li>An email from your IBM Manager confirming your IBM Start Date <b>OR</b> a screen print of the relevant page of the "About You" system.<br/><b>Further information and evidence</b> will be required if it is <b>less</b> than 5 years ago.</li>';
$pesEmail.= '<li>A Certified copy of the photo page of your Passport along with your VISA/Work Permit if required</li>';
$pesEmail.= '<li>A Certified copy of your ID Card/Driving Licence photocard that shows your current address (other documents can be provided, please contact PES team if required).</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p style="text-align:center"><b>The Certification MUST be done by another IBM\'er</b>, to confirm that they have seen the original document.  The following statement should be <b>handwritten</b> on <b>each document</b>, on the <b>same side as the image</b>.</span></p>';
$pesEmail.= '<p style="text-align:center;color:red">True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date</br>Signature of certifier</span></p>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team on <a href=\'mailto:LBGVETPR@uk.ibm.com\'>LBGVETPR@uk.ibm.com</a></p>';

$pesEmail.= '<p><b>Please Note</b></p>';
$pesEmail.= "<p>Due to the recent situation we understand that many people will be unable to meet with fellow IBM'ers to have their documents Certified.  We have implemented a  'provisional clearance' process and wil accept documents without certification, at this time.  However these documents will require to be certified as soon as the restrictions are lifted.</p>";
$pesEmail.= "<p>This will not give you full PES clearance for the account, so if you can get them certified correctly (ie another IBM'er seeing the document and signing) please do so.</p>";

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');

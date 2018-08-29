<?php

$pesExternalIndianEmail = 'Hello &&firstName&&,';
$pesExternalIndianEmail.= '<p>We believe you are about to engage on the IBM/Lloyds account.  IBM are contractually obliged to ensure that anyone engaging on the Lloyds account is PES cleared.  To allow us to process this requirement, please return the below required documents to us <b>as one attachment</b> at your earliest convenience.</p>';
$pesExternalIndianEmail.= '<ul>';
$pesExternalIndianEmail.= '<li>Fully Completed Application Form <b>(Attached)</b>';
$pesExternalIndianEmail.= '<br/><span style=\'color:red\'>Omissions & inaccuracies in your application form may prevent your PES clearance.</span></li>';
$pesExternalIndianEmail.= '<li>Fully completed ODC application form, with full 5 years address history (please return this as <b>one separate .pdf document with a physical signature</b> - Please do not have this document certified by another IBM\'er) <b>(Attached)</b></li>';
$pesExternalIndianEmail.= '<li>A copy of the photo page of your Passport along with your VISA/Work Permit if required</li>';
$pesExternalIndianEmail.= '<li>A copy of <b>one</b> utility bill (not mobile) or <b>one</b> Bank/Credit Card Statement less than 3 months old (in your name, showing your current address)</li>';
$pesExternalIndianEmail.= '<li>Evidence of your last 5 years activity will be required.  If you were     employed/in education, please arrange to send your service/transfer certificates or mark sheets, alternatively you can arrange for your previous employer/education provider to email us directly with confirmation of your start and end dates.  If you were doing some other form of activity, please contact us with a summary and we can advise what evidence we will require.</li>';
$pesExternalIndianEmail.= '</ul>';
$pesExternalIndianEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team on <a href=\'mailto:LBGVETPR@uk.ibm.com\'>LBGVETPR@uk.ibm.com</a></p>';
$pesExternalIndianEmail.= '<p>Many Thanks for your cooperation</p>';
$pesExternalIndianEmail.= '<h5>Lloyds PES Team</h5>';




$pesExternalIndianEmailPattern = array('/&&firstName&&/');
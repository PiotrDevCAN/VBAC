<?php

$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We believe you are about to engage on the IBM/Lloyds account.  IBM are contractually obliged to ensure that anyone engaging on the Lloyds account is PES cleared.  To allow us to process this requirement, please return the below required documents to us <b>as one attachment</b> at your earliest convenience.</p>';
$pesEmail.= '<p style="color:blue;text-align:center">Please note, due to customer deadlines, you need to respond (with your document pack or questions) <b>within 10 working days</b>.  No response will mean escalation and potential removal from the PES process</p>';
$pesEmail.= '<p style="color:red;"><b>Omissions & inaccuracies in your application form may prevent your PES clearance.</b></p>';
$pesEmail.= '<p style="color:red;">If you are unsure about any of the information required in the above application form (or the below documents) <b>PLEASE</b> contact <a href=\'mailto:lbgvetpr@uk.ibm.com\'>lbgvetpr@uk.ibm.com</a> to discuss before completing/sending incorrect information as this can delay the PES process and hold up your availability for project.  
EVERY FIELD IN THE APPLICATION FORM IS <b>CRITICAL</b> FOR LBG ON-BOARDING THEREFORE PLEASE ENSURE THERE ARE NO GAPS.
</p>';
$pesEmail.= '<h3>Next Steps</h3>';
$pesEmail.= '<ol>';
$pesEmail.= '<li><b>Read carefully Word Docs <b>Czech Republic - IBM Lloyds Bank PES Application Form v1.1</b> (Attached)</li>';
$pesEmail.= '<li>Fill in the <b>IBM Lloyds Bank PES Application Form v1.1</b> (Attached) signature has to have the see date as in the database ("I confirm" button, see below) </li>';
$pesEmail.= '<li>Push "I confirm" to sign electronically in the database (Delivery Centre CE Data Privacy database <a href="Notes://D06DBL067/80257ACC0030BBCA/">Notes://D06DBL067/80257ACC0030BBCA/</a>)</li>';
$pesEmail.= '<li><b>Take your ID Card/Passport to HR Admin (IBM/MPW/Natek)</b>. HR Admin will then copy and certify the document which should be scanned and emailed to this task ID along with the application form</li>';
$pesEmail.= '<li>To have the criminal record check performed, please contact promptly the Data Privacy Office at DC CE Data Privacy/Czech Republic/IBM, this request will be processed via Data Privacy Office.</li>';
$pesEmail.= '</ol>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team on <a href=\'mailto:LBGVETPR@uk.ibm.com\'>LBGVETPR@uk.ibm.com</a></p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
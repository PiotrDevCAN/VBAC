<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We have recently made changes to our LBG contracts, this includes the requirement for PES revalidation.  It is now within 8 weeks of your revalidation date.</p>';
$pesEmail.= '<p>If you would like to retain your LBG PES, please see below the requirements.</p>';
$pesEmail.= '<p>Please return the following documents:</p>';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully completed Global Application Form <Global Application Form v2.1>(attached)</li>';
$pesEmail.= '<li>If you are living and working in India - fully completed ODC application form - <ODC form>(attached)</li>';
$pesEmail.= '<li>If you are living and working in a country outside of UK, Poland, Korea or India - Fully completed Owens International Form <owens form>(attached)</li>';
$pesEmail.= '<li>A Certified copy of the photopage of your Passport.</li>';
$pesEmail.= '<li>A Certified copy of a utility bill (not mobile)/Bank/credit Card statement (less than 3 months old) - as evidence of your current address</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p>The Certification MUST be done by another IBM\'er, to confirm that they have seen the original document.  The following statement should be handwritten on each document, on the same side as the image.</p>';
$pesEmail.= '<p style="text-align:center;color:red">True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date</br>Signature of certifier</span></p>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.</p>';
$pesEmail.= '<p><i>We understand you will probably be unable to get documents certified, due to the recent situation.  Therefore, we have implemented a \'provisional clearance\' process and we are accepting all documents without certification - however these documents will need to be certified as soon as the restrictions are lifted or as soon as you can.</i></p>';
$pesEmail.= '<p><i>Please note that this will not give your full PES clearance for the account, so if you can get them certified correctly (ie another IBM\'er seeing the document and signing) please do so.  Can you let us know if you can get your documents certified or if you intend to proceed with the provisional acceptance of your documents.</i></p>';

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We have contacted you a number of times regarding your LBG PES process. We require further information or documents to move forward with your clearance and we have not received a response.</p>';
$pesEmail.= '<p>If we do not have your documents/information within the next 4 working days, we will have to remove you from this process.<p>';
$pesEmail.= '<p>If you have questions or concerns please contact us to discuss before any action is taken - as we can help you move forward with this.</p>';

$pesEmail.= '<p><b>Please Note</b></p>';
$pesEmail.= "<p>Due to the recent situation we understand that many people will be unable to meet with fellow IBM'ers to have their documents Certified.  We have implemented a  'provisional clearance' process and wil accept documents without certification, at this time.  However these documents will require to be certified as soon as the restrictions are lifted.</p>";
$pesEmail.= "<p>This will not give you full PES clearance for the account, so if you can get them certified correctly (ie another IBM'er seeing the document and signing) please do so.</p>";

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
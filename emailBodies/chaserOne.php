<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>A short while ago we contacted you regarding your LBG PES process. We required further information or documents, however we do not appear to have received a response.</p>';
$pesEmail.= '<p>Please can you reply at your earliest convenience or contact us with any questions you may have.</p>';
$pesEmail.= '<p><b>Please Notes</b></p>';
$pesEmail.= "<p>Due to the recent situation we understand that many people will be unable to meet with fellow IBM'ers to have their documents Certified.  We have implemented a  'provisional clearance' process and wil accept documents without certification, at this time.  However these documents will require to be certified as soon as the restrictions are lifted.</p>";
$pesEmail.= "<p>This will not give you full PES clearance for the account, so if you can get them certified correctly (ie another IBM'er seeing the document and signing) please do so.</p>";
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We have previously contacted you a couple of times regarding your LBG PES process. We require further information or documents to move forward with your clearance and we have not received a response.</p>';
$pesEmail.= '<p>Please can you reply at your earliest convenience or contact us with any questions you may have.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>A short while ago we contacted you regarding your LBG PES process. We required further information or documents, however we do not appear to have received a response.</p>';
$pesEmail.= '<p>Please can you reply at your earliest convenience or contact us with any questions you may have.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');
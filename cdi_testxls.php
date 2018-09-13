<?php

use vbac\pesEmail;

$pesEmailObj = new pesEmail();

$emailDetails = $pesEmailObj->getEmailDetails('fred.smith@uk.ibm.com', 'United Kingdom');
var_dump($emailDetails['attachmentFileNames']);
var_dump($emailDetails['filename']);

die('here');

$emailResponse = $pesEmailObj->sendPesEmail('fred','smith','fred.smith@uk.ibm.com', 'United Kingdom');

var_dump($emailResponse);
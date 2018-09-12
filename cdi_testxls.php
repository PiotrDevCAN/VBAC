<?php

use vbac\pesEmail;

$pesEmailObj = new pesEmail();
$emailResponse = $pesEmailObj->sendPesEmail('fred','smith','fred.smith@uk.ibm.com', 'United Kingdom');


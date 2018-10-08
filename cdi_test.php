<?php

echo "<pre>";

var_dump(scandir('..'));

var_dump(scandir('../'));

var_dump(scandir(('../public')));

var_dump(scandir(('../public/emailAttachments')));


$filename = "emailAttachments/Overseas Consent Form Owens (2).pdf";
$handle = fopen($filename, "r");
var_dump($handle);



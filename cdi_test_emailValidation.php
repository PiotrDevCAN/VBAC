<?php
use itdq\BlueMail;

$emailAddress = array($_ENV['devemailid'],'bert@ibm.com','fred@gmail.com');



function validateIbmEmail($emailAddress){
    $domain = strtolower(substr($emailAddress,-7));
    $hasTheAt = stripos($emailAddress, '@');
    return $domain=='ibm.com' && $hasTheAt;
}


foreach ($emailAddress as $key => $person){

    if(!validateEmail($person)){
        unset($emailAddress[$key]);
    }

}



var_dump($emailAddress);
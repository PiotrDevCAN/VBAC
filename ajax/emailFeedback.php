<?php

use itdq\BlueMail;

if(!empty($_POST['feedback'])){
    $sendResponse = BlueMail::send_mail(array('IBM.LBG.IAM.Requests@uk.ibm.com'), 'vBAC Question', $_POST['feedback'],$_POST['sender']);
}
ob_clean();


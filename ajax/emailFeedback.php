<?php

use itdq\BlueMail;

if(!empty($_POST['feedback'])){
    $sendResponse = BlueMail::send_mail(array('lbgrep@uk.ibm.com'), 'vBAC Feedback', $_POST['feedback'],$_POST['sender']);
}
ob_clean();


<?php

use itdq\BlueMail;

$sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'vBAC Feedback', $_POST['feedback'],$_POST['sender']);

ob_clean();


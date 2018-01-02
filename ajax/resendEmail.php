<?php
use itdq\BlueMail;

$recordId = $_POST['recordId'];
$resendUrl = $_POST['url'];

$status = BlueMail::resend($recordId, $resendUrl);

ob_clean();
echo $status;

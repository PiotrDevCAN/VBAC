<?php
use itdq\BlueMail;

$recordId = $_POST['recordId'];
$statusUrl = $_POST['url'];
$prevStatus = $_POST['prevStatus'];

$status = BlueMail::getStatus($recordId, $statusUrl, $prevStatus);

ob_clean();
echo $status;

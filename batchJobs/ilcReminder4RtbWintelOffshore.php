<?php

use itdq\AuditTable;
use itdq\slack;

$slack = new slack();
$slack->sendMessageToChannel("<!channel> *AUTOMATED CLAIM REMINDER* - Please consider this as a reminder to submit your ILC (Claim) before the end of week business deadline. Remember your claim must accurately reflect ALL hours worked. This reminder is not an instruction to submit your Claim prior to the effort actually being performed. Thank you.", slack::CHANNEL_RTB_WINTEL_OFFSHORE);


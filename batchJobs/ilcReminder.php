<?php

use itdq\AuditTable;
use itdq\slack;

$slack = new slack();

AuditTable::audit("ilcReminder invoked.",AuditTable::RECORD_TYPE_AUDIT);
$result = $slack->slackApiPostMessage(slack::CHANNEL_GENERAL,"<!channel> *AUTOMATED CLAIM REMINDER* - Please consider this as a reminder to submit your ILC (Claim) before the end of week business deadline. Remember your claim must accurately reflect ALL hours worked. This reminder is not an instruction to submit your Claim prior to the effort actually being performed. Thank you.");
var_dump($result);
// old $slack->sendMessageToChannel("<!channel> *AUTOMATED CLAIM REMINDER* - Please consider this as a reminder to submit your ILC (Claim) before the end of week business deadline. Remember your claim must accurately reflect ALL hours worked. This reminder is not an instruction to submit your Claim prior to the effort actually being performed. Thank you.", slack::CHANNEL_GENERAL);



<?php
namespace ajax;

use itdq\OKTAGroups;
use itdq\OKTAUsers;

set_time_limit(0);

ob_start();

$groupName = !empty($_POST['GROUP']) ? trim($_POST['GROUP']) : null;
$emailAddress = !empty($_POST['EMAIL_ADDRESS']) ? trim($_POST['EMAIL_ADDRESS']) : null;

if (!empty($groupName) && !empty($emailAddress)) {

    $OKTAGroups = $GLOBALS['OKTAGroups'];
    $groupId = $OKTAGroups->getGroupId($groupName);

    $OKTAUsers = $GLOBALS['OKTAUsers'];
    $userId = $OKTAUsers->getUserID($emailAddress);

    $result = $OKTAGroups->addMember($groupId, $userId);

    $OKTAGroups->clearGroupMembersCache($groupName);
}

$success = true;
$messages = ob_get_clean();

$response = array('success'=>$success, 'group' => $_POST['GROUP'], 'email' => $_POST['EMAIL_ADDRESS'], 'messages'=>$messages);

echo json_encode($response);
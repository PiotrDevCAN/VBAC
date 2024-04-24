<?php
namespace ajax;

use itdq\OKTAGroups;

set_time_limit(0);

ob_start();

$groupId = !empty($_POST['GROUP_ID']) ? trim($_POST['GROUP_ID']) : null;
$userId = !empty($_POST['USER_ID']) ? trim($_POST['USER_ID']) : null;

if (!empty($groupId) && !empty($userId)) {

    $OKTAGroups = $GLOBALS['OKTAGroups'];
    $result = $OKTAGroups->removeMember($groupId, $userId);

    $groupName = $OKTAGroups->getGroupName($groupId);
    $OKTAGroups->clearGroupMembersCache($groupName);
}

$success = true;
$messages = ob_get_clean();

$response = array('success'=>$success, 'groupId' => $_POST['GROUP_ID'], 'userId' => $_POST['USER_ID'], 'messages'=>$messages);

echo json_encode($response);
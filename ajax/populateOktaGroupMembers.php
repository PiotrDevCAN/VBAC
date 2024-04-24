<?php

use itdq\Navbar;
use itdq\OKTAGroups;

set_time_limit(0);
ob_start();

$data = array();

$group = !empty($_POST['group']) ? trim($_POST['group']) : null;
if (!empty($group)) {
    $OKTAGroups = $GLOBALS['OKTAGroups'];
    $groupName = $GLOBALS['site']['allGroups'][$group];
    $groupId = $OKTAGroups->getGroupId($groupName);
    $membersData = $OKTAGroups->getGroupMembers($groupName);
    list('users' => $members, 'source' => $source) = $membersData;
}

foreach ($members as $key => $member){

    $userId = $member['id'];
    $displayName = $member['profile']['displayName'];
    $emailAddress = $member['profile']['email'];

    $row = array();
    $row['NAME'] = "";
    $row['NAME'] .="<button type='button' class='btn btn-success btn-xs editRecord ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-groupname='" . $groupName . "' data-groupid='" . $groupId . "' data-userid='" . $userId . "' data-emailid='" . $emailAddress . "'>
        <span data-toggle='tooltip' class='glyphicon glyphicon-edit ' aria-hidden='true' title='Edit Record'></span>
    </button>";
    $row['NAME'] .="&nbsp;<button type='button' class='btn btn-danger btn-xs deleteRecord ".Navbar::$ACCESS_CDI."' aria-label='Left Align' data-groupname='" . $groupName . "' data-groupid='" . $groupId . "' data-userid='" . $userId . "' data-emailid='" . $emailAddress . "'>
        <span class='glyphicon glyphicon-trash' aria-hidden='true'  data-toggle='tooltip' title='Delete Record' ></span>
    </button>";
    $row['NAME'] .= " <span>".$displayName."</span>";
    $row['EMAIL_ADDRESS'] = $emailAddress;
    $data[]  = $row;
}

$messages = ob_get_clean();
ob_start();

$response = array('data'=>$data,'messages'=>$messages);

ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

echo json_encode($response);


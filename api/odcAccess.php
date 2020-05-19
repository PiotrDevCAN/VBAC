<?php

use vbac\allTables;

if($_REQUEST['token']!= $token){
    return;
}

$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$row = false;

$sql = " SELECT count(*) as FOUND ";
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$ODC_ACCESS_LIVE . " AS O ";
$sql.= " WHERE 1=1 ";
$sql.= !empty($notesId) ? " AND lower(O.OWNER_NOTES_ID) = '" . db2_escape_string(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(O.OWNER_CNUM_ID) = '" . db2_escape_string(strtolower($cnum)) . "'; " : null;

$rs = db2_exec($_SESSION['conn'], $sql);

if($rs){
    $row = db2_fetch_assoc($rs);
    $found = $row['FOUND'] > 0 ? 'Yes' : 'No';
}

ob_clean();
echo json_encode($found);
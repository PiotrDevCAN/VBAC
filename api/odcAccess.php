<?php

use vbac\allTables;

if($_REQUEST['token']!= $token){
    return;
}

$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$row = false;

$sql = " SELECT count(*) as FOUND ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ODC_ACCESS_LIVE . " AS O ";
$sql.= " WHERE 1=1 ";
$sql.= !empty($notesId) ? " AND lower(O.OWNER_NOTES_ID) = '" . htmlspecialchars(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(O.OWNER_CNUM_ID) = '" . htmlspecialchars(strtolower($cnum)) . "'; " : null;

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if($rs){
    $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
    $found = $row['FOUND'] > 0 ? 'Yes' : 'No';
}

ob_clean();
echo json_encode($found);
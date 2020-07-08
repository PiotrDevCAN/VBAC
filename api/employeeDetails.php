<?php

use vbac\allTables;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$row = false;
$trimmedRow = false;

$sql = " SELECT P.*, M.EMAIL_ADDRESS as FM_EMAIL, M.NOTES_ID as FM_NOTES_ID,  trim(P.FIRST_NAME) CONCAT ' ' CONCAT trim(P.LAST_NAME) as FULL_NAME  ";
$sql.= " , CASE WHEN T.DESCRIPTION is not null then T.DESCRIPTION else P.EMPLOYEE_TYPE end as EMPLOYEE_TYPE ";
$sql.= " , SQ.SQUAD_NAME, SQ.SQUAD_LEADER, SQP.EMAIL_ADDRESS as SQUAD_LEADER_EMAIL ";
$sql.= " , TR.TRIBE_NAME, TR.TRIBE_LEADER, TRP.EMAIL_ADDRESS as TRIBE_LEADER_EMAIL, TR.ORGANISATION ";
$sql.= " , 'tbc_notes' as ITERATION_MGR,'tbc_email' as ITERATION_MGR_EMAIL  ";
$sql.= " FROM " . $_ENV['environment'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$PERSON . " AS M ";
$sql.= " ON P.FM_CNUM = M.CNUM ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$EMPLOYEE_TYPE_MAPPING .  " AS T ";
$sql.= " ON upper(P.EMPLOYEE_TYPE) = upper(T.CODE) ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$AGILE_SQUAD .  " AS SQ ";
$sql.= " ON P.SQUAD_NUMBER = SQ.SQUAD_NUMBER ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$AGILE_TRIBE .  " AS TR ";
$sql.= " ON SQ.TRIBE_NUMBER = SQ.TRIBE_NUMBER ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$PERSON .  " AS SQP ";
$sql.= " ON SQ.SQUAD_LEADER = SQP.NOTES_ID ";
$sql.= " LEFT JOIN " . $_ENV['environment'] . "." . allTables::$PERSON .  " AS TRP ";
$sql.= " ON TR.TRIBE_LEADER = TRP.NOTES_ID ";
$sql.= " WHERE 1=1 ";
$sql.= !empty($emailID) ? " AND lower(P.EMAIL_ADDRESS) = '" . db2_escape_string(strtolower($emailID)) . "'; " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . db2_escape_string(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . db2_escape_string(strtolower($cnum)) . "'; " : null;

error_log($sql);

$rs = db2_exec($GLOBALS['conn'], $sql);

if($rs){
    $row = db2_fetch_assoc($rs);
    $trimmedRow = array_map('trim',$row);
}

ob_clean();
echo json_encode($trimmedRow);
<?php
use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if($_REQUEST['token']!= $token){
    return;
}

ob_start();

$sql = " SELECT P.NOTES_ID  FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND " . personTable::activePersonPredicate(true, 'P');
$sql.= " AND FM_MANAGER_FLAG='Yes' ";
$sql.= " ORDER BY P.NOTES_ID ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if($rs){
    while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $notesIds[] = trim($row['NOTES_ID']);
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    return;
}

ob_clean();
echo json_encode($notesIds);

<?php
use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if($_REQUEST['token']!= $token){
    return;
}



$sql = " SELECT P.NOTES_ID  FROM " . $_SERVER['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND " . personTable::activePersonPredicate();
$sql.= " AND FM_MANAGER_FLAG='Yes' ";
$sql.= " ORDER BY P.NOTES_ID ";

$rs = db2_exec($_SESSION['conn'], $sql);

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $notesIds[] = trim($row['NOTES_ID']);
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
}

ob_clean();
echo json_encode($notesIds);

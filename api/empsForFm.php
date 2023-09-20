<?php
use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;

if($_REQUEST['token']!= $token){
    return;
}

$notesIds = array();
$activePersonPredicate = str_replace(array(' REV',' PES'), array(' P.REV',' P.PES'), personTable::activePersonPredicate());

$sql = " SELECT P.NOTES_ID  FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS F ";
$sql.= " ON P.FM_CNUM = F.CNUM ";

$sql.= " WHERE 1=1 AND trim(P.NOTES_ID) != ''  AND " . $activePersonPredicate;
$sql.= " AND F.NOTES_ID='" . htmlspecialchars($_REQUEST['fm_notes_id']) . "' ";
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
}

ob_clean();
echo json_encode($notesIds);

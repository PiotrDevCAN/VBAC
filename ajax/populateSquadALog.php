<?php

use vbac\personTable;
use vbac\allTables;
use itdq\DbTable;

set_time_limit(0);
ob_start();

$personTable = new personTable(allTables::$PERSON);

$sql = " SELECT distinct P.CNUM, P.NOTES_ID, P.ROLE_ON_THE_ACCOUNT as JRSS, AS1.SQUAD_TYPE, AS1.TRIBE_NUMBER as TRIBE, ";
$sql.= " AS1.SHIFT, AT.ITERATION_MGR,  AS1.SQUAD_LEADER, F.CNUM as FLL_CNUM, F.NOTES_ID as FLL_NOTES_ID, U.CNUM as SLL_CNUM, U.NOTES_ID as SLL_NOTES_ID, AS1.SQUAD_NUMBER, AS1.SQUAD_NAME ";
$sql.= " ,AT.TRIBE_NAME ";

$sql.= personTable::getTablesForQuery();

$sql.= " WHERE " . personTable::activePersonPredicate(true,"P");
$sql.= " AND ( P.SQUAD_NUMBER is not null  AND P.SQUAD_NUMBER > 0 ) ";

$data = array();
$rs = sqlsrv_query($GLOBALS['conn'], $sql, $data);

if(!$rs){
    DbTable::displayErrorMessage($rs, null, __FILE__, $sql);
}

while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
    $row = array_map('trim', $row);
    $cnum = $row['CNUM'];
    $row['CNUM'] = array('display'=>$row['CNUM'] . "<br/><small>" . $row['NOTES_ID'] . "</small>", 'sort'=>$row['CNUM']);
    $row['NOTES_ID'] = array('display'=>$row['NOTES_ID'] . "<br/><small>" . $cnum . "</small>", 'sort'=>$row['NOTES_ID']);
    $row['TRIBE'] = array('display'=>"Tribe " . $row['TRIBE'] . "<br/><small>Shift:" . $row['SHIFT'] . "</small>", 'sort'=>"Tribe " .$row['TRIBE']);
    $row['FLL'] = array('display'=>$row['FLL_NOTES_ID'] . "<br/><small>" . $row['FLL_CNUM'] . "</small>", 'sort'=>$row['FLL_NOTES_ID']);
    $row['SLL'] = array('display'=>$row['SLL_NOTES_ID'] . "<br/><small>" . $row['SLL_CNUM'] . "</small>", 'sort'=>$row['SLL_NOTES_ID']);
    $row['SQUAD'] = array('display'=>$row['SQUAD_NAME'] . "<br/><small>" . $row['SQUAD_NUMBER'] . "</small>",'sort'=>$row['SQUAD_NUMBER']);
    //unset($row['NOTES_ID']);
    //unset($row['FLL_CNUM']);
    //unset($row['FLL_NOTES_ID']);
    //unset($row['SLL_CNUM']);
    //unset($row['SLL_NOTES_ID']);
    unset($row['SQUAD_NUMBER']);
    $data[] = $row;
}
/* Free the statement resources. */
sqlsrv_free_stmt($rs);

$dataJsonAble = json_encode($data);
$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true),'sql'=>$sql);
 } else {
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();
    ob_start();
    echo $dirtyDetails;
    exit();
 }
ob_clean();
echo json_encode($response);
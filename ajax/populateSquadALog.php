<?php

use vbac\personTable;
use vbac\allTables;
use itdq\DbTable;

set_time_limit(0);
ob_start();

// session_start();

$personTable = new personTable(allTables::$PERSON);
// { "data": "CNUM" , "defaultContent": "" },
// { "data": "NOTES_ID" ,"defaultContent": "" },
// { "data": "JRSS"       ,"defaultContent": "<i>unknown</i>"},
// { "data": "SQUAD_TYPE", "defaultContent": "<i>unknown</i>" },
// { "data": "TRIBE", "defaultContent": "<i>unknown</i>" },
// { "data": "SHIFT", "defaultContent": "<i>unknown</i>" },
// { "data": "SQUAD_LEADER", "defaultContent": "<i>unknown</i>" },
// { "data": "FLL", "defaultContent": "" },
// { "data": "SLL", "defaultContent": "" },
// { "data": "SQUAD_NUMBER", "defaultContent": "" },

$sql = " SELECT distinct P.CNUM, P.NOTES_ID, P.ROLE_ON_THE_ACCOUNT as JRSS, S.SQUAD_TYPE, S.TRIBE_NUMBER as TRIBE, ";
$sql.= " S.SHIFT, T.ITERATION_MGR,  S.SQUAD_LEADER, F.CNUM as FLL_CNUM, F.NOTES_ID as FLL_NOTES_ID, U.CNUM as SLL_CNUM, U.NOTES_ID as SLL_NOTES_ID, S.SQUAD_NUMBER, S.SQUAD_NAME ";
$sql.= " ,T.TRIBE_NAME ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS F "; // lookup firstline
$sql.= " ON P.FM_CNUM = F.CNUM ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS U "; // lookup upline ( second line )
$sql.= " ON F.FM_CNUM = U.CNUM ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS S "; // lookup upline ( second line )
$sql.= " ON P.SQUAD_NUMBER  = S.SQUAD_NUMBER ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS T "; // lookup upline ( second line )
$sql.= " ON S.TRIBE_NUMBER  = T.TRIBE_NUMBER ";

$sql.= " WHERE " . personTable::activePersonPredicate(true,"P");
$sql.= " AND ( P.SQUAD_NUMBER is not null  AND P.SQUAD_NUMBER > 0 ) ";

$data = array();
$preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

$rs = sqlsrv_execute($preparedStmt);

if(!$rs){
    DbTable::displayErrorMessage($rs, null, __FILE__, $sql);
}

$data = false;

while(($row = sqlsrv_fetch_array($preparedStmt))==true){
    $row = array_map('trim',$row);
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
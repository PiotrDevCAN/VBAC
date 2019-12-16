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

$sql = " SELECT P.CNUM, P.NOTES_ID, P.ROLE_ON_THE_ACCOUNT as JRSS, S.SQUAD_TYPE, CONCAT('Tribe ', S.TRIBE_NUMBER) as TRIBE, ";
$sql.= " S.SHIFT, S.SQUAD_LEADER, F.NOTES_ID as FLL, U.NOTES_ID as SLL, S.SQUAD_NUMBER ";
$sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " AS F "; // lookup firstline
$sql.= " ON P.FM_CNUM = F.CNUM ";
$sql.= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " AS U "; // lookup upline ( second line )
$sql.= " ON F.FM_CNUM = U.CNUM ";
$sql.= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS S "; // lookup upline ( second line )
$sql.= " ON P.SQUAD_NUMBER  = S.SQUAD_NUMBER ";
$sql.= " WHERE " . personTable::activePersonPredicate(false,"P");

$rs = db2_exec($_SESSION['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, null, __FILE__, $sql);
}

$data = false;

while(($row = db2_fetch_assoc($rs))==true){
    $row = array_map('trim',$row);
    $data[] = $row;
}

$dataJsonAble = json_encode($data);
$messages = ob_get_clean();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
 } else {
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();
    echo $dirtyDetails;
    exit();
 }
ob_clean();
echo json_encode($response);
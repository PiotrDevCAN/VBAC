<?php

$sql = " SELECT distinct ORDERIT_VARB_REF ";
$sql .= " FROM " . $_SESSION['Db2Schema'] . "." . "ASSET_REQUESTS";
$sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is null and STATUS = 'Exported' ";

$rs = db2_exec($_SESSION['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
    return false;
}


while(($row=db2_fetch_assoc($rs))==true){
    var_dump($row);
    $data[]=$row['ORDERIT_VARB_REF'];
}


var_dump($data);
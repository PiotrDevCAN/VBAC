<?php
namespace vbac;

use itdq\DbTable;

class assetRequestsTable extends DbTable{

    function saveRecord(AssetRequestRecord $record, $populatedColumns, $nullColumns, $commit){
        parent::saveRecord($record, $populatedColumns, $nullColumns, $commit);
    }

    static function returnAsArray($predicate=null){
        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " WHERE 1=1 ";
        $sql .= $predicate;

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($row=db2_fetch_array($rs))==true){
            $data[] = $row;
        }

        return $data;

    }

}
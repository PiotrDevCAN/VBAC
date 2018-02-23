<?php
namespace vbac;

use itdq\DbTable;

class assetRequestsTable extends DbTable{

    function saveRecord(AssetRequestRecord $record, $populatedColumns, $nullColumns, $commit){
        parent::saveRecord($record, $populatedColumns, $nullColumns, $commit);
    }
}
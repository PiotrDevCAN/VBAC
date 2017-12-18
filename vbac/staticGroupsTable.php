<?php
namespace vbac;

use itdq\Loader;
use vbac\allTables;
use vbac\staticDataTable;

class staticGroupsTable extends staticDataTable {


    static function getallGroups(){
        $loader = new Loader();
        $allGroups = $loader->loadIndexed('GROUP','GROUP_ID',allTables::$STATIC_GROUPS );
        return $allGroups;
    }

}
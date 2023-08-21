<?php
namespace vbac;

use itdq\Loader;
use vbac\allTables;
use vbac\staticDataTable;

class staticDataGroupsTable extends staticDataTable {


    static function getallGroups(){
        $loader = new Loader();
        $allGroups = $loader->loadIndexed('GROUP','GROUP_ID',allTables::$STATIC_GROUPS );
        return $allGroups;
    }

    static function getStaticDataValuesForEdit(){
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_GROUPS;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            db2_stmt_error();
            db2_stmt_errormsg();
            return false;
        }

        $allData = null;
        $row = array();
        $row[] = "<button type='button' class='btn btn-default btn-xs newEntry' aria-label='Left Align' data-value='' data-uid='newEntry' ><span class='glyphicon glyphicon-plus ' aria-hidden='true'></span></button><span style='font-style:italic'>new_entry</span>";
        $row[] = null;
        $row[] = null;
        while (($record=sqlsrv_fetch_array($rs))==true){
            $allData[] = $row;
            $row = array();
            $groupName  = "<button type='button' class='btn btn-default btn-xs editRecord' aria-label='Left Align' ";
            $groupName .= "data-value='" . trim($record['GROUP_NAME']) . "' data-uid='" . trim($record['GROUP_ID']) . "' > ";
            $groupName .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span> ";
            $groupName .= "</button> ";
            $groupName .= "<button type='button' class='btn btn-default btn-xs disableRecord' aria-label='Left Align' ";
            $groupName .= "data-value='" . trim($record['GROUP_NAME']) . "' data-uid='" . trim($record['GROUP_ID']) . "' > ";
            $groupName .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span> ";
            $groupName .= "</button>" . trim($record['GROUP_NAME']);
            $row[] = $groupName;
            $allData[] = $row;
        }
        return $allData;
    }
}
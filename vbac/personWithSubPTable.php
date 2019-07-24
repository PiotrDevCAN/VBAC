<?php
namespace vbac;

use itdq\DbTable;
use itdq\AuditTable;
use itdq\Loader;
use itdq\slack;

class personWithSubPTable extends personTable {

    protected $personSubPlatform;

    function __construct($table,$pwd=null, $log=true){
        parent::__construct($table,$pwd,$log);

        $sql = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON_SUBPLATFORM;
        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        while(($subPlatformRecord=db2_fetch_assoc($rs))==true){
            $subPlatformRecord = array_map('trim', $subPlatformRecord);
            $this->personSubPlatform[$subPlatformRecord['CNUM']][] = $subPlatformRecord['SUBPLATFORM'];
        }
    }

    function addButtons($row){
        $row = parent::addButtons($row);
        $row['SUBPLATFORM'] = !empty($this->personSubPlatform[$row['CNUM']]) ? implode(',',$this->personSubPlatform[$row['CNUM']]) :  '';
        return $row;
    }
}
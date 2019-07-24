<?php
namespace vbac;

use itdq\DbTable;


/*
 * CREATE TABLE ROB_DEV.PERSON_SUBPLATFORM ( CNUM CHAR(9) NOT NULL, SUBPLATFORM CHAR(50) NOT NULL ) IN USERSPACE1;
 * ALTER TABLE ROB_DEV.PERSON_SUBPLATFORM ADD CONSTRAINT SSP_PK PRIMARY KEY (CNUM,SUBPLATFORM ) NOT ENFORCED;
 *
 */

class PersonSubPlatformTable extends DbTable
{
    static function saveSubplatformValues($cnum=null,$subplatformValues=null){
        $sql = " DELETE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON_SUBPLATFORM ;
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        foreach ($subplatformValues as $subPlatform) {
            $sql = " INSERT  INTO " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON_SUBPLATFORM;
            $sql.= " (CNUM, SUBPLATFORM) values ('" . db2_escape_string($cnum) ."','" . db2_escape_string($subPlatform) . "') ";

            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
        }

        return true;

    }

}


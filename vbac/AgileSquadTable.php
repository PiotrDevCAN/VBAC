<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;

/*
 *
 * CREATE TABLE VBAC.AGILE_SQUAD ( SQUAD_NUMBER NUMERIC(5) NOT NULL, SQUAD_TYPE CHAR(60) NOT NULL, TRIBE_NUMBER CHAR(10) NOT NULL, SHIFT CHAR(1) NOT NULL, SQUAD_LEADER CHAR(50) ) IN userspace1;
 * ALTER TABLE VBAC.AGILE_SQUAD ADD CONSTRAINT Squad_PK PRIMARY KEY (SQUAD_NUMBER ) ENFORCED;
 *
 * ALTER TABLE "VBAC"."AGILE_SQUAD" ADD COLUMN "SQUAD_NAME" CHAR(60);

 *
 *
 *
 */


class AgileSquadTable extends DbTable{

    static function nextAvailableSquadNumber($version=null) {

        $table = $version=='Original' ? allTables::$AGILE_SQUAD : allTables::$AGILE_SQUAD_OLD;

        $sql = " SELECT MAX(SQUAD_NUMBER) as SQUAD_NUMBER FROM " . $GLOBALS['Db2Schema'] . "." . $table ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        return !empty($row['SQUAD_NUMBER']) ? (int)($row['SQUAD_NUMBER'])+1 : 1;

    }

    function returnAsArray($version=null){
        $tribeTable = $version=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_OLD;

        $sql = " SELECT S.SQUAD_NUMBER, S.SQUAD_TYPE, S.TRIBE_NUMBER, S.SHIFT, S.SQUAD_LEADER, S.SQUAD_NAME,    
        CASE WHEN S.ORGANISATION is null THEN T.ORGANISATION ELSE S.ORGANISATION END AS ORGANISATION, T.TRIBE_NAME ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as S ";
        $sql.= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . $tribeTable . " as T ";
        $sql.= " ON S.TRIBE_NUMBER = T.TRIBE_NUMBER ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
// echo $sql;
// exit;
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = false;
        while(($row=sqlsrv_fetch_array($rs))==true){
            $row = array_map('trim',$row);
            $rowWithIcons = $this->addIcons($row);
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){

        $squadNumber = $row['SQUAD_NUMBER'];

        $squadNumberNumberWithIcon  = "<button type='button' class='btn btn-default btn-xs btnEditSquad' aria-label='Left Align' ";
        $squadNumberNumberWithIcon .= "data-squadnumber='" .$squadNumber . "' ";
        $squadNumberNumberWithIcon .= "data-squadname='" .$row['SQUAD_NAME'] . "' ";
        $squadNumberNumberWithIcon .= "data-squadleader='" .$row['SQUAD_LEADER'] . "' ";
        $squadNumberNumberWithIcon .= "data-squadtype='" .$row['SQUAD_TYPE'] . "' ";
        $squadNumberNumberWithIcon .= "data-shift='" .$row['SHIFT'] . "' ";
        $squadNumberNumberWithIcon .= "data-tribenumber='" .$row['TRIBE_NUMBER'] . "' ";
        $squadNumberNumberWithIcon .= "data-organisation='" .$row['ORGANISATION'] . "' ";
        $squadNumberNumberWithIcon .= " data-toggle='tooltip' data-placement='top' title='Edit Tribe'";
        $squadNumberNumberWithIcon .= " > ";
        $squadNumberNumberWithIcon .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $squadNumberNumberWithIcon .= " </button> ";
        $squadNumberNumberWithIcon .= "&nbsp; " . $squadNumber;

        $row['SQUAD_NUMBER'] = array('display'=>$squadNumberNumberWithIcon,'sort'=>$squadNumber);
        return $row;
    }


    static function getSquadDetails($squadNumber, $version='original'){

        $squadTable = $version=='original'  ? allTables::$AGILE_SQUAD : allTables::$AGILE_SQUAD_OLD;
        $tribeTable = $version=='original'  ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_OLD;


        $sql = " SELECT S.SQUAD_NUMBER,S.SQUAD_NAME, S.SQUAD_TYPE, S.SQUAD_LEADER, S.TRIBE_NUMBER, T.TRIBE_NAME, T.TRIBE_LEADER ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $squadTable . " AS S ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $tribeTable . " AS T ";
        $sql.= " ON S.TRIBE_NUMBER = T.TRIBE_NUMBER ";
        $sql.= " WHERE S.SQUAD_NUMBER = " . htmlspecialchars($squadNumber);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);

        return $row;

    }


    static function buildTribeSelects(){
        $loader = new Loader();
        $allTribeSelects = array();
        $allTribeSelects['original']['managed'] = $loader->loadIndexed('TRIBE_NAME','TRIBE_NUMBER', allTables::$AGILE_TRIBE, " ORGANISATION='Managed Services' ");
        $allTribeSelects['original']['project'] = $loader->loadIndexed('TRIBE_NAME','TRIBE_NUMBER', allTables::$AGILE_TRIBE, " ORGANISATION='Project Services' ");
        $allTribeSelects['old']['managed'] = $loader->loadIndexed('TRIBE_NAME','TRIBE_NUMBER', allTables::$AGILE_TRIBE_OLD, " ORGANISATION='Managed Services' ");
        $allTribeSelects['old']['project'] = $loader->loadIndexed('TRIBE_NAME','TRIBE_NUMBER', allTables::$AGILE_TRIBE_OLD, " ORGANISATION='Project Services' ");
        ?>
        <script type="text/javascript">
        <?php
        foreach ($allTribeSelects as $tableSet => $organisation) {
            foreach ($organisation as $org => $selectData) {?>
                var tribes<?=ucfirst($tableSet);?><?=ucfirst($org);?> = [];
                // tribes<?=ucfirst($tableSet);?><?=ucfirst($org)?>.push({id:0,text:""});
                <?php
                foreach ($selectData as $tribeNumber => $tribeName) {
                ?>
                    tribes<?=ucfirst($tableSet);?><?=ucfirst($org)?>.push({
                        id:"<?=$tribeNumber?>",
                        text:"<?=$tribeName?>",
                        // organisation: "TEST",
                        // leader: "TEST 2",
                        // iterationMgr: "TEST 3",
                    });
                <?php
                }
            }
        }
        ?>
        </script>
        <?php
    }

    static function buildTribeSelects_NEW(){

        $sql = " SELECT T.*";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " as T ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = false;
        while(($row=sqlsrv_fetch_array($rs))==true){
            $row = array_map('trim',$row);
            $data[] = $row;
        }
        ?>
        <script type="text/javascript">
        var tribes = [];
        <?php
        foreach ($data as $key => $tribe) {
            $tribeNumber = $tribe['TRIBE_NUMBER'];
            $tribeName = $tribe['TRIBE_NAME'];
            $tribeOrganisation = $tribe['ORGANISATION'];
            $tribeLeader = $tribe['TRIBE_LEADER'];
            $tribeIterationMgr = $tribe['ITERATION_MGR'];
            ?>
            tribes.push({
                id:"<?=$tribeNumber?>",
                text:"<?=$tribeName?>",
                organisation: "<?=$tribeOrganisation?>",
                leader: "<?=$tribeLeader?>",
                iterationMgr: "<?=$tribeIterationMgr?>",
            });
            <?php
        }
        ?>
        </script>
        <?php
    }
}
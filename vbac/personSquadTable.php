<?php
namespace vbac;

use itdq\DbTable;

class personSquadTable extends DbTable{

    const ASSIGNMENT_TYPE_SELECT = " EA.TYPE AS ASSIGNMENT_TYPE_CODE, CASE WHEN EA.TYPE = '" . personSquadRecord::PRIMARY . "' THEN '" . personSquadRecord::PRIMARY_NAME . "' WHEN EA.TYPE = '" . personSquadRecord::SECONDARY . "' THEN '" . personSquadRecord::SECONDARY_NAME . "' ELSE '' END AS ASSIGNMENT_TYPE ";
    
    static function deleteAssignment($id){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$EMPLOYEE_AGILE_MAPPING;
        $sql.= " WHERE ID = '" . htmlspecialchars($id) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    function returnAsArray($withButtons = true){
        
        $data = array();

        $personPortalReport = new personPortalReport(allTables::$PERSON);
        $preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

        // main predicate
        $preBoardersPredicate = $personPortalReport->buildPreBoardersPredicate($preBoardersAction);

        $sql = "SELECT
            EA.ID,
            P.CNUM,
            P.WORKER_ID,
            P.EMAIL_ADDRESS,
            P.KYN_EMAIL_ADDRESS,
            P.FIRST_NAME,
            P.LAST_NAME,
            AT.TRIBE_NAME,
            AT.TRIBE_NUMBER,
            AS1.SQUAD_NAME,
            AS1.SQUAD_NUMBER,
            EA.TYPE ";

        $sql .= " FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$EMPLOYEE_AGILE_MAPPING . " AS EA
            ON EA.CNUM = P.CNUM AND EA.WORKER_ID = P.WORKER_ID
        LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS1
            ON AS1.SQUAD_NUMBER = EA.SQUAD_NUMBER
        LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT
            ON AT.TRIBE_NUMBER = AS1.TRIBE_NUMBER ";
        $sql .= " WHERE " . $preBoardersPredicate;
    
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
                $row = array_map('trim', $row);
                $rowWithIcons = $withButtons ?  $this->addIcons($row) : $row;
                $data[] = $rowWithIcons;
            }
        }
 
        return array('data'=>$data,'sql'=>$sql);
    }

    function addIcons($row){
        $id = $row['ID'];
        $cnum = $row['CNUM'];
        $workerId = $row['WORKER_ID'];
        $email = $row['EMAIL_ADDRESS'];
        $fullName = $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'];
        $tribeId = $row['TRIBE_NUMBER'];
        $squadId = $row['SQUAD_NUMBER'];
        $typeId = $row['TYPE'];
        $type = '';
        if (!empty($typeId)) {        
            $type = personSquadRecord::$allTypes[$typeId];
        }

        // TRIBE_NAME
        $tribeNumberField = $row['TRIBE_NUMBER'];
        $tribeName = !empty($row['TRIBE_NAME']) ? $row['TRIBE_NAME'] : AgileTribeRecord::NOT_ALLOCATED;
        $row['TRIBE_NAME'] = $tribeName;

        // SQUAD_NAME
        $squadNumberField = $row['SQUAD_NUMBER'];
        $squadName = !empty($row['SQUAD_NAME']) ? $row['SQUAD_NAME'] : AgileSquadRecord::NOT_ALLOCATED;
        
        $editButton = '';
        $deleteButton = '';
        if (!empty($squadNumberField)) {
            $editButton  = "<button type='button' class='btn btn-default btn-xs btnEditAssignment' aria-label='Left Align' ";
            $editButton .= "data-id='" .$id . "' ";
            $editButton .= "data-cnum='" .$cnum . "' ";
            $editButton .= "data-workerid='" .$workerId . "' ";
            $editButton .= "data-email='" .$email . "' ";
            $editButton .= "data-fullname='" .$fullName . "' ";
            $editButton .= "data-tribeid='" .$tribeId . "' ";
            $editButton .= "data-squadid='" .$squadId . "' ";
            $editButton .= "data-type='" .$typeId . "' ";
            $editButton .= "data-toggle='tooltip' data-placement='top' title='Edit Assignment'";
            $editButton .= " > ";
            $editButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $editButton .= " </button> ";
    
            $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteAssignment btn-danger' aria-label='Left Align' ";
            $deleteButton .= "data-id='" .$id . "' ";
            $deleteButton .= "data-cnum='" .$cnum . "' ";
            $deleteButton .= "data-workerid='" .$workerId . "' ";
            $deleteButton .= "data-email='" .$email . "' ";
            $deleteButton .= "data-tribeid='" .$tribeId . "' ";
            $deleteButton .= "data-tribename='" .$tribeName . "' ";
            $deleteButton .= "data-squadid='" .$squadId . "' ";
            $deleteButton .= "data-squadname='" .$squadName . "' ";
            $deleteButton .= "data-type='" .$typeId . "' ";
            $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Assignment'";
            $deleteButton .= " > ";
            $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
            $deleteButton .= " </button> ";
        }

        $idWithIcon = $editButton . $deleteButton . " &nbsp; " . $squadName;

        $row['SQUAD_NAME'] = array('display'=>$idWithIcon,'sort'=>$squadName);

        $row['TYPE'] = $type;
        
        return $row;
    }

    function headerRowForDatatable(){
        $personPortalColumns = array(
            'ID',
            'CNUM',
            'WORKER_ID',
            'EMAIL_ADDRESS',
            'KYN_EMAIL_ADDRESS',
            'SQUAD_NAME',
            'TRIBE_NAME',
            'TYPE'
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName ) . "</th>";
        }
        $headerRow.= "</tr>";
        return $headerRow;
    }
}
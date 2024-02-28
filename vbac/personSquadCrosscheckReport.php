<?php
namespace vbac;

use itdq\DbTable;

class personSquadCrosscheckReport extends DbTable{

    function returnAsArray($withButtons = true){
        
        $data = array();
        
        $personPortalReport = new personPortalReport(allTables::$PERSON);
        $preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

        // main predicate
        $preBoardersPredicate = $personPortalReport->buildPreBoardersPredicate($preBoardersAction);

        $sql = "SELECT
            P.CNUM,
            P.WORKER_ID,
            P.EMAIL_ADDRESS,
            P.KYN_EMAIL_ADDRESS,
            AS1.SQUAD_NUMBER, 
            AS1.SQUAD_NAME,
            AS1.SQUAD_LEADER, 
            AT.TRIBE_NUMBER,
            AT.TRIBE_NAME,
            AT.TRIBE_LEADER,
            AT.ORGANISATION,
            AT.ITERATION_MGR,";
        
        $sql .= personTable::ORGANISATION_SELECT;

        $sql .= " FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS1
            ON AS1.SQUAD_NUMBER = P.SQUAD_NUMBER
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
                $rowWithIcons = $withButtons ?  $this->addIcons($row) : $row ;
                $data[] = $rowWithIcons;                   
            }
        }
 
        return array('data'=>$data,'sql'=>$sql);
    }

    function addIcons($row){
        // TRIBE_NAME
        $tribeName = !empty($row['TRIBE_NAME']) ? $row['TRIBE_NAME'] : AgileTribeRecord::NOT_ALLOCATED;
        $row['TRIBE_NAME'] = $tribeName;

        // SQUAD_NAME
        $row['SQUAD_NAME'] = $this->getAgileSquadWithButtons($row, true);
        return $row;
    }

    public function getAgileSquadWithButtons($row, $original = true)
    {
        $squadNumberField = $row['SQUAD_NUMBER'];
        $squadName = !empty($row['SQUAD_NAME']) ? $row['SQUAD_NAME'] : AgileSquadRecord::NOT_ALLOCATED;
        $cnum = $row['CNUM'];
        $workerId = $row['WORKER_ID'];

        $agileSquadWithButton = $original ? "<button type='button' class='btn btn-default btn-xs btnEditAgileNumber accessRestrict  accessCdi' aria-label='Left Align' " : null;
        $agileSquadWithButton .= $original ? " data-cnum='" . $cnum . "' " : null;
        $agileSquadWithButton .= $original ? " data-workerid='" . $workerId . "' " : null;
//        $agileSquadWithButton.= $original ? " data-version='original' " : " data-version='old' ";
        $agileSquadWithButton .= $original ? " data-version='original' " : null;
        $agileSquadWithButton .= $original ? " data-toggle='tooltip' data-placement='top' " : null;
//        $agileSquadWithButton.= $original ?  " title='Amend Agile Squad'" : " title='Amend Old Agile Squad'";
        $agileSquadWithButton .= $original ? " title='Amend Agile Squad'" : null;
        $agileSquadWithButton .= $original ? " > " : null;
        $agileSquadWithButton .= $original ? "<span class='glyphicon glyphicon-edit' aria-hidden='true' ></span>" : null;
        $agileSquadWithButton .= $original ? "</button>" : null;
        $agileSquadWithButton .= $original ? "&nbsp;" : null;

        if (!empty($squadNumberField) && $original) {
            $agileSquadWithButton .= "<button type='button' class='btn btn-danger btn-xs btnClearSquadNumber accessRestrict  accessCdi' aria-label='Left Align' ";
            $agileSquadWithButton .= " data-cnum='" . $cnum . "' ";
            $agileSquadWithButton .= " data-workerid='" . $workerId . "' ";
            $agileSquadWithButton .= $original ? " data-version='original' " : " data-version='new' ";
            $agileSquadWithButton .= " data-toggle='tooltip' data-placement='top' ";
            $agileSquadWithButton .= $original ? " title='Clear Squad Number'" : " title='Clear New Squad Number'";
            $agileSquadWithButton .= " > ";
            $agileSquadWithButton .= "<span class='glyphicon glyphicon-erase' aria-hidden='true' ></span>";
            $agileSquadWithButton .= "</button>";
            $agileSquadWithButton .= "&nbsp;";
        }

        $agileSquadWithButton .= $squadName;

        return array('display' => $agileSquadWithButton, 'sort' => $squadName);
    }

    function headerRowForDatatable(){
        $personPortalColumns = array(
            'CNUM',
            'WORKER_ID',
            'EMAIL_ADDRESS',
            'KYN_EMAIL_ADDRESS',
            'SQUAD_NUMBER', 
            'SQUAD_NAME',
            'SQUAD_LEADER', 
            'TRIBE_NUMBER',
            'TRIBE_NAME',
            'TRIBE_LEADER',
            'ORGANISATION',
            'ITERATION_MGR'
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName ) . "</th>";
        }
        $headerRow.= "</tr>";
        return $headerRow;
    }
}
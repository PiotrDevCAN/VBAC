<?php
namespace vbac;

use itdq\DbTable;


class odcAccessTable extends DbTable {

    private $xlsDateFormat = 'd-M-Y';
    private $db2DateFormat = 'm-d-Y';


    function copyXlsxToDb2($fileName, $withTimings = false){
        $elapsed = -microtime(true);
        ob_start();
        $odcAccessTable = new odcAccessTable(allTables::$ODC_ACCESS);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileName);
        $reader->setReadDataOnly(true);
        $objPHPExcel  = $reader->load($fileName);

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        $firstRow = false;
        $columnHeaders = array();
        $recordData = array();
        $failedRecords = 0;
        // $autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);
        for ($row = 1; $row <= $highestRow; $row++){
            set_time_limit(10);
            $time = -microtime(true);
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);
            //  Insert row data array into your database of choice here
            if(!$firstRow){
                foreach ($rowData[0] as $key => $value){
                    $columnHeaders[$key] = DbTable::toColumnName(strtoupper($value));
                }
                $firstRow = true;
            } else {
                $prepareArrary = -microtime(true);
                foreach ($rowData[0] as $key => $value) {
                    $recordData[$columnHeaders[$key]] = trim($value);
                }
                $prepareArrary += microtime(true);
                echo $withTimings ? "Row: $row Cnum " . $recordData['OWNER_CNUM_ID'] . " Prepare Array:" . sprintf('%f', $prepareArrary) . PHP_EOL : null;


                if($row==2){
                    // delete the previous data for this Secured Area.
                    $secureAreaName = trim($recordData['SECURED_AREA_NAME']);
                    $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
                    $sql.= " WHERE SECURED_AREA_NAME = '" . htmlspecialchars($secureAreaName) . "' ";
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);


                    if(!$rs){
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                    }
                }


                if(!empty($recordData['OWNER_CNUM_ID'] )){
                    // avoid trying to save empty rows.
                    // save the row to DB2
                    $convertDates = -microtime(true);
                    $recordDataWithDb2Dates = $this->convertDate($recordData);
                    $convertDates += microtime(true);
                    echo  $withTimings ? "Row: $row Cnum " . $recordData['OWNER_CNUM_ID'] . " Convert Dates:" . sprintf('%f', $convertDates) . PHP_EOL : null;

                    try {
                        $insert = -microtime(true);
                        $inserted = $odcAccessTable->InsertFromArray($recordDataWithDb2Dates, $withTimings, DbTable::ROLLBACK_NO);
                        $inserted ? null : $failedRecords++;
                        $insert += microtime(true);
                        echo  $withTimings ?  "Row: $row Cnum " . $recordData['OWNER_CNUM_ID'] . " Insert Row:" . sprintf('%f', $insert) . PHP_EOL : null ;

                    } catch (Exception $e) {
                        echo $e->getMessage();
                        echo $e->getCode();
                        echo $e->getTrace();
                        die('here');
                    }
                    $time += microtime(true);
                    echo  $withTimings ?  "Row: $row Cnum " . $recordData['OWNER_CNUM_ID'] . " Total Time:" . sprintf('%f', $time) . PHP_EOL : null;
                }



            }
        }

        sqlsrv_commit($GLOBALS['conn']);  // Save what we have done.
        // db2_autocommit($GLOBALS['conn'],$autoCommit);

        $response = ob_get_clean();
        ob_start();
        $errors = !empty($response);

        $recordsForLocation = $this->numberOfRecordsForLocation($secureAreaName);

        $elapsed += microtime(true);
        $dataRecords = $row-2;

        echo $errors ? "<span style='color:red'><h2 >Errors writing to DB2 occured</h2><br/>" . $dataRecords . " Records Read from xlsx<br/>$failedRecords failed to insert into DB<br/>Error Details Follow:<br/></span>" : "<span  style='color:green'><h3> Well that appears to have gone well !!</h3><br/>" . $dataRecords . " Records Read from xlsx<br/>$failedRecords failed to insert into DB</span>";
        echo "<span style='color:blue'>";
        echo "<h5>Location: <b>" . $secureAreaName . "</b> has " .  $recordsForLocation . " records ";
        echo "<br/>Load Run time : ". sprintf('%f Seconds', $elapsed);
        $mSecPerRow = $elapsed / $row;
        echo "<br/>Seconds/Record : " . sprintf('%f', $mSecPerRow) ;
        $rowPerMsec = $row / $elapsed;
        echo "<br/>Records/Second : " . sprintf('%f', $rowPerMsec) ;
        echo "</span>";
        echo "<hr/>";

        echo $response;




    }


    function convertDate(array $recordData)
    {
        $adjustedData = array();
        foreach ($this->columns as $key => $properties) {
            if(!empty($recordData[$key])){
                switch ($properties['DATA_TYPE']) {
                    case 91:
                    case 93:
                        $date = date_create_from_format($this->xlsDateFormat, trim($recordData[$key]));
                        $adjustedData[$key] = $date->format($this->db2DateFormat);
                        break;
                    default:
                        $adjustedData[$key] = $recordData[$key];
                        break;
                }
            }
         }
        return $adjustedData;
    }


    function numberOfRecordsForLocation($location=null){
        $sql = " SELECT COUNT(*) as RECORDS ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE 1= 1 ";
        $sql.= !empty($location) ? " AND SECURED_AREA_NAME='" . htmlspecialchars($location) . "' " : null;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql,null,null,null,null,DbTable::ROLLBACK_NO);
        }

        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

        return $row['RECORDS'];

    }

    function locationMismatch(){

        $vbacActivePredicate = personTable::activePersonPredicate();

        // records found in ODC_ACCESS as having access to a Secured Area that doesn't match with their LBG_LOCATION in VBAC PERSON table.
        $sql = "select * ";
        $sql.= "from ( ";
        $sql.= "SELECT trim(P.NOTES_ID),trim(P.LBG_LOCATION),trim(O.SECURED_AREA_NAME)";
        $sql.= ",case when O.SECURED_AREA_NAME like 'PUNE%' and P.LBG_LOCATION like '%Pune' then 'Pune Match' else ";
        $sql.= "    case when O.SECURED_AREA_NAME like 'PUNE%' then 'Pune Mismatch - ODC Access not reflected in VBAC' else ";
        $sql.= "        case when P.LBG_LOCATION like '%Pune'  then 'Pune Mismatch - VBAC LBG_LOCATION not reflected in ODC Access' else '' ";
        $sql.= "        end ";
        $sql.= "    end ";
        $sql.= "end as PUNE_MATCHED ";
        $sql.= ",case when O.SECURED_AREA_NAME like 'BANGALORE%' and P.LBG_LOCATION like '%Bangalore' then 'Bangalore Matched' ELSE ";
        $sql.= "    case when O.SECURED_AREA_NAME like 'BANGALORE%'  then 'Bangalore Mismatch - ODC Access not reflected in VBAC' else ";
        $sql.= "        case when P.LBG_LOCATION like '%Bangalore'   then 'Bangalore Mismatch - VBAC LBG_LOCATION not reflected in ODC Access' else '' ";
        $sql.= "        end ";
        $sql.= "    end ";
        $sql.= "end as BANGALORE_MATCHED ";
        $sql.= ",P.CNUM, P.REVALIDATION_STATUS, P.PES_STATUS ";
        $sql.= "from " . $GLOBALS['Db2Schema'] . "." . allTables::$ODC_ACCESS_LIVE . " as O ";
        $sql.= "left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= "on O.OWNER_CNUM_ID = P.CNUM ";
        $sql.= ") ";
        $sql.= "WHERE 1=1 ";
        $sql.= " and ( PUNE_MATCHED like 'Pune Mismatch%' ";
        $sql.= "       or BANGALORE_MATCHED like 'Bangalore Mismatch%' ) ";
        $sql.= " and " . $vbacActivePredicate;


        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        return $rs;
    }


    function odcAccessMissingFromVbac(){
        // records found in ODC_ACCESS as having access to a Secured Area but CNUM not found in  VBAC PERSON table.

        $sql = "SELECT O.*";
        $sql.= "from " . $GLOBALS['Db2Schema'] . "." . allTables::$ODC_ACCESS_LIVE . " as O ";
        $sql.= "left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= "on O.OWNER_CNUM_ID = P.CNUM ";
        $sql.= "WHERE P.CNUM is null ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        return $rs;
    }



    function odcPopulationByPlatform(){
        $personTable = new personTable(allTables::$PERSON);

        $activePredicate = $personTable->activePersonPredicate();


        $sql = " SELECT upper(trim(WORK_STREAM)) AS WORK_STREAM, COUNT( distinct O.OWNER_CNUM_ID) as Platform_Population ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as O ";
        $sql.= " ON O.OWNER_CNUM_ID = P.CNUM ";
        $sql.= " WHERE 1=1 ";
        $sql.= " AND " . $activePredicate;
        $sql.= " AND TT_BAU='BAU' ";
        $sql.= " GROUP BY WORK_STREAM ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            echo print_r(sqlsrv_errors());
            echo print_r(sqlsrv_errors());
            DbTable::displayErrorMessage($rs, '', '', $sql);
        }

        $totalPopulation=0;
        $platformPopulation = array();

        while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
            $platformPopulation[strtoupper(trim($row['WORK_STREAM']))] = $row['PLATFORM_POPULATION']+0;
            $totalPopulation += $row['PLATFORM_POPULATION'];
        }

        return array('TotalPopulation'=>$totalPopulation,'PlatformPopulations'=>$platformPopulation,'sql'=>$sql);


    }

}
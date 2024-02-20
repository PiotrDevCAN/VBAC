<?php
namespace vbac;

use itdq\DbTable;

class personStatusCrosscheckReport extends DbTable{

    function returnAsArray(){
        
        $data = array();
        
        $preBoardersPredicate = " 1=1  ";
        $preBoardersPredicate .= " AND (";
        $preBoardersPredicate .= " (" . personTable::externalCNUMPredicate(true, 'P') . ")";
        $preBoardersPredicate .= " AND NOT " . personTable::hasPreBoarderPredicate(true, 'P');
        $preBoardersPredicate .= " OR (" . personTable::regularCNUMPredicate(true, 'P') . ")";
        $preBoardersPredicate .= ")";
        $preBoardersPredicate .= " AND " . personTable::notArchivedPersonPredicate(true, 'P');

        $sql = "SELECT
        QUERY.EMAIL_ADDRESS,
        (SELECT TOP 1
            P.CNUM
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
        ) AS VBAC_CNUM,
        (SELECT TOP 1
            P.WORKER_ID
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
        ) AS VBAC_WORKER_ID,
        (SELECT TOP 1
            CF.CANDIDATE_ID
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CF
        WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
        ) AS CFIRST_CANDIDATE_ID,
        CASE WHEN 
            EXISTS (
            SELECT 1
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                    ) THEN 'found' ELSE 'not found' END
                    AS FOUND_IN_VBAC,
        CASE WHEN 
            EXISTS (
            SELECT 1
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CF
        WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
            ) THEN 'found' ELSE 'not found' END
            AS FOUND_IN_CFIRST,
        STATUS_IN_VBAC = (
            SELECT TOP 1
            P.PES_STATUS
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
        WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
        ORDER BY P.REVALIDATION_DATE_FIELD DESC
                ),
        (SELECT TOP 1
            CF.STATUS
        FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CF
        WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
        ) AS STATUS_IN_CFIRST,
        CASE 
            WHEN (
                SELECT TOP 1
                P.PES_STATUS
            FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
            WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
            ORDER BY P.REVALIDATION_DATE_FIELD DESC
            ) = 'Cleared' AND (
                SELECT TOP 1
                CF.STATUS
            FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CF
            WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
            ) = 'Completed'
            THEN 'match' 
            ELSE 'mismatch'
        END AS STATUS_MATCH
    FROM
        (
            SELECT P.EMAIL_ADDRESS
            FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
            WHERE 1 = 1
                AND P.EMAIL_ADDRESS IS NOT NULL
                AND TRIM(P.EMAIL_ADDRESS) != ''
                AND ( (P.CNUM LIKE '%XXX' OR P.CNUM LIKE '%xxx' OR P.CNUM LIKE '%999' )
                AND NOT EXISTS (
                    SELECT 1
                FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P1
                WHERE P.CNUM = P1.PRE_BOARDED )
                OR (P.CNUM NOT LIKE '%XXX'
                AND P.CNUM NOT LIKE '%xxx'
                AND P.CNUM NOT LIKE '%999' 
                    )
                )
        UNION
            SELECT
                DISTINCT CF.EMAIL_ADDRESS
            FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " AS CF
                LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P
                ON P.EMAIL_ADDRESS = CF.EMAIL_ADDRESS
            WHERE CF.EMAIL_ADDRESS IS NOT NULL
                AND TRIM(CF.EMAIL_ADDRESS) != ''
            ) AS QUERY";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){                         
                $data[] = $row;
            }
        }
 
        return array('data'=>$data,'sql'=>$sql);
    }

    function headerRowForDatatable(){
        $personPortalColumns = array(
            'EMAIL_ADDRESS',
            'FOUND_IN_VBAC',
            'FOUND_IN_CFIRST',
            'STATUS_IN_VBAC',
            'STATUS_IN_CFIRST',
            'STATUS_MATCH',
            'VBAC_CNUM',
            'VBAC_WORKER_ID',
            'CFIRST_CANDIDATE_ID'
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName ) . "</th>";
        }
        $headerRow.= "</tr>";
        return $headerRow;
    }
}
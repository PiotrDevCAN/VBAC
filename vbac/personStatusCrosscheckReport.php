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

        // $sql = " SELECT CNUM, WORKER_ID, FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, PES_STATUS, STATUS, STATUS_MATCH, CFIRST_ENTRY";
        // $sql .= " FROM (";
        // $sql .= " SELECT P.CNUM, P.WORKER_ID, P.FIRST_NAME, P.LAST_NAME, P.EMAIL_ADDRESS, P.PES_STATUS, ";
        // $sql .= " CASE 
        //     WHEN
        //         CF.STATUS IS NULL THEN 'Other'
        //     ELSE CF.STATUS END 
        // AS STATUS, ";
        // $sql .= " CASE 
        //     WHEN
        //         P.PES_STATUS = 'Cleared' AND CF.STATUS = 'Completed' THEN 'Match' 
        //     WHEN
        //         P.PES_STATUS = CF.STATUS THEN 'Match' 
        //     ELSE 'No Match' END 
        // AS STATUS_MATCH, ";
        // $sql .= " CASE WHEN CF.EMAIL_ADDRESS IS NOT NULL THEN 'found' ELSE 'not found' END AS CFIRST_ENTRY ";
        // $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " as P ";
        // $sql .= " LEFT JOIN ". $GLOBALS['Db2Schema'] . "." . allTables::$CFIRST_PERSON . " as CF ";
        // $sql .= " ON LOWER(P.EMAIL_ADDRESS) = LOWER(CF.EMAIL_ADDRESS) ";
        // $sql .= " WHERE P.EMAIL_ADDRESS IS NOT NULL";
        // $sql .= " AND " . $preBoardersPredicate;
        // $sql .= " AND CF.EMAIL_ADDRESS IS NOT NULL";
        // $sql .= " ) AS QUERY";

        $sql = "SELECT
            QUERY.EMAIL_ADDRESS,
            CASE WHEN 
                EXISTS (
                SELECT 1
            FROM VBAC.PERSON AS P
            WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                ) THEN 'found' ELSE 'not found' END
                AS FOUND_IN_VBAC,
            CASE WHEN 
                EXISTS (
                    SELECT 1
            FROM VBAC.CFIRST_PERSON AS CF
            WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                ) THEN 'found' ELSE 'not found' END
                AS FOUND_IN_CFIRST,
            STATUS_IN_VBAC = (
                SELECT TOP 1
                P.PES_STATUS
            FROM VBAC.PERSON AS P
            WHERE P.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
            ORDER BY P.REVALIDATION_DATE_FIELD DESC
            ),
            CASE 
                    WHEN  (
                        SELECT TOP 1
                CF.STATUS
            FROM VBAC.CFIRST_PERSON AS CF
            WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                    ) = 'Completed'
                    THEN 'Cleared' 
                    WHEN  (
                        SELECT TOP 1
                CF.STATUS
            FROM VBAC.CFIRST_PERSON AS CF
            WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                    ) = 'Cancelled'
                    THEN 'Cancelled'
                    WHEN  (
                        SELECT TOP 1
                CF.STATUS
            FROM VBAC.CFIRST_PERSON AS CF
            WHERE CF.EMAIL_ADDRESS = QUERY.EMAIL_ADDRESS
                    ) = 'In Progress'
                    THEN 'In Progress'
                    ELSE 'Other' 
                END AS STATUS_IN_CFIRST,
            '' AS STATUS_MATCH
        FROM
            (
                        SELECT P.EMAIL_ADDRESS
                FROM VBAC.PERSON AS P
                WHERE 1 = 1
                    AND P.EMAIL_ADDRESS IS NOT NULL
                    AND ( (P.CNUM LIKE '%XXX' OR P.CNUM LIKE '%xxx' OR P.CNUM LIKE '%999' ) AND NOT EXISTS (SELECT 1
                    FROM VBAC.PERSON as P1
                    WHERE P.CNUM = P1.PRE_BOARDED )
                    OR (P.CNUM NOT LIKE '%XXX' AND P.CNUM NOT LIKE '%xxx' AND P.CNUM NOT LIKE '%999' ))
                    AND P.PES_STATUS not in ('Left IBM','Removed','Declined')
            UNION
                SELECT
                    DISTINCT CF.EMAIL_ADDRESS
                FROM VBAC.CFIRST_PERSON AS CF
                    LEFT JOIN VBAC.PERSON AS P
                    ON P.EMAIL_ADDRESS = CF.EMAIL_ADDRESS
                WHERE CF.EMAIL_ADDRESS IS NOT NULL
                    AND NOT EXISTS (
                        SELECT 1
                    FROM VBAC.CFIRST_PERSON AS CF1
                    WHERE CF.EMAIL_ADDRESS = CF1.EMAIL_ADDRESS
                        AND CF1.STATUS = 'Completed'
                        AND (CF.STATUS NOT LIKE 'Completed'
                        OR CF.STATUS IS NULL)
                    )
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
            'STATUS_MATCH'
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow.= "<th>" . str_replace("_"," ", $columnName ) . "</th>";
        }
        $headerRow.= "</tr>";
        return $headerRow;
    }
}
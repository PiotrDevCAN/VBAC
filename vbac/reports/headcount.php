<?php

namespace vbac\reports;

use itdq\DbTable;
use vbac\interfaces\report;
use vbac\personRecord;
use vbac\personTable;

class headcount implements report
{
    public function getReport($resultSetOnly = false)
    {
        $predicate = " AND P.PMO_STATUS = '" . personRecord::PMO_STATUS_AWARE . "'";
        $predicate.= " AND " . personTable::activePersonPredicate(true, 'P');
        $predicate.= " AND NOT " . personTable::hasPreBoarderPredicate(true, 'P');

        $sql = " SELECT ";
        $sql.=" P.KYN_EMAIL_ADDRESS AS Email_Address,";
        $sql.=" P.CNUM AS CNUM,";
        $sql.=" P.WORKER_ID AS Peronsal_ID_SAP,";
        $sql.=" P.FIRST_NAME AS First_Name,";
        $sql.=" P.LAST_NAME AS Last_Name,";

        // $sql.=personTable::FULLNAME_SELECT.",";
        $str1 = personTable::FULLNAME_SELECT.",";
        $search = array('AS FULL_NAME');
        $replace = array('AS full_name');
        $sql.=str_replace($search, $replace, $str1);
        
        // $sql.=personTable::EMPLOYEE_TYPE_SELECT.",";
        // $search = array('AS EMPLOYEE_TYPE_CODE', 'AS EMPLOYEE_TYPE');
        // $replace = array('AS employee_type_code', 'AS employee_type');
        $str2 = personTable::EMPLOYEE_TYPE_SELECT_WITHOUT_CODE.",";
        $search = array('AS EMPLOYEE_TYPE');
        $replace = array('AS employee_type');
        $sql.=str_replace($search, $replace, $str2);

        $sql.=" P.BUSINESS_TITLE AS Job_Title,";
        
        // $sql.=" 'tbc' AS Band,";
        $str3 = personTable::BAND_SELECT.",";
        $search = array('AS BAND');
        $replace = array('AS band');
        $sql.=str_replace($search, $replace, $str3);

        $sql.=" P.COUNTRY AS Country,";
        $sql.=" P.LBG_LOCATION AS LBG_Approved_Location,";
        $sql.=" SS.SKILLSET AS Skillset,";
        $sql.=" P.START_DATE AS Start_Date,";
        $sql.=" P.PROJECTED_END_DATE AS Projected_End_Date,";
        $sql.=" P.PES_STATUS AS PES_Status,";
        $sql.=" P.PES_CLEARED_DATE AS PES_Cleared_Date,";
        $sql.=" P.PES_RECHECK_DATE AS PES_Recheck_Date,";
        $sql.=" P.PES_LEVEL AS PES_Level,";
        $sql.=" AS1.SQUAD_NAME AS Squad_Name,";
        $sql.=" AS1.SQUAD_LEADER AS Squad_Leader,";

        // $sql.=" AS1.ORGANISATION AS Squad_Organisation,";
        $str4 = personTable::ORGANISATION_SELECT.",";
        $search = array('AS ORGANISATION');
        $replace = array('AS Squad_Organisation');
        $sql.=str_replace($search, $replace, $str4);

        $sql.=" AT.TRIBE_NAME AS Tribe_Name,";
        $sql.=" AT.TRIBE_LEADER AS Tribe_Leader,";
        // $sql.=" AT.ORGANISATION AS Tribe_Organisation,";
        $sql.=" F.KYN_EMAIL_ADDRESS AS Functional_Manager,";
        $sql.=" P.MATRIX_MANAGER_EMAIL AS Workday_Manager,";
        $sql.=" P.CT_ID AS CT_ID,";
        $sql.=" P.LBG_EMAIL AS LBG_Email_Address,";
        $sql.=" P.EMAIL_ADDRESS AS Email_Address_VLookUp";

        // $sql.=personTable::ORGANISATION_SELECT_ALL.", ";
        // $sql.=personTable::FLM_SELECT.", ";
        // $sql.=personTable::SLM_SELECT.", ";

        $sql.= personTable::getTablesForQuery();
        $sql.= " WHERE 1=1 ";
        // $sql.= " AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
        $sql.= $predicate;
        $sql.= " ORDER BY P.KYN_EMAIL_ADDRESS ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        if ($resultSetOnly) {
            return $rs;
        }

        $data = array();
        while (($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) == true) {
            //$report[] = array_map('trim', $row);
            $data[] = array_map('trim', $row);
        }

        return array('data' => $data, 'sql' => $sql);
    }
}
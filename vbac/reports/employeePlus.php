<?php

namespace vbac\reports;

use itdq\DbTable;
use vbac\AgileSquadRecord;
use vbac\AgileTribeRecord;
use vbac\interfaces\report;
use vbac\personRecord;
use vbac\personTable;
use vbac\staticDataSkillsetsRecord;

class employeePlus implements report
{
    const ADDITIONAL_FIELDS = array(
        "ROLE_ON_THE_ACCOUNT",
        "COUNTRY", 
        "START_DATE", 
        "PROJECTED_END_DATE", 
        "SQUAD_NUMBER",
        "SQUAD_NAME",
        "SQUAD_LEADER",
        "TRIBE_NUMBER",
        "TRIBE_NAME",
        "TRIBE_LEADER",
        "CNUM",
        "WORKER_ID",
        "CFIRST_ID",
        "OFFBOARDED_DATE",
        "ORGANISATION",
        "FM",
        "SM"
    );

    public function getReport($resultSetOnly = false)
    {
        $withProvClear = null;

        // default fields
        $additionalSelect = " P.KYN_EMAIL_ADDRESS, ";
        $additionalSelect .= personTable::getStatusSelect($withProvClear, 'P');

        $onlyActiveBool = false;
        $onlyActiveInTimeBool = false;

        if (!is_null(self::ADDITIONAL_FIELDS)) {

            $personRecord = new personRecord();
            $availablePersonColumns = $personRecord->getColumns();
            $personTableAliases = array('P.', 'F.', 'U.');

            $agileSquadRecord = new AgileSquadRecord();
            $availableAgileSquadColumns = $agileSquadRecord->getColumns();
            $agileSquadTableAliases = array('AS1.');

            $agileTribeRecord = new AgileTribeRecord();
            $availableAgileTribeColumns = $agileTribeRecord->getColumns();
            $agileTribeTableAliases = array('AT.');

            $skillsetRecord = new staticDataSkillsetsRecord();
            $skillsetRecordColumns = $skillsetRecord->getColumns();
            $skillsetTableAliases = array('SS.');

            foreach (self::ADDITIONAL_FIELDS as $field) {

                $field = trim($field);

                // an additional mapping
                switch($field) {
                    case 'ORGANISATION':
                        $fieldExpression = personTable::ORGANISATION_SELECT_ALL;
                        $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                        continue 2;
                        break;
                    case 'FM':
                        $fieldExpression = personTable::FLM_SELECT;
                        $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                        continue 2;
                        break;
                    case 'SM':
                        $fieldExpression = personTable::SLM_SELECT;
                        $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                        continue 2;
                        break;
                    default:
                        break;
                }

                // validate field against PERSON table
                $tableField = str_replace($personTableAliases, '', $field);

                if (array_key_exists($tableField, $availablePersonColumns)) {
                    $additionalSelect .= ", " . htmlspecialchars("P.".$tableField);
                    continue;
                }
                
                // validate field against AGILE_SQUAD table
                $tableField = str_replace($agileSquadTableAliases, '', $field);

                if (array_key_exists($tableField, $availableAgileSquadColumns)) {
                    $additionalSelect .= ", " . htmlspecialchars("AS1.".$tableField);
                    continue;
                }

                // validate field against AGILE_TRIBE table
                $tableField = str_replace($agileTribeTableAliases, '', $field);

                if (array_key_exists($tableField, $availableAgileTribeColumns)) {
                    $additionalSelect .= ", " . htmlspecialchars("AT.".$tableField);
                    continue;
                }

                // validate field against STATIC_SKILLSET table
                $tableField = str_replace($skillsetTableAliases, '', $field);

                if (array_key_exists($tableField, $skillsetRecordColumns)) {
                    $additionalSelect .= ", " . htmlspecialchars("SS.".$tableField);
                    continue;
                }
            }
        }

        $sql = " SELECT DISTINCT ";
        $sql.= $additionalSelect;
        $sql.= personTable::getTablesForQuery();
        $sql.= " WHERE 1=1 AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
        // $sql.= $onlyActiveBool ? " AND " . personTable::activePersonPredicate($withProvClear, 'P') : null;
        // $sql.= $onlyActiveInTimeBool ? " AND (" . personTable::activePersonPredicate($withProvClear, 'P') . " OR P.OFFBOARDED_DATE > '" . $offboardedDate->format('Y-m-d') . "')" : null;
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
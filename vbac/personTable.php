<?php
namespace vbac;

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use itdq\Loader;
use itdq\slack;
use vbac\personRecord;
use vbac\pesTrackerTable;

class personTable extends DbTable
{

    private $preparedRevalidationStmt;
    private $preparedRevalidationLeaverStmt;
    private $preparedRevalidationPotentialLeaverStmt;
    private $preparedLeaverProjectedEndDateStmt;
    private $preparedUpdateBluepagesFields;
    private $preparedUpdateLbgLocationStmt;
    private $preparedUpdateSecurityEducationStmt;

    public $employeeTypeMapping;

    private $allNotesIdByCnum;
    private $allKyndrylEmailIdByCnum;
    private $loader;

    private $thirtyDaysHence;

    private $slack;

    private $squadNames;

    protected $allDelegates;

    const PORTAL_PRE_BOARDER_EXCLUDE = 'exclude';
    const PORTAL_PRE_BOARDER_INCLUDE = 'include';
    const PORTAL_PRE_BOARDER_WITH_LINKED = 'withLinked';
    const PORTAL_ONLY_ACTIVE = 'onlyActive';
    const PORTAL_ONLY_ARCHIVED = 'onlyArchived';

    const PERSON_DETAILS = 'Details';
    const PERSON_DETAILS_FULL = 'Details Full';
    const PERSON_DETAILS_ACTIVE = 'Details Active';
    const PERSON_DETAILS_ACTIVE_ODC = 'Details Active Odc';
    const PERSON_DETAILS_INACTIVE = 'Details Inactive';
    const PERSON_BAU = 'Bau';
    const ORGANISATION_SELECT = 'CASE WHEN AS1.ORGANISATION is null THEN AT.ORGANISATION ELSE AS1.ORGANISATION END AS ORGANISATION';

    private static $revalStatusChangeEmail = 'Functional Manager,'
        . '<br/>You have been identified from VBAC as being the functional manager of :  &&leaversNotesid&&'
        . '<br/>This is to inform you that their Revalidation Status has been set to : &&revalidationStatus&&'
        . '<br/>This status means : &&statusDescription&&'
        . '<br/>If you feel this is an error, please contact your local PMO Team ';
    private static $revalStatusChangeEmailPattern = array('/&&leaversNotesid&&/', '/&&revalidationStatus&&/', '/&&statusDescription&&/');

    const ACTIVE_WITH_PROVISIONAL_CLEARANCE = true;
    const ACTIVE_WITHOUT_PROVISIONAL_CLEARANCE = false;

    const PES_LEVEL_ONE = 'Level 1';
    const PES_LEVEL_TWO = 'Level 2';
    const PES_LEVEL_DEFAULT = self::PES_LEVEL_TWO;

    const ACCT_ACCESS_SPRH = 'SPRH';
    const ACCT_ACCESS_SRH = 'SRH';

    private static $pesRecheckPeriods = array(self::PES_LEVEL_ONE => '1 Year', self::PES_LEVEL_TWO => '3 Years'); // must be Db2 date period

    private static $excludeFromRecheckNotification = "'" . personRecord::PES_STATUS_RECHECK_REQ . "'"
    . ",'" . personRecord::PES_STATUS_RECHECK_PROGRESSING . "'"
    . ",'" . personRecord::PES_STATUS_PROVISIONAL . "'"
    . ",'" . personRecord::PES_STATUS_LEFT_IBM . "'"
    . ",'" . personRecord::PES_STATUS_REVOKED . "'"
    . ",'" . personRecord::PES_STATUS_DECLINED . "'"
    . ",'" . personRecord::PES_STATUS_MOVER . "'"
    . ",'" . personRecord::PES_STATUS_REQUESTED . "'"
    . ",'" . personRecord::PES_STATUS_FAILED . "'"
    . ",'" . personRecord::PES_STATUS_INITIATED . "'"
    . ",'" . personRecord::PES_STATUS_REMOVED . "'";

    public function __construct($table, $pwd = null, $log = true)
    {
        $this->slack = new slack();

        $this->allDelegates = delegateTable::allDelegates();

        $this->thirtyDaysHence = new \DateTime();
        $this->thirtyDaysHence->add(new \DateInterval('P60D')); // Modified 4th July 2017


        parent::__construct($table, $pwd, $log);
    }

    public static function getNextVirtualCnum()
    {
        $sql = " SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM LIKE '%XXX' or CNUM LIKE '%xxx' or CNUM LIKE '%999' ";
        $sql .= " order by CNUM desc ";
        $sql .= " OPTIMIZE FOR 1 ROW ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $topRow = sqlsrv_fetch_array($rs);
        if (isset($topRow[0])) {
            $thisCnum = substr($topRow[0], 1, 5);
            $next = $thisCnum + 1;
            $nextVirtualCnum = 'V' . substr('000000' . $next, -5) . 'XXX';
        } else {
            $nextVirtualCnum = 'V00001XXX';
        }
        return $nextVirtualCnum;
    }

    public static function activePersonPredicate($includeProvisionalClearance = true, $tableAbbrv = null)
    {
        $activePredicate = " ((( ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "REVALIDATION_STATUS in ('" . personRecord::REVALIDATED_FOUND . "','" . personRecord::REVALIDATED_VENDOR . "','" . personRecord::REVALIDATED_POTENTIAL . "')";
        $activePredicate .= " OR ";
        $activePredicate .= " trim( ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "REVALIDATION_STATUS) is null or ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDING . "%') ";
        $activePredicate .= " OR ";
        $activePredicate .= " ( trim( ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "REVALIDATION_STATUS) is null ) )";
        $activePredicate .= " AND ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "REVALIDATION_STATUS not like '" . personRecord::REVALIDATED_OFFBOARDING . "%:%" . personRecord::REVALIDATED_LEAVER . "%' ";
        $activePredicate .= " AND ";
        $activePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $activePredicate .= "PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED . "','" . personRecord::PES_STATUS_CLEARED_PERSONAL . "','" . personRecord::PES_STATUS_CLEARED_AMBER . "','" . personRecord::PES_STATUS_EXCEPTION . "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING . "','" . personRecord::PES_STATUS_MOVER . "'";
        $activePredicate .= $includeProvisionalClearance ? ",'" . personRecord::PES_STATUS_PROVISIONAL . "'" : null;
        $activePredicate .= " ) ) ";
        return $activePredicate;
    }

    public static function inactivePersonPredicate($includeProvisionalClearance = true, $tableAbbrv = null)
    {
        $inactivePredicate = " ((( ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "REVALIDATION_STATUS in ('" . personRecord::REVALIDATED_FOUND . "','" . personRecord::REVALIDATED_VENDOR . "','" . personRecord::REVALIDATED_POTENTIAL . "'";
        $inactivePredicate .= " OR ";
        $inactivePredicate .= " trim( ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "REVALIDATION_STATUS) is null or ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDING . "%') ";
        $inactivePredicate .= " OR ";
        $inactivePredicate .= " ( trim( ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "REVALIDATION_STATUS) is null ) )";
        $inactivePredicate .= " AND ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "REVALIDATION_STATUS not like '" . personRecord::REVALIDATED_OFFBOARDING . "%:%" . personRecord::REVALIDATED_LEAVER . "%' ";
        $inactivePredicate .= " AND ";
        $inactivePredicate .= !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $inactivePredicate .= "PES_STATUS not in (" . self::$excludeFromRecheckNotification . ") ";
        $inactivePredicate .= " ) ) ";
        return $inactivePredicate;
    }

    public static function archivedPersonPredicate($includeProvisionalClearance = true, $tableAbbrv = null)
    {
        $archivedPredicate = !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $archivedPredicate .= "PES_STATUS in ('" . personRecord::PES_STATUS_LEFT_IBM . "','" . personRecord::PES_STATUS_REMOVED . "','" . personRecord::PES_STATUS_DECLINED . "')";
        return $archivedPredicate;   
    }

    public static function notArchivedPersonPredicate($includeProvisionalClearance = true, $tableAbbrv = null)
    {
        $archivedPredicate = !empty($tableAbbrv) ? $tableAbbrv . "." : null;
        $archivedPredicate .= "PES_STATUS not in ('" . personRecord::PES_STATUS_LEFT_IBM . "','" . personRecord::PES_STATUS_REMOVED . "','" . personRecord::PES_STATUS_DECLINED . "')";
        return $archivedPredicate;   
    }

    public static function odcPredicate()
    {
        $odcPredicate = " ( lower(LBG_LOCATION) LIKE '%pune' or lower(LBG_LOCATION)  LIKE '%bangalore' ) ";
        return $odcPredicate;
    }

    public function getForRfFlagReport($resultSetOnly = false, $withButtons = true)
    {
        $sql = "select P.cnum ";
        $sql .= " ,P.NOTES_ID ";
        $sql .= ", P.LOB ";
        $sql .= ", P.CTB_RTB ";
        $sql .= ", case when F.notes_id is not null then F.NOTES_ID else P.FM_CNUM end as FM ";
        $sql .= ", P.REVALIDATION_STATUS as REVAL ";
        $sql .= ", P.PROJECTED_END_DATE as EXP ";
        $sql .= ", P.RF_Start as FROM ";
        $sql .= ", P.RF_End as TO ";

        $sql .= " from  " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql .= " on P.FM_CNUM = F.CNUM ";
        $sql .= " WHERE P.RF_FLAG = '1' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        if ($resultSetOnly) {
            return $rs;
        }

        $data = array();
        while (($row = sqlsrv_fetch_array($rs)) == true) {
            //$report[] = array_map('trim', $row);
            $data[] = $withButtons ? $this->addRfflagButtons(array_map('trim', $row)) : array_map('trim', $row);
        }

        return array('data' => $data, 'sql' => $sql);
    }

    public function addRfflagButtons($row)
    {
        $deleteButton = "<button type='button' class='btn btn-default btn-xs btnDeleteRfFlag btn-danger' aria-label='Left Align' ";
        $deleteButton .= "data-cnum='" . trim($row['CNUM']) . "' ";
        $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Ring Fence'";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";
        $notesId = $row['NOTES_ID'];

        $row['NOTES_ID'] = $deleteButton . "&nbsp;" . $notesId;
        return $row;
    }

    public static function preparePersonStmt($start = 0, $length = 10, $preBoardersPredicate = null, $predicate = null, $sorting = null)
    {
        $sql = " SELECT P.*,
        SS.SKILLSET,
        PT.PROCESSING_STATUS,
        PT.PROCESSING_STATUS_CHANGED,
        AT.TRIBE_NUMBER,
        AT.TRIBE_NAME,
        AT.TRIBE_LEADER,
        AT.ITERATION_MGR,
        AS1.SQUAD_NUMBER,
        AS1.SQUAD_NAME, 
        AS1.SHIFT,
        AS1.SQUAD_LEADER,";
        $sql .= self::ORGANISATION_SELECT;
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " as PT ";
        $sql .= " ON P.CNUM = PT.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS1 ";
        $sql .= " ON P.SQUAD_NUMBER = AS1.SQUAD_NUMBER ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " as AT ";
        $sql .= " ON AS1.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " as SS ";
        $sql .= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
        $sql .= " WHERE " . $preBoardersPredicate;
        $sql .= !empty($predicate) ? " $predicate " : null;
        $sql .= !empty($sorting) ? " $sorting " : null;

        if ($length != '-1') {
            // $sql .= " LIMIT " . $length . ' OFFSET ' . $start;
            $sql .= ' OFFSET ' . $start . ' ROWS FETCH FIRST ' . $length . ' ROWS ONLY';
        }

        return $sql;
    }

    public static function preparePersonCountStmt($preBoardersPredicate = null, $predicate = null)
    {
        $sql = " SELECT COUNT(*) AS COUNTER ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " as PT ";
        $sql .= " ON PT.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS1 ";
        $sql .= " ON AS1.SQUAD_NUMBER = P.SQUAD_NUMBER ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " as AT ";
        $sql .= " ON AS1.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " as SS ";
        $sql .= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
        $sql .= " WHERE " . $preBoardersPredicate;
        $sql .= !empty($predicate) ? " $predicate " : null;

        return $sql;
    }

    public static function recordsFiltered($preBoardersPredicate = null, $predicate = null)
    {
        $sql = self::preparePersonCountStmt($preBoardersPredicate, $predicate);

        $data = array();
        $preparedCountStatement = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        $rs = sqlsrv_execute($preparedCountStatement);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $counter = 0;
        while ($row = sqlsrv_fetch_array($preparedCountStatement)) {
            $counter = $row['COUNTER'];
        }
        return $counter;
    }

    public static function totalRows($preBoardersPredicate = null)
    {
        $sql = self::preparePersonCountStmt($preBoardersPredicate);

        $data = array();
        $preparedCountStatement = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        $rs = sqlsrv_execute($preparedCountStatement);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $counter = 0;
        while ($row = sqlsrv_fetch_array($preparedCountStatement)) {
            $counter = $row['COUNTER'];
        }
        return $counter;
    }

    public function returnAsArray($start = 0, $length = 10, $preBoardersPredicate = null, $predicate = null, $sorting = null)
    {
        $sql = self::preparePersonStmt($start, $length, $preBoardersPredicate, $predicate, $sorting);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        $allData = array();
        $allData['data'] = array();

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while (($row = sqlsrv_fetch_array($rs)) == true) {
                // Only editable, if they're not a "pre-Boarder" who has now been boarded.
                $preparedRow = $this->prepareFields($row);
                $rowWithButtonsAdded = (substr($row['PES_STATUS_DETAILS'], 0, 10) == personRecord::PES_STATUS_DETAILS_BOARDED_AS) ? $preparedRow : $this->addButtons($preparedRow);
                $allData['data'][] = $rowWithButtonsAdded;
            }
        }

        $allData['sql'] = $sql;

        return $allData;
    }

    public function returnManualUpdateArray()
    {
        $predicate = " PES_STATUS in ('" . personRecord::PES_STATUS_NOT_REQUESTED . "')";
        $data = array();

        $sql = " SELECT CNUM, FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, NOTES_ID, PES_STATUS ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE " . $predicate;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while (($row = sqlsrv_fetch_array($rs)) == true) {
                // $cnum = trim($row['CNUM']);
                // $preparedRow = $this->prepareFields($row);
                // $fmCnumField = $preparedRow['FM_CNUM'];
                // $transferButton = "<button type='button' class='btn btn-default btn-xs btnTransfer' aria-label='Left Align' ";
                // $transferButton.= "data-cnum='" .$cnum . "' ";
                // $transferButton.= "data-notesid='" .trim($row['NOTES_ID']) . "' ";
                // $transferButton.= "data-fromCnum ='" .trim($row['FM_CNUM']) . "' ";
                // $transferButton.= "data-fromNotesid ='" .$preparedRow['FM_CNUM'] . "' ";
                // $transferButton.= " > ";
                // $transferButton.= "<span class='glyphicon glyphicon-transfer ' aria-hidden='true'></span>";
                // $transferButton.= " </button> ";
                // $preparedRow['FM_CNUM'] = $transferButton . $fmCnumField;
                // $data[] = $preparedRow;
                $data[] = $row;
            }
        }
        return $data;
    }

    public function returnPersonFinderArray($includeProvisionalClearance = false)
    {
        $activePredicate = $this->activePersonPredicate($includeProvisionalClearance);
        $data = array();

        $sql = " SELECT 
            CNUM, 
            FIRST_NAME, 
            LAST_NAME, 
            EMAIL_ADDRESS, 
            KYN_EMAIL_ADDRESS, 
            NOTES_ID, 
            FM_CNUM ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE " . $activePredicate;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while (($row = sqlsrv_fetch_array($rs)) == true) {
                $cnum = trim($row['CNUM']);
                $preparedRow = $this->prepareFields($row);
                $fmCnumField = $preparedRow['FM_CNUM'];
                $transferButton = "<button type='button' class='btn btn-default btn-xs btnTransfer' aria-label='Left Align' ";
                $transferButton .= "data-cnum='" . $cnum . "' ";
                $transferButton .= "data-notesid='" . trim($row['NOTES_ID']) . "' ";
                $transferButton .= "data-fromCnum ='" . trim($row['FM_CNUM']) . "' ";
                $transferButton .= "data-fromNotesid ='" . $preparedRow['FM_CNUM'] . "' ";
                $transferButton .= " > ";
                $transferButton .= "<span class='glyphicon glyphicon-transfer ' aria-hidden='true'></span>";
                $transferButton .= " </button> ";
                $preparedRow['FM_CNUM'] = $transferButton . $fmCnumField;
                $data[] = $preparedRow;
            }
        }
        return array('data' => $data, 'sql' => $sql);
    }

    public function findDirtyData($autoClear = false)
    {
        $sql = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " ORDER BY CNUM ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while (($row = sqlsrv_fetch_array($rs)) == true) {
                $jsonEncodable = json_encode($row);
                if (!$jsonEncodable) {
                    echo "<hr/><br/>Dirty Data Found in record for : " . $row['CNUM'];
                    foreach ($row as $key => $value) {
                        $jsonEncodableField = json_encode($value);
                        if (!$jsonEncodableField) {
                            echo "Column: $key Value: $value";
                            if ($autoClear && !$jsonEncodable) {
                                $row[$key] = null;
                                $personRecord = new personRecord();
                                $personRecord->setFromArray($row);
                                $this->saveRecord($personRecord);
                            }
                        }
                    }
                }
            }
        }
    }

    public function prepareFields($row)
    {
        $this->loader = empty($this->loader) ? new Loader() : $this->loader;
        $this->allKyndrylEmailIdByCnum = empty($this->allKyndrylEmailIdByCnum) ? $this->loader->loadIndexed('KYN_EMAIL_ADDRESS', 'CNUM', allTables::$PERSON) : $this->allKyndrylEmailIdByCnum;
        $this->employeeTypeMapping = empty($this->employeeTypeMapping) ? $this->loader->loadIndexed('DESCRIPTION', 'CODE', allTables::$EMPLOYEE_TYPE_MAPPING) : $this->employeeTypeMapping;

        $preparedRow = array_map('trim', $row);
        $fmNotesid = isset($this->allKyndrylEmailIdByCnum[trim($row['FM_CNUM'])]) ? $this->allKyndrylEmailIdByCnum[trim($row['FM_CNUM'])] : trim($row['FM_CNUM']);
        $preparedRow['fmCnum'] = $row['FM_CNUM'];
        $preparedRow['FM_CNUM'] = $fmNotesid;

        if (isset($preparedRow['EMPLOYEE_TYPE'])) {
            $preparedRow['EMPLOYEE_TYPE'] = isset($this->employeeTypeMapping[strtoupper($preparedRow['EMPLOYEE_TYPE'])]) ? $this->employeeTypeMapping[strtoupper($preparedRow['EMPLOYEE_TYPE'])] : $preparedRow['EMPLOYEE_TYPE'];
            $preparedRow['EMPLOYEE_TYPE'] = ucwords($preparedRow['EMPLOYEE_TYPE'], ' -');
        }
        return $preparedRow;
    }

    public function addButtons($row)
    {
        // save some fields before we change the,
        $notesId = trim($row['NOTES_ID']);
        $email = trim($row['EMAIL_ADDRESS']);
        $kyndrylEmail = trim($row['KYN_EMAIL_ADDRESS']);
        $cnum = trim($row['CNUM']);
        $employeeType = trim($row['EMPLOYEE_TYPE']);
        $row['actualCNUM'] = $cnum;
        $flag = isset($row['FM_MANAGER_FLAG']) ? $row['FM_MANAGER_FLAG'] : null;
        $status = empty($row['PES_STATUS']) ? personRecord::PES_STATUS_NOT_REQUESTED : trim($row['PES_STATUS']);
        $projectedEndDateObj = !empty($row['PROJECTED_END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['PROJECTED_END_DATE']) : false;
        $potentialForOffboarding = $projectedEndDateObj ? $projectedEndDateObj <= $this->thirtyDaysHence : false; // Thirty day rule.
        $potentialForOffboarding = $potentialForOffboarding || $row['REVALIDATION_STATUS'] == personRecord::REVALIDATED_LEAVER ? true : $potentialForOffboarding; // Any leaver - has potential to be offboarded
        $potentialForOffboarding = substr(trim($row['REVALIDATION_STATUS']), 0, 10) == personRecord::REVALIDATED_OFFBOARDED ? false : $potentialForOffboarding;
        $potentialForOffboarding = substr(trim($row['REVALIDATION_STATUS']), 0, 11) == personRecord::REVALIDATED_OFFBOARDING ? false : $potentialForOffboarding;
        $potentialForOffboarding = trim($row['REVALIDATION_STATUS']) == personRecord::REVALIDATED_PREBOARDER ? true : $potentialForOffboarding;

        $offboardingHint = $projectedEndDateObj <= $this->thirtyDaysHence ? '&nbsp;End date within 60 days' : null; // Thirty day rule. (MOdified 4th July
        $offboardingHint = $row['REVALIDATION_STATUS'] == personRecord::REVALIDATED_LEAVER ? '&nbsp;Flagged as Leaver' : $offboardingHint; // flagged as a leaver.
        $offboardingHint = $row['REVALIDATION_STATUS'] == personRecord::REVALIDATED_PREBOARDER ? '&nbsp;Is a preboarder' : $offboardingHint; // flagged as a preboarder.

        $revalidationStatus = trim($row['REVALIDATION_STATUS']);
        $ctid = trim($row['CT_ID']);

        if (!empty($row['PRE_BOARDED'])) {
            $row['CNUM'] = $cnum . "<br/><small>" . $row['PRE_BOARDED'] . "</small>";
        }

        // PMO_STATUS
        if ($_SESSION['isPmo'] || $_SESSION['isCdi']) {
            // depending on what the current status is - well give buttons to set to "Confirmed" or "Aware";
            $pmoStatus = trim($row['PMO_STATUS']);
            $pmoStatus = empty($pmoStatus) ? personRecord::PMO_STATUS_TBA : $pmoStatus;
            $row['PMO_STATUS'] = "";

            if ($pmoStatus == personRecord::PMO_STATUS_TBA || $pmoStatus == personRecord::PMO_STATUS_AWARE) {
                $row['PMO_STATUS'] .= "<button type='button' class='btn btn-default btn-xs btnSetPmoStatus' aria-label='Left Align' ";
                $row['PMO_STATUS'] .= "data-cnum='" . $cnum . "' ";
                $row['PMO_STATUS'] .= "data-setpmostatusto='" . personRecord::PMO_STATUS_CONFIRMED . "' ";
                $row['PMO_STATUS'] .= " data-toggle='tooltip' data-placement='top' title='Set PMO Status Aware'";
                $row['PMO_STATUS'] .= " > ";
                $row['PMO_STATUS'] .= "<span class='glyphicon glyphicon-thumbs-up ' aria-hidden='true'></span>";
                $row['PMO_STATUS'] .= " </button> ";
            }

            if ($pmoStatus == personRecord::PMO_STATUS_TBA || $pmoStatus == personRecord::PMO_STATUS_CONFIRMED) {
                $row['PMO_STATUS'] .= "<button type='button' class='btn btn-default btn-xs btnSetPmoStatus' aria-label='Left Align' ";
                $row['PMO_STATUS'] .= "data-cnum='" . $cnum . "' ";
                $row['PMO_STATUS'] .= "data-setpmostatusto='" . personRecord::PMO_STATUS_AWARE . "' ";
                $row['PMO_STATUS'] .= " data-toggle='tooltip' data-placement='top' title='Set PMO Status Confirmed'";
                $row['PMO_STATUS'] .= " > ";
                $row['PMO_STATUS'] .= "<span class='glyphicon glyphicon-thumbs-down ' aria-hidden='true'></span>";
                $row['PMO_STATUS'] .= " </button> ";
            }
            $row['PMO_STATUS'] .= "&nbsp;" . $pmoStatus;
        }

        // FM_MANAGER_FLAG
        if ($_SESSION['isPmo'] || $_SESSION['isCdi']) {
            if (strtoupper(substr($flag, 0, 1)) == 'N' || empty($flag)) {
                $row['FM_MANAGER_FLAG'] = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row['FM_MANAGER_FLAG'] .= "data-cnum='" . $cnum . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-notesid='" . $notesId . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-fmflag='Yes' ";
                $row['FM_MANAGER_FLAG'] .= " data-toggle='tooltip' data-placement='top' title='Toggle FM Flag'";
                $row['FM_MANAGER_FLAG'] .= " > ";
                $row['FM_MANAGER_FLAG'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row['FM_MANAGER_FLAG'] .= " </button> ";
            } elseif (strtoupper(substr($flag, 0, 1) == 'Y')) {
                $row['FM_MANAGER_FLAG'] = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row['FM_MANAGER_FLAG'] .= "data-cnum='" . $cnum . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-notesid='" . $notesId . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-fmflag='No' ";
                $row['FM_MANAGER_FLAG'] .= " data-toggle='tooltip' data-placement='top' title='Toggle FM Flag'";
                $row['FM_MANAGER_FLAG'] .= " > ";
                $row['FM_MANAGER_FLAG'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row['FM_MANAGER_FLAG'] .= " </button> ";
            }
            $row['FM_MANAGER_FLAG'] .= $flag;
        }

        if ($_SESSION['isPes'] || $_SESSION['isPmo'] || $_SESSION['isFm'] || $_SESSION['isCdi']) {
            $row['PES_STATUS'] = self::getPesStatusWithButtons($row);
        } else {
            $row['PES_STATUS'] = array('display' => $row['PES_STATUS'], 'sort' => $row['PES_STATUS']);
        }

        if ($_SESSION['isPes'] || $_SESSION['isPmo'] || $_SESSION['isFm'] || $_SESSION['isCdi']) {
            $row['PES_LEVEL'] = self::getPesLevelWithButtons($row);
        } else {
            $row['PES_LEVEL'] = array('display' => $row['PES_LEVEL'], 'sort' => $row['PES_LEVEL']);
        }

        $row['EMAIL_ADDRESS'] = '';
        if (($_SESSION['isCdi'])) {
            $row['EMAIL_ADDRESS'] .= "<button type='button' class='btn btn-default btn-xs btnEditEmail' aria-label='Left Align' ";
            $row['EMAIL_ADDRESS'] .= " data-cnum='" . $cnum . "'";
            $row['EMAIL_ADDRESS'] .= " data-email='" . $email . "'";
            $row['EMAIL_ADDRESS'] .= " data-toggle='tooltip' data-placement='top' title='Edit Email Address'";
            $row['EMAIL_ADDRESS'] .= " > ";
            $row['EMAIL_ADDRESS'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $row['EMAIL_ADDRESS'] .= " </button> ";
        }
        $row['EMAIL_ADDRESS'] .= $email;

        $row['KYN_EMAIL_ADDRESS'] = '';
        if (($_SESSION['isCdi'])) {
            if (endsWith($email, 'ocean.ibm.com')) {
                $row['KYN_EMAIL_ADDRESS'] .= "<button type='button' class='btn btn-default btn-xs btnEditKyndrylEmail' aria-label='Left Align' ";
                $row['KYN_EMAIL_ADDRESS'] .= " data-cnum='" . $cnum . "'";
                $row['KYN_EMAIL_ADDRESS'] .= " data-email='" . $kyndrylEmail . "'";
                $row['KYN_EMAIL_ADDRESS'] .= " data-toggle='tooltip' data-placement='top' title='Edit Kyndryl Email Address'";
                $row['KYN_EMAIL_ADDRESS'] .= " > ";
                $row['KYN_EMAIL_ADDRESS'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row['KYN_EMAIL_ADDRESS'] .= " </button> ";
            }
        }
        $row['KYN_EMAIL_ADDRESS'] .= $kyndrylEmail;

        $btnColor = isset($this->allDelegates[$row['CNUM']]) ? 'btn-success' : 'btn-secondary';
        $row['NOTES_ID'] = "<button ";
        $row['NOTES_ID'] .= " type='button' class='btn $btnColor  btn-xs ' aria-label='Left Align' ";

        if (isset($this->allDelegates[$row['CNUM']])) {
            $delegates = implode(",", $this->allDelegates[$row['CNUM']]);
            $row['NOTES_ID'] .= " data-placement='bottom' data-toggle='popover' title='' data-content='$delegates' data-original-title='Delegates' ";
            $row['HAS_DELEGATES'] = 'Yes';
        } else {
            $row['NOTES_ID'] .= " data-placement='bottom' data-toggle='popover' title='' data-content='Has not defined a delegate' data-original-title='Delegates' ";
            $row['HAS_DELEGATES'] = 'No';
        }

        $row['NOTES_ID'] .= " > ";
        $row['NOTES_ID'] .= "<i class='fas fa-user-friends'></i>";
        $row['NOTES_ID'] .= " </button>";

        $btnClass = 'btnEditPerson';
        switch(strtolower($employeeType)) {
            case strtolower(personRecord::EMPLOYEE_TYPE_REGULAR):
            case strtolower(personRecord::EMPLOYEE_TYPE_CONTRACTOR):
                $btnClass = 'btnEditRegularPerson';
                break;
            case strtolower(personRecord::EMPLOYEE_TYPE_PRE_HIRE):
            case strtolower(personRecord::EMPLOYEE_TYPE_PREBOARDER):
            case strtolower(personRecord::EMPLOYEE_TYPE_VENDOR):
                $btnClass = 'btnEditVendorPerson';
                break;
        }

        if (($_SESSION['isPes'] || $_SESSION['isPmo'] || $_SESSION['isFm'] || $_SESSION['isCdi']) && ($revalidationStatus != personRecord::REVALIDATED_OFFBOARDED)) {
            $row['NOTES_ID'] .= "<button type='button' class='btn btn-default btn-xs ".$btnClass."' aria-label='Left Align' ";
            $row['NOTES_ID'] .= "data-cnum='" . $cnum . "'";
            $row['NOTES_ID'] .= " data-toggle='tooltip' data-placement='top' title='Edit Person Record (".$employeeType.")'";
            $row['NOTES_ID'] .= " > ";
            $row['NOTES_ID'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $row['NOTES_ID'] .= " </button> ";
        }

        $row['NOTES_ID'] .= $notesId;

        if (($_SESSION['isPmo'] || $_SESSION['isCdi']) && (substr(trim($row['REVALIDATION_STATUS']), 0, 11) == personRecord::REVALIDATED_OFFBOARDING)) {
            $row['REVALIDATION_STATUS'] = "<button type='button' class='btn btn-default btn-xs btnStopOffboarding btn-danger' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" . $cnum . "'";
            $row['REVALIDATION_STATUS'] .= " data-toggle='tooltip' data-placement='top' title='Stop Offboarding Process'";
            $row['REVALIDATION_STATUS'] .= "title='Stop Offboarding'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-remove-sign ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= "<button type='button' class='btn btn-default btn-xs btnOffboarded btn-danger' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" . $cnum . "'";
            $row['REVALIDATION_STATUS'] .= "title='Complete Offboarding.'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-out ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= $revalidationStatus;
        }

        if ($potentialForOffboarding && ($_SESSION['isPmo'] || $_SESSION['isCdi']) && substr(trim($row['REVALIDATION_STATUS']), 0, 11) != personRecord::REVALIDATED_OFFBOARDING) {
            $row['REVALIDATION_STATUS'] = "<button type='button' class='btn btn-default btn-xs btnOffboarding btn-warning' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" . $cnum . "'";
            $row['REVALIDATION_STATUS'] .= " data-toggle='tooltip' data-placement='top' title='Initiate Offboarding." . $offboardingHint . "' ";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-out ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= $revalidationStatus;
        }

        if (($_SESSION['isPmo'] || $_SESSION['isCdi']) && substr(trim($row['REVALIDATION_STATUS']), 0, 10) == personRecord::REVALIDATED_OFFBOARDED) {
            $row['REVALIDATION_STATUS'] = "<button type='button' class='btn btn-default btn-xs btnDeoffBoarding btn-danger' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" . $cnum . "'";
            $row['REVALIDATION_STATUS'] .= "title='Bring back from Offboarding.'";
            $row['REVALIDATION_STATUS'] .= " data-toggle='tooltip' data-placement='top' title='Recover person from Offboarding'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-in ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= $revalidationStatus;
        }

        if (($_SESSION['isPmo'] || $_SESSION['isCdi']) && !empty($ctid)) {
            $row['CT_ID'] = "<button type='button' class='btn btn-default btn-xs btnClearCtid btn-danger' aria-label='Left Align' ";
            $row['CT_ID'] .= "data-cnum='" . $cnum . "'";
            $row['CT_ID'] .= "title='Delete CT ID.'";
            $row['CT_ID'] .= " data-toggle='tooltip' data-placement='top' title='Clear CT ID'";
            $row['CT_ID'] .= " > ";
            $row['CT_ID'] .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
            $row['CT_ID'] .= " </button> ";
            $row['CT_ID'] .= $ctid;
        }

        $functionalMgr = $row['FM_CNUM'];
        $btnColor = isset($this->allDelegates[$row['fmCnum']]) ? 'btn-success' : 'btn-secondary';

        $row['FM_CNUM'] = "<button ";
        $row['FM_CNUM'] .= " type='button' class='btn $btnColor  btn-xs ' aria-label='Left Align' ";

        if (isset($this->allDelegates[$row['fmCnum']])) {
            $delegates = implode(",", $this->allDelegates[$row['fmCnum']]);
            $row['FM_CNUM'] .= " data-placement='bottom' data-toggle='popover' title='' data-content='$delegates' data-original-title='Delegates' ";
        } else {
            $row['FM_CNUM'] .= " data-placement='bottom' data-toggle='popover' title='' data-content='Has not defined a delegate' data-original-title='Delegates' ";
        }
        $row['FM_CNUM'] .= " > ";
        $row['FM_CNUM'] .= "<i class='fas fa-user-friends'></i>";
        $row['FM_CNUM'] .= " </button>";
        $row['FM_CNUM'] .= $functionalMgr;

        $row['SQUAD_NAME'] = $this->getAgileSquadWithButtons($row, true);
        $row['OLD_SQUAD_NAME'] = $this->getAgileSquadWithButtons($row, false);

        return $row;
    }

    public function setPesRequested($cnum = null, $requestor = null, $recheck = false)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }
        $statusToSetTo = $recheck ? personRecord::PES_STATUS_RECHECK_PROGRESSING : personRecord::PES_STATUS_INITIATED;
        $result = $this->setPesStatus($cnum, $statusToSetTo, $requestor);
        return $result;
    }

    public function setPesEvidence($cnum = null, $requestor = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }
        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;
        $result = $this->setPesStatus($cnum, personRecord::PES_STATUS_REQUESTED, $requestor);
        return $result;
    }

    public function setPesStatus($cnum = null, $status = null, $requestor = null, $dateToUse = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $dateToUseObj = isset($dateToUse) ? \DateTime::createFromFormat('Y-m-d', $dateToUse) : new \DateTime();

        if (!$dateToUseObj) {
            throw new \Exception('Date format mismatch. Expected Y-m-d. Date was:' . $dateToUse);
        }

        $status = empty($status) ? personRecord::PES_STATUS_NOT_REQUESTED : $status;
        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        switch ($status) {
            case personRecord::PES_STATUS_INITIATED:
            case personRecord::PES_STATUS_RECHECK_PROGRESSING:
                $requestor = empty($requestor) ? 'Unknown' : $requestor;
                $dateField = personRecord::COLUMN_PES_DATE_REQUESTED;
                break;
            case personRecord::PES_STATUS_REQUESTED:
            case personRecord::PES_STATUS_RECHECK_REQ:
            case personRecord::PES_STATUS_MOVER:
                $dateField = personRecord::COLUMN_PES_DATE_EVIDENCE;
                break;
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
            case personRecord::PES_STATUS_CLEARED_AMBER:
                $dateField = personRecord::COLUMN_PES_CLEARED_DATE;
                $this->setPesRescheckDate($cnum, $requestor, $dateToUse);
                break;
            case personRecord::PES_STATUS_PROVISIONAL:
            default:
                $dateField = personRecord::COLUMN_PES_DATE_RESPONDED;
                break;
        }
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET $dateField = date('" . $dateToUseObj->format('Y-m-d') . "'), PES_STATUS='" . htmlspecialchars($status) . "' ";
        $sql .= trim($status) == personRecord::PES_STATUS_INITIATED ? ", PES_REQUESTOR='" . htmlspecialchars($requestor) . "' " : null;
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
        $pesTracker->savePesComment($cnum, "PES_STATUS set to :" . $status . " Date Used:" . $dateToUseObj->format('Y-m-d'));

        AuditTable::audit("PES Status set for:" . $cnum . " To : " . $status . " By:" . $requestor, AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function setPesLevel($cnum = null, $level = null, $requestor = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET PES_LEVEL = '" . htmlspecialchars($level) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("PES Level set for:" . $cnum . " To : " . $level . " By:" . $requestor, AuditTable::RECORD_TYPE_AUDIT);
        return true;
    }

    public function setPesRescheckDate($cnum = null, $requestor = null, $dateToUse = null, $recheckImmediately = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        if (!$recheckImmediately) {

            $loader = new Loader();
            $predicate = " CNUM='" . htmlspecialchars(trim($cnum)) . "' ";

            $pesLevels = $loader->loadIndexed('PES_LEVEL', 'CNUM', allTables::$PERSON, $predicate);
            $pesLevel = isset($pesLevels[trim($cnum)]) ? $pesLevels[trim($cnum)] : self::PES_LEVEL_DEFAULT;
            $pesRecheckPeriod = isset(self::$pesRecheckPeriods[$pesLevel]) ? self::$pesRecheckPeriods[$pesLevel] : self::$pesRecheckPeriods[self::PES_LEVEL_DEFAULT];

        } else {
            $pesRecheckPeriod = 0;
        }

        $dateToUseObj = isset($dateToUse) ? \DateTime::createFromFormat('Y-m-d', $dateToUse) : new \DateTime();

        if (!$dateToUseObj) {
            throw new \Exception('Date format mismatch. Expected Y-m-d. Date was:' . $dateToUse);
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET PES_RECHECK_DATE = date('" . $dateToUseObj->format('Y-m-d') . "') + " . $pesRecheckPeriod;
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $sql = " SELECT PES_RECHECK_DATE FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $res = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$res) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($res);

        $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
        $pesTracker->savePesComment($cnum, "PES_RECHECK_DATE set to :" . $row['PES_RECHECK_DATE']);

        AuditTable::audit("PES_RECHECK_DATE set to :  " . $row['PES_RECHECK_DATE'] . " by " . $requestor, AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    /*
    function setPesStatus_Simple($cnum=null,$status=null,$requestor=null){
    if (!$cnum){
    throw new \Exception('No CNUM provided in ' . __METHOD__);
    }

    $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
    $sql .= " SET PES_STATUS='" . htmlspecialchars($status)  . "' ";
    $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

    try {
    $result = sqlsrv_query($GLOBALS['conn'], $sql);
    } catch (\Exception $e) {
    var_dump($e);
    }

    if (!$result){
    DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
    return false;
    }

    AuditTable::audit("PES Status for cnum: $cnum set to : $status by " . $_SESSION['ssoEmail'], AuditTable::RECORD_TYPE_AUDIT );

    return true;
    }
     */

    public function setPmoStatus($cnum = null, $status = null, $requestor = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET PMO_STATUS='" . htmlspecialchars($status) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        try {
            $result = sqlsrv_query($GLOBALS['conn'], $sql);
        } catch (\Exception $e) {
            var_dump($e);
        }

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        AuditTable::audit("PMO Status for cnum: $cnum set to : $status by " . $_SESSION['ssoEmail'], AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function setFirstName($cnum = null, $firstName = null)
    {
        if (!$cnum) {
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }
        if (!$firstName) {
            throw new \Exception('No FIRST_NAME provided in ' . __METHOD__);
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET FIRST_NAME ='" . htmlspecialchars($firstName) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        try {
            $result = sqlsrv_query($GLOBALS['conn'], $sql);
        } catch (\Exception $e) {
            var_dump($e);
        }

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        AuditTable::audit("FIRST_NAME for cnum: $cnum set to : $firstName by " . $_SESSION['ssoEmail'], AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function saveCtid($cnum, $ctid)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET CT_ID='" . htmlspecialchars($ctid) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set CT_ID to $ctid for $cnum", AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function setFmFlag($cnum, $flag)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET FM_MANAGER_FLAG='" . htmlspecialchars($flag) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set FM_MANAGER_FLAG to $flag for $cnum", AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function setEmailField($cnum, $field, $email)
    {
        switch ($field) {
            case 'EMAIL_ADDRESS':
            case 'IBM_EMAIL_ADDRESS':
            case 'KYN_EMAIL_ADDRESS':
                $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
                $sql .= " SET " . htmlspecialchars($field) . "='" . htmlspecialchars($email) . "' ";
                $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

                $result = sqlsrv_query($GLOBALS['conn'], $sql);

                if (!$result) {
                    DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
                    return false;
                }
                AuditTable::audit("Set " . htmlspecialchars($field) . " to $email for $cnum", AuditTable::RECORD_TYPE_AUDIT);

                return true;
                break;
            default:
                return false;
        }
    }

    public function clearCtid($cnum)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET CT_ID = null ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Clear CT ID for $cnum", AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function clearSquadNumber($cnum, $version = 'original')
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET ";
        $sql .= $version == 'original' ? " SQUAD_NUMBER = null " : " OLD_SQUAD_NUMBER = null";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        AuditTable::audit("Clear " . $version . " Agile Number for $cnum", AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function clearCioAlignment($cnum)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET CIO_ALIGNMENT = null ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Clear CIO Alignment for $cnum", AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }

    public function transferIndividual($cnum, $toFmCnum)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET FM_CNUM='" . htmlspecialchars($toFmCnum) . "' ";
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $result = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$result) {
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set FM_CNUM to $toFmCnum for $cnum", AuditTable::RECORD_TYPE_AUDIT);
        return true;
    }

    public static function isManager($emailAddress)
    {
        if (isset($_SESSION['isFm'])) {
            return $_SESSION['isFm'];
        }

        if (empty($emailAddress)) {
            return false;
        }

        $sql = ' SELECT FM_MANAGER_FLAG FROM "' . $GLOBALS['Db2Schema'] . '".' . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper(trim($emailAddress))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        if (is_bool($row['FM_MANAGER_FLAG'])) {
            throw new \Exception('problem in' . __FILE__ . __FUNCTION__);
        }

        $flagValue = strtoupper(substr(trim($row['FM_MANAGER_FLAG']), 0, 1));
        $_SESSION['isFm'] = ($flagValue == 'Y');
        return $_SESSION['isFm'];
    }

    public static function myCnum()
    {
        if (isset($_SESSION['myCnum'])) {
            return $_SESSION['myCnum'];
        }

        if (!isset($_SESSION['ssoEmail'])) {
            return false;
        }

        $sql = " SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper(trim($_SESSION['ssoEmail']))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $myCnum = strtoupper(trim($row['CNUM']));
        $_SESSION['myCnum'] = $myCnum;
        return $_SESSION['myCnum'];
    }

    public static function myManagersCnum()
    {
        if (isset($_SESSION['myManagersCnum'])) {
            return $_SESSION['myManagersCnum'];
        }

        if (!isset($_SESSION['ssoEmail'])) {
            return false;
        }

        $sql = " SELECT FM_CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper(trim($_SESSION['ssoEmail']))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $myManagersCnum = strtoupper(trim($row['FM_CNUM']));
        $_SESSION['myManagersCnum'] = $myManagersCnum;
        return $_SESSION['myManagersCnum'];
    }

    public function activeFmEmailAddressesByCnum()
    {
        $loader = new Loader();
        $allActivePeople = $loader->loadIndexed('EMAIL_ADDRESS', 'CNUM', $this->tableName, personTable::activePersonPredicate());
        $allFuncMgr = $loader->loadIndexed('FM_CNUM', 'FM_CNUM', $this->tableName, personTable::activePersonPredicate());

        $activeManagers = array_intersect_key($allActivePeople, $allFuncMgr);

        return $activeManagers;
    }

    public static function getRevalidationFromCnum($cnum = null, $email = null)
    {
        $sql = " SELECT REVALIDATION_STATUS FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE ";
        $sql .= !empty($cnum) ? " CNUM = '" . htmlspecialchars(strtoupper(trim($cnum))) . "' " : null;
        $sql .= !empty($email) ? " upper(EMAIL_ADDRESS) = upper('" . htmlspecialchars(strtoupper(trim($email))) . "') " : null;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $revalidationStatus = trim($row['REVALIDATION_STATUS']);
        return $revalidationStatus;
    }

    public static function getCnumFromEmail($emailAddress)
    {
        $sql = " SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper(trim($emailAddress))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $cnum = strtoupper(trim($row['CNUM']));
        return $cnum;
    }

    public static function getEmailFromCnum($cnum)
    {
        $sql = " SELECT EMAIL_ADDRESS FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM = '" . htmlspecialchars(strtoupper(trim($cnum))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $email = trim($row['EMAIL_ADDRESS']);
        return $email;
    }

    public static function getPesLevelFromEmail($email)
    {
        $sql = " SELECT PES_LEVEL FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper(trim($email))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $pesLevel = trim($row['PES_LEVEL']);
        return $pesLevel;
    }

    public static function getNamesFromCnum($cnum)
    {
        $sql = " SELECT case when PT.PASSPORT_FIRST_NAME is null then P.FIRST_NAME else PT.PASSPORT_FIRST_NAME end as FIRST_NAME ";
        $sql .= ",       case when PT.PASSPORT_SURNAME is null then P.LAST_NAME else PT.PASSPORT_SURNAME end as LAST_NAME  ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " as PT ";
        $sql .= " ON P.CNUM = PT.CNUM ";
        $sql .= " WHERE P.CNUM = '" . htmlspecialchars(strtoupper(trim($cnum))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return array(
                'FIRST_NAME' => '',
                'LAST_NAME' => '',
            );
        }

        $names = array_map('trim', $row);
        return $names;
    }

    public static function getCnumFromNotesid($notesid)
    {
        $sql = " SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(NOTES_ID) = '" . htmlspecialchars(strtoupper(trim($notesid))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $cnum = strtoupper(trim($row['CNUM']));
        return $cnum;
    }

    public static function getNotesidFromCnum($cnum)
    {
        $sql = " SELECT NOTES_ID FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM = '" . htmlspecialchars(strtoupper(trim($cnum))) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $notesid = trim($row['NOTES_ID']);
        return $notesid;
    }

    public static function optionsForPreBoarded($preBoarded = null)
    {

        if (empty($preBoarded)) {
            $availPreBoPredicate = " ( CNUM LIKE '%xxx' or CNUM LIKE '%XXX' or CNUM LIKE '%999' ) ";
            $availPreBoPredicate .= " AND ( trim(REVALIDATION_STATUS) like '%" . personRecord::REVALIDATED_PREBOARDER . "' or trim(REVALIDATION_STATUS) like '%" . personRecord::REVALIDATED_VENDOR . "') ";
            $availPreBoPredicate .= " AND ((PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' )  or ( PES_STATUS_DETAILS is null)) ";
            $availPreBoPredicate .= " AND PES_STATUS not in (";
            $availPreBoPredicate .= " '" . personRecord::PES_STATUS_FAILED . "' "; // Pre-boarded who haven't been boarded
            $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_REMOVED . "' ";
            $availPreBoPredicate .= " )";
        } else {
            $availPreBoPredicate = " ( CNUM = '" . htmlspecialchars($preBoarded) . "' ) ";
        }

        $sql = " SELECT distinct FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, CNUM  FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE " . $availPreBoPredicate;
        $sql .= " ORDER BY FIRST_NAME, LAST_NAME ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $options = array();
        while (($row = sqlsrv_fetch_array($rs)) == true) {
            $option = "<option value='" . trim($row['CNUM']) . "'";
            $option .= trim($row['CNUM']) == trim($preBoarded) ? ' selected ' : null;
            if (!empty(trim($row['EMAIL_ADDRESS']))) {
                $option .= " >" . trim($row['FIRST_NAME']) . " " . trim($row['LAST_NAME']) . " (" . trim($row['EMAIL_ADDRESS']) . ") ";
            } else {
                $option .= " >" . trim($row['FIRST_NAME']) . " " . trim($row['LAST_NAME']);
            }
            $option .= "</option>";
            $options[] = $option;
        }
        return $options;
    }

    public static function dataFromPreBoarder($cnum)
    {
        $sql = " SELECT CTB_RTB, PES_DATE_REQUESTED, PES_DATE_RESPONDED, PES_REQUESTOR,  PES_STATUS, PES_STATUS_DETAILS, FM_CNUM ";
        $sql .= " , CT_ID_REQUIRED, CT_ID, LOB, OPEN_SEAT_NUMBER ";
        $sql .= " , START_DATE, PROJECTED_END_DATE, CIO_ALIGNMENT, SKILLSET_ID ";
        $sql .= " , PES_LEVEL, PES_CLEARED_DATE, PES_RECHECK_DATE, PROPOSED_LEAVING_DATE  ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM='" . htmlspecialchars(trim($cnum)) . "' ";
        $sql .= " OPTIMIZE for 1 row ";
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = sqlsrv_fetch_array($rs);
        return $data;
    }

    private function prepareRevalidationStmt($data)
    {
        if (empty($this->preparedRevalidationStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET NOTES_ID = ?, EMAIL_ADDRESS = ?,  REVALIDATION_STATUS = '" . personRecord::REVALIDATED_FOUND . "' , REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM=? ";

            $this->preparedRevalidationStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedRevalidationStmt) {
                DbTable::displayErrorMessage($this->preparedRevalidationStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedRevalidationStmt;
    }

    private function prepareLeaverProjectedEndDateStmt($data)
    {
        if (empty($this->preparedLeaverProjectedEndDateStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET PROJECTED_END_DATE = current date ";
            $sql .= " WHERE CNUM=? AND PROJECTED_END_DATE is null ";

            $this->preparedLeaverProjectedEndDateStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedLeaverProjectedEndDateStmt) {
                DbTable::displayErrorMessage($this->preparedLeaverProjectedEndDateStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedLeaverProjectedEndDateStmt;
    }

    private function prepareRevalidationLeaverStmt($data)
    {
        if (empty($this->preparedRevalidationLeaverStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS = '" . personRecord::REVALIDATED_LEAVER . "' , REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM=? ";

            $this->preparedRevalidationLeaverStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedRevalidationLeaverStmt) {
                DbTable::displayErrorMessage($this->preparedRevalidationStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedRevalidationLeaverStmt;
    }

    private function prepareRevalidationPotentialLeaverStmt($data)
    {
        if (empty($this->preparedRevalidationPotentialLeaverStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
//            $sql .= " SET REVALIDATION_STATUS = '" . personRecord::REVALIDATED_POTENTIAL . "' , REVALIDATION_DATE_FIELD = current date ";
            $sql .= " SET REVALIDATION_STATUS = '" . personRecord::REVALIDATED_POTENTIAL . "'  "; // Storing the date was cutting to many history records
            $sql .= " WHERE CNUM=? ";

            $this->preparedRevalidationPotentialLeaverStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedRevalidationPotentialLeaverStmt) {
                DbTable::displayErrorMessage($this->preparedRevalidationPotentialLeaverStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedRevalidationPotentialLeaverStmt;
    }

    public function confirmRevalidation($notesId, $email, $cnum)
    {
        $data = array(trim($notesId), trim($email), trim($cnum));
        $preparedStmt = $this->prepareRevalidationStmt($data);

        $rs = sqlsrv_execute($preparedStmt);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: revalidationStmt");
            return false;
        }
        return true;
    }

    public function flagLeaver($cnum)
    {
        $data = array(trim($cnum));
        $preparedStmt = $this->prepareRevalidationLeaverStmt($data);
        $rs = sqlsrv_execute($preparedStmt);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: revalidationLeaverStmt");
            return false;
        }

        $data = array(trim($cnum));
        $preparedStmt = $this->prepareLeaverProjectedEndDateStmt($data);
        $rs = sqlsrv_execute($preparedStmt);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: leaverProjectedEndDateStmt");
            return false;
        }

        AuditTable::audit("Flaging leaver: $cnum ", AuditTable::RECORD_TYPE_REVALIDATION);
        $this->slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT, $_ENV['environment'] . " Flaging leaver : $cnum ");

        return true;
    }

    public function flagPotentialLeaver($cnum)
    {
        $data = array(trim($cnum));
        $preparedStmt = $this->prepareRevalidationPotentialLeaverStmt($data);
        $rs = sqlsrv_execute($preparedStmt);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: revalidationPotentialLeaverStmt");
            return false;
        }

        AuditTable::audit("Flaging potential leaver : $cnum ", AuditTable::RECORD_TYPE_REVALIDATION);
        $this->slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT, $_ENV['environment'] . " Flaging potential leaver : $cnum ");
        return true;
    }

    public function flagPreboarders()
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET REVALIDATION_STATUS = '" . personRecord::REVALIDATED_PREBOARDER . "', REVALIDATION_DATE_FIELD = current date ";
        $sql .= " WHERE (CNUM like '%999' or CNUM like '%xxx' or CNUM like '%XXX' )  AND ( REVALIDATION_STATUS is null )";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Revalidation has flagged Pre-Boarders", AuditTable::RECORD_TYPE_REVALIDATION);
        return true;
    }

    public function flagOffboarding($cnum, $revalidationStatusWas, $notesId, $proposedLeavingDate)
    {
        if (!empty($cnum)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET
            REVALIDATION_STATUS = CONCAT(TRIM('" . personRecord::REVALIDATED_OFFBOARDING . "'),':', TRIM(REVALIDATION_STATUS)),
            REVALIDATION_DATE_FIELD = current date,
            PROPOSED_LEAVING_DATE = '" . htmlspecialchars($proposedLeavingDate) . "'";
            $sql .= " WHERE CNUM = '" . htmlspecialchars($cnum) . "'";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }

            $this->notifyFmOfRevalStatusChange($cnum, personRecord::REVALIDATED_OFFBOARDING);
            pesEmail::notifyPesTeamOfOffboarding($cnum, $revalidationStatusWas, $notesId);
            AuditTable::audit("CNUM: $cnum (Reval:$revalidationStatusWas) has been flagged as :" . personRecord::REVALIDATED_OFFBOARDING, AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
    }

    public function flagOffboarded($cnum, $revalidationStatus)
    {
        if (!empty($cnum)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            // $sql .= " SET REVALIDATION_STATUS = CONCAT(TRIM('" . personRecord::REVALIDATED_OFFBOARDED . "'), ':', TRIM(SUBSTR(REVALIDATION_STATUS,13))), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = current date ";
            $sql .= " SET REVALIDATION_STATUS = CONCAT(
                TRIM('" . personRecord::REVALIDATED_OFFBOARDED . "'), ':',
                CASE
                    WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDING . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,13))
                    WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDED . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,12))
                    ELSE TRIM(REVALIDATION_STATUS)
                END
            ), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = current date ";
            $sql .= " WHERE CNUM = '" . htmlspecialchars($cnum) . "'";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }

            $this->notifyFmOfRevalStatusChange($cnum, personRecord::REVALIDATED_OFFBOARDED);
            pesEmail::notifyPesTeamOfOffboarded($cnum, $revalidationStatus);
            AuditTable::audit("CNUM: $cnum  has been flagged as :" . personRecord::REVALIDATED_OFFBOARDED, AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
    }

    public function stopOffboarded($cnum)
    {
        if (!empty($cnum)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            // $sql .= " SET REVALIDATION_STATUS = TRIM(SUBSTR(REVALIDATION_STATUS,13)), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = null  ";
            $sql .= " SET REVALIDATION_STATUS = CASE
                WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDING . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,13))
                WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDED . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,12))
                ELSE TRIM(REVALIDATION_STATUS)
            END,
            REVALIDATION_DATE_FIELD = current date,
            OFFBOARDED_DATE = null,
            PROPOSED_LEAVING_DATE = null ";
            $sql .= " WHERE CNUM = '" . htmlspecialchars($cnum) . "'";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }

            $this->notifyFmOfRevalStatusChange($cnum, personRecord::REVALIDATED_OFFBOARDING_STOPPED);
            AuditTable::audit("CNUM: $cnum  has been been STOPPED from Offboarding", AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
    }

    public function deOffboarded($cnum)
    {
        if (!empty($cnum)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            // $sql .= " SET REVALIDATION_STATUS = TRIM(SUBSTR(REVALIDATION_STATUS,12)), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = null  ";
            $sql .= " SET REVALIDATION_STATUS = CASE
                WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDING . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,13))
                WHEN REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARDED . "%' THEN TRIM(SUBSTR(REVALIDATION_STATUS,12))
                ELSE TRIM(REVALIDATION_STATUS)
            END,
            REVALIDATION_DATE_FIELD = current date,
            OFFBOARDED_DATE = null,
            PROPOSED_LEAVING_DATE = null ";
            $sql .= " WHERE CNUM = '" . htmlspecialchars($cnum) . "'";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }

            $this->notifyFmOfRevalStatusChange($cnum, personRecord::REVALIDATED_OFFBOARDED_REVERSED);
            AuditTable::audit("CNUM: $cnum  has been been REVERSED from Offboarded", AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
    }

    public function notifyFmOfRevalStatusChange($employeeCnum, $revalidationStatus)
    {
        $this->loader = new Loader();
        $empsFm = $this->loader->loadIndexed('FM_CNUM', 'CNUM', allTables::$PERSON, " CNUM='" . htmlspecialchars($employeeCnum) . "' ");
        $empsNotesid = $this->loader->loadIndexed('NOTES_ID', 'CNUM', allTables::$PERSON, " CNUM='" . htmlspecialchars($employeeCnum) . "' ");
        $fmCnum = !empty($empsFm[$employeeCnum]) ? $empsFm[$employeeCnum] : false;

        if ($fmCnum) {
            $fmsEmail = $this->loader->loadIndexed('EMAIL_ADDRESS', 'CNUM', allTables::$PERSON, " CNUM='" . htmlspecialchars($fmCnum) . "' ");
            $fmsEmailAddress = !empty($fmsEmail[$fmCnum]) ? $fmsEmail[$fmCnum] : false;
            if (!$fmsEmailAddress) {
                throw new \Exception("Unable to find email address for Functional Manager for $employeeCnum");
            }
            switch ($revalidationStatus) {
                case personRecord::REVALIDATED_LEAVER:
                    $statusDescription = " The employee's cnum has not been found in Bluepages, this is interpretted as meaning they are no longer an IBM Employee, the offboarding process will be initiated.";
                    break;
                case personRecord::REVALIDATED_OFFBOARDING:
                    $statusDescription = " The offboarding process has been initiated for your employee.";
                    break;
                case personRecord::REVALIDATED_OFFBOARDED:
                    $statusDescription = "The individual has been offboarded from the account.";
                default:
                    $statusDescription = " Status not recognised!!";
            }

            //array('/&&leaversNotesid&&/','/&&revalidationStatus&&/','/&&statusDescription&&/');
            $replacements = array($empsNotesid[$employeeCnum], $revalidationStatus, $statusDescription);

            $message = preg_replace(self::$revalStatusChangeEmailPattern, $replacements, self::$revalStatusChangeEmail);

            BlueMail::send_mail(array($fmsEmailAddress), "vBAC Revalidation Status Change Notification", $message, personRecord::$vbacNoReplyId);

        } else {
            throw new \Exception("Unable to find Functional Manager for $employeeCnum");
        }
        // ('FM_CNUM','CNUM');
    }

    private function prepareUpdateLbgLocationStmt($data)
    {
        if (empty($this->preparedUpdateLbgLocationStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET LBG_LOCATION=? ";
            $sql .= " WHERE CNUM=?  ";

            $this->preparedUpdateLbgLocationStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedUpdateLbgLocationStmt) {
                DbTable::displayErrorMessage($this->preparedRevalidationLeaverStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedUpdateLbgLocationStmt;
    }

    public function updateLbgLocationForCnum($lbgLocation, $cnum)
    {
        if (!empty($cnum) && !empty($lbgLocation)) {
            $data = array($lbgLocation, $cnum);
            $preparedStmt = $this->prepareUpdateLbgLocationStmt($data);
            $rs = sqlsrv_execute($preparedStmt);
            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been recorded as working from Aurora Location :" . $lbgLocation, AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
        return false;
    }

    public static function getLbgLocationForCnum($cnum)
    {
        if (!empty($cnum)) {
            $sql = " SELECT LBG_LOCATION FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $locationRow = sqlsrv_fetch_array($rs);
            if (!$locationRow) {
                $locationRow = array(
                    'LBG_LOCATION' => false,
                );
            }

            $sql = " SELECT FM_CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $fmrow = sqlsrv_fetch_array($rs);
            if (!$fmrow) {
                $fmrow = array(
                    'FM_CNUM' => false,
                );
            }

            $location = !empty($locationRow['LBG_LOCATION']) ? trim($locationRow['LBG_LOCATION']) : false;
            $fmCnum = !empty($fmrow['FM_CNUM']) ? trim($fmrow['FM_CNUM']) : false;
            return array('location' => $location, 'fmCnum' => $fmCnum);
        }
        return false;
    }

    private function prepareUpdateSecurityEducationStmt($data)
    {
        if (empty($this->preparedUpdateSecurityEducationStmt)) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET SECURITY_EDUCATION=? ";
            $sql .= " WHERE CNUM=?  ";

            $this->preparedUpdateSecurityEducationStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);

            if (!$this->preparedUpdateSecurityEducationStmt) {
                DbTable::displayErrorMessage($this->preparedUpdateSecurityEducationStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedUpdateSecurityEducationStmt;
    }

    public function updateSecurityEducationForCnum($securityEducation, $cnum)
    {
        if (!empty($cnum) && !empty($securityEducation)) {
            $data = array($securityEducation, $cnum);
            $preparedStmt = $this->prepareUpdateSecurityEducationStmt($data);
            $rs = sqlsrv_execute($preparedStmt);
            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }
            AuditTable::audit("CNUM: $cnum  Security Education :" . $securityEducation, AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
        return false;
    }

    public static function getSecurityEducationForCnum($cnum)
    {
        if (!empty($cnum)) {
            $sql = " SELECT SECURITY_EDUCATION FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $row = sqlsrv_fetch_array($rs);
            if (!$row) {
                return personRecord::SECURITY_EDUCATION_NOT_COMPLETED;
            }

            $education = !empty($row['SECURITY_EDUCATION']) ? trim($row['SECURITY_EDUCATION']) : personRecord::SECURITY_EDUCATION_NOT_COMPLETED;
            return $education;
        }
        return false;
    }

    public function assetUpdate($cnum, $assetTitle, $primaryUid)
    {
        $columnName = DbTable::toColumnName($assetTitle);
        if (!empty($this->columns[$columnName])) {
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET " . $columnName . "='" . htmlspecialchars(trim($primaryUid)) . "' ";
            $sql .= " WHERE CNUM='" . htmlspecialchars(trim($cnum)) . "' ";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);

            if (!$rs) {
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return true;
    }

    public static function countOdcStaff()
    {

        if (isset($_SESSION['Odcstaff'])) {
            if (!empty($_SESSION['Odcstaff'])) {
                return $_SESSION['Odcstaff'];
            }
        }

//         $odcActive = self::activePersonPredicate() . " AND " . self::odcPredicate();
        $sql = " SELECT COUNT(*) as ACTIVE_ODC ";
        $sql .= self::odcStaffSql();

//         $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ODC_ACCESS_LIVE . " as O ";
        //         $sql.= " ON O.OWNER_CNUM_ID = P.CNUM ";
        //         $sql.= " WHERE 1=1 and  " . $odcActive;
        //         $sql.= " AND O.OWNER_CNUM_ID is not null "; // they have to have access

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs);
        if (!$row) {
            return false;
        }

        $_SESSION['Odcstaff'] = $row['ACTIVE_ODC'];
        return $_SESSION['Odcstaff'];
    }

    public static function odcStaffSql($joins = null)
    {
        $activePredicate = self::activePersonPredicate();

        $sql = " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ODC_ACCESS_LIVE . " as O ";
        $sql .= " ON O.OWNER_CNUM_ID = P.CNUM ";
        $sql .= !empty($joins) ? $joins : null;

        $activeSql = $sql . " WHERE 1=1 ";
        $activeSql .= " AND " . $activePredicate;
        $activeSql .= " AND O.OWNER_CNUM_ID is not null "; // they have to have currect access to ODC

        return $activeSql;
    }

    public function updateRfFlag($cnum, $rfFlag, $rfStart = null, $rfEnd = null)
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET RF_FLAG='" . htmlspecialchars($rfFlag) . "' ";
        $sql .= !empty($rfStart) ? ", RF_START=DATE('" . htmlspecialchars($rfStart) . "') " : null;
        $sql .= !empty($rfEnd) ? ", RF_END=DATE('" . htmlspecialchars($rfEnd) . "') " : null;
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    public static function getPesStatusWithButtons($row)
    {
        $notesId = trim($row['NOTES_ID']);
        $email = trim($row['EMAIL_ADDRESS']);
        $actualCnum = isset($row['actualCNUM']) ? trim($row['actualCNUM']) : trim($row['CNUM']);
        $status = trim($row['PES_STATUS']);
        $currentValue = $status;
        $boarder = stripos(trim($row['PES_STATUS_DETAILS']), personRecord::PES_STATUS_DETAILS_BOARDED_AS) !== false;
        $passportFirst = array_key_exists('PASSPORT_FIRST_NAME', $row) ? $row['PASSPORT_FIRST_NAME'] : null;
        $passportSurname = array_key_exists('PASSPORT_SURNAME', $row) ? $row['PASSPORT_SURNAME'] : null;

        $pesStatusWithButton = '';
        $pesStatusWithButton .= "<span class='pesStatusField' data-cnum='" . $actualCnum . "'>" . $status . "</span><br/>";
        switch (true) {
            case $boarder:
                // Don't add buttons if this is a boarded - pre-boarder record.
                break;
            case $status == personRecord::PES_STATUS_TBD && !$_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_NOT_REQUESTED:
                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                $pesStatusWithButton .= "aria-label='Left Align' ";
                $pesStatusWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton .= " data-pesstatus='$status' ";
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='Initiate PES Request'";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-plane' aria-hidden='true'></span>";
                $pesStatusWithButton .= "</button>&nbsp;";
                /*
                $pesStatusWithButton.= "<button type='button' class='btn btn-default btn-xs btnPesProgressing accessRestrict accessCdi accessPes' ";
                $pesStatusWithButton.= "aria-label='Left Align' ";
                $pesStatusWithButton.= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton.= " data-pesstatus='$status' ";
                $pesStatusWithButton.= " data-newpesstatus='" . personRecord::PES_STATUS_PES_PROGRESSING . "' ";
                $pesStatusWithButton.= " data-toggle='tooltip' data-placement='top' title='Toggle Not Requested to PES Progressing'";
                $pesStatusWithButton.= " > ";
                $pesStatusWithButton.= "<span class='glyphicon glyphicon-fire' aria-hidden='true'></span>";
                $pesStatusWithButton.= "</button>&nbsp;";
                 */
                break;
            case $status == personRecord::PES_STATUS_INITIATED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_RESTART && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_RECHECK_REQ && $_SESSION['isPes']:
                $emailAddress = trim($row['EMAIL_ADDRESS']);
                $firstName = trim($row['FIRST_NAME']);
                $lastName = trim($row['LAST_NAME']);
                $country = trim($row['COUNTRY']);
                $openseat = trim($row['OPEN_SEAT_NUMBER']);
                $cnum = trim($row['CNUM']);
                $recheck = ($status == personRecord::PES_STATUS_RECHECK_REQ) ? 'yes' : 'no';
                $aeroplaneColor = ($status == personRecord::PES_STATUS_RECHECK_REQ) ? 'yellow' : 'green';

                $missing = !empty($emailAddress) ? '' : ' Email Address';
                $missing .= !empty($firstName) ? '' : ' First Name';
                $missing .= !empty($lastName) ? '' : ' Last Name';
                $missing .= !empty($country) ? '' : ' Country';

                $valid = empty(trim($missing));

                $disabled = $valid ? '' : 'disabled';
                $tooltip = $valid ? 'Confirm PES Email details' : "Missing $missing";

                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnSendPesEmail accessRestrict accessPmo accessFm' ";
                $pesStatusWithButton .= "aria-label='Left Align' ";
                $pesStatusWithButton .= " data-emailaddress='$emailAddress' ";
                $pesStatusWithButton .= " data-firstname='$firstName' ";
                $pesStatusWithButton .= " data-lastname='$lastName' ";
                $pesStatusWithButton .= " data-country='$country' ";
                $pesStatusWithButton .= " data-openseat='$openseat' ";
                $pesStatusWithButton .= " data-cnum='$cnum' ";
                $pesStatusWithButton .= " data-recheck='$recheck' ";
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='$tooltip'";
                $pesStatusWithButton .= " $disabled  ";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-send ' aria-hidden='true' style='color:$aeroplaneColor' ></span>";
                $pesStatusWithButton .= "</button>&nbsp;";

                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                $pesStatusWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton .= " data-notesid='" . $notesId . "' ";
                $pesStatusWithButton .= " data-email='" . $email . "' ";
                $pesStatusWithButton .= " data-pesdaterequested='" . trim($row['PES_DATE_REQUESTED']) . "' ";
                $pesStatusWithButton .= " data-pesrequestor='" . trim($row['PES_REQUESTOR']) . "' ";
                $pesStatusWithButton .= " data-revalidationstatus='" . trim($row['REVALIDATION_STATUS']) . "' ";
                $pesStatusWithButton .= " data-pesstatus='" . $status . "' ";
                $pesStatusWithButton .= array_key_exists('PASSPORT_FIRST_NAME', $row) ? " data-passportfirst='" . $passportFirst . "' " : null;
                $pesStatusWithButton .= array_key_exists('PASSPORT_SURNAME', $row) ? " data-passportsurname='" . $passportSurname . "' " : null;
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='Amend PES Status'";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $pesStatusWithButton .= "</button>";
                break;
            case $status == personRecord::PES_STATUS_DECLINED && ($_SESSION['isFm'] || $_SESSION['isCdi']):
            case $status == personRecord::PES_STATUS_FAILED && ($_SESSION['isFm'] || $_SESSION['isCdi']):
            case $status == personRecord::PES_STATUS_REMOVED && ($_SESSION['isFm'] || $_SESSION['isCdi']):
                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesRestart accessRestrict accessFm accessCdi' aria-label='Left Align' ";
                $pesStatusWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton .= " data-notesid='" . $notesId . "' ";
                $pesStatusWithButton .= " data-email='" . $email . "' ";
                $pesStatusWithButton .= " data-pesdaterequested='" . trim($row['PES_DATE_REQUESTED']) . "' ";
                $pesStatusWithButton .= " data-pesrequestor='" . trim($row['PES_REQUESTOR']) . "' ";
                $pesStatusWithButton .= " data-pesstatus='" . $status . "' ";
                $pesStatusWithButton .= array_key_exists('PASSPORT_FIRST_NAME', $row) ? " data-passportfirst='" . $passportFirst . "' " : null;
                $pesStatusWithButton .= array_key_exists('PASSPORT_SURNAME', $row) ? " data-passportsurname='" . $passportSurname . "' " : null;
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='Restart PES Process'";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-refresh ' aria-hidden='true' ></span>";
                $pesStatusWithButton .= "</button>";
                break;
            case $status == personRecord::PES_STATUS_REQUESTED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_CLEARED_PERSONAL && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_CLEARED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_CLEARED_AMBER && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_EXCEPTION && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_DECLINED && $_SESSION['isPes'];
            case $status == personRecord::PES_STATUS_FAILED && $_SESSION['isPes'];
            case $status == personRecord::PES_STATUS_REMOVED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_REVOKED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_LEFT_IBM && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_PROVISIONAL && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_TBD && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_MOVER && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_RECHECK_PROGRESSING && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_CANCEL_CONFIRMED && $_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_PES_PROGRESSING && $_SESSION['isPes']:
                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                $pesStatusWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton .= " data-notesid='" . $notesId . "' ";
                $pesStatusWithButton .= " data-email='" . $email . "' ";
                $pesStatusWithButton .= " data-pesdaterequested='" . trim($row['PES_DATE_REQUESTED']) . "' ";
                $pesStatusWithButton .= " data-pesrequestor='" . trim($row['PES_REQUESTOR']) . "' ";
                $pesStatusWithButton .= " data-revalidationstatus='" . trim($row['REVALIDATION_STATUS']) . "' ";
                $pesStatusWithButton .= " data-pesstatus='" . $status . "' ";
                $pesStatusWithButton .= array_key_exists('PASSPORT_FIRST_NAME', $row) ? " data-passportfirst='" . $passportFirst . "' " : null;
                $pesStatusWithButton .= array_key_exists('PASSPORT_SURNAME', $row) ? " data-passportsurname='" . $passportSurname . "' " : null;
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='Amend PES Status'";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $pesStatusWithButton .= "</button>";
                break;
            case $status == personRecord::PES_STATUS_REQUESTED && !$_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_RECHECK_REQ && !$_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_MOVER && !$_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_INITIATED && !$_SESSION['isPes']:
            case $status == personRecord::PES_STATUS_RECHECK_PROGRESSING && !$_SESSION['isPes']:
                $pesStatusWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesStop accessRestrict accessFm' aria-label='Left Align' ";
                $pesStatusWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesStatusWithButton .= " data-notesid='" . $notesId . "' ";
                $pesStatusWithButton .= " data-email='" . $email . "' ";
                $pesStatusWithButton .= " data-pesdaterequested='" . trim($row['PES_DATE_REQUESTED']) . "' ";
                $pesStatusWithButton .= " data-pesrequestor='" . trim($row['PES_REQUESTOR']) . "' ";
                $pesStatusWithButton .= array_key_exists('PASSPORT_FIRST_NAME', $row) ? " data-passportfirst='" . $passportFirst . "' " : null;
                $pesStatusWithButton .= array_key_exists('PASSPORT_SURNAME', $row) ? " data-passportsurname='" . $passportSurname . "' " : null;
                $pesStatusWithButton .= " data-toggle='tooltip' data-placement='top' title='Request PES be Stopped'";
                $pesStatusWithButton .= " > ";
                $pesStatusWithButton .= "<span class='glyphicon glyphicon-ban-circle ' aria-hidden='true' ></span>";
                $pesStatusWithButton .= "</button>";
                break;
            case $status == personRecord::PES_STATUS_CANCEL_CONFIRMED && $_SESSION['isPes']:
            default:
                break;
        }

        if (isset($row['PROCESSING_STATUS']) && ($row['PES_STATUS'] == personRecord::PES_STATUS_INITIATED || $row['PES_STATUS'] == personRecord::PES_STATUS_RECHECK_PROGRESSING || $row['PES_STATUS'] == personRecord::PES_STATUS_REQUESTED || $row['PES_STATUS'] == personRecord::PES_STATUS_RECHECK_REQ || $row['PES_STATUS'] == personRecord::PES_STATUS_MOVER)) {

            $pesStatusWithButton .= "<br/><button type='button' class='btn btn-default btn-xs btnTogglePesTrackerStatusDetails' aria-label='Left Align' data-toggle='tooltip' data-placement='top' title='See PES Tracker Status' >";
            $pesStatusWithButton .= !empty($row['PROCESSING_STATUS']) ? "&nbsp;<small>" . $row['PROCESSING_STATUS'] . "</small>&nbsp;" : null;
            $pesStatusWithButton .= "<span class='glyphicon glyphicon-search  ' aria-hidden='true' ></span>";
            $pesStatusWithButton .= "</button>";

            $pesStatusWithButton .= "<div class='alert alert-info text-center pesProcessStatusDisplay' role='alert' style='display:none' >";
            ob_start();
            pesTrackerTable::formatProcessingStatusCell($row);
            $pesStatusWithButton .= ob_get_clean();
            $pesStatusWithButton .= "</div>";
        }
        return array('display' => $pesStatusWithButton, 'sort' => $currentValue);
    }

    public static function getPesLevelWithButtons($row)
    {
        $notesId = trim($row['NOTES_ID']);
        $email = trim($row['EMAIL_ADDRESS']);
        $actualCnum = isset($row['actualCNUM']) ? trim($row['actualCNUM']) : trim($row['CNUM']);
        $level = trim($row['PES_LEVEL']);
        $currentValue = $level;
        $test = true;

        $pesLevelWithButton = '';
        $pesLevelWithButton .= "<span class='pesLevelField' data-cnum='" . $actualCnum . "'>" . $level . "</span><br/>";
        switch (true) {
            case $test:
                $pesLevelWithButton .= "<button type='button' class='btn btn-default btn-xs btnPesLevel' aria-label='Left Align' ";
                $pesLevelWithButton .= " data-cnum='" . $actualCnum . "' ";
                $pesLevelWithButton .= " data-notesid='" . $notesId . "' ";
                $pesLevelWithButton .= " data-email='" . $email . "' ";
                $pesLevelWithButton .= " data-pesdatecleared='" . trim($row['PES_CLEARED_DATE']) . "' ";
                $pesLevelWithButton .= " data-pesdaterecheck='" . trim($row['PES_RECHECK_DATE']) . "' ";
                $pesLevelWithButton .= " data-peslevel='" . $level . "' ";
                $pesLevelWithButton .= " data-toggle='tooltip' data-placement='top' title='Amend PES Level'";
                $pesLevelWithButton .= " > ";
                $pesLevelWithButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $pesLevelWithButton .= "</button>";
            default:
                break;
        }
        return array('display' => $pesLevelWithButton, 'sort' => $currentValue);
    }

    public function getAgileSquadWithButtons($row, $original = true)
    {
//         if (empty($this->squadNames)){
        //             $loader = new Loader();
        //             $this->squadNames = $loader->loadIndexed('SQUAD_NAME','SQUAD_NUMBER',allTables::$AGILE_SQUAD);
        //         }
        $originalSquad = !empty($row['SQUAD_NUMBER']) ? $row['SQUAD_NUMBER'] : 'none';
        $oldSquad = !empty($row['OLD_SQUAD_NUMBER']) ? $row['OLD_SQUAD_NUMBER'] : 'none';
        $squadNumber = $original ? $originalSquad : $oldSquad;

        $squadNumberField = $original ? $row['SQUAD_NUMBER'] : $row['OLD_SQUAD_NUMBER'];

        $originalSquadName = !empty($row['SQUAD_NAME']) ? $row['SQUAD_NAME'] : "Not allocated to Squad";
        $oldSquadName = !empty($row['OLD_SQUAD_NAME']) ? $row['OLD_SQUAD_NAME'] : "Not allocated to Squad";
        $squadName = $original ? $originalSquadName : $oldSquadName;
        $cnum = $row['actualCNUM'];

        $agileSquadWithButton = $original ? "<button type='button' class='btn btn-default btn-xs btnEditAgileNumber accessRestrict  accessCdi' aria-label='Left Align' " : null;
        $agileSquadWithButton .= $original ? " data-cnum='" . $cnum . "' " : null;
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

    public function updateAgileSquadNumber($cnum, $agileNumber, $version = 'original')
    {
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET ";
        $sql .= $version == 'original' ? " SQUAD_NUMBER=" : " OLD_SQUAD_NUMBER=";
        $sql .= htmlspecialchars($agileNumber);
        $sql .= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $this->lastUpdateSql = $sql;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    public function linkPreBoarderToIbmer($preboarderCnum, $ibmerCnum)
    {
        // db2_autocommit($GLOBALS['conn'], DB2_AUTOCOMMIT_OFF);

        $preBoarder = new personRecord();
        $preBoarder->setFromArray(array('CNUM' => $preboarderCnum));
        $preBoarderData = $this->getFromDb($preBoarder);

        $preboarderPesStatus = $preBoarderData['PES_STATUS'];
        $preboarderPesStatusD = $preBoarderData['PES_STATUS_DETAILS'];
        $preBoarderPesEvidence = $preBoarderData['PES_DATE_EVIDENCE'];
        $preboarderPesCleared = $preBoarderData['PES_CLEARED_DATE'];
        $preboarderPesRecheck = $preBoarderData['PES_RECHECK_DATE'];
        $preboarderPesLevel = $preBoarderData['PES_LEVEL'];

        $ibmer = new personRecord();
        $ibmer->setFromArray(array('CNUM' => $ibmerCnum));
        $ibmerData = $this->getFromDb($ibmer);
        $ibmerData['PRE_BOARDED'] = $preboarderCnum;

        $ibmerPesStatus = $ibmerData['PES_STATUS'];
        $ibmerPesStatusD = $ibmerData['PES_STATUS_DETAILS'];

        if (trim($ibmerPesStatus) == personRecord::PES_STATUS_INITIATED
            || trim($ibmerPesStatus) == personRecord::PES_STATUS_REQUESTED
            || trim($ibmerPesStatus) == personRecord::PES_STATUS_NOT_REQUESTED
            || trim($ibmerPesStatus) == personRecord::PES_STATUS_RECHECK_REQ
            || trim($ibmerPesStatus) == personRecord::PES_STATUS_RECHECK_PROGRESSING
            || trim($ibmerPesStatus) == personRecord::PES_STATUS_MOVER) {
            $ibmerData['PES_STATUS'] = $preboarderPesStatus;
            $ibmerData['PES_STATUS_DETAILS'] = $ibmerPesStatusD . ":" . $preboarderPesStatusD;
            $ibmerData['PES_DATE_EVIDENCE'] = $preBoarderPesEvidence;
            $ibmerData['PES_CLEARED_DATE'] = $preboarderPesCleared;
            $ibmerData['PES_RECHECK_DATE'] = $preboarderPesRecheck;
            $ibmerData['PES_LEVEL'] = $preboarderPesLevel;
        }
        $ibmer->setFromArray($ibmerData);

        if (!$this->update($ibmer)) {
            sqlsrv_rollback($GLOBALS['conn']);
            throw new \Exception("Failed to update IBMer record for CNUM: $ibmerCnum when linking to $preboarderCnum");
            return false;
        }

        $preBoarderData['PES_STATUS_DETAILS'] = personRecord::PES_STATUS_DETAILS_BOARDED_AS . " " . $ibmerData['CNUM'] . ":" . $ibmerData['NOTES_ID'] . " Status was:" . $preboarderPesStatus;
        $preBoarderData['EMAIL_ADDRESS'] = str_replace('ibm.com', '###.com', strtolower($preBoarderData['EMAIL_ADDRESS']));
        $preBoarder->setFromArray($preBoarderData);
        if (!$this->update($preBoarder)) {
            sqlsrv_rollback($GLOBALS['conn']);
            throw new \Exception("Failed to update Preboarder record for CNUM: $preboarderCnum when linking to $ibmerCnum");
            return false;
        }

        $pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);

        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM' => $ibmerCnum));
        if (!$pesTrackerTable->existsInDb($trackerRecord)) {
            if (!$pesTrackerTable->changeCnum($preboarderCnum, $ibmerCnum)) {
                sqlsrv_rollback($GLOBALS['conn']);
                throw new \Exception("Failed amending PES TRACKER Table to reflect that pre-boarder($preboarderCnum has been boarded as ($ibmerCnum) ");
                return false;
            }
        } else {
            $loader = new Loader();
            $emailAddress = $loader->loadIndexed('EMAIL_ADDRESS', 'CNUM', allTables::$PERSON, " CNUM in('" . htmlspecialchars(trim($preboarderCnum)) . "','" . htmlspecialchars(trim($ibmerCnum)) . "') ");

            $pesTrackerTable->savePesComment($ibmerCnum, "Serial Number changed from $preboarderCnum to $ibmerCnum");
            $pesTrackerTable->savePesComment($ibmerCnum, "Email Address changed from $emailAddress[$preboarderCnum] to $emailAddress[$ibmerCnum] ");
        }

        sqlsrv_commit($GLOBALS['conn']);

        // db2_autocommit($GLOBALS['conn'], DB2_AUTOCOMMIT_ON);
    }

    public function notifyRecheckDateApproaching()
    {
        $localConnection = $GLOBALS['conn']; // So we can keep reading this RS whilst making updates to the TRACKER TABLE.
        $pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);
        include "connect.php"; // get new connection on $GLOBALS['conn'];

        $sql = " SELECT CNUM, NOTES_ID, PES_STATUS, REVALIDATION_STATUS, PES_RECHECK_DATE ";
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE 1=1 ";
        $sql .= " AND PES_STATUS not in (" . self::$excludeFromRecheckNotification . ") ";
        $sql .= " and PES_RECHECK_DATE is not null ";
        $sql .= " and PES_RECHECK_DATE <= CURRENT DATE + 56 DAYS ";

        $rs = sqlsrv_query($localConnection, $sql);

        if (!$rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $allRecheckers = false;
        while (($row = sqlsrv_fetch_array($rs)) == true) {
            $trimmedRow = array_map('trim', $row);
            $allRecheckers[] = $trimmedRow;
            $this->setPesStatus($trimmedRow['CNUM'], personRecord::PES_STATUS_RECHECK_REQ);
            $pesTrackerTable->resetForRecheck($trimmedRow['CNUM']);
        }

        if ($allRecheckers) {
            pesEmail::notifyPesTeamOfUpcomingRechecks($allRecheckers);
        } else {
            pesEmail::notifyPesTeamNoUpcomingRechecks();
        }
        return $allRecheckers;
    }

    public function headerRowForFullDatatable()
    {
        $personPortalColumns = array(
            'CNUM',
            'OPEN_SEAT_NUMBER',
            'FIRST_NAME',
            'LAST_NAME',
            'EMAIL_ADDRESS',
            'KYN_EMAIL_ADDRESS',
            'NOTES_ID',
            'LBG_EMAIL',
            'EMPLOYEE_TYPE',
            'FUNCTIONAL_MGR',
            'FM_MANAGER_FLAG',
            'CTB_RTB',
            'LOB',
            'SKILLSET',
            'ROLE_TECHNOLOGY',
            'START_DATE',
            'PROJECTED_END_DATE',
            'COUNTRY',
            'IBM_BASE_LOCATION',
            'LBG_LOCATION',
            'OFFBOARDED_DATE',
            'PES_DATE_REQUESTED',
            'PES_REQUESTOR',
            'PES_DATE_RESPONDED',
            'PES_STATUS_DETAILS',
            'PES_STATUS',
            'REVALIDATION_DATE_FIELD',
            'REVALIDATION_STATUS',
            'PROPOSED_LEAVING_DATE',
            'CBN_DATE_FIELD',
            'CBN_STATUS',
            'CT_ID_REQUIRED',
            'CT_ID',
            'CIO_ALIGNMENT',
            'PRE_BOARDED',
            'SECURITY_EDUCATION',
            'PMO_STATUS',
            'PES_DATE_EVIDENCE',
            'RSA_TOKEN',
            'CALLSIGN_ID',
            'PROCESSING_STATUS',
            'PROCESSING_STATUS_CHANGED',
            'PES_LEVEL',
            'PES_RECHECK_DATE',
            'PES_CLEARED_DATE',
            'SQUAD_NUMBER',
            'SQUAD_NAME',
            'SQUAD_LEADER',
            'TRIBE_NUMBER',
            'TRIBE_NAME',
            'TRIBE_LEADER',
            'ORGANISATION',
            'ITERATION_MGR',
            'HAS_DELEGATES',
        );
        $headerRow = "<tr>";
        foreach ($personPortalColumns as $key => $columnName) {
            $headerRow .= "<th>" . str_replace("_", " ", $columnName) . "</th>";
        }
        $headerRow .= "</tr>";
        return $headerRow;
    }
}

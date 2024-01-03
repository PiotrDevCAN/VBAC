<?php
namespace vbac;

use itdq\DbTable;

class personPortalReport extends DbTable
{
    protected $availableColumns;
    protected $personTableAliases;
    protected $pesTrackerColumns;
    protected $pesTrackerTableAliases;
    protected $agileSquadColumns;
    protected $agileSquadTableAliases;
    protected $agileTribeColumns;
    protected $agileTribeTableAliases;
    protected $skillsetRecordColumns;
    protected $skillsetTableAliases;

    public function prepareColumn($column = '')
    {
        $personTableAliases = $this->personTableAliases;
        $availableColumns = $this->availableColumns;

        $pesTrackerTableAliases = $this->pesTrackerTableAliases;
        $pesTrackerColumns = $this->pesTrackerColumns;

        $agileSquadTableAliases = $this->agileSquadTableAliases;
        $agileSquadColumns = $this->agileSquadColumns;

        $agileTribeTableAliases = $this->agileTribeTableAliases;
        $agileTribeColumns = $this->agileTribeColumns;

        $skillsetTableAliases = $this->skillsetTableAliases;
        $skillsetRecordColumns = $this->skillsetRecordColumns;

        // validate field against PERSON table
        $tableField = str_replace($personTableAliases, '', $column);

        if (array_key_exists($tableField, $availableColumns)) {
            return $personTableAliases[0] . $tableField;
        }

        // validate field against PES_TRACKER table
        $tableField = str_replace($pesTrackerTableAliases, '', $column);

        if (array_key_exists($tableField, $pesTrackerColumns)) {
            return $pesTrackerTableAliases[0] . $tableField;
        }

        // validate field against AGILE_SQUAD table
        $tableField = str_replace($agileSquadTableAliases, '', $column);

        if (array_key_exists($tableField, $agileSquadColumns)) {
            return $agileSquadTableAliases[0] . $tableField;
        }

        // validate field against AGILE_TRIBE table
        $tableField = str_replace($agileTribeTableAliases, '', $column);

        if (array_key_exists($tableField, $agileTribeColumns)) {
            return $agileTribeTableAliases[0] . $tableField;
        }

        // validate field against STATIC_SKILLSETS table
        $tableField = str_replace($skillsetTableAliases, '', $column);

        if (array_key_exists($tableField, $skillsetRecordColumns)) {
            return $skillsetTableAliases[0] . $tableField;
        }

        return false;
    }

    public function addFieldToSearch($searchValue = null, $column = '')
    {
        $searchPredicate = '';
        $columnWithAlias = $this->prepareColumn($column);
        if ($columnWithAlias !== false) {
            $searchPredicate .= " " . $columnWithAlias . " LIKE '%$searchValue%'";
        }
        return $searchPredicate;
    }

    public function addFieldToSorting($direction = '', $column = '')
    {
        $sortingPredicate = '';
        $columnWithAlias = $this->prepareColumn($column);
        if ($columnWithAlias !== false) {
            $sortingPredicate .= " upper(" . $columnWithAlias . ") " . $direction;
        }
        return $sortingPredicate;
    }

    public function __construct($table, $pwd = null, $log = null)
    {
        $table = empty($table) ? allTables::$PERSON_PORTAL_REPORTS : $table;
        parent::__construct($table, $pwd, $log);

        // PERSON
        $personRecord = new personRecord();
        $availableColumns = $personRecord->getColumns();
        $personTableAliases = array('P.', 'F.', 'U.');

        unset($availableColumns['PROCESSING_STATUS']);
        unset($availableColumns['PROCESSING_STATUS_CHANGED']);

        $this->availableColumns = $availableColumns;
        $this->personTableAliases = $personTableAliases;

        // PES_TRACKER
        $pesTrackerRecord = new pesTrackerRecord();
        $pesTrackerColumns = $pesTrackerRecord->getColumns();
        $pesTrackerTableAliases = array('PT.');

        $this->pesTrackerColumns = $pesTrackerColumns;
        $this->pesTrackerTableAliases = $pesTrackerTableAliases;

        // AGILE_SQUAD
        $agileSquadRecord = new AgileSquadRecord();
        $agileSquadColumns = $agileSquadRecord->getColumns();
        $agileSquadTableAliases = array('AS1.');

        $this->agileSquadColumns = $agileSquadColumns;
        $this->agileSquadTableAliases = $agileSquadTableAliases;

        // AGILE_TRIBE
        $agileTribeRecord = new AgileTribeRecord();
        $agileTribeColumns = $agileTribeRecord->getColumns();
        $agileTribeTableAliases = array('AT.');

        $this->agileTribeColumns = $agileTribeColumns;
        $this->agileTribeTableAliases = $agileTribeTableAliases;

        // STATIC_SKILLSETS
        $skillsetRecord = new staticDataSkillsetsRecord();
        $skillsetRecordColumns = $skillsetRecord->getColumns();
        $skillsetTableAliases = array('SS.');

        $this->skillsetRecordColumns = $skillsetRecordColumns;
        $this->skillsetTableAliases = $skillsetTableAliases;
    }

    public function buildPreBoardersPredicate($preboadersAction = personTable::PORTAL_PRE_BOARDER_EXCLUDE)
    {
        $preboadersAction = empty($preboadersAction) ? personTable::PORTAL_PRE_BOARDER_EXCLUDE : $preboadersAction;

        $isFM = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();

        $justaUser = !$_SESSION['isCdi'] && !$_SESSION['isPmo'] && !$_SESSION['isPes'] && !$_SESSION['isFm'];

        $preBoardersPredicate = " 1=1  ";

        $preBoardersPredicate .= $isFM ? " AND P.FM_CNUM='" . htmlspecialchars(trim($myCnum)) . "' " : "";
        $preBoardersPredicate .= $justaUser ? " AND P.CNUM='" . htmlspecialchars(trim($myCnum)) . "' " : ""; // FM Can only see their own people.

        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_PRE_BOARDER_EXCLUDE ? " AND ( PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PES_STATUS_DETAILS is null) " : null;
        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_PRE_BOARDER_WITH_LINKED ? " AND ( PES_STATUS_DETAILS like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PRE_BOARDED is not null) " : null;
        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_ONLY_ACTIVE ? "  AND ( PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' or PES_STATUS_DETAILS is null ) AND " . personTable::activePersonPredicate() : null;

        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_PRE_BOARDER_EXCLUDE ? " AND ( PRE_BOARDED is null) " : null;
        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_PRE_BOARDER_WITH_LINKED ? " AND ( PRE_BOARDED is not null) " : null;
        // $preBoardersPredicate .= $preboadersAction==personTable::PORTAL_ONLY_ACTIVE ? "  AND ( PRE_BOARDED is null ) AND " . personTable::activePersonPredicate() : null;

        switch ($preboadersAction) {
            // Person Portal
            case personTable::PORTAL_PRE_BOARDER_EXCLUDE:
                $preBoardersPredicate .= " AND (";
                $preBoardersPredicate .= " (" . personTable::externalCNUMPredicate(true, 'P') . ")";
                $preBoardersPredicate .= " AND NOT " . personTable::hasPreBoarderPredicate(true, 'P');
                $preBoardersPredicate .= " OR (" . personTable::regularCNUMPredicate(true, 'P') . ")";
                $preBoardersPredicate .= ")";
                $preBoardersPredicate .= " AND " . personTable::notArchivedPersonPredicate(true, 'P');
                break;
            // Person Portal - Lite
            case personTable::PORTAL_ONLY_ACTIVE:
                $preBoardersPredicate .= " AND (";
                $preBoardersPredicate .= " (" . personTable::externalCNUMPredicate(true, 'P') . ")";
                $preBoardersPredicate .= " AND NOT " . personTable::hasPreBoarderPredicate(true, 'P');
                $preBoardersPredicate .= " OR (" . personTable::regularCNUMPredicate(true, 'P') . ")";
                $preBoardersPredicate .= ")";
                $preBoardersPredicate .= " AND " . personTable::activePersonPredicate(true, 'P');
                break;
            // Linked Portal
            case personTable::PORTAL_PRE_BOARDER_WITH_LINKED:
                $preBoardersPredicate .= " AND (" . personTable::isPreBoardedPredicate(true, 'P') . ")";
                $preBoardersPredicate .= " AND " . personTable::notArchivedPersonPredicate(true, 'P');
                break;
            // Person Portal - Archive
            case personTable::PORTAL_ONLY_ARCHIVED:
                $preBoardersPredicate .= " AND " . personTable::archivedPersonPredicate(true, 'P');
                break;
            default:
                break;
        }
        return $preBoardersPredicate;
    }

    public function buildGlobalSearchPredicate($searchValue = null, $columnsFromQuery = array())
    {
        $predicate = "";
        if (!empty($searchValue)) {
            $predicate .= " AND (";
            foreach ($columnsFromQuery as $key => $column) {
                if ($key > 0) {
                    $predicate .= " OR ";
                }
                $predicate .= $this->addFieldToSearch($searchValue, $column);
            }
            $predicate .= ") ";
        }
        return $predicate;
    }

    public function buildSearchPredicate($columnsFromRequest = array())
    {
        $predicate = '';
        if (!empty($columnsFromRequest)) {
            foreach ($columnsFromRequest as $key => $data) {
                $column = $data['data'];
                $searchValue = $data['search']['value'];
                if (!empty($searchValue)) {
                    $predicate .= " AND ";
                    $predicate .= $this->addFieldToSearch($searchValue, $column);
                }
            }
        }
        return $predicate;
    }

    public function buildSortingPredicate($sorting = array(), $columnsFromRequest = array())
    {
        $predicate = "";
        if (!empty($sorting)) {
            if (!empty($columnsFromRequest)) {
                $predicate .= " ORDER BY ";
                foreach ($sorting as $key => $data) {
                    if ($key > 0) {
                        $predicate .= " ,";
                    }

                    $columnId = $data['column'];
                    $direction = $data['dir'];
                    $column = $columnsFromRequest[$columnId]['data'];

                    $predicate .= $this->addFieldToSorting($direction, $column);
                }
            }
        }
        return $predicate;
    }
}

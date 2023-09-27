<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;

class personPortalReportsTable extends DbTable{

    protected $myReports;

    function __construct($table,$pwd=null,$log=null){

        $table = empty($table) ? allTables::$PERSON_PORTAL_REPORTS : $table;
        parent::__construct($table,$pwd,$log);

        $loader = new Loader();
        $predicate = " upper(EMAIL_ADDRESS) = '" . htmlspecialchars(strtoupper($_SESSION['ssoEmail'])) . "' ";
        $this->myReports = $loader->loadIndexed('SETINGS','REPORT_NAME',$table,$predicate);

    }

}
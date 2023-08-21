<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;

// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

echo "<pre>";

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
->setLastModifiedBy('vBAC')
->setTitle('Aurora Person Table Extract generated from vBAC')
->setSubject('Full Person Table')
->setDescription('urora Person Table Extract generated from vBAC')
->setKeywords('office 2007 openxml php vbac tracker')
->setCategory('Person Extract');
// Add some data

$now = new DateTime();

$activePredicate = personTable::activePersonPredicate();

try {
    $sheet = 1;
    $sql = " Select P.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, AT.ORGANISATION, AT.ITERATION_MGR  ";
    $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
    $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
    $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
    $sql .= " WHERE P.CNUM NOT IN ( ";
    $sql .= "    SELECT CNUM " ;
    $sql .= "    FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
    $sql .= "    WHERE 1=1 AND $activePredicate ";
    $sql .= "    ) ";
    $sql .= " AND (PES_STATUS_DETAILS is null or PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' )"; // dont show boarded pre-boarders

    set_time_limit(60);

    $rs = sqlsrv_query($GLOBALS['conn'], $sql);

    if($rs){
        $recordsFound = personTable::writeResultSetToXls($rs, $spreadsheet);
        if($recordsFound){
            DbTable::autoFilter($spreadsheet);
            DbTable::autoSizeColumns($spreadsheet);
            DbTable::setRowColor($spreadsheet,'105abd19',1);
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Person Table - Inactive');
            DbTable::autoSizeColumns($spreadsheet);
            $fileNameSuffix = $now->format('Ymd_His');

            ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="personExtractInactive' . $fileNameSuffix . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } else {
            echo "<h1>No records found to prepare this report</h1>";
        }
    }
} catch (Exception $e) {

    echo "<br/><br/><br/><br/><br/>";

    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>Problem found. See above.</h1>";
}
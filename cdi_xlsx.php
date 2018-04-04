
<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use itdq\Loader;
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
->setTitle('Aurora Master Tracker generated from vBAC')
->setSubject('Aurora Master Tracker')
->setDescription('Aurora Master Tracker generated from vBAC')
->setKeywords('office 2007 openxml php vbac tracker')
->setCategory('Master Tracker');
// Add some data

$now = new DateTime();

$loader = new Loader();
$allStatus = $loader->load('ORDERIT_STATUS',allTables::$ASSET_REQUESTS);
array_map('trim',$allStatus);

$sheet=1;

foreach ($allStatus as $key => $value) {
    $sql = " SELECT AR.ORDERIT_NUMBER, AR.ORDERIT_STATUS,Ar.ORDERIT_VARB_REF, AR.REQUEST_REFERENCE, AR.ASSET_TITLE, AR.BUSINESS_JUSTIFICATION, AR.REQUESTOR_EMAIl, AR.REQUESTED, AR.APPROVER_EMAIL, AR.APPROVED, P.FIRST_NAME, P.LAST_NAME, P.EMAIL_ADDRESS, P.LBG_EMAIL, P.EMPLOYEE_TYPE, P.CNUM, P.CT_ID, FM.CNUM as MGR_CNUM, FM.EMAIL_ADDRESS as MGR_EMAIL, FM.NOTES_ID as MGR_NOTESID, P.PES_STATUS, P.WORK_STREAM,P.CTB_RTB, P.TT_BAU, P.LOB, P.ROLE_ON_THE_ACCOUNT, P.CIO_ALIGNMENT,  AR.PRIMARY_UID, AR.SECONDARY_UID, AR.DATE_ISSUED_TO_IBM, AR. DATE_ISSUED_TO_USER, AR.DATE_RETURNED ";
    $sql .= " FROM " . $_SESSION['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as P ";
    $sql .= " ON P.CNUM = AR.CNUM ";
    $sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as FM ";
    $sql .= " ON P.FM_CNUM = FM.CNUM ";
    $sql .= " WHERE AR.ORDERIT_STATUS = '" . db2_escape_string($value) . "'";
    $sql .= " ORDER BY AR.REQUESTED asc ";
    
    $rs = db2_exec($_SESSION['conn'], $sql);
    
    DbTable::writeResultSetToXls($rs, $spreadsheet);
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'8099ccff',1);

    // Rename worksheet & create next.
    $spreadsheet->getActiveSheet()->setTitle($value);
    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex($sheet++);
    
}



// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Xlsx)

DbTable::autoSizeColumns($spreadsheet);

$fileNameSuffix = $now->format('Ymd_His');

ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="masterTracker' . $fileNameSuffix . '.xlsx"');
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
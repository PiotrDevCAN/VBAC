<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use itdq\BlueMail;
use vbac\AgileSquadRecord;
use vbac\AgileTribeRecord;
use vbac\personRecord;
use vbac\personTable;
use vbac\staticDataSkillsetsRecord;

set_time_limit(0);
ini_set('memory_limit','2048M');

$_ENV['email'] = 'on';

// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

$noreplemailid = $_ENV['noreplykyndrylemailid'];
$emailAddress = array(
    'philip.bibby@kyndryl.com',
    $_ENV['automationemailid']
);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
    ->setLastModifiedBy('vBAC')
    ->setTitle('Employee Plus Report')
    ->setSubject('Employee Plus Report')
    ->setDescription('Employee Plus Report generated by vBAC')
    ->setKeywords('office 2007 openxml php vbac employee plus report')
    ->setCategory('Emplyee Plus');
    // Add some data

$now = new DateTime();

$plus = "NOTES_ID,ROLE_ON_THE_ACCOUNT,EMAIL_ADDRESS,COUNTRY,START_DATE,PROJECTED_END_DATE,SQUAD_NUMBER,KYN_EMAIL_ADDRESS,CNUM,OFFBOARDED_DATE,ORGANISATION";

$withProvClear = null;
$additionalFields = !empty($plus) ? explode(",", $plus) : null;
$additionalSelect = null;

$onlyActiveBool = false;
$onlyActiveInTimeBool = false;

if (!is_null($additionalFields)) {

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
    $skillseTableAliases = array('SS.');

    foreach ($additionalFields as $field) {

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
        $tableField = str_replace($skillseTableAliases, '', $field);

        if (array_key_exists($tableField, $skillsetRecordColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("SS.".$tableField);
            continue;
        }
    }
}

try {
    
    ob_clean();

    $sql = " SELECT DISTINCT P.NOTES_ID, P.KYN_EMAIL_ADDRESS, ";
    $sql.=" CASE WHEN " . personTable::activePersonPredicate($withProvClear, 'P') . " THEN 'active' ELSE 'inactive' END AS INT_STATUS ";
    $sql.= $additionalSelect;
    $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD .  " AS AS1 ";
    $sql.= " ON P.SQUAD_NUMBER = AS1.SQUAD_NUMBER ";
    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE .  " AS AT ";
    $sql.= " ON AS1.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
    $sql.= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " as SS ";
    $sql.= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
    $sql.= " WHERE 1=1 AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
    // $sql.= " WHERE 1=1 AND trim(NOTES_ID) != '' ";
    // $sql.= $onlyActiveBool ? " AND " . personTable::activePersonPredicate($withProvClear, 'P') : null;
    // $sql.= $onlyActiveInTimeBool ? " AND (" . personTable::activePersonPredicate($withProvClear, 'P') . " OR P.OFFBOARDED_DATE > '" . $offboardedDate->format('Y-m-d') . "')" : null;
    $sql.= " ORDER BY P.NOTES_ID ";

    $resultSet = sqlsrv_query($GLOBALS['conn'], $sql);

    if ($resultSet) {
        DbTable::writeResultSetToXls($resultSet, $spreadsheet);
    }
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('Employee Plus');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    $filePrefix = 'Employee Plus - ';
    $fileNameSuffix = $now->format('Ymd_His');
    $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
    $scriptsDirectory = '/var/www/html/extracts/';
    $fileName = $scriptsDirectory.$fileNamePart;  

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    // $writer->save('php://output');
    $writer->save($fileName);

    $attachments = array();
    $handle = fopen($fileName, "r", true);
    if ($handle !== false) {
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        $encodedAttachmentFile = base64_encode($applicationForm);
        $attachments[] = array(
            'filename'=>$fileNamePart,
            'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'data'=>$encodedAttachmentFile,
            'path'=>$fileName
        );
    }
    
    $result = BlueMail::send_mail($emailAddress, 'Employee Plus Report : ' . $fileNameSuffix, 'Please find attached Employee Plus Report XLS',$noreplemailid,array(),array(),true,$attachments);    
    var_dump($result);

} catch (Exception $e) {
    
    //    ob_clean();
    
    echo "<br/><br/><br/><br/><br/>";
    
    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>No data found to export to tracker</h1>";
}
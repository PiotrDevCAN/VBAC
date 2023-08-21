<?php

use itdq\BlueMail;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use vbac\assetRequestsTable;
use vbac\personRecord;
use vbac\personTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("download Aurora Person Table Extract process started");

if (isset($argv[1])) {

    // default parameters
    $title = 'Aurora Person Table Extract generated from vBAC';
    $subject = 'Full Person Table';
    $description = 'Aurora Person Table Extract generated from vBAC';

    $toEmailParam = trim($argv[1]);
    $trackerType = strtolower(trim($argv[2]));

    error_log("Execution parameters: ".$toEmailParam." ".$trackerType);

    switch ($trackerType) {
        case 'details':
            $type = personTable::PERSON_DETAILS;
            $filePrefix = 'personExtract';
            
            $personTable = new assetRequestsTable(allTables::$PERSON);
            $activePredicate = '';

            $sql = " SELECT * ";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
        
            break;
        case 'details_full':
            $type = personTable::PERSON_DETAILS_FULL;
            $filePrefix = 'personExtractFull';

            $personTable = new assetRequestsTable(allTables::$PERSON);
            $activePredicate = '';

            $sql = " SELECT P.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, " . personTable::ORGANISATION_SELECT . ", AT.ITERATION_MGR";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
            $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
            $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
            $sql.= " WHERE P.PES_STATUS_DETAILS is null or P.PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' ";  // dont show boarded pre-boarders
        
            break;
        case 'details_active':
            $type = personTable::PERSON_DETAILS_ACTIVE;
            $filePrefix = 'personExtractActive';
            
            $personTable = new personTable(allTables::$PERSON);
            $activePredicate = personTable::activePersonPredicate();

            $sql = " SELECT P.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, " . personTable::ORGANISATION_SELECT . ", AT.ITERATION_MGR";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
            $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
            $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
            $sql.= " WHERE 1=1 AND " . $activePredicate;
        
            break;
        case 'details_active_odc':
            $type = personTable::PERSON_DETAILS_ACTIVE_ODC;
            $filePrefix = 'personExtractActiveOdc';

            $title = 'Aurora Person Table Extract(ODC) generated from vBAC';
            $subject = 'Person Table(ODC)';
            $description = 'Aurora Person Table Extract(ODC) generated from vBAC';

            $personTable = null;
            $activePredicate = '';
            
            $joins = " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
            $joins.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
            $joins.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
            $joins.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
            
            $sql = " SELECT P.*, O.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, " . personTable::ORGANISATION_SELECT . ", AT.ITERATION_MGR ";
            $sql.= personTable::odcStaffSql($joins);

            break;
        case 'details_inactive':
            $type = personTable::PERSON_DETAILS_INACTIVE;
            $filePrefix = 'personExtractInactive';

            $personTable = null;
            $activePredicate = personTable::activePersonPredicate();

            $sql = " SELECT P.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, " . personTable::ORGANISATION_SELECT . ", AT.ITERATION_MGR";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
            $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
            $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
            $sql.= " WHERE P.CNUM NOT IN ( ";
            $sql.= "  SELECT CNUM " ;
            $sql.= "  FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON;
            $sql.= "  WHERE 1=1 AND $activePredicate ";
            $sql.= "  ) ";
            $sql.= " AND (PES_STATUS_DETAILS is null or PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%' )"; // dont show boarded pre-boarders

            break;
        case 'bau':
            $type = personTable::PERSON_BAU;
            $filePrefix = 'personBauReport';

            $title = 'Aurora BAU Person Table Extract generated from vBAC';
            $subject = 'BAU Report';
            $description = 'BAU report from Person Table Extract generated from vBAC';

            $personTable = new personTable(allTables::$PERSON);
            $activePredicate = personTable::activePersonPredicate();

            $sql = " SELECT P.*, AS.SQUAD_LEADER, AS.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, AT.TRIBE_LEADER, " . personTable::ORGANISATION_SELECT . ", AT.ITERATION_MGR";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS AS ";
            $sql.= " ON P.SQUAD_NUMBER = AS.SQUAD_NUMBER ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS AT ";
            $sql.= " ON AS.TRIBE_NUMBER = AT.TRIBE_NUMBER ";
            $sql.= " WHERE 1=1 AND " . $activePredicate;
            $sql.= " AND P.LOB     in ('GTS','Cloud','Security') ";
            $sql.= " AND P.TT_BAU  in ('BAU') ";
            $sql.= " AND P.CTB_RTB in ('CTB','RTB') ";
            $sql.= " AND trim(P.REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "' ";

            break;
        default:
            throw new \Exception('Incorrect way of execution of script.');
            break;
    }

    // require_once __DIR__ . '/../../src/Bootstrap.php';
    // $helper = new Sample();
    // if ($helper->isCli()) {
    //     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    //     return;
    // }

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    // Set document properties
    $spreadsheet->getProperties()->setCreator('vBAC')
        ->setLastModifiedBy('vBAC')
        ->setTitle($title)
        ->setSubject($subject)
        ->setDescription($description)
        ->setKeywords('office 2007 openxml php vbac tracker')
        ->setCategory('Person Extract');
        // Add some data

    $now = new DateTime();

    $sheet = 1;

    set_time_limit(0);

    $rs = sqlsrv_query($GLOBALS['conn'], $sql);

    if($rs){
        $recordsFound = personTable::writeResultSetToXls($rs, $spreadsheet);
        if($recordsFound){
            DbTable::autoFilter($spreadsheet);
            DbTable::autoSizeColumns($spreadsheet);
            DbTable::setRowColor($spreadsheet,'105abd19',1);
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Person Table');
            DbTable::autoSizeColumns($spreadsheet);
            $fileNameSuffix = $now->format('Ymd_His');
            $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
            $scriptsDirectory = '/var/www/html/extracts/';
            $fileName = $scriptsDirectory.$fileNamePart;

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            // $writer->save('php://output');
            $writer->save($fileName);

            // $excelOutput = ob_get_clean();

            $toEmail = array($toEmailParam);
            $subject = 'The Aurora Person Table RAW extract';
            
            $extractRequestEmail = 'Hello &&requestor&&,
            <br/>
            <br/>Please find the attached Aurora Person Table RAW extract.
            <br/>File name: &&fileName&&
            <hr>
            <br/>Many thanks for your cooperation
            <br>vBAC Team';
            
            $extractRequestEmailPattern = array('/&&requestor&&/','/&&fileName&&/');

            $replacements = array($toEmailParam, $fileNamePart);
            $emailBody = preg_replace($extractRequestEmailPattern, $replacements, $extractRequestEmail);

            $pesTaskid = $_ENV['noreplyemailid'];

            if (file_exists($fileName)) {
                // throw new \Exception("The file $fileName exists");
            } else {
                // throw new \Exception("The file $fileName does not exist");
            }

            $pesAttachments = array();
            $handle = fopen($fileName, "r", true);
            if ($handle !== false) {
                $applicationForm = fread($handle, filesize($fileName));
                fclose($handle);
                $encodedAttachmentFile = base64_encode($applicationForm);
                $pesAttachments[] = array(
                    'filename'=>$fileNamePart,
                    'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'data'=>$encodedAttachmentFile,
                    'path'=>$fileName
                );
            }

            $sendResponse = BlueMail::send_mail($toEmail, $subject, $emailBody, $pesTaskid, array(), array(), false, $pesAttachments);

            var_dump($sendResponse);

            // if ($handle !== false) {
            //     unlink($fileName);
            // }

        } else {
            echo "<h1>No records found to prepare this report</h1>";
        }
    }
} else {
    throw new \Exception('Recipient email address was missing.');
}
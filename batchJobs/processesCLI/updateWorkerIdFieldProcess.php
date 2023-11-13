<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\BlueMail;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$timeMeasurements = array();
$start =  microtime(true);

// get number of employees with potential status
$startPhase1 = microtime(true);
$emptyWorkerIdPredicate = " WORKER_ID IS NULL";
$allEntriesToUpdate = $loader->load('CNUM',allTables::$PERSON, $emptyWorkerIdPredicate );
$allEntriesToUpdateCounterStart = count($allEntriesToUpdate);
// $allEntriesToUpdate = null; // free up some storage
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

// check if employee has a record in BluePages
$startPhase2 = microtime(true);
$workerAPI = new WorkerAPI();
foreach ($allEntriesToUpdate as $key => $CNUM) {
    $data = $workerAPI->getworkerByCNUM($CNUM);
    if (array_key_exists('count', $data) && $data['count'] > 0) {
        $employeeData = $data['results'][0];
        $serial = $employeeData['cnum'];
        $workerId = $employeeData['workerID'];
        $personTable->setWorkerId($serial, $workerId);
        unset($allEntriesToUpdate[$serial]);
    }
}
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    // $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'Piotr.Tajanowicz@kyndryl.com';
}

$subject = 'Update Worker ID field timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>All potential records before Worker Id update ' . $allEntriesToUpdateCounterStart;
$message .= '<BR/>All potential records left upon Worker Id update ' . $allEntriesToUpdateCounterEnd;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of updating: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
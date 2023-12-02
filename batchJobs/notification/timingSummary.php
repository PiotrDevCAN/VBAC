<?php
namespace vbac\notification;

use itdq\BlueMail;

/**
 *
 * @author gb001399
 *
 */
class timingSummary
{
    function __construct() {

    }

    function send_mail($url='', $pesData, $employeesEmails) {

        $updatedVBACPerson = '';
        $notFoundInVBAC = '';
        $noPESDataForPerson = '';
        $noPESDataForTracker = '';

        $timeMeasurements = array();

        $personFields = array();
        $pesTrackerFields = array();

        $to = array($_ENV['devemailid']);
        $cc = array();
        if (strstr($_ENV['environment'], 'vbac')) {
            $cc[] = 'Anthony.Stark@kyndryl.com';
            $cc[] = 'philip.bibby@kyndryl.com';
        }

        $subject = 'PES Status update timings';
        $message = 'PES API Url: ' . $url;
        $message .= '<BR/>Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

        $message .= '<HR>';

        $message .= "<BR/>PES Fields update read " . count($pesData) . " people from PES Application.";
        $message .= "<BR/>PES Fields update will check " . count($employeesEmails) . " people currently existing in the vBAC.";

        $message .= '<HR>';

        $message .= "<BR/>Quantity of PES_TRACKER and PERSON Records updated: " . $updatedVBACPerson;
        $message .= "<BR/>Quantity of email addresses from PES Application not found in vBAC: " . $notFoundInVBAC;

        $message .= "<BR/>Quantity of email addresses with no data for PERSON update: " . $noPESDataForPerson;
        $message .= "<BR/>Quantity of email addresses with no data for PES Tracker update: " . $noPESDataForTracker;

        $message .= '<HR>';

        $message .= '<BR/>cURL time: ' . $timeMeasurements['CURL'];
        $message .= '<BR/>PES API call time: ' . $timeMeasurements['getVBACEmployees'];
        $message .= '<BR/>vBAC records update time: ' . $timeMeasurements['dataUpdate'];
        $message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];
        
        $message .= '<HR>';

        $message .= '<BR/>PERSON fields updated during the last execution: ';
        foreach($personFields as $key => $field) {
            $message .= '<BR/>'.$field;
        }

        $message .= '<HR>';

        $message .= '<BR/> PES Tracker fields updated during the last execution: ';
        foreach($pesTrackerFields as $key => $field) {
            $message .= '<BR/>'.$field;
        }

        $message .= '<HR>';

        $replyto = $_ENV['noreplyemailid'];
        $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    }
}
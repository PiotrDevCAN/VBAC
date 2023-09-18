<?php
use itdq\BlueMail;
use vbac\personTable;
use vbac\allTables;

$sql = " SELECT * ";
$sql.= " FROM VBAC.EMAIL_LOG ";
$sql.= " WHERE  SUBJECT like 'Reminder%' ";
//$sql.= " fetch first 30 rows only " ;

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

echo "<div class='container'>";

$nonRecipients = false;

while ($row = sqlsrv_fetch_array($rs)){
    ini_set('max_execution_time', 360);
    echo "<br/>";
    echo "<br/><b>To: </b>" . implode(",",unserialize($row['TO']));
    echo "<br/><b>Subject: </b>" . $row['SUBJECT'];

    $dataJson = json_decode($row['DATA_JSON']);

    echo "<br/><b>cc:</b>" .  $dataJson->cc[0]->recipient;

    $responsObj = json_decode($row['RESPONSE']);

    foreach ($responsObj->link as  $value) {
        if($value->rel=='status'){
            $statusUrl = $value->href;
        }
    }

    echo "<br/><b>Status URL:</b>" . $statusUrl;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $statusUrl);

    $currentStatus = curl_exec($ch);

    sleep(2);

    $statusObj = json_decode($currentStatus);
    $sent = $statusObj->sent ? 'Yes' : 'No';

    echo "<br/><b>Sent:</b>" . $sent;
    echo "<br/><b>Status:</b>" . $statusObj->status;

    if($sent=='No'){
        $nonRecipients[implode(" ",unserialize($row['TO']))] = $row['SUBJECT'];
    }

}

echo "<h2>Reminder not sent</h2>";
echo "<pre>";
print_r($nonRecipients);
echo "</pre>";


echo "</div>";
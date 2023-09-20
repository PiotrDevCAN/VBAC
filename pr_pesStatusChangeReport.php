<?php
use itdq\Trace;
use vbac\allTables;

Trace::pageOpening($_SERVER['PHP_SELF']);

?><div class='container'><?php

$matches = array();

$pattern  = '/(PES[ _]STATUS set to :(.*?)<br\/><small>(.*?):(.*?) (.*?)<\/small>)/';

$otherRecords = "";

$sql = " SELECT T.CNUM, P.NOTES_ID,  T.COMMENT ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " as T ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
$sql.= " ON T.CNUM = P.CNUM ";
$sql.= " ORDER BY 1 asc ";

// echo $sql;

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

?>
<h2>PES Status Change Details Report</h2>
<table id='pesStatus' class='table table-responsive table-condensed' >
<thead><tr><th>Cnum</th><th>Notes Id</th><th>Status</th><th>Actioner</th><th>Date</th><th>Time	</th></tr>
</thead>
<tbody>
<?php

while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
    $response = preg_match_all($pattern, $row['COMMENT'], $matches);
    if($response>0){
        foreach ($matches[2] as $key => $status) {
            ?><tr<?=trim($status)=='Cleared' ? " class='success' " : null;?>><td><?=$row['CNUM']?></td><td><?=$row['NOTES_ID'];?></td><td><?=$status;?></td><td><?=$matches[3][$key]?></td><td><?=$matches[4][$key];?></td><td><?=$matches[5][$key];?></td></tr><?php
        }
    } else {
        ob_start();
        echo "<br/>Resp:" . $response;
        ?><pre><?php
        print_r($row);
        ?></pre><?php
        $otherRecords.= ob_get_clean();
        ob_start();

    }
}

?></tbody></table>

<?php
echo "<h2>Other PES Tracker Records</h2>";
echo $otherRecords;
?>

</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
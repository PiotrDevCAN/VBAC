<?php
use itdq\Trace;

?><div class='container'><?php

$matches = array();

//$string = 'PES STATUS set to : Cleared<br/><small>RSmith1@uk.ibm.com:2019-01-25 09:00:19</small><br/>Process Status set to Unknown<br/><small>RSmith1@uk.ibm.com:2019-01-25 09:00:13</small><br/>Stage PROOF_OF_RESIDENCY Set to Yes<br/><small>RSmith1@uk.ibm.com:2019-01-25 09:00:11</small><br/>PES STATUS set to : Evidence Requested<br/><small>carrabooth@uk.ibm.com:2019-01-23 10:47:17</small><br/>Priority set to : 1<br/><small>carrabooth@uk.ibm.com:2019-01-18 10:56:51</small><br/>Process Status set to PES<br/><small>Zoe.O.Flaherty@ibm.com:2019-01-18 09:35:45</small><br/>Process Status set to User<br/><small>RSmith1@uk.ibm.com:2019-01-17 13:37:51</small><br/>chased for POR<br/><small>RSmith1@uk.ibm.com:2019-01-17 13:37:47</small><br/>Stage PROOF_OF_ACTIVITY Set to Yes<br/><small>RSmith1@uk.ibm.com:2019-01-17 13:37:42</small><br/>Process Status set to PES<br/><small>RSmith1@uk.ibm.com:2018-12-17 08:58:09</small><br/>Process Status set to PES<br/><small>carrabooth@uk.ibm.com:2018-12-14 12:50:52</small><br/>Process Status set to User<br/><small>carrabooth@uk.ibm.com:2018-12-05 10:23:03</small><br/>Stage CRIMINAL_RECORDS_CHECK Set to Yes<br/><small>carrabooth@uk.ibm.com:2018-12-05 10:23:01</small><br/>Stage FINANCIAL_SANCTIONS Set to Yes<br/><small>carrabooth@uk.ibm.com:2018-12-05 10:23:01</small><br/>Process Status set to PES<br/><small>Zoe.O.Flaherty@ibm.com:2018-12-03 09:48:25</small><br/>Process Status set to User<br/><small>RSmith1@uk.ibm.com:2018-11-27 13:37:12</small><br/>ODC sent to BG checks ZO, emailed for POA<br/><small>RSmith1@uk.ibm.com:2018-11-26 12:14:43</small><br/>Stage PROOF_OF_ID Set to Yes<br/><small>RSmith1@uk.ibm.com:2018-11-26 12:14:31</small><br/>Stage RIGHT_TO_WORK Set to Yes<br/><small>RSmith1@uk.ibm.com:2018-11-26 12:14:29</small><br/>Process Status set to PES<br/><small>Zoe.O.Flaherty@ibm.com:2018-11-26 09:15:37</small><br/>Process Status set to User<br/><small>RSmith1@uk.ibm.com:2018-11-20 09:32:57</small><br/>chased<br/><small>RSmith1@uk.ibm.com:2018-11-20 09:32:56</small><br/>Process Status set to PES<br/><small>Zoe.O.Flaherty@ibm.com:2018-11-16 12:50:33</small><br/>Process Status set to User<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:01:13</small><br/>emailed for fully completed ODC, certified passport, POR and POA<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:01:12</small><br/>Stage CREDIT_CHECK Set to N/A<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:00:57</small><br/>Stage PROOF_OF_ID Set to Prov<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:00:53</small><br/>Stage RIGHT_TO_WORK Set to Prov<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:00:52</small><br/>Stage CONSENT Set to Yes<br/><small>RSmith1@uk.ibm.com:2018-11-07 12:00:51</small><br/>Priority set to : 2<br/><small>RSmith1@uk.ibm.com:2018-11-07 11:49:02</small><br/>Priority set to : 99<br/><small>Rob.Daniel@uk.ibm.com:2018-10-26 12:07:21</small></br/>Priority set to : 3<br/><small>Rob.Daniel@uk.ibm.com:2018-10-26 12:07:18</small></br/>Priority set to : 2<br/><small>Rob.Daniel@uk.ibm.com:2018-10-26 12:07:10</small></br/>';

$pattern  = '/(PES[ _]STATUS set to :(.*?)<br\/><small>(.*?):(.*?)<\/small>)/';


// $response = preg_match_all($pattern, $string, $matches);

// var_dump($string);

// echo "<pre>>";

// var_dump($response);

// echo "</pre>";

// echo "<pre>>";

// print_r($matches);


// exit();

$otherRecords = "";

$sql = " SELECT T.CNUM, P.NOTES_ID,  T.COMMENT ";
$sql.= " FROM VBAC.PES_TRACKER as T ";
$sql.= " LEFT JOIN VBAC.PERSON as P ";
$sql.= " ON T.CNUM = P.CNUM ";
$sql.= " ORDER BY 1 asc ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

?>
<h2>PES Status Change Details Report</h2>


<table id='pesStatus' class='table table-responsive table-condensed' >
<thead><tr><th>Cnum</th><th>Notes Id</th><th>Status</th><th>Actioner</th><th>Timestamp</th></tr>
</thead>
<tbody>
<?php

while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
    $response = preg_match_all($pattern, $row['COMMENT'], $matches);
    if($response>0){
        foreach ($matches[2] as $key => $status) {
            ?><tr<?=trim($status)=='Cleared' ? " class='success' " : null;?>><td><?=$row['CNUM']?></td><td><?=$row['NOTES_ID'];?></td><td><?=$status;?></td><td><?=$matches[3][$key]?></td><td><?=$matches[4][$key];?></td></tr><?php
        }
    } else {
        ob_start();
        echo "<br/>Resp:" . $response;
        ?><pre><?php
        print_r($row);
        ?></pre><?php
        $otherRecords.= ob_get_clean();

    }
}

?></tbody></table>

<?php
echo "<h2>Other PES Tracker Records</h2>";
echo $otherRecords;
?>

</div>


<script>

$(document).ready(function(){

	$('#pesStatus').DataTable({
        orderCellsTop: true,
    	autoWidth: true,
    	responsive: true,
    	processing: true,
    	dom: 'Blfrtip',
        buttons: [
                  'colvis',
                  'excelHtml5'
             ],

        order: [[ 1, "asc" ]],


		});


});



</script>


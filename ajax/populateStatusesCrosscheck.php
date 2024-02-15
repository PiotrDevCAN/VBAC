<?php

use vbac\allTables;
use vbac\personStatusCrosscheckReport;

set_time_limit(0);
ob_start();

$personTable = new personStatusCrosscheckReport(allTables::$PERSON);

$dataAndSql = $personTable->returnAsArray();
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
   $response = array("data"=>$data,'messages'=>$messages,'sql'=>$sql,'post'=>print_r($_POST,true));
} else {
//    $personTable->findDirtyData();
   $dirtyDetails = ob_get_clean();
   ob_start();
   echo $dirtyDetails;
   exit();
}
ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
   if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
       ob_start("ob_gzhandler");
   } else {
       ob_start("ob_html_compress");
   }
} else {
   ob_start("ob_html_compress");
}

echo json_encode($response);
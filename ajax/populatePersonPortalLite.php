<?php

use vbac\allTables;
use vbac\personPortalLiteTable;

set_time_limit(0);
ob_start();

// session_start();

// $GLOBALS['Db2Schema'] = 'VBAC';

// $personTable = new personPortalLiteTable(allTables::$PERSON_PORTAL_LITE);
$personTable = new personPortalLiteTable(allTables::$PERSON);
$preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

$dataAndSql = $personTable->returnAsArray($preBoardersAction);
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
   $response = array("data"=>$data,'messages'=>$messages,'sql'=>$sql,'post'=>print_r($_POST,true));
} else {
   $personTable->findDirtyData();
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
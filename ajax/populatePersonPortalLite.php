<?php

use vbac\personTable;
use vbac\allTables;
use vbac\personPortalLiteTable;

set_time_limit(0);
ob_start();

// session_start();

function ob_html_compress($buf){
   return str_replace(array("\n","\r"),'',$buf);
}

$personTable = new personPortalLiteTable(allTables::$PERSON_PORTAL_LITE);
$preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

$data = $personTable->returnAsArray($preBoardersAction);

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
   $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
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
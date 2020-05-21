<?php

use vbac\delegateTable;
use vbac\allTables;

set_time_limit(0);
ob_start();


$delegateTable = new delegateTable(allTables::$DELEGATE);
$data = $delegateTable->returnForDataTables($_POST['requestorCnum']);

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);
<?php

use vbac\staticDataTable;

ob_start();

$allData = staticDataTable::getStaticDataValuesForEdit();
$messages = ob_get_clean();
ob_start();

$response = array("data"=>$allData,'messages'=>$messages);

ob_clean();
echo json_encode($response);
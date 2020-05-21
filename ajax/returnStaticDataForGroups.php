<?php

use vbac\staticDataGroupsTable;

ob_start();

$allData = staticDataGroupsTable::getStaticDataValuesForEdit();
$messages = ob_get_clean();
ob_start();

$response = array("data"=>$allData,'messages'=>$messages);

ob_clean();
echo json_encode($response);
<?php
use vbac\requestableAssetListTable;

ob_start();
$success = requestableAssetListTable::flagAsDeleted($_POST['ASSET_TITLE'], $_POST['DELETED_BY']);
$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);
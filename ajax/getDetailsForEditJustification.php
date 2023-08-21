<?php

use itdq\Loader;
use vbac\allTables;

ob_start();

$loader = new Loader();
$reference = trim($_GET['reference']);

$justification = $loader->loadIndexed('BUSINESS_JUSTIFICATION','REQUEST_REFERENCE',allTables::$ASSET_REQUESTS," REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ");
$comment       = $loader->loadIndexed('COMMENT','REQUEST_REFERENCE',allTables::$ASSET_REQUESTS," REQUEST_REFERENCE='" . htmlspecialchars($reference) . "' ");

$justification = !empty(trim($justification[$reference])) ? trim($justification[$reference]) : null;
$comment = !empty(trim($comment[$reference])) ? trim($comment[$reference]) : null;


$messages = ob_get_clean();
ob_start();

$response = array('justification'=>$justification,'comment'=>$comment, 'messages'=>$messages);

ob_clean();
echo json_encode($response);
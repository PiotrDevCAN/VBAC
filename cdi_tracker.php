<?php


use vbac\assetRequestsTable;
use vbac\allTables;

$assettRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$assettRequestTable->extractForTracker();
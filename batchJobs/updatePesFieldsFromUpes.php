<?php

use vbac\allTables;
use itdq\DbTable;

$url = $_ENV['upes_url'] . '/api/pesStatus.php?token=' . $_ENV['upes_api_token'] . '&accountid=1330';

include "updatePesFields.php";
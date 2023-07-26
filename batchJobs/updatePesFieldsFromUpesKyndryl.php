<?php

// $url = $_ENV['kpes_url'] . '/api/pesStatus.php?token=' . $_ENV['upes_api_token'] . '&accountid=1330';
$url = $_ENV['kpes_url'] . '/api/pesStatus.php';
$rootScriptName = __FILE__;

// include "updatePesFields.php";
include "updatePesFieldsAsProcess.php";
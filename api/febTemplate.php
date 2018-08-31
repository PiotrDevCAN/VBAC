<?php

$sql = "INSERT INTO " . $_SERVER['environment'] . ".FEB_TRAVEL_REQUEST_TEMPLATES ";
$sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . db2_escape_string($_GET['email_address']) . "','" .  db2_escape_string($_GET['title']) . "','" . db2_escape_string(print_r($_GET['template'],true)) . "') ";

$rs = db2_exec($_SESSION['conn'], $sql);


ob_start();

echo db2_stmt_error();
echo db2_stmt_errormsg();


var_dump($sql);

$messages = ob_get_clean();


echo json_encode($messages);
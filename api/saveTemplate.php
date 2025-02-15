<?php

use vbac\allTables;

ob_start();

switch ($_REQUEST['mode']) {
    case 'write':
        if (sqlsrv_begin_transaction($GLOBALS['conn']) === false) {
            die( print_r( sqlsrv_errors(), true ));
        }
        
        $sql = "DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
        $sql.= " WHERE EMAIL_ADDRESS='". htmlspecialchars($_REQUEST['email_address']) . "' AND TITLE='" . htmlspecialchars($_REQUEST['title']) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            error_log("Sql:" . $sql);
            error_log(json_encode(sqlsrv_errors()));
            error_log(json_encode(sqlsrv_errors()));
        }
        
        sqlsrv_commit($GLOBALS['conn']);
        
        $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
        $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . htmlspecialchars($_REQUEST['email_address']) . "','" . htmlspecialchars($_REQUEST['title']) . "','" . htmlspecialchars($_REQUEST['template']) . "') ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            error_log("Sql:" . $sql);
            error_log(json_encode(sqlsrv_errors()));
            error_log(json_encode(sqlsrv_errors()));
        }
        
    break;
    case 'read':
        ;
        break;
    default:
        ;
    break;
}

$messages = ob_get_clean();
$success = empty($messages);
$response['success'] = $success;
$response['messages'] = $messages;

if(!$success){
    ob_clean();
    error_log('SaveTemplateError:' . json_encode($response , JSON_NUMERIC_CHECK));;
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);
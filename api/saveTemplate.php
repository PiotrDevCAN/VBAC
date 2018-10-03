<?php

ob_start();

switch ($_REQUEST['mode']) {
    case 'write':
        $sql = "DELETE FROM " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
        $sql.= " WHERE EMAIL_ADDRESS='". db2_escape_string($_REQUEST['email_address']) . "' AND TITLE='" . db2_escape_string($_REQUEST['title']) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);
        
        
        error_log("Sql:" . $sql);
        
        
        if(!$rs){
            echo $sql;
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
        }
        
        db2_commit($_SESSION['conn']);
        
        $sql = "INSERT INTO " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
        $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . db2_escape_string($_REQUEST['email_address']) . "','" . db2_escape_string($_REQUEST['title']) . "','" . db2_escape_string($_REQUEST['template']) . "') ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            echo $sql;
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
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
<?php

if($_REQUEST['token']!= $token){
    return;
}

ob_start();  

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Save a template to the database
        $sql = "INSERT INTO " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ; 
        $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . db2_escape_string($_GET['email_address']) . "','" .  db2_escape_string($_GET['title']) . "','" . db2_escape_string(print_r($_GET['template'],true)) . "') ";
        $rs = db2_exec($_SESSION['conn'], $sql);     

        if(!$rs){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            var_dump($sql);
        }
        break;
    case 'GET':
        switch (true) {
            case isset($_GET['emailAddress']) && isset($_GET['title']):
                // Get a specific template
                $sql = " SELECT TEMPLATE FROM " . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE EMAIL_ADDRESS='" . db2_escape_string(trim($_GET['emailAddress'])) . "' ";
                $sql.= " AND TITLE='" . db2_escape_string(trim($_GET['title'])) . "' ";   
                ob_start();
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    var_dump($sql);
                }
                
                $row = db2_fetch_assoc($rs);
                $response['template'] = $row['TEMPLATE'];
            break;

            case isset($_GET['emailAddress']):
                // Get list of titles for this person
                $sql = " SELECT DISTINT TITLE FROM " . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE EMAIL_ADDRESS='" . db2_escape_string(trim($_GET['emailAddress'])) . "' ";
                ob_start();
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    var_dump($sql);
                }
                
                while(($row = db2_fetch_assoc($rs))==true){
                    $response['titles'][] = $row['TITLE'];
                }
            break;
            default:
                http_response_code(405); 
                die(); 
            break;
        }

    break;
    
    default:
        http_response_code(405);
        die();
    break;
}

$messages = ob_get_clean();
$success = empty($messages);

$response['success'] = $success;
$response['messages'] = $messages;

echo json_encode($response);
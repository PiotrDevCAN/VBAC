<?php

if($_REQUEST['token']!= $token){
    http_response_code(405);
    die(); 
}

ob_start();  

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        switch (true) {
            case !empty($_POST['email_address']) && !empty($_POST['title']) && !empty($_POST['template']):
                // Save a template to the database
                $sql = "INSERT INTO " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . db2_escape_string($_POST['email_address']) . "','" .  db2_escape_string($_POST['title']) . "','" . db2_escape_string(print_r($_POST['template'],true)) . "') ";
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    var_dump($sql);
                }               
            break;            
            default:
                http_response_code(405);
                die(); 
            break;
        }
        break;
    case 'GET':
        switch (true) {
            case isset($_GET['email_address']) && isset($_GET['title']):
                // Get a specific template
                $sql = " SELECT TEMPLATE FROM " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE EMAIL_ADDRESS='" . db2_escape_string(trim($_GET['email_address'])) . "' ";
                $sql.= " AND TITLE='" . db2_escape_string(trim($_GET['title'])) . "' ";   
                ob_start();
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    var_dump($sql);
                }
                
                $row = db2_fetch_assoc($rs);
                
                $templateArray = array();
                
                $template = $row['TEMPLATE'];
                if(!empty($template)){
                    $allElements = explode(",", $template);
                    foreach ($allElements as $element){
                        $keyValuePair = explode(":", $element);
                        
                        if(is_integer($keyValuePair[1])){
                            $templateArray[$keyValuePair[0]] = (int)$keyValuePair[1];
                        } elseif (is_float($keyValuePair[1])){
                            $templateArray[$keyValuePair[0]] = (float)$keyValuePair[1];
                        } else {
                            $templateArray[$keyValuePair[0]] = $keyValuePair[1];
                        }
                    }
                }
                echo empty($row['TEMPLATE']) ? "No Template found for Email:" . $_GET['email_address'] . " Title:" . $_GET['title'] : null;
            break;

            case isset($_GET['email_address']):
                // Get list of titles for this person
                $sql = " SELECT DISTINCT TITLE FROM " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE EMAIL_ADDRESS='" . db2_escape_string(trim($_GET['email_address'])) . "' ";
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
                echo empty($response['titles']) ? "No Titles found for Email:" . $_GET['email_address'] : null;
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

if(!$success){
    $response['success'] = $success;
    $response['messages'] = $messages;
    ob_clean();
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);
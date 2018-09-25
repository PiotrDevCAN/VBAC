<?php

if($_REQUEST['token']!= $token){
    http_response_code(405);
    die(); 
}

ob_start();  

// Problem with FEB when using API - you need to perform a "fetch", that's how FEB understands what the API returnes, but this "FETCH" is always a GET - so we can't distinguish POST from GET as we can't configure the api in FEB



switch ($_REQUEST['mode']) {
    case 'write':
        switch (true) {
            case !empty($_REQUEST['email_address']) && !empty($_REQUEST['title']) && !empty($_REQUEST['template']):
                // Save a template to the database
                $sql = "INSERT INTO " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . db2_escape_string($_REQUEST['email_address']) . "','" .  db2_escape_string($_REQUEST['title']) . "','" . db2_escape_string(print_r($_REQUEST['template'],true)) . "') ";
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    $messages = ob_get_clean();
                    $success = empty($messages);
                    $response['success'] = $success;
                    $response['messages'] = $messages;
                    $response['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
                    http_response_code(409);
                    echo json_encode($response , JSON_NUMERIC_CHECK);
                    die(); 
                }               
            break;            
            default:
                http_response_code(406);

            break;
        }
        break;
    case 'read':
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
                $response = $templateArray;
                echo empty($row['TEMPLATE']) ? "No Template found for Email:" . $_GET['email_address'] . " Title:" . $_GET['title'] : null;
            break;

            case isset($_GET['email_address']):
                // Get list of titles for this person
                $sql = " SELECT DISTINCT TITLE FROM " . $_SERVER['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE UPPER(EMAIL_ADDRESS)='" . db2_escape_string(strtoupper(trim($_GET['email_address']))) . "' ";
                ob_start();
                
                $response['sql']= $sql;
                
                
                $rs = db2_exec($_SESSION['conn'], $sql);
                
                if(!$rs){
                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                    var_dump($sql);
                }
                
                while(($row = db2_fetch_assoc($rs))==true){
                    $response['titles'][] = $row['TITLE'];
                    var_dump($response);
                }
                echo empty($response['titles']) ? "No Titles found for Email:" . $_GET['email_address'] : null;
            break;
            default:
                http_response_code(407); 
                die(); 
            break;
        }
    break;    
    default:
        http_response_code(408);
        die();
    break;
}

$messages = ob_get_clean();
$success = true;
$response['success'] = empty($messages);
$response['messages'] = $messages;
$response['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];

if(!$success){
    ob_clean();
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);
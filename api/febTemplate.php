<?php

use vbac\allTables;

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
                $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " (EMAIL_ADDRESS, TITLE, TEMPLATE) VALUES ('" . htmlspecialchars($_REQUEST['email_address']) . "','" .  htmlspecialchars($_REQUEST['title']) . "','" . htmlspecialchars(print_r($_REQUEST['template'],true)) . "') ";
                $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                
                if(!$rs){
                    echo json_encode(sqlsrv_errors());
                    echo json_encode(sqlsrv_errors());
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
                $sql = " SELECT TEMPLATE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE EMAIL_ADDRESS='" . htmlspecialchars(trim($_GET['email_address'])) . "' ";
                $sql.= " AND TITLE='" . htmlspecialchars(trim($_GET['title'])) . "' ";   
                ob_start();
                $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                
                if(!$rs){
                    echo json_encode(sqlsrv_errors());
                    echo json_encode(sqlsrv_errors());
                    var_dump($sql);
                }
                
                $row = sqlsrv_fetch_array($rs);
                
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
                $sql = " SELECT DISTINCT TITLE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$FEB_TRAVEL_REQUEST_TEMPLATES ;
                $sql.= " WHERE UPPER(EMAIL_ADDRESS)='" . htmlspecialchars(strtoupper(trim($_GET['email_address']))) . "' ";
                ob_start();
                
                $response['sql']= $sql;
                
                
                $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                
                if(!$rs){
                    echo json_encode(sqlsrv_errors());
                    echo json_encode(sqlsrv_errors());
                    var_dump($sql);
                }
                
                while ($row = sqlsrv_fetch_array($rs)){
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
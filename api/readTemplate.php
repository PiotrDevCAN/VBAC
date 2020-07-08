<?php
if($_REQUEST['token']!= $token){
    http_response_code(405);
    die();
}

ob_start(); 

switch (true) {
    case isset($_GET['email_address']) && isset($_GET['title']):
        // Get a specific template
        $sql = " SELECT TEMPLATE FROM " . $_ENV['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES;
        $sql .= " WHERE EMAIL_ADDRESS='" . db2_escape_string(trim($_GET['email_address'])) . "' ";
        $sql .= " AND TITLE='" . db2_escape_string(trim($_GET['title'])) . "' ";
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        $response['sql'] = $sql;
        
        if (! $rs) {
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            var_dump($sql);
        }
        
        $row = db2_fetch_assoc($rs);
        
        $templateArray = array();
        
        $template = $row['TEMPLATE'];
        if (! empty($template)) {
            $allElements = explode(",", $template);
            
            
            $totalElements = count($allElements)-1;
            
            for ($i = $totalElements; $i >= 0; $i--) {
                if(strpos($allElements[$i],"F_") === false){
                    $allElements[$i-1] =  $allElements[$i-1] . "," . $allElements[$i];
                    unset($allElements[$i]);
                }
            }
            
            foreach ($allElements as $element) {
                $keyValuePair = explode(":", $element);
                
                if (is_integer($keyValuePair[1])) {
                    $templateArray[$keyValuePair[0]] = (int) $keyValuePair[1];
                } elseif (is_float($keyValuePair[1])) {
                    $templateArray[$keyValuePair[0]] = (float) $keyValuePair[1];
                } else {
                    $templateArray[$keyValuePair[0]] = $keyValuePair[1];
                }
            }
        }
        $response = $templateArray;
        break;
    case isset($_GET['email_address']):
        // Get list of titles for this person
        $sql = " SELECT DISTINCT TITLE FROM " . $_ENV['environment'] . "." . \vbac\allTables::$FEB_TRAVEL_REQUEST_TEMPLATES;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS)='" . db2_escape_string(strtoupper(trim($_GET['email_address']))) . "' ";
        $response['sql'] = $sql;
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if (! $rs) {
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            var_dump($sql);
        }
        
        while (($row = db2_fetch_assoc($rs)) == true) {
            $response['titles'][] = $row['TITLE'];
        }
        break;
    default:
        ;
        break;
}

$messages = ob_get_clean();
$success = empty($messages);;

$response['success'] = $success;
$response['messages'] = $messages;
$response['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];

if(!$success){
    ob_clean();
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);
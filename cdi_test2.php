<?php

$sql = " SELECT TEMPLATE, TITLE, EMAIL_ADDRESS FROM VBAC.FEB_TRAVEL_REQUEST_TEMPLATES ";
$sql .= " WHERE 1=1 "; 
// $sql .= " AND EMAIL_ADDRESS='elliotre@uk.ibm.com' ";
// $sql .= " AND TITLE='R3 OAT' ";
$rs = db2_exec($GLOBALS['conn'], $sql);

echo "<pre>";

while(($row = db2_fetch_assoc($rs))==true){
    $templateArray = array();
    
    $template = $row['TEMPLATE'];
    

    var_dump($row['TITLE']);
    var_dump($row['EMAIL_ADDRESS']);
    var_dump($template);
   
    
    if (! empty($template)) {
        $allElements = explode(",",$template);
       
        $totalElements = count($allElements)-1;
        
        for ($i = $totalElements; $i >= 0; $i--) {
            if(strpos($allElements[$i],"F_") === false){
                echo "<h5>Comma found</h5>";
                $allElements[$i-1] =  $allElements[$i-1] . "," . $allElements[$i];
                unset($allElements[$i]);
            }
        }
        
        foreach ($allElements as $key => $element) {
            echo "<br/>Key:$key Element:$element";
        }
        
        echo "<hr/>";
        
        
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
    
    
    var_dump($response);
    
    
}




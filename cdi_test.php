<?php

use itdq\Loader;
use vbac\allTables;

$loader = new Loader();



$_SESSION['Db2Schema']  = 'vbac';


$allCnum = $loader->loadIndexed('LAST_NAME','CNUM',allTables::$PERSON, " REVALIDATION_STATUS='found'  ");


foreach ($allCnum as $cnum => $lastName) {
    
    if(!empty($lastName)){
        set_time_limit(21);
        
        $personDetails = array();
        
        $ch = curl_init();
        
        $url = "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" . $cnum . ").search/byjson?sn&hrLastName";
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        
        
        $resultJson = json_decode($result);
        
        curl_close($ch);
        
        //     echo $result;
        //     echo "<pre>";
        //   // echo var_dump($resultJson);
        //     echo var_dump($resultJson->search->entry[0]->attribute);
        
        if(isset($resultJson->search->entry[0])){
            foreach ($resultJson->search->entry[0]->attribute as $entry){
                //  echo "<br/>$cnum : " . $entry->name . " => " . $entry->value[0];
                $personDetails[$cnum][$entry->name] = $entry->value[0];
            }
            
            // $hrLastName = isset($personDetails[$cnum]['hrLastName'] ) ? $personDetails[$cnum]['hrLastName'] : ' hrLastName not populated' ;
            //        $hrfamilyname = isset($personDetails[$cnum]['hrfamilyname'] ) ? $personDetails[$cnum]['hrfamilyname'] : ' hrfamilyname not populated' ;
            $sn = isset($personDetails[$cnum]['sn'] ) ? $personDetails[$cnum]['sn'] : 'sn not populated' ;
            
            echo "<br/>Checking $cnum : $sn";
            ob_flush();
            
            // echo "<br/>$cnum : vbac:$lastName  : sn => " . $sn; //  . " hrfamilyname => " . $hrfamilyname;
            if(  strtolower(trim($lastName)) != strtolower(trim($sn))){
                echo "<br/><b>SN differs COMPLETELY from vbac Surname</b> $cnum BP: <b>$sn</b> vBAC: <b>$lastName</b>  ";
            } elseif (trim($lastName) != trim($sn)){
                echo "<br/>SN different capitilisation from vbac Surname $cnum BP: <b>$sn</b> vBAC: <b>$lastName</b>";                
            }

        }
        
        
        
        //     echo "</pre>";
        //     echo "<hr/>";
        
    }
    


    
}
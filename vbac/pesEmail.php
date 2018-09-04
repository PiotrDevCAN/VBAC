<?php

use itdq\DbTable;
use vbac\allTables;

class pesEmail {
    
    function determineInternalExternal($emailAddress){
        $ibmEmail = stripos(strtolower($emailAddress), "ibm.com") !== false;
        
        return $ibmEmail ? 'internal' : 'external';
    }
    
    function getEmailBody($emailAddress, $country){
        $countryCodeTable = new DbTable(allTables::$STATIC_COUNTRY_CODES);
        
        $sql = ' SELECT '
        
    }
    
    
}
    
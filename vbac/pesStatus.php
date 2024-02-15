<?php
namespace vbac;

use itdq\AuditTable;

class pesStatus {

    function change(personTable $personTable, personRecord $person, $status = null, $requestor = null, $pesDetail = null, $pesDateResponded = null) {

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');

        $setPesStatusResult = $personTable->setPesStatus($cnum, $workerId, $status, $requestor, $pesDateResponded);
        $setPesStatusDetailsResult = $personTable->setPesStatusDetails($cnum, $workerId, $pesDetail, $pesDateResponded);
        
        if(!$setPesStatusResult || !$setPesStatusDetailsResult){
            echo json_encode(sqlsrv_errors());
            AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . json_encode(sqlsrv_errors()) . "</b> Msg:<b>" . json_encode(sqlsrv_errors()) . "</b>", AuditTable::RECORD_TYPE_DETAILS);
            $success = false;
        } else {
            AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
            $success = true;
        }
        return $success;
    }
}
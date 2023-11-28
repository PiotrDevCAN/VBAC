<?php
namespace vbac;

use itdq\DbTable;

class employeeCompleteTable extends DbTable {

    static function preProcessRowForWriteToXls($row){
        
        $fields = array(
            'EMAIL_ADDRESS',
            'NOTES_ID',
            'LOB',
            'ROLE_TECHNOLOGY',
            'RSA_TOKEN',
            'OLD_SQUAD_NUMBER',
            'IBM_CNUM',
            'TRANSITION_CNUM',
            'KYNDRYL_EMAIL_ADDRESS',
            'OCEAN_EMAIL_ADDRESS',
            'OCEAN_NOTES_ID',
            'IBM_EMAIL_ADDRESS',
            'IBM_NOTES_ID',
            'SQUAD_TYPE'
        );
        foreach($fields as $key => $value) {
            unset($row[$value]);
        }
        
        return $row;
    }
}
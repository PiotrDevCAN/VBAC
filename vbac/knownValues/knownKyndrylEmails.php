<?php

namespace vbac\knownValues;

// use vbac\personTable;

class knownKyndrylEmails extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownKyndrylEMails';
        $this->loaderField = 'KYN_EMAIL_ADDRESS';
        // condition was too strict since we cannot rely on CNUM or Worker ID
        // $this->predicate = personTable::normalCNUMPredicate();
        $this->predicate = ' KYN_EMAIL_ADDRESS IS NOT NULL';
        parent::__construct();
    }
}
<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownKyndrylEmails extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownKyndrylEMails';
        $this->loaderField = 'KYN_EMAIL_ADDRESS';
        $this->predicate = ' KYN_EMAIL_ADDRESS IS NOT NULL' 
        . ' AND ' . personTable::activePersonPredicate()
        . ' AND ' . personTable::notOffboarded();
        parent::__construct();
    }
}
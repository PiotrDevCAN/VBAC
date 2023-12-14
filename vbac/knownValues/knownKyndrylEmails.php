<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownKyndrylEmails extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownKyndrylEMails';
        $this->loaderField = 'KYN_EMAIL_ADDRESS';
        $this->predicate = personTable::normalCNUMPredicate();
        parent::__construct();
    }
}
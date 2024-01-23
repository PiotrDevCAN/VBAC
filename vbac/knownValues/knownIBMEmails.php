<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownIBMEmails extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownIBMEMails';
        $this->loaderField = 'EMAIL_ADDRESS';
        $this->predicate = personTable::regularCNUMPredicate()
        . ' AND ' . personTable::activePersonPredicate()
        . ' AND ' . personTable::notOffboarded();
        parent::__construct();
    }
}
<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownExternalEmails extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownExternalEmails';
        $this->loaderField = 'EMAIL_ADDRESS';
        $this->predicate = personTable::externalCNUMPredicate()
        . ' AND ' . personTable::activePersonPredicate()
        . ' AND ' . personTable::notOffboarded();
        parent::__construct();
    }
}
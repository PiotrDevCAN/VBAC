<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownCNUMs extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownCNUMs';
        $this->loaderField = 'CNUM';
        $this->predicate = personTable::availableCNUMPredicate();
        parent::__construct();
    }
}
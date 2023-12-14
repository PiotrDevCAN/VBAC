<?php

namespace vbac\knownValues;

use vbac\personTable;

class knownWorkerIDs extends knownValues
{
    public function __construct()
    {
        $this->redisMainKey = 'getKnownWorkerIDs';
        $this->loaderField = 'WORKER_ID';
        $this->predicate = personTable::normalWorkerIDPredicate();
        parent::__construct();
    }
}
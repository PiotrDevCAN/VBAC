<?php
namespace vbac;

use itdq\Loader;

class staticDataDomainsTable extends staticDataTable {


    static function getallDomains(){
        $loader = new Loader();
        $allRoles = $loader->loadIndexed('DOMAIN','DOMAIN_ID',allTables::$STATIC_DOMAINS);
        return $allRoles;
    }
}
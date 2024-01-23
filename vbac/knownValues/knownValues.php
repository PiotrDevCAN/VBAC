<?php

namespace vbac\knownValues;

use itdq\Loader;
use vbac\allTables;

abstract class knownValues
{   
    private $loader;

    private $redis;
    private $redisKey;

    protected $redisMainKey;
    protected $loaderField;
    protected $predicate;

    function __construct()
    {
        $this->redis = $GLOBALS['redis'];    
        $this->redisKey = md5($this->redisMainKey.'_key_'.$_ENV['environment']);
    
        $this->loader = new Loader();
    }

    function getFreshData() {
        $data = $this->loader->load($this->loaderField, allTables::$PERSON, $this->predicate, false);
        return $data;
    }

    function reloadCache() {
        $data = $this->getFreshData();

        $this->redis->set($this->redisKey, json_encode($data));
        $this->redis->expire($this->redisKey, REDIS_EXPIRE);

        return $data;
    }

    function getData() {
        // if (!$this->redis->get($this->redisKey)) {
            $source = 'SQL Server';
            $data = $this->reloadCache();
        // } else {
        //     $source = 'Redis Server';
        //     $data = json_decode($this->redis->get($this->redisKey), true);
        // }  
        $return = array('data'=>$data, 'source'=>$source);
        return $return;
    }
}
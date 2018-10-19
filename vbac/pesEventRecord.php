<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;
use itdq\DbTable;
use vbac\allTables;


/**
 *
 * @author gb001399
 *
 */
class pesEventRecord extends DbRecord
{
    
    protected $CNUM;
    protected $EVENT;
    protected $EVENT_TIMESTAMP;
    protected $EVENT_COMMENT;
   
    protected $EVENT_CREATOR;
    protected $EVENT_CREATED;
    protected $EVENT_UPDATER;
    protected $EVENT_UPDATED;
    
    const PES_EVENT_CONSENT = 'Consent Form';
    const PES_EVENT_WORK    = 'Right to Work';
    const PES_EVENT_ID      = 'Proof of Id';
    const PES_EVENT_RESIDENCY = 'Residency';
    const PES_EVENT_CREDIT  = 'Credit Check';
    const PES_EVENT_SANCTIONS = 'Financial Sanctions';
    const PES_EVENT_CRIMINAL = 'Criminal Records Check';
    const PES_EVENT_ACTIVITY = 'Activity';

    
    static public $pesEvents = array('Consent Form','Right to Work','Proof of Id','Residency','Credit Check','Financial Sanctions','Criminal Records Check','Activity');
   
  
}

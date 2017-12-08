<?php
namespace vbac;

use itdq\DbRecord;


/**
 *
 * @author gb001399
 *
 */
class personRecord extends DbRecord
{

    protected $NAME;
    protected $NAME_NOTES_ID;
    protected $NAME_INTRANET_ID;
    protected $CNUM;
    protected $FUNCTIONAL_MGR_FLAG;
    protected $FUNCTIONAL_MGR_CNUM;


    function displayBpDetails($mode){
        $this->formUserid('NAME', 'Name');

    }

    function displayForm($mode){

    }

}
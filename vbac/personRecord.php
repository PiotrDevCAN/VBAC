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
    protected $CNUM;
    protected $FUNCTIONAL_MGR_FLAG;
    protected $FUNCTIONAL_MGR_CNUM;

    protected $person_NOTES_ID;
    protected $person_INTRANET_ID;
    protected $person_PHONE;


    function displayBpDetails($mode){
        ?>
        <form id='displayBpDetails' name='displayBpDetails' class="form-horizontal"  method='post'>
        <?php
        $this->setfcformName('displayBpDetails');
        $this->formUserid('person', 'Name');
        ?>
        </form>
        <?php

    }

    function displayForm($mode){

    }

}
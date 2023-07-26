<?php

use itdq\Trace;
use vbac\allTables;
use vbac\personRecord;
use itdq\FormClass;
use itdq\Loader;

Trace::pageOpening($_SERVER['PHP_SELF']);

$allStatus = personRecord::$pesStatus;
asort($allStatus);

$loader = new Loader();
$allPersons = $loader->loadIndexed('EMAIL_ADDRESS', 'CNUM', allTables::$PERSON, " PES_STATUS = '".personRecord::PES_STATUS_NOT_REQUESTED."'");

?>
<div class="container">
<div class='row'>
<div class='col-sm-6'>
<h1 id='portalTitle'>Manual Status Override</h1>
<p>Not Requested colleagues shown only!</p>
<p>New PES Status options limited to PES Progressing only!</p>
</div>
</div>

<form id='updateStatus' class='form-horizontal' >
 	<div class='form-group required'>
    <label for='person' class='col-sm-2 control-label ceta-label-left'>Person</label>
       <div class='col-sm-4'>
       	<select class='form-control select2' id='person'
                name='cnum'
                required='required'
                data-placeholder="Select Person" data-allow-clear="true"
                >
        	<option value=''>Select Person<option>
            <?php
            foreach ($allPersons as $cnum => $emailAddress) {
                ?><option value='<?=$cnum;?>'><?=$emailAddress;?> (<?=$cnum;?>)</option><?php
            }
            ?>
       </select>
   	   </div>
	</div>

 	<div class='form-group required'>
    <label for='status' class='col-sm-2 control-label ceta-label-left'>Set Status to</label>
       <div class='col-sm-4'>
       	<select class='form-control select2' id='pesStatus'
                name='status'
                required='required'
                disabled
                data-placeholder="Select Status" data-allow-clear="true"
                >
        	<option value=''>Select Status</option>
            <option value='<?=personRecord::PES_STATUS_PES_PROGRESSING?>' selected='selected'><?=personRecord::PES_STATUS_PES_PROGRESSING;?></option>
            <?php
            /*
            foreach ($allStatus as  $status) {
                $disabled = (strtolower(trim($status))!==strtolower(trim(personRecord::PES_STATUS_PES_PROGRESSING))) ? 'disabled' : null;
            ?>
                <option value='<?=$status?>' <?=$disabled?>><?=$status;?></option>
            <?php
            }
            */
            ?>
       </select>
   	   </div>
	</div>
    <div class='form-group'>
        <div class='col-sm-offset-2 -col-md-3'>
            <?php
            $form = new FormClass();
            $allButtons = array();
            $submitButton = $form->formButton('submit','Submit','updatePerson','disabled','Update');
            $resetButton  = $form->formButton('reset','Reset','resetPersonForm',null,'Reset','btn-warning');
            $allButtons[] = $submitButton;
            $allButtons[] = $resetButton;
            $form->formBlueButtons($allButtons);
            $form->formHiddenInput('status', personRecord::PES_STATUS_PES_PROGRESSING, 'status');
            ?>
        </div>
    </div>

</form>
</div>

<hr/>

<div class='container'>
<h3>Person Database</h3>
<button id='reportPesUpdate' class='btn btn-primary btn-sm '>Update PES Status</button>
&nbsp;
<button id='reportReload' class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset' class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
<table id='updateStatusTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><th>CNUM</th><th>First Name</th><th>Last Name</th><th>Email Address</th><th>Notes Id</th><th>PES Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>First Name</th><th>Last Name</th><th>Email Address</th><th>Notes Id</th><th>PES Status</th></tr>
</tfoot>
</table>
</div>
</div>

<?php
include_once 'includes/modalShowUpdateResult.html';
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;


/**
 *
 * @author gb001399
 *
 */
class delegateRecord extends DbRecord
{

    protected $CNUM;
    protected $EMAIL_ADDRESS;
    protected $DELEGATE_CNUM;
    protected $DELEGATE_EMAIL;

    function displayForm(){
        $loader = new Loader();
        $predicate = "  REVALIDATION_STATUS in ('". personRecord::REVALIDATED_FOUND . "','" . personRecord::REVALIDATED_VENDOR . "','" . personRecord::REVALIDATED_POTENTIAL . "') or REVALIDATION_STATUS is null ";
        $selectableNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$predicate);

        ?>

  		<div class="panel panel-primary">
 		<div class="panel-heading">
			<h3 class="panel-title">Define Delegate</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
                <select class='form-control select select2 '
                			  id='delegate'
                              name='delegate'
                              required
                              data-toggle="tooltip" title="Select individial who can act as a delegate for yourself."
                              placeholder="select delegate"
                      >
                    <option value=''></option>
                    <?php
                    foreach ($selectableNotesId as $cnum => $notesId){
                            $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
                            //$selected = !$isFm && trim($cnum)==trim($myCnum) ? ' selected ' : null    // If they don't select the user - we don't fire the CT ID & Education prompts.
                            $selected = null;
                            ?><option value='<?=trim($cnum);?>'><?=$displayedName?></option><?php
                        };
                        ?>
            	</select>
            	</div>
            	<div id='resultHere'></div>
           	</div>


        <div class='panel-footer'>
        	<?php
        	$myCnum = personTable::myCnum();
            $allButtons = null;
            $submitButton =   $this->formButton('submit','Submit','saveDelegate',null,'Save','btn btn-primary');
            $allButtons[] = $submitButton;
            $this->formBlueButtons($allButtons);
            $this->formHiddenInput('requestorEmail',$_SESSION['ssoEmail'],'requestorEmail');
            $this->formHiddenInput('requestorCnum',$myCnum,'requestorCnum');
            ?>
        </div>

        </div>

        </div> <!--  Panel     -->

		<?php
    }


    function displayMyDelegates(){
        ?>
       	<div class="panel panel-primary">
 		<div class="panel-heading">
			<h3 class="panel-title">My Delegates</h3>
		</div>

		<div class="panel-body">

          	<table id='myDelegatesTable' class='table table-striped table-bordered compact'   style='width:100%'>
        	<thead>
        	<tr><th>Manager Email</th><th>Delegate Email</th><th>Delete</th></tr>
        	</thead>
        	</table>
        </div>
        <div class='panel-footer'>
        </div>
        </div>
        <?php
    }

}
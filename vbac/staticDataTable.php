<?php
namespace vbac;

use itdq\DbTable;

class staticDataTable extends DbTable {


    static function setScreenTabs(){
        ?>
        <ul class="nav nav-tabs">
        <li role="presentation" class="active" id='selectStaticData' ><a>Static Data</a></li>
        <li role="presentation"                id='selectGroupsForRoles'><a >Groups for Role</a></li>
        <li role="presentation"                id='selectGroupDetails'><a >Group Details</a></li>
        </ul>
        <?php
    }


    static function editStaticDataTable(){
        ?>
        <div id='editStaticDataTables'>
			<div class="panel panel-default">
  			<div class="panel-heading">
    			<h3 class="panel-title">Manage Static Data Tables</h3>
  			</div>
  			<div class="panel-body">
  			<div class='table-responsive'>
				<table class="table table-striped table-bordered" cellspacing="0" width="50%" id='staticDataValues'>
				<thead><tr><th>Table Name</th><th>Entry</th></tr></thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr><th>Table Name</th><th>Entry</th></tr>
				</tfoot>
				</table>
			</div>
			</div>
			</div>
		</div>
		<?php
    }


    static function amendStaticDataModal(){
        ?>
    	 <!-- Modal -->
		<div id="amendStaticDataModal" class="modal fade" role="dialog">
  		<div class="modal-dialog">

            <!-- Modal content-->
    		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
        		<h4 class="modal-title">Amend Static Data Entry</h4>
      		</div>
      		<div class="modal-body" >
        		<form id='manageStaticDataForm'>
        			<div class='row originalValueRow'>
        			<div class='form-group' >
            			<label for='amendedValue' class='col-md-3 control-label ceta-label-left'>Original Value</label>
           				<div class='col-sm-6'>
           				<input class='form-control' id='originalValue' name='originalValue' type='text' disabled >
           				</div>
           			</div>
       				</div>
       				<div class='row'>
        			<div class='form-group' >
       					<label for='amendedValue' class='col-md-3 control-label ceta-label-left' id='amendedLabel'>Amended Value</label>
        				<div class='col-sm-6'>
           					<input class='form-control' id='amendedValue' name='amendedValue' type='text' >
           					<input id='amendTable' name='amendTable' type='hidden' >
           					<input id='amendUid' name='amendUid' type='hidden' >
           				</div>
      				</div>
      				</div>
      			</form>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-primary" id='saveAmendedStaticData'>Save</button>
        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      		</div>
    		</div>
  		</div>
		</div>
        <?php
    }

}
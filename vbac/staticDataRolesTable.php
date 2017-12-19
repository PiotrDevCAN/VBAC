<?php
namespace vbac;

use itdq\Loader;

class staticDataRolesTable extends staticDataTable {


    static function getallRoles(){
        $loader = new Loader();
        $allRoles = $loader->loadIndexed('ROLE_TITLE','ROLE_ID',allTables::$STATIC_ROLES);
        return $allRoles;
    }

    static function editGroupsForRoles(){
        ?>
        <div id='editGroupsForRoles' hidden >
			<div class="panel panel-default">
  				<div class="panel-heading">
    				<h3 class="panel-title">Manage Groups for Roles</h3>
  				</div>
  			<div class="panel-body">
  				<table class='table table-striped table-bordered' cellspacing='0' width='50%' id='groupRolesValues'>
				<thead><tr><th>Table Name</th><th>Entry</th></tr></thead>
				<tbody>
<!-- 				will be populated by ajax all when DataTables is initiated by JS functiom -->
				</tbody>
				<tfoot>
				<tr><th>Table Name</th><th>Entry</th></tr>
				</tfoot>
				</table>
			</div>
			</div>
		</div>
        <?php
    }


}
<?php
namespace vbac;

use itdq\Loader;

class staticRolesTable extends staticDataTable {


    static function getallRoles(){
        $loader = new Loader();
        $allRoles = $loader->loadIndexed('ROLE_TITLE','ROLE_ID',allTables::$STATIC_ROLES);
        return $allRoles;
    }

    static function editGroupsForRoles(){
        $allRoles = staticRolesTable::getallRoles();
        ?>
        <div id='editGroupsForRoles' hidden >
			<div class="panel panel-default">
  				<div class="panel-heading">
    				<h3 class="panel-title">Manage Groups for Roles</h3>
  				</div>
  			<div class="panel-body">






			</div>
			</div>
		</div>
        <?php
    }


}
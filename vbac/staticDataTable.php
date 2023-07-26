<?php
namespace vbac;

use itdq\DbTable;
use vbac\staticDataRolesTable;

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
				<table class="table table-striped table-bordered"   style='width:50%' id='staticDataValues'>
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
		</div>
		<?php
    }

    static function getStaticDataValuesForEdit(){
        $allRoles = staticDataRolesTable::getallRoles();
        $allDomains = staticDataDomainsTable::getallDomains();
        $allTables = array(allTables::$STATIC_ROLES=> $allRoles, allTables::$STATIC_DOMAINS=>$allDomains);

        $allData = null;
        foreach ($allTables as $tableName => $allEntries){
            $row =array();
            $row[] = trim($tableName);
            $row[] = "<button type='button' class='btn btn-default btn-xs newEntry' aria-label='Left Align' data-tablename='" . trim($tableName) . "' data-value='' data-uid='newEntry' >
              <span class='glyphicon glyphicon-plus ' aria-hidden='true'></span>
              </button><span style='font-style:italic'>new_entry</span>";
            $allData[] = $row;
            foreach ($allEntries as $uid => $value){
                $row = array();
                $row[]= trim($tableName);
                $row[] = "<button type='button' class='btn btn-default btn-xs editRecord' aria-label='Left Align'
                      data-tablename='" . trim($tableName) . "' data-value='" . trim($value) . "' data-uid='" . trim($uid) . "' >
            <span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>
            </button>
              <button type='button' class='btn btn-default btn-xs disableRecord' aria-label='Left Align'
                      data-tablename='" . trim($tableName) . "' data-value='" . trim($value) . "' data-uid='" . trim($uid) . "' >
            <span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>
            </button>" . trim($value);
                $allData[] = $row;
            }
        }
        return $allData;

    }

}
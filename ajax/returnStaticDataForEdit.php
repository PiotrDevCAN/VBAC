<?php
use itdq\Loader;
use vbac\allTables;

ob_start();
$loader = new Loader();

$allRoles = $loader->loadIndexed('ROLE_TITLE','ROLE_ID',allTables::$STATIC_ROLES);
$allGroups =  $loader->loadIndexed('GROUP','GROUP_ID',allTables::$STATIC_GROUPS);
$allTables = array(allTables::$STATIC_ROLES=> $allRoles, allTables::$STATIC_GROUPS=>$allGroups);

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

$messages = ob_get_clean();

$response = array("data"=>$allData,'messages'=>$messages);

ob_clean();
echo json_encode($response);
<?php
ob_start();
$allRoles = array('001'=>'DBA','002'=>'Storage Analyst','003'=>'Sys prog','004'=>'Service Mgr','005'=>'Resource Mgr ');
$allGroups = array();
$allTables = array('Roles'=> $allRoles, 'Groups'=>$allGroups);

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
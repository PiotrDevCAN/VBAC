<?php


use itdq\BlueGroups;
use vbac\personTable;
use vbac\allTables;


ob_start();

$bgMembers = BlueGroups::listMembers('ODCRequests_FunctionalManagers');


$sql = " SELECT P.EMAIL_ADDRESS  FROM " . $_SERVER['environment'] . "." . allTables::$PERSON . " AS P ";

$sql.= " WHERE 1=1 AND trim(NOTES_ID) != ''  AND " . personTable::activePersonPredicate();
$sql.= " AND FM_MANAGER_FLAG='Yes' ";
$sql.= " ORDER BY 1 ";

$rs = db2_exec($_SESSION['conn'], $sql);

if($rs){
    while(($row = db2_fetch_assoc($rs))==true){
        $functionalMgrs[] = trim($row['EMAIL_ADDRESS']);
    }
} else {
    ob_clean();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
}

natcasesort($bgMembers);
natcasesort($functionalMgrs);

$leavers = array_diff($bgMembers,$functionalMgrs);
$joiners = array_diff($functionalMgrs, $bgMembers);

foreach ($joiners as $joiner){
    var_dump("adding $joiner");
    $res = BlueGroups::addMember('ODCRequests_FunctionalManagers', $joiner);    
}


$bgMembers = BlueGroups::listMembers('ODCRequests_FunctionalManagers');

echo "<pre>";
var_dump($bgMembers);






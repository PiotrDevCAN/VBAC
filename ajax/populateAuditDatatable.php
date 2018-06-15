<?php

use itdq\AllItdqTables;
use itdq\AuditTable;

set_time_limit(0);
ob_start();

$type = isset($_POST['type']) ? $_POST['type'] : null;
$length = isset($_POST['length']) ? $_POST['length'] : 5;

switch ($type){
    case 'revalidation':
        $predicate =" AND DATA like 'Revalidation %' " ;
        break;
    default:
        $predicate = null;
}

$fromRecord = 1;

$draw = $_POST['draw'] * 1;
$start = $_POST['start'] * 1;



$predicate .= !empty($_POST['columns']['1']['search']['value']) ? " and to_char(TIMESTAMP,'YYYY-MM-DD-HH24:MI:SS.NNNNNN') like '%" . strtolower(trim($_POST['columns']['1']['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['columns']['2']['search']['value']) ? " AND lower(EMAIL_ADDRESS) like '%" . strtolower(trim($_POST['columns']['2']['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['columns']['3']['search']['value']) ? " AND lower(DATA) like '%" . strtolower(trim($_POST['columns']['3']['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['columns']['4']['search']['value']) ? " AND lower(TYPE) like '%" . strtolower(trim($_POST['columns']['4']['search']['value'])) . "%' " : null;

$predicate .= !empty($_POST['search']['value']) ? " AND (to_char(TIMESTAMP,'YYYY-MM-DD-HH24:MI:SS.NNNNNN') like '%" . strtolower(trim($_POST['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['search']['value']) ? " or lower(EMAIL_ADDRESS) like '%" . strtolower(trim($_POST['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['search']['value']) ? " or lower(DATA) like '%" . strtolower(trim($_POST['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['search']['value']) ? " or lower(TYPE) like '%" . strtolower(trim($_POST['search']['value'])) . "%' " : null;
$predicate .= !empty($_POST['search']['value']) ? " ) " : null;

echo "Predicate:$predicate";

$auditTable = new AuditTable(AllItdqTables::$AUDIT);
$recordsTotal    = AuditTable::totalRows();
$recordsFiltered = AuditTable::recordsFiltered($predicate);
$data = $auditTable->returnAsArray($start, $length , $predicate);

$messages = ob_get_clean();

$response = array('draw'=>$draw,'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>$data,'messages'=>trim($messages),'post'=>print_r($_POST,true),'predicate'=>$predicate);

ob_clean();
echo json_encode($response);
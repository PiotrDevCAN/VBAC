<?php
use vbac\allTables;
use vbac\staticDataWorkstreamTable;

if($_REQUEST['token']!= $token){
    return;
}



switch ($_GET['data']) {
    case 'workstream':
        $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
        $allWorkstream = $workstreamTable->getallWorkstream();
        ob_clean();        
        echo json_encode($allWorkstream);
    break;
    
    default:
        $errorMessage = 'No table provided';
        ob_clean();
        echo json_encode($errorMessage);
        return;
    break;
}



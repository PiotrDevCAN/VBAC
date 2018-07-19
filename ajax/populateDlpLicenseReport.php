<?php


use itdq\Loader;
use vbac\dlpTable;
use vbac\allTables;
use vbac\personTable;
use vbac\dlpRecord;

set_time_limit(0);
ob_start();


$GLOBALS['ltcuser']['mail'] = $_SESSION['ssoEmail'];

$loader = new Loader();
$dlpTable = new dlpTable(allTables::$DLP);
$withButtons = true;
$predicate = null;

$allDelegates = $loader->load('DELEGATE_CNUM',allTables::$DELEGATE);

switch (true) {
    case $_SESSION['isFm']:
        $myCnum = personTable::myCnum();
        $predicate .= " AND ( F.CNUM='". db2_escape_string($myCnum) . "' ";
        $predicate .= "       OR ";
        $predicate .= "       G.DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ";
        $predicate .= "     ) ";        
        break;
    case $_SESSION['isCdi']:
    case $_SESSION['isPmo']:
        $myCnum = personTable::myCnum();
        break;
    default:
        $myCnum = personTable::myCnum();
        $predicate .= " AND  ( D.CNUM = '". db2_escape_string($myCnum) . "' ";
        $predicate .= "        OR G.DELEGATE_CNUM='" . db2_escape_string($myCnum) . "' ";
        $predicate .= "      ) ";
        $withButtons = false;
        break;
}

switch ($_POST['showType']) {
    case 'active' :
        $predicate.= " AND D.STATUS in ('" . dlpRecord::STATUS_PENDING . "','" . dlpRecord::STATUS_APPROVED . "') ";
        $predicate.= " AND D.TRANSFERRED_TO_HOSTNAME is null ";
        break; 
    case 'pending':
        $predicate.= " AND D.STATUS='" . dlpRecord::STATUS_PENDING . "' ";
        $predicate.= " AND D.TRANSFERRED_TO_HOSTNAME is null ";
        break;
    case 'rejected':
        $predicate.= " AND D.STATUS='" . dlpRecord::STATUS_REJECTED . "' ";
        $predicate.= " AND D.TRANSFERRED_TO_HOSTNAME is null ";
        break;
    case 'transferred':
        $predicate.= " AND T.TRANSFERRED_TO_HOSTNAME is not null ";
        break;
    case 'all':
    default:
       
        break;
}

$withButtons = isset($allDelegates[$myCnum]) && $withButtons ? true : $withButtons; // Delegates can have buttons, if we're doing buttons that is.

$withButtons = $_POST['withButtons']=='true' ? $withButtons : false;  // Only if this report allows buttons.

$dataAndSql = $dlpTable->getForPortal($predicate, $withButtons);
$data = $dataAndSql['data'];
$sql  = $dataAndSql['sql'];

$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages,'sql'=>$sql,'post'=>print_r($_POST,true));

ob_clean();
echo json_encode($response);
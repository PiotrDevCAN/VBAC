<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;
use itdq\Loader;
use vbac\assetRequestRecord;

set_time_limit(0);
ob_start();

$_SESSION['ssoEmail'] = $_SESSION['ssoEmail'];

$loader = new Loader();
// $_SESSION['isFm']   = !empty($isFm)   ? true : false;
// $_SESSION['isCdi']  = !empty($isCdi)  ? true : false;
// $_SESSION['isPmo']  = !empty($isPmo)  ? true : false;
// $_SESSION['isPes']  = !empty($isPes)  ? true : false;
// $_SESSION['isUser'] = !empty($isUser) ? true : false;
$show = !empty($_POST['show']) ? $_POST['show'] : 'all';
$pmoRaised = !empty($_POST['pmoRaised']) ?$_POST['pmoRaised'] : false;

$pmoRaised = strtolower($pmoRaised)=='true';
// $showAll = $showAll==='true' ? true : false;

$withButtons = true;
$predicate = null;
switch (true) {
    case $_SESSION['isFm']:
        // echo "is FM";
        $myCnum = personTable::myCnum();

//         $amADelegateForRaw = $loader->load('EMAIL_ADDRESS',allTables::$DELEGATE," DELEGATE_CNUM='" . htmlspecialchars($myCnum) . "' ");
//         $amADelegateFor = array_map('strtolower',$amADelegateForRaw);

        $myEmail = trim(strtolower($_SESSION['ssoEmail']));
        $myPeople = $loader->load("CNUM",allTables::$PERSON," FM_CNUM='" . trim($myCnum) . "' ");
        $myPeopleListed = "'";
        foreach ($myPeople as $personCnum){
            $myPeopleListed .= htmlspecialchars($personCnum) . "','";
        }
        $myPeopleListed .= "'";
        $predicate .= " AND ( AR.CNUM in ('". htmlspecialchars($myCnum) . "'," . $myPeopleListed . ") or lower(APPROVER_EMAIL) ='" . htmlspecialchars($myEmail) . "' ";
        $predicate .= "       OR ";
        $predicate .= "       D.DELEGATE_CNUM='" . htmlspecialchars($myCnum) . "' ) ";

        break;
    case $_SESSION['isCdi']:
        // echo "is CDI";
    case $_SESSION['isPmo']:
        // echo "is PMO";
        $assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

        switch ($show) {
            case 'awaitingIam':
                $predicate .= " AND STATUS='" . assetRequestRecord::STATUS_AWAITING_IAM . "' ";
            break;
            case 'exportable':
                $predicate .= " AND (( 1=1 " . $assetRequestTable->predicateForPmoExportableRequest() . " ) ";
                $predicate .= " OR ( 1=1 " . $assetRequestTable->predicateExportNonPmoRequests() . " )) ";
                $predicate .= $pmoRaised ?  " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " : " AND USER_CREATED='" . assetRequestRecord::CREATED_USER . "' ";
            break;
            case 'exported':
                $predicate .= " AND STATUS='" . assetRequestRecord::STATUS_EXPORTED . "' ";
                break;
            case 'bauRaised':
                $predicate .= " AND ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' AND P.TT_BAU='BAU' " ;
                $predicate .= " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " ;
                break;
            case 'nonBauRaised':
                $predicate .= " AND ORDERIT_STATUS='" . assetRequestRecord::STATUS_ORDERIT_RAISED . "' AND ( P.TT_BAU!='BAU' or P.TT_BAU is null )  " ;
                $predicate .= " AND USER_CREATED='" . assetRequestRecord::CREATED_PMO . "' " ;
                break;
            default:
                ;
            break;
        }
        break;
    default:
        // echo "is default";
        $myCnum = personTable::myCnum();
        $predicate .= " AND ( AR.CNUM = '". htmlspecialchars($myCnum) . "' ";
        $predicate .= "       OR ";
        $predicate .= "       D.DELEGATE_CNUM='" . htmlspecialchars($myCnum) . "' ) ";
        $withButtons = false;
    break;
}

$dataAndSql = assetRequestsTable::returnForPortal($predicate, $withButtons);
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$messages = ob_get_clean();
// ob_start();
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

$response = array("data"=>$data, 'messages'=>$messages, 'sql'=>$sql, 'post'=>print_r($_POST,true));

// ob_clean();
echo json_encode($response);
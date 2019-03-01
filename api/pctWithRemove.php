<?php
use vbac\personTable;
use vbac\allTables;
use itdq\Loader;
use itdq\DbTable;
use itdq\AuditTable;
use vbac\odcAccessTable;
use vbac\personRecord;
use vbac\odcAssetRemovalTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);

if($_REQUEST['token']!= $token){
    return;
}

$odcAccessTable = new odcAccessTable(allTables::$ODC_ACCESS_LIVE);
$odcAssetRemovalTable = new odcAssetRemovalTable(allTables::$ODC_ASSET_REMOVAL_LIVE);

// validate parameters will fit into the columns, without hard coding the column length here.

$platformSpecified = !empty($_REQUEST['PLATFORM']);
$loader = new Loader();
$platforms = isset($_REQUEST['PLATFORM']) ? $loader->load('WORK_STREAM',allTables::$PERSON, " UPPER(WORK_STREAM) = '" . strtoupper(trim($_REQUEST['PLATFORM'])) . "' ") : null;

$platformExists = count($platforms)>=1;

if( !$platformSpecified or !$platformExists){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Invalid Parameters provided. Details follow:';
    if(!$platformSpecified){
        $response['messages'].= " Platform value not supplied";
    }
    if(!$platformExists){
        $response['messages'].= " Platform not found in vbac";
        $response['platforms'] = print_r($platforms,true);
    }
    $response['parameters'] = print_r($_REQUEST,true);
    error_log('Invalid Parameters provided :' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(422);
    echo json_encode($response);
    return;
}

// Process request here.


$populations = $odcAccessTable->odcPopulationByPlatform();
$platformPopulation = $populations['PlatformPopulations'];
$totalPopulation     = $populations['TotalPopulation'];

$populationsWithRemove = $odcAssetRemovalTable->odcPopulationWithRemoveByPlatform();
$platformPopulationWithRemove = $populationsWithRemove['PlatformPopulationsWithRemove'];
$totalPopulationWithRemove     = $populationsWithRemove['TotalPopulationWithRemove'];

if(isset($platformPopulation[strtoupper(trim($_REQUEST['PLATFORM']))]) && isset($platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))]) ){
    $platformPctWithRemove = ($platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))] / $platformPopulation[strtoupper(trim($_REQUEST['PLATFORM']))])*100;   
} else {
    $platformPctWithRemove = 0; // Nobody for this platform has remove.    
}

$totalPopulationPctWithRemove = $totalPopulationWithRemove > 0 ?  ($totalPopulationWithRemove/$totalPopulation)*100 : 0;


$totalPopulationAllowedToRemove = $totalPopulation * 0.30;
$platformPopulationAllowedToRemove = $platformPopulation[strtoupper(trim($_REQUEST['PLATFORM']))] * 0.30 ;

$totalHeadroom = $totalPopulationAllowedToRemove - $totalPopulationWithRemove;
$platformHeadroom = isset($platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))]) ? $platformPopulationAllowedToRemove - $platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))] : $platformPopulationAllowedToRemove;

$response['Platform'] = trim($_REQUEST['PLATFORM']);

$response['Platform_Population'] = $platformPopulation[strtoupper(trim($_REQUEST['PLATFORM']))] ;
$response['Platform_Population_With_Remove'] = isset($platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))]) ?  $platformPopulationWithRemove[strtoupper(trim($_REQUEST['PLATFORM']))] : 0 ;
$response['Platform_Population_Pct_With_Remove'] = sprintf('%3.2f', $platformPctWithRemove);
$response['Platform_Population_Headroom'] = sprintf('%+.1f',$platformHeadroom);
$response['Platform_Over_Target'] = $platformHeadroom >= 1 ? 'No' : 'Yes';

$response['Total_Population'] = $totalPopulation;
$response['Total_Population_With_Remove'] = $totalPopulationWithRemove;
$response['Total_Population_Pct_With_Remove'] = sprintf('%3.2f',$totalPopulationPctWithRemove) ;
$response['Total_Population_Headroom'] = sprintf('%+.1f',$totalHeadroom);
$response['Total_Population_Over_Target'] = $totalHeadroom >= 1 ? 'No' : 'Yes';


// prepare response to caller.

$messages = ob_get_clean();
$success = empty($messages);
$response['success'] = $success;
$response['messages'] = $messages;

if(!$success){
    ob_clean();
    error_log('SaveSecurityEducationError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);
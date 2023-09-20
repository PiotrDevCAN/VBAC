<?php
use vbac\allTables;
use itdq\DbTable;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}
$cmd       = !empty($_GET['cmd']) ? strtolower($_GET['cmd']) : null;
$assetSerial= !empty($_GET['assetSerial']) ? $_GET['assetSerial'] : null;
$cnum      = !empty($_GET['cnum']) ? $_GET['cnum'] : null;
$startDate = !empty($_GET['startDate']) ? DateTime::createFromFormat('Y-m-d', $_GET['startDate']) : null;
$endDate   = !empty($_GET['endDate']) ? DateTime::createFromFormat('Y-m-d',$_GET['endDate']) : null;
$errorMsg  = null;
$httpCode  = 200;

if($startDate===false || $endDate===false){
    $errorMsg = " Date Format must be YYYY-MM-DD ";
    $httpCode = 400;
} else {
    switch ($cmd) {
        case 'add':
            switch (TRUE) {
                case empty($assetSerial):
                case empty($cnum):
                case empty($startDate);
                $errorMsg = " Asset Serial, Cnum & Start Date are all required for an ADD";
                $httpCode = 400;
                break;
                
                default:
                    $sql = " INSERT INTO " . $GLOBALS['Db2Schema']  . "." . allTables::$ODC_ASSET_REMOVAL ;
                    $sql.= " ( CNUM,  ASSET_SERIAL_NUMBER, START_DATE ";
                    $sql.= !empty($endDate) ? " , END_DATE " : null ;
                    $sql.= " ) VALUES ( '";
                    $sql.= htmlspecialchars($cnum);
                    $sql.= "','";
                    $sql.= htmlspecialchars($assetSerial);
                    $sql.= "',";
                    $sql.= "'" . htmlspecialchars($startDate->format('Y-m-d')) . "' ";
                    $sql.= !empty($endDate) ? ",'" . htmlspecialchars($endDate->format('Y-m-d')) . "' " : ",'2099-12-31' " ;
                    $sql.= ") ";
                    
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                    
                    if(!$rs){
                        $errorMsg = json_encode(sqlsrv_errors());
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                        $httpCode = 460;
                    }
                    
                    break;
            }
            break;
        case 'list':
            switch (TRUE) {
                case !empty($assetSerial):
                case !empty($cnum):
                    $sql = " SELECT CAST( CURRENT_TIMESTAMP AS Date ) as TODAY "; // means the next statement can begin with an ,
                    $sql.= empty($assetSerial) ? " ,ASSET_SERIAL_NUMBER ": null; // if they didn't provide an asset serial, that's what they want back
                    $sql.= empty($cnum) ?        " ,CNUM ": null; // if they didn't provide a cnum, that's what they want back.
                    $sql.= " FROM ". $GLOBALS['Db2Schema']  . "." . allTables::$ODC_ASSET_REMOVAL ;
                    $sql.= " WHERE 1=1 ";
                    $sql.= " and START_DATE <= CURRENT_TIMESTAMP ";
                    $sql.= " and END_DATE >= CURRENT_TIMESTAMP ";
                    $sql.= !empty($assetSerial) ? " and ASSET_SERIAL_NUMBER='" . htmlspecialchars($assetSerial) . "' " : null;
                    $sql.= !empty($cnum) ?        " and CNUM='" . htmlspecialchars($cnum) . "' " : null;
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                    
                    if(!$rs){
                        $errorMsg = json_encode(sqlsrv_errors());
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                        $httpCode = 461;
                    } else {
                        $list = array();
                        while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                            isset($row['ASSET_SERIAL_NUMBER']) ? $list[] = trim($row['ASSET_SERIAL_NUMBER']) : null; 
                            isset($row['CNUM']) ? $list[] = trim($row['CNUM']) : null;
                        }
                    }
                    break;
                default:
                    $errorMsg = " Asset Serial or Cnum are all required for a LIST";
                    $httpCode = 400;
                    break;
            }
            break;
        case 'remove':
            switch (TRUE) {
                case empty($cnum):
                case empty($assetSerial);
                $errorMsg = " Cnum & Asset Serial required for a REMOVE";
                $httpCode = 400;
                break;
                default:
                    $sql = " DELETE  ";
                    $sql.= " FROM ". $GLOBALS['Db2Schema']  . "." . allTables::$ODC_ASSET_REMOVAL ;
                    $sql.= " WHERE 1=1 ";
                    $sql.= !empty($assetSerial) ? " AND ASSET_SERIAL_NUMBER='" . htmlspecialchars($assetSerial) . "' " : null;
                    $sql.= !empty($cnum) ?        " AND CNUM='" . htmlspecialchars($cnum) . "' " : null;
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                    
                    if(!$rs){
                        $errorMsg = json_encode(sqlsrv_errors());
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                        $httpCode = 461;
                    }
                    break;
            }
            break;
        case 'extend':
            switch (TRUE) {
                case empty($cnum):
                case empty($endDate);
                case empty($assetSerial);
                $errorMsg = " Cnum, Asset Serial & endDate required for a EXTEND";
                $httpCode = 400;
                break;
                default :
                    $sql = " UPDATE  " . $GLOBALS['Db2Schema']  . "." . allTables::$ODC_ASSET_REMOVAL ;
                    $sql.= " SET ";
                    $sql.= " END_DATE='" . htmlspecialchars($endDate->format('Y-m-d')) . "' ";
                    $sql.= " WHERE 1=1 ";
                    $sql.= !empty($assetSerial) ? " AND ASSET_SERIAL_NUMBER='" . htmlspecialchars($assetSerial) . "' " : null;
                    $sql.= !empty($cnum) ?        " AND CNUM='" . htmlspecialchars($cnum) . "' " : null;
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                    
                    if(!$rs){
                        $errorMsg = json_encode(sqlsrv_errors());
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                        $httpCode = 461;
                    }
                    
                    break;
            }
            break;
        case 'check':
            switch (TRUE) {
                case empty($cnum):
                case empty($assetSerial);
                $errorMsg = " Cnum & Asset Serial  required for a CHECK";
                $httpCode = 400;
                break;
                default :
                    $sql = " SELECT count(*) as VALID FROM  " . $GLOBALS['Db2Schema']  . "." . allTables::$ODC_ASSET_REMOVAL ;
                    $sql.= " WHERE 1=1 ";
                    $sql.= " and START_DATE <= CURRENT_TIMESTAMP ";
                    $sql.= " and END_DATE >= CURRENT_TIMESTAMP ";
                    $sql.= " and ASSET_SERIAL_NUMBER='" . htmlspecialchars($assetSerial) . "' " ;
                    $sql.= " and CNUM='" . htmlspecialchars($cnum) . "' " ;
                    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
                    
                    if(!$rs){
                        $errorMsg = json_encode(sqlsrv_errors());
                        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                        $httpCode = 461;
                    } else {
                        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
                        if($row['VALID']>0){
                            $permitted = 'Yes';
                        } else {
                            $permitted = 'No';
                        }
                    }
                    
                    break;
            }

            break;
        default:
            $errorMsg = 'No Command specified';
            $httpCode = 401;
            ;
            break;
    }
}

$errorMsg.= ob_get_clean();

$response = array('success'=>empty($errorMsg),'errorMsg'=>$errorMsg);

!empty($errorMsg) && !empty($sql) ? $response['sql'] = $sql : null;

switch ($cmd) {
    case 'check':
        $response['permitted'] = $permitted;
        break;
    case 'list':
        $response['list'] = $list;
        break;
    default:
        ;
        break;
}

ob_clean();
http_response_code($httpCode); 
echo json_encode($response);
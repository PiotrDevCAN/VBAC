<?php
use vbac\pesTrackerTable;
use vbac\allTables;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
// ob_start();

$pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);
$table = $pesTrackerTable->buildTable($_REQUEST['records']);

$dataJsonAble = json_encode($table);

if($dataJsonAble) {
    $messages = ob_get_clean();
    ob_start();
    $success = empty($messages);
    $response = array("records"=>$_REQUEST['records'],"success"=>$success,'messages'=>$messages,'table'=>$table);
    
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start("ob_gzhandler");
        } else {
            ob_start("ob_html_compress");
        }
    } else {
        ob_start("ob_html_compress");
    }
    echo json_encode($response);
} else {
    var_dump($dataJsonAble);
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
    $success = empty($messages);
    $response = array("records"=>$_REQUEST['records'], "success"=>$success, 'messages'=>$messages);
    echo json_encode($response);
}


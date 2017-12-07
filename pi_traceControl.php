<?php
use itdq\TraceControlTable;
use itdq\TraceControlRecord;
use itdq\TraceControlList;
use itdq\FormClass;
use itdq\AllItdqTables;
do_auth($_SESSION['itdqBg']);
echo "<div class='container'>";
$csv = null;
$traceControlValue = null; 


if(isset($_REQUEST['mode'])){
   
    if($_REQUEST['mode']=='insert' 
        &&    
        ((($_REQUEST['TRACE_CONTROL_TYPE'] == TraceControlRecord::CONTROL_TYPE_CLASS_INCLUDE or $_REQUEST['TRACE_CONTROL_TYPE'] == TraceControlRecord::CONTROL_TYPE_CLASS_EXCLUDE) && !empty($_REQUEST['trace_class_name']))
         or
         (($_REQUEST['TRACE_CONTROL_TYPE'] == TraceControlRecord::CONTROL_TYPE_METHOD_INCLUDE or $_REQUEST['TRACE_CONTROL_TYPE'] == TraceControlRecord::CONTROL_TYPE_METHOD_EXCLUDE) && !empty($_REQUEST['trace_method_name']))
        ))
    {
        $traceControlValue = substr($_REQUEST['TRACE_CONTROL_TYPE'],0,5)=='class' ? $_REQUEST['trace_class_name'] : $_REQUEST['trace_method_name'];

        TraceControlTable::insertTraceControl($_REQUEST['TRACE_CONTROL_TYPE'],$traceControlValue);
    } elseif($_REQUEST['mode']=='delete' and (isset($_REQUEST['TRACE_CONTROL_TYPE']) or !empty($traceControlValue))){
		TraceControlTable::deleteTraceControl($_REQUEST['TRACE_CONTROL_TYPE'],$_REQUEST['TRACE_CONTROL_VALUE']);
	}
}

echo "<div id='messagePlaceholder'>";
echo "</div>";

echo "<FORM role='form' name='traceControlForm' method='post' enctype='application/x-www-form-urlencoded' action='" . $_SERVER['PHP_SELF'] . "' >";
$record = new TraceControlRecord();
$record->getForm();
$record->displayForm (FormClass::$modeEDIT);
echo "</FORM>";

echo "<FORM role='form' name='myForm' method='post' enctype='application/x-www-form-urlencoded' action='" . $_SERVER['PHP_SELF'] . "'>";
$list = new TraceControlList( AllItdqTables::$TRACE_CONTROL,  $_SERVER['PHP_SELF'], $csv, $pwd );
$list->displayTable ( $list->fetchList ());
echo "</FORM>";
echo "</div>"; // container

$list->dataTablesScript();

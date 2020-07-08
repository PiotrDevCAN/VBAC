<?php
namespace itdq;
use \DateTime;
use itdq\TraceControlRecord;
/**
 * Displays the LOG table
 *
 * @author GB001399
 * @package bgdm
 *
 */
class TraceList extends LogList {
    
function __construct($tableName=null, $pwd=null){
	parent::__construct($tableName,$pwd);
	$this->tableTag = "<TABLE class='table table-striped responsive' id='traceTable'>";
	$this->DbTable = new DbTable ( $tableName, null );
	$this->fields = $this->DbTable->getColumns ();	
	$dates = array();
	$firstDate = new DateTime ();
	$firstDate->setISODate ( "2010", "01", 5 );
	$lastDate = new DateTime ();
	$lastDate = date_create ();
	if(!isset($_REQUEST['sbsFrom'])){
		$_REQUEST['sbsFrom'] = $lastDate->format("Y-m-d");
	}
	if(!isset($_REQUEST['sbsTo'])){
		$_REQUEST['sbsTo'] = $lastDate->format("Y-m-d");
	}
	for($day = $lastDate; $day >= $firstDate; $day->modify ( "-1 day" )) {
		$dates[$day->format("d M Y")] = $day->format("Y-m-d");
	}	
	$loader = new Loader($pwd);
	$this->dropSelect = array(
		'Updater'     => array('label'=>'Updater','first'=>'All...','column'=>"LASTUPDATER",'array'=>$loader->load("LASTUPDATER",$tableName))
		, 'Class'     => array('label'=>'Class','first'=>'All...','column'=>"CLASS",'array'=>$loader->load("CLASS",$tableName))
		, 'Method'    => array('label'=>'Method','first'=>'All...','column'=>"METHOD",'array'=>$loader->load("METHOD",$tableName))
		, 'Page'      => array('label'=>'Page','first'=>'All...','column'=>"PAGE",'array'=>$loader->load("PAGE",$tableName))	
		, 'From'      => array ('label' => 'From', 'first' => 'All...', 'column' => "DATE(LASTUPDATED)", 'array' => $dates , 'type'=>'date', 'operator' => ">=")
		, 'To'        => array ('label' => 'To', 'first' => 'All...', 'column' => "DATE(LASTUPDATED)", 'array' => $dates , 'type'=>'date', 'operator' => '<=')
		);
	}
	
function fetchList(){
	Trace::traceComment(null,__METHOD__);	
	$this->sql  = " SELECT ";
	foreach($this->fields as $col => $label){
	//	$this->sql .= ", " . $label['COLUMN'] . " as $col";
		$this->sql .= ", " . $label . " as $col";
	}
	$this->sql .= " from " . $GLOBALS['Db2Schema'] . "." . $this->DbTable->getName() . " as E";
	$this->sql = str_replace('SELECT ,','SELECT ',$this->sql);

	$predicateParm = $this->predicateSelect->getPredicate();

	if($predicateParm != null){
		$predicate = " WHERE " . trim($predicateParm);
		$predicate = str_replace('WHERE AND','WHERE ',$predicate);
		$this->sql .= $predicate;
	}

	$this->sql .= " ORDER BY LASTUPDATED DESC ";	
	return parent::fetchList();
}

	function processField($key, $value, $row, $type=null) {
		switch ($key) {
			case 'LOG_ENTRY':
				
				$newValue = wordwrap ( $value, 50,"\n",TRUE);
				$newValue = "<pre>" . $newValue . "</pre>";
				
// 				$newValue = "<pre>" . $value . "</pre>";
				
				
				// $newValue = wordwrap ( str_replace("[","<BR/>[" ,str_replace("-</B>:","-</B>: <br/>", $value)) , 55,"\n",TRUE);
			break;
			default:
				$newValue = $value;
			break;
		}
		parent::processField($key,$newValue,$row,$type);
	}
	
	function displayTable($rs, $subTotal = false, $full = true, $editLink = false, $deleteLink = false, $width = '95%', $totalOnly=false) {
		parent::displayTable($rs, $subTotal, $full, $editLink, $deleteLink, $width, $totalOnly);
	}
	
	function listOptions($controlType){	
	    echo "<div class='list-group col-xs-4'>";
	    echo "<a href='#' class='list-group-item active'>";
	    echo "<h4 class='list-group-item-heading'>";
	    echo TraceControlRecord::$controlTypeHeadings[$controlType];
	    echo "</h4>";
	    	    foreach($_SESSION[$controlType] as $method => $value){
	    	        echo "<p class='list-group-item-text'>$method</p>";
	    	    }
	    echo "</a>";
	    echo "</div>";
	}
	
	
	function dataTablesScript(){
	    echo "<script>
            $(document).ready(function(){
                $('#traceTable').dataTable( {  
	                    'pagingType': 'full_numbers',
                        'order': [[ 2, 'desc' ]],
	                    'dom': 'C<\"clear\">lfrtip'
                    } );
                });
            </script>";
	}
}
?>
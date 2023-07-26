<?php
namespace itdq;
use itdq\DbTable;

/**
 * Lists the Trace Control table
 *
 * @author GB001399
 * @package bgdm
 *
 */
class TraceControlList extends SortableList {


	function __construct($tableName, $pwd=null ) {
		Trace::traceComment(null,__METHOD__);
		$excel = null;
		parent::__construct ( $tableName, $pwd );		
		$this->tableTag = "<TABLE class='table table-striped responsive' id='traceControlTable'>";
		$this->readonlyBoxes = array ('Records' => array ('title' => 'Records Listed', 'label' => 'lines', 'size' => 8, 'maxLength' => 8, 'value' => 0 ) );
		$loader = new Loader ();
		$this->dropSelect = array (
			'Type' => array ('label' => 'Type', 'first' => 'All...', 'column' => "TRACE_CONTROL_TYPE", 'array' => $loader->load ( "TRACE_CONTROL_TYPE", $tableName ) ),
			'Values'  => array ('label' => 'Value', 'first' => 'All...', 'column' => "TRACE_CONTROL_VALUE", 'array' => $loader->load ( "TRACE_CONTROL_VALUE", $tableName ) ),
			);

		$noLog = false;
	}
	
	function fetchList() {
		$this->sql = $this->DbTable->getSelect ( null );
		$this->sql .= " ORDER BY 1,2 ";
		return parent::fetchList ();
	}

	function displayTable($rs, $width = '50%') {
		Trace::traceComment(null,__METHOD__);	
		$editLink = FALSE;
		$deleteLink = TRUE;	
		$pivot=FALSE;
		$full = TRUE;
		parent::displayTable($rs, $pivot, $full, $editLink, $deleteLink, $width);
	}	
	
	function insertDelete($row){
		echo "<TD style='text-align:center'>";
		echo "<A href='" . $_SERVER['PHP_SELF'] . "?TRACE_CONTROL_TYPE=". $row['TRACE_CONTROL_TYPE'] . "&amp;TRACE_CONTROL_VALUE=" . $row['TRACE_CONTROL_VALUE'] .  "&amp;mode=delete'>";
		echo "<img src='../ItdqLib_V1/ui/images/icon-link-delete-dark.gif' width='14' height='14' alt='delete' /></a></TD>";
	}
	
	
	function dataTablesScript(){
	    echo "<script>
            $(document).ready(function(){
                $('#traceControlTable').dataTable( {
	                   'responsive' : true,
	                    'pagingType': 'full_numbers',
                    } );
                });
            </script>";
	}
	
	
}
?>
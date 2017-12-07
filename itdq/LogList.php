<?php
namespace itdq;
/*
 * This class Displays the LOG table.
 * 
 * @author GB001399
 * @package itdqLib
 * 
 */

class LogList extends SortableList {
	
	function __construct($tableName=null, $pwd=null) {
		parent::__construct ($tableName, $pwd );
		$this->DbTable = new DbTable ( $tableName, null );
		$this->fields = $this->DbTable->getColumns ();
		$this->colSelect = new SelectionBar ();
		$this->predicateSelect = new SelectionBar ();
		$dates = array ();
		$firstDate = new \DateTime();
		$firstDate->setISODate ( "2010", "01", 5 );
		$lastDate = new \DateTime ();
		$lastDate = date_create ();
		if (! isset ( $_REQUEST ['sbsFrom'] )) {
			$_REQUEST ['sbsFrom'] = $lastDate->format ( "Y-m-d" );
		}
		if (! isset ( $_REQUEST ['sbsTo'] )) {
			$_REQUEST ['sbsTo'] = $lastDate->format ( "Y-m-d" );
		}
		for($day = $lastDate; $day >= $firstDate; $day->modify ( "-1 day" )) {
			$dates [$day->format ( "d M Y" )] = $day->format ( "Y-m-d" );
		}
		if(!empty($pwd)){
			$loader = new ELoader ( $pwd );
			$updaterColumn = "DECRYPT_CHAR(LASTUPDATER,'$this->pwd')";
		} else {
			$loader = new Loader ();
			$updaterColumn = "LASTUPDATER";
		}
		
		if(!empty($this->dropSelect)){
			$this->dropSelect = array ('Updater' => array ('label' => 'Updater', 'first' => 'All...', 'column' => "$updaterColumn",'array' => $loader->load ( "LASTUPDATER", $tableName, false ) ),
									   'From' => array ('label' => 'From', 'first' => 'All...', 'column' => "DATE(LASTUPDATED)", 'array' => $dates, 'type' => 'date', 'operator' => ">=" ),
									   'To' => array ('label' => 'To', 'first' => 'All...', 'column' => "DATE(LASTUPDATED)", 'array' => $dates, 'type' => 'date', 'operator' => '<=' ) );
		}
	
	}
	
	function fetchList() {
		$this->sql = " SELECT ";
		foreach ( $this->fields as $col => $label ) {
			if ($col != "LASTUPDATED" and !empty($this->pwd)) {
				$this->sql .= ",DECRYPT_CHAR(" . $col . ",'" . $this->pwd . "') as $label";
			} else {
				$this->sql .= ", " . $col . " as $label";
			}
		}
		
		$this->sql .= " from " . $_SESSION['Db2Schema'] . "." . $this->DbTable->getName(). " as E";
		
		$this->sql = str_replace ( 'SELECT ,', 'SELECT ', $this->sql );
		
		$predicateParm = $this->predicateSelect->getPredicate ();
		
		if ($predicateParm != null) {
			$predicate = " WHERE " . trim ( $predicateParm );
			$predicate = str_replace ( 'WHERE AND', 'WHERE ', $predicate );
			$this->sql .= $predicate;
		}
		$this->sql .= " ORDER BY LASTUPDATED DESC ";
		return parent::fetchList ( TRUE );
	
	}
	
	function processField($key, $value, $row, $type = null) {
		/*
		 * Overwrite the default because we don't want to call htmlspecial chars
		 * we want the <B></B> tags to be obeyed.
		 */
		echo "<TD>" . trim ( $value ) . "</TD>";
	}

}

?>
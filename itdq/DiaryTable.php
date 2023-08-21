<?php
namespace itdq;
use itdq\AllItdqTables;
use itdq\DbTable;

/**
 * Interfaces to the DIARY table, basically by inserting entries.
 *
 * @author GB001399
 * @package esoft
 *
 */
class DiaryTable extends DbTable {

	static function insertEntry( $entry) {
		$sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " ( ENTRY, INTRANET_ID) ";
		$sql .= " Values ('" . htmlspecialchars(trim($entry)) . "','" . htmlspecialchars($_SESSION['ssoEmail']) . "' ) ";

		$rs = DB2_EXEC ( $_SESSION ['conn'], $sql );
		if (! $rs) {
			print_r ( $_SESSION );
			echo "<BR/>" . db2_stmt_error ();
			echo "<BR/>" . db2_stmt_errormsg () . "<BR/>";
			exit ( "Error in: " . __METHOD__ . " running: " . $sql );
		}
		return	db2_last_insert_id($_SESSION ['conn']);

	}
}

?>
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

	static function insertEntry( $entry ) {
		$sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$DIARY . " ( ENTRY, CREATOR ) ";
		$sql .= " Values ('" . htmlspecialchars(trim($entry)) . "','" . htmlspecialchars($_SESSION['ssoEmail']) . "' ) ";

		$rs = sqlsrv_query( $GLOBALS['conn'], $sql );
		if (! $rs) {
			print_r ( $_SESSION );
			echo "<BR>" . json_encode(sqlsrv_errors());
			echo "<BR>" . json_encode(sqlsrv_errors()) . "<BR>";
			exit ( "Error in: " . __METHOD__ . " running: " . $sql );
		}

		$diaryTable = new DiaryTable(AllItdqTables::$DIARY);

		return $diaryTable->lastId();
	}
}

?>
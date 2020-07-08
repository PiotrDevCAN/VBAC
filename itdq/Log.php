<?php
namespace itdq;
/**
 * This class logs simple messages to a LOG table.
 *
 * The definition of the LOG table is hardcoded within this class.
 * Any $entry sent to the Log will first be db2_escape_string'd then any occurances of $pwd will be replaced by '********', so it's quite safe to log almost anything.
 *
 * @author GB001399
 * @package itdqLib
 *
 */
class Log extends DbTable  {

static function logEntry($entry,$pwd=null){




	$userid = $_SESSION['ssoEmail'];

	$sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$LOG . " ( LOG_ENTRY,LASTUPDATER) ";
	$db2Entry = db2_escape_string($entry);
	$db2Entry =  str_replace($pwd,'********',$db2Entry);
	if($pwd!=null){
		$sql .= " VALUES (ENCRYPT_RC2('$db2Entry','$pwd'),ENCRYPT_RC2('$userid','$pwd')) ";
	} else {
		$sql .= " VALUES ('$db2Entry','$userid') ";
	}
	$rs = DB2_EXEC($GLOBALS['conn'],$sql);
	if(!$rs)
		{
		echo "<BR>Error: " . db2_stmt_error();
		echo "<BR>Msg: " . db2_stmt_errormsg() . "<BR>";
		exit("Error in: " . __FILE__ . ":" .  __METHOD__ . "-" .  __LINE__ . "<BR>running: $sql");
	}
}

	static function deleteLogRecords($keepDays=1){
		$sql = "DELETE FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$LOG . " WHERE LASTUPDATED < (CURRENT TIMESTAMP - $keepDays DAYS) ";
		$rs = DB2_EXEC($GLOBALS['conn'],$sql);
		if(!$rs)
			{
			echo "<BR>Error: " . db2_stmt_error();
			echo "<BR>Msg: " . db2_stmt_errormsg() . "<BR>";
			exit("Error in: " . __METHOD__ .  __LINE__ . "<BR>running: $sql");
		}
	}
}
?>

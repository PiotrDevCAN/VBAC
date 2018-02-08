<?php
namespace itdq;

/**
 * Provides a list of public static properties that define the specific table names used in the application.
 *
 * This removes the need to hardcode the table name in the app itself.
 *
 *
 */
class AllItdqTables
{

    public static $AUDIT            = 'AUDIT';

    public static $DB2_ERRORS       = 'DB2_ERRORS';
//     public static $DIARY            = 'DIARY';

    public static $EMAIL_LOG        = 'EMAIL_LOG';

    public static $TRACE            = 'TRACE';
    public static $TRACE_CONTROL    = 'TRACE_CONTROL';
}

?>
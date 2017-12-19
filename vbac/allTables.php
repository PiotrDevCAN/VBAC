<?php
namespace vbac;

/**
 * Provides a list of public static properties that define the specific table names used in the application.
 *
 * This removes the need to hardcode the table name in the app itself.
 *
 *
 */
class allTables
{
    public static $DB2_ERRORS       = 'DB2_ERRORS';

    public static $STATIC_DOMAINS  = 'STATIC_DOMAINS';
    public static $STATIC_GROUPS  = 'STATIC_GROUPS';
    public static $STATIC_ROLES   = 'STATIC_ROLES';



    public static $TRACE            = 'TRACE';
    public static $TRACE_CONTROL    = 'TRACE_CONTROL';



}
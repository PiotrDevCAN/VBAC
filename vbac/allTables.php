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
    public static $ASSET_REQUESTS     = 'ASSET_REQUESTS';

    public static $DB2_ERRORS         = 'DB2_ERRORS';

    public static $PERSON             = 'PERSON';
    public static $PERSON_PORTAL_REPORTS = 'PERSON_PORTAL_REPORTS';
    public static $REQUESTABLE_ASSET_LIST = 'REQUESTABLE_ASSET_LIST';
    public static $STATIC_COUNTRY_CODES = 'STATIC_COUNTRY_CODES';
    public static $STATIC_DOMAINS     = 'STATIC_DOMAINS';
    public static $STATIC_GROUPS      = 'STATIC_GROUPS';
    public static $STATIC_ROLES       = 'STATIC_ROLES';
    public static $STATIC_WORKSTREAMS = 'STATIC_WORKSTREAMS';

    public static $TRACE              = 'TRACE';
    public static $TRACE_CONTROL      = 'TRACE_CONTROL';
}
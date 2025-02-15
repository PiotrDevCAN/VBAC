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
    public static $AGILE_SQUAD     = 'AGILE_SQUAD';
    public static $AGILE_TRIBE     = 'AGILE_TRIBE';

    public static $AGILE_SQUAD_OLD = 'AGILE_SQUAD_OLD';
    public static $AGILE_TRIBE_OLD = 'AGILE_TRIBE_OLD';

    public static $EMPLOYEE_AGILE_MAPPING  = 'EMPLOYEE_AGILE_MAPPING';

    public static $ASSET_REQUESTS     = 'ASSET_REQUESTS';
    public static $ASSET_REQUESTS_EVENTS  = 'ASSET_REQUESTS_EVENTS';
    public static $ASSET_REQUESTS_EVENTS_SUMMARY  = 'ASSET_REQUESTS_EVENTS_SUMMARY';

    public static $DB2_ERRORS         = 'DB2_ERRORS';
    public static $DELEGATE           = 'DELEGATE';
    public static $DLP                = 'DLP';

    public static $EMPLOYEE_TYPE_MAPPING  = 'EMPLOYEE_TYPE_MAPPING';
    public static $BUSINESS_TITLE_MAPPING  = 'BUSINESS_TITLE_MAPPING';

    public static $FEB_TRAVEL_REQUEST_TEMPLATES = 'FEB_TRAVEL_REQUEST_TEMPLATES';

    public static $ODC_ACCESS             = 'ODC_ACCESS';
    public static $ODC_ACCESS_LIVE        = 'ODC_ACCESS_LIVE';
    public static $ODC_ASSET_REMOVAL      = 'ODC_ASSET_REMOVAL';
    public static $ODC_ASSET_REMOVAL_LIVE = 'ODC_ASSET_REMOVAL_LIVE';

    public static $ORDER_IT_VARB_TRACKER  = 'ORDER_IT_VARB_TRACKER';

    public static $PERSON                 = 'PERSON';
    public static $PERSON_PORTAL_REPORTS  = 'PERSON_PORTAL_REPORTS';
    public static $PERSON_PORTAL          = 'PERSON_PORTAL_VIEW';
    public static $PERSON_PORTAL_LITE     = 'PERSON_PORTAL_LITE_VIEW';

    public static $PES_EVENTS             = 'PES_EVENTS';
    public static $PES_TRACKER            = 'PES_TRACKER';

    public static $REQUESTABLE_ASSET_LIST = 'REQUESTABLE_ASSET_LIST';

    public static $STATIC_COUNTRY_CODES   = 'STATIC_COUNTRY_CODES';
    public static $STATIC_DOMAINS         = 'STATIC_DOMAINS';
    public static $STATIC_GROUPS          = 'STATIC_GROUPS';
    public static $STATIC_LOCATIONS       = 'STATIC_LOCATIONS';
    public static $STATIC_COUNTRIES       = 'STATIC_COUNTRIES';
    public static $STATIC_CITIES          = 'STATIC_CITIES';
    public static $STATIC_ROLES           = 'STATIC_ROLES';
    public static $STATIC_WORKSTREAMS     = 'STATIC_WORKSTREAMS';
    public static $STATIC_SKILLSETS       = 'STATIC_SKILLSETS';

    public static $TRACE                  = 'TRACE';
    public static $TRACE_CONTROL          = 'TRACE_CONTROL';

    public static $ADD_CANDIDATE          = 'ADD_CANDIDATE_REQUEST';
    public static $CANDIDATE_STATUS_REQ   = 'CANDIDATE_STATUS_REQUEST';

    public static $CANDIDATE_DETAILS      = 'CANDIDATE_DETAILS';
    public static $CANDIDATE_STATUS       = 'CANDIDATE_STATUS';
    public static $CANDIDATE_DOCUMENTS    = 'CANDIDATE_DOCUMENT';

    public static $CFIRST_PERSON          = 'CFIRST_PERSON';
    public static $TEST_TBL               = 'TEST_TBL';
}
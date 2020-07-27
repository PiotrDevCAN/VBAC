--<ScriptOptions statementTerminator=";"/>

CREATE TABLE "VBAC_UT"."AGILE_SQUAD" (
		"SQUAD_NUMBER" DECIMAL(5 , 0) NOT NULL,
		"SQUAD_TYPE" CHAR(60) NOT NULL,
		"TRIBE_NUMBER" CHAR(10) NOT NULL,
		"SHIFT" CHAR(1) NOT NULL,
		"SQUAD_LEADER" CHAR(50),
		"SQUAD_NAME" CHAR(60)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."AGILE_SQUAD_OLD" (
		"SQUAD_NUMBER" DECIMAL(5 , 0) NOT NULL,
		"SQUAD_TYPE" CHAR(60) NOT NULL,
		"TRIBE_NUMBER" CHAR(10) NOT NULL,
		"SHIFT" CHAR(1) NOT NULL,
		"SQUAD_LEADER" CHAR(50),
		"SQUAD_NAME" CHAR(60)
	)
		in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."AGILE_TRIBE" (
		"TRIBE_NUMBER" CHAR(10) NOT NULL,
		"TRIBE_NAME" CHAR(70) NOT NULL,
		"TRIBE_LEADER" CHAR(60),
		"ORGANISATION" CHAR(25) NOT NULL DEFAULT 'Managed Services'
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."AGILE_TRIBE_OLD" (
		"TRIBE_NUMBER" CHAR(10) NOT NULL,
		"TRIBE_NAME" CHAR(70) NOT NULL,
		"TRIBE_LEADER" CHAR(60),
		"ORGANISATION" CHAR(25) NOT NULL DEFAULT 'Managed Services'
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ASSET_REQUESTS" (
		"REQUEST_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"CNUM" CHAR(9) NOT NULL,
		"ASSET_TITLE" CHAR(60),
		"USER_LOCATION" VARCHAR(256),
		"PRIMARY_UID" CHAR(60),
		"SECONDARY_UID" CHAR(60),
		"DATE_ISSUED_TO_IBM" DATE,
		"DATE_ISSUED_TO_USER" DATE,
		"DATE_RETURNED" DATE,
		"BUSINESS_JUSTIFICATION" VARCHAR(512),
		"PRE_REQ_REQUEST" INTEGER,
		"REQUESTOR_EMAIL" CHAR(60),
		"REQUESTED" TIMESTAMP,
		"REQUEST_BY_DEFAULT" CHAR(1),
		"APPROVER_EMAIL" CHAR(60),
		"APPROVED" TIMESTAMP,
		"EDUCATION_CONFIRMED" CHAR(3),
		"STATUS" CHAR(30),
		"ORDERIT_VARB_REF" CHAR(10),
		"ORDERIT_NUMBER" CHAR(20),
		"ORDERIT_STATUS" CHAR(50),
		"USER_CREATED" CHAR(3) DEFAULT 'No',
		"COMMENT" VARCHAR(512) DEFAULT NULL,
		"REQUEST_RETURN" CHAR(3) DEFAULT 'No',
		"ORDERIT_RESPONDED" DATE
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ASSET_REQUESTS_EOD20181127" (
		"REQUEST_REFERENCE" INTEGER NOT NULL,
		"CNUM" CHAR(9) NOT NULL,
		"ASSET_TITLE" CHAR(60),
		"USER_LOCATION" VARCHAR(256),
		"PRIMARY_UID" CHAR(60),
		"SECONDARY_UID" CHAR(60),
		"DATE_ISSUED_TO_IBM" DATE,
		"DATE_ISSUED_TO_USER" DATE,
		"DATE_RETURNED" DATE,
		"BUSINESS_JUSTIFICATION" VARCHAR(512),
		"PRE_REQ_REQUEST" INTEGER,
		"REQUESTOR_EMAIL" CHAR(60),
		"REQUESTED" TIMESTAMP,
		"REQUEST_BY_DEFAULT" CHAR(1),
		"APPROVER_EMAIL" CHAR(60),
		"APPROVED" TIMESTAMP,
		"EDUCATION_CONFIRMED" CHAR(3),
		"STATUS" CHAR(30),
		"ORDERIT_VARB_REF" CHAR(10),
		"ORDERIT_NUMBER" CHAR(20),
		"ORDERIT_STATUS" CHAR(50),
		"USER_CREATED" CHAR(3) DEFAULT 'No',
		"COMMENT" VARCHAR(512) DEFAULT NULL,
		"REQUEST_RETURN" CHAR(3) DEFAULT 'No',
		"ORDERIT_RESPONDED" DATE
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS" (
		"REQUEST_REFERENCE" INTEGER,
		"EVENT" CHAR(75),
		"OCCURED" TIMESTAMP,
		"INITIATED_BY" VARCHAR(150)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ASSET_REQUEST_DIARY" (
		"DIARY_REFERENCE" INTEGER NOT NULL,
		"REQUEST_REFERENCE" INTEGER NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."AUDIT" (
		"TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"EMAIL_ADDRESS" CHAR(60),
		"DATA" CLOB(1048576),
		"TYPE" CHAR(20)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."AUDIT_VC" (
		"TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"EMAIL_ADDRESS" CHAR(60),
		"DATA" VARCHAR(32000),
		"TYPE" CHAR(10)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."DB2_ERRORS" (
		"USERID" CHAR(50) DEFAULT NULL,
		"PAGE" VARCHAR(200) DEFAULT NULL,
		"DB2_ERROR" CHAR(10) DEFAULT NULL,
		"DB2_MESSAGE" CHAR(200) DEFAULT NULL,
		"BACKTRACE" VARCHAR(1024) DEFAULT NULL,
		"REQUEST" VARCHAR(2048) DEFAULT NULL,
		"TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."DELEGATE" (
		"CNUM" CHAR(9) NOT NULL,
		"EMAIL_ADDRESS" CHAR(60) NOT NULL,
		"DELEGATE_CNUM" CHAR(9) NOT NULL,
		"DELEGATE_EMAIL" CHAR(60) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."DIARY" (
		"DIARY_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"ENTRY" CLOB(1024) DEFAULT NULL,
		"CREATOR" CHAR(50) DEFAULT NULL,
		"CREATED" TIMESTAMP DEFAULT CURRENT TIMESTAMP
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."DLP" (
		"CNUM" CHAR(9) NOT NULL,
		"APPROVER_EMAIL" CHAR(40),
		"APPROVED_DATE" DATE,
		"HOSTNAME" CHAR(15) NOT NULL,
		"CREATION_DATE" DATE NOT NULL DEFAULT CURRENT DATE,
		"TRANSFERRED_TO_HOSTNAME" CHAR(15),
		"TRANSFERRED_DATE" DATE,
		"TRANSFERRED_EMAIL" CHAR(40),
		"EXCEPTION_CODE" CHAR(5) NOT NULL DEFAULT '266',
		"STATUS" CHAR(20) NOT NULL DEFAULT 'pending'
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."EMAIL_LOG" (
		"RECORD_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"TO" VARCHAR(512) DEFAULT NULL,
		"SUBJECT" VARCHAR(200) DEFAULT NULL,
		"MESSAGE" CLOB(262144) DEFAULT NULL,
		"DATA_JSON" CLOB(1048576) DEFAULT NULL,
		"RESPONSE" CLOB(10240) DEFAULT NULL,
		"LAST_STATUS" CLOB(1024) DEFAULT NULL,
		"SENT_TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"STATUS_TIMESTAMP" TIMESTAMP DEFAULT NULL,
		"CC" CLOB(1048576),
		"BCC" CLOB(1048576)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."EMPLOYEE_TYPE_MAPPING" (
		"CODE" CHAR(5) NOT NULL,
		"DESCRIPTION" CHAR(25) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."FEB_BKUP" (
		"APPLICATION_UID" CHAR(37) NOT NULL,
		"FORMID" CHAR(50) NOT NULL,
		"BACKUP_TS" TIMESTAMP NOT NULL,
		"DATA" CLOB(200000000) NOT NULL COMPACT
	)
	in USERSPACE1
	DATA CAPTURE NONE 
	VALUE COMPRESSION;

CREATE TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" (
		"EMAIL_ADDRESS" CHAR(120),
		"TITLE" VARCHAR(255),
		"TEMPLATE" CLOB(5000)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ODC_ACCESS" (
		"S_NO" INTEGER,
		"REQUEST_ID" CHAR(11),
		"OWNER_CNUM_ID" CHAR(20) NOT NULL DEFAULT ' ',
		"OWNER_NOTES_ID" CHAR(50),
		"ACCESS_FOR" CHAR(40),
		"SECURED_AREA_NAME" CHAR(50),
		"REQUEST_TYPE" CHAR(20),
		"START_DATE" DATE,
		"END_DATE" DATE,
		"REQUEST_STATUS" CHAR(50),
		"WORK_FLOW_TYPE" CHAR(20),
		"WORK_FLOW_STATUS" CHAR(50),
		"CREATED_TMSP" DATE,
		"PEOPLE_MANAGERS_NOTES_ID" CHAR(50),
		"SECURE_AREA_MANAGERS_NAME" CHAR(50),
		"CREATED" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ODC_ASSET_REMOVAL" (
		"CNUM" CHAR(9) NOT NULL,
		"ASSET_SERIAL_NUMBER" CHAR(50) NOT NULL,
		"START_DATE" DATE NOT NULL DEFAULT CURRENT DATE,
		"END_DATE" DATE,
		"SYS_START" TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN IMPLICITLY HIDDEN,
		"SYS_END" TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END IMPLICITLY HIDDEN,
		"CREATE_ID" TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID IMPLICITLY HIDDEN,
		PERIOD SYSTEM_TIME(sys_start,sys_end)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ODC_ASSET_REMOVAL_HIST" LIKE  "VBAC_UT"."ODC_ASSET_REMOVAL";

ALTER TABLE "VBAC_UT"."ODC_ASSET_REMOVAL"
ADD VERSIONING USE HISTORY TABLE "VBAC_UT"."ODC_ASSET_REMOVAL_HIST";


CREATE TABLE "VBAC_UT"."ODC_LOCATION" (
		"SECURED_AREA_NAME" CHAR(50) NOT NULL,
		"MANAGERS_CNUM" CHAR(9) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."ORDER_IT_VARB_TRACKER" (
		"VARB" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"CREATED_DATE" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"CREATED_BY" CHAR(40) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."PERSON" (
		"CNUM" CHAR(9) NOT NULL,
		"OPEN_SEAT_NUMBER" CHAR(15),
		"FIRST_NAME" CHAR(30),
		"LAST_NAME" CHAR(40),
		"EMAIL_ADDRESS" CHAR(60),
		"NOTES_ID" CHAR(60),
		"LBG_EMAIL" CHAR(60),
		"EMPLOYEE_TYPE" CHAR(20),
		"FM_CNUM" CHAR(9),
		"FM_MANAGER_FLAG" CHAR(3),
		"CTB_RTB" CHAR(10),
		"TT_BAU" CHAR(3),
		"LOB" CHAR(40),
		"ROLE_ON_THE_ACCOUNT" VARCHAR(120),
		"ROLE_TECHNOLOGY" CHAR(40),
		"START_DATE" DATE,
		"PROJECTED_END_DATE" DATE,
		"COUNTRY" VARCHAR(80),
		"IBM_BASE_LOCATION" VARCHAR(512),
		"LBG_LOCATION" VARCHAR(512),
		"OFFBOARDED_DATE" DATE,
		"PES_DATE_REQUESTED" DATE,
		"PES_REQUESTOR" CHAR(60),
		"PES_DATE_RESPONDED" DATE,
		"PES_STATUS_DETAILS" VARCHAR(200),
		"PES_STATUS" CHAR(50),
		"REVALIDATION_DATE_FIELD" DATE,
		"REVALIDATION_STATUS" CHAR(30),
		"CBN_DATE_FIELD" DATE,
		"CBN_STATUS" CHAR(10),
		"WORK_STREAM" CHAR(150),
		"CT_ID_REQUIRED" CHAR(3),
		"CT_ID" CHAR(10),
		"CIO_ALIGNMENT" CHAR(30),
		"PRE_BOARDED" CHAR(9),
		"SECURITY_EDUCATION" CHAR(3) NOT NULL DEFAULT 'No',
		"RF_FLAG" CHAR(1) NOT NULL DEFAULT '0',
		"RF_START" DATE,
		"RF_END" DATE,
		"PMO_STATUS" CHAR(15),
		"PES_DATE_EVIDENCE" DATE,
		"RSA_TOKEN" CHAR(9),
		"CALLSIGN_ID" CHAR(13),
		"PES_LEVEL" CHAR(10),
		"PES_RECHECK_DATE" DATE,
		"PES_CLEARED_DATE" DATE,
		"OLD_SQUAD_NUMBER" DECIMAL(5 , 0),
		"SQUAD_NUMBER" DECIMAL(5 , 0) DEFAULT 0,
		"SYS_START" TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN IMPLICITLY HIDDEN,
		"SYS_END" TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END IMPLICITLY HIDDEN,
		"CREATE_ID" TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID IMPLICITLY HIDDEN,
		PERIOD SYSTEM_TIME(sys_start,sys_end)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."PERSON_HISTORY"  like "VBAC_UT"."PERSON";
ALTER TABLE "VBAC_UT"."PERSON"
ADD VERSIONING USE HISTORY TABLE "VBAC_UT"."PERSON_HISTORY";

	
	
CREATE TABLE "VBAC_UT"."PERSON_ACCESS" (
		"CNUM" CHAR(9) NOT NULL,
		"SUBPLATFORM" CHAR(50) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;



CREATE TABLE "VBAC_UT"."PERSON_SUBPLATFORM" (
		"CNUM" CHAR(9) NOT NULL,
		"SUBPLATFORM" CHAR(50) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."PES_TRACKER" (
		"CNUM" CHAR(9) NOT NULL,
		"PASSPORT_FIRST_NAME" CHAR(200),
		"PASSPORT_SURNAME" CHAR(200),
		"JML" CHAR(20),
		"CONSENT" CHAR(10),
		"RIGHT_TO_WORK" CHAR(10),
		"PROOF_OF_ID" CHAR(10),
		"PROOF_OF_RESIDENCY" CHAR(10),
		"CREDIT_CHECK" CHAR(10),
		"FINANCIAL_SANCTIONS" CHAR(10),
		"CRIMINAL_RECORDS_CHECK" CHAR(10),
		"PROOF_OF_ACTIVITY" CHAR(10),
		"PROCESSING_STATUS" CHAR(20),
		"PROCESSING_STATUS_CHANGED" TIMESTAMP,
		"DATE_LAST_CHASED" DATE,
		"COMMENT" VARCHAR(4096),
		"PRIORITY" CHAR(10)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."REQUESTABLE_ASSET_LIST" (
		"ASSET_TITLE" CHAR(60) NOT NULL,
		"ASSET_PREREQUISITE" CHAR(60),
		"ASSET_PRIMARY_UID_TITLE" CHAR(60),
		"ASSET_SECONDARY_UID_TITLE" CHAR(60),
		"APPLICABLE_ONSHORE" CHAR(1),
		"APPLICABLE_OFFSHORE" CHAR(1),
		"BUSINESS_JUSTIFICATION_REQUIRED" CHAR(1),
		"REQUEST_BY_DEFAULT" CHAR(1),
		"RECORD_DATE_ISSUED_TO_IBM" CHAR(1),
		"RECORD_DATE_ISSUED_TO_USER" CHAR(1),
		"RECORD_DATE_RETURNED" CHAR(1),
		"LISTING_ENTRY_CREATED" TIMESTAMP,
		"LISTING_ENTRY_CREATED_BY" CHAR(120),
		"LISTING_ENTRY_REMOVED" TIMESTAMP,
		"LISTING_ENTRY_REMOVED_BY" CHAR(120),
		"PROMPT" VARCHAR(512),
		"ORDER_IT_TYPE" INTEGER NOT NULL DEFAULT 1,
		"ORDER_IT_REQUIRED" CHAR(1)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."RESOURCE_TYPE_MAPPING" (
		"RESOURCE_TYPE" CHAR(200) NOT NULL,
		"RESOURCE_NOTESID" CHAR(75) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_ACCESS" (
		"WORK_STREAM_ID" INTEGER,
		"SUB_PLATFORM" CHAR(50)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" (
		"COUNTRY_CODE" CHAR(3) NOT NULL,
		"COUNTRY_NAME" CHAR(40) NOT NULL,
		"PES_EMAIL" CHAR(75)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_DOMAINS" (
		"DOMAIN_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"DOMAIN" CHAR(100) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_GROUPS" (
		"GROUP_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"GROUP_NAME" CHAR(100) NOT NULL,
		"DOMAIN_NAME" CHAR(50) NOT NULL,
		"GROUP_DESCRIPTION" VARCHAR(255)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_GROUPS_FOR_ROLES" (
		"GROUP_ID" INTEGER NOT NULL,
		"ROLE_ID" INTEGER NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_LOCATIONS" (
		"COUNTRY" CHAR(60) NOT NULL,
		"CITY" CHAR(60) NOT NULL,
		"ADDRESS" CHAR(160) NOT NULL,
		"ONSHORE" CHAR(1),
		"CBC_IN_PLACE" CHAR(10)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_ROLES" (
		"ROLE_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"ROLE_TITLE" CHAR(100) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_SUBPLATFORM" (
		"WORK_STREAM_ID" INTEGER,
		"SUB_PLATFORM" CHAR(50)
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."STATIC_WORKSTREAMS" (
		"WORKSTREAM_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"WORKSTREAM" CHAR(100) NOT NULL,
		"ACCOUNT_ORGANISATION" CHAR(10) NOT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."TRACE" (
		"LOG_ENTRY" VARCHAR(32000) NOT NULL,
		"LASTUPDATER" CHAR(50) NOT NULL,
		"LASTUPDATED" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"CLASS" CHAR(50) DEFAULT NULL,
		"METHOD" CHAR(50) DEFAULT NULL,
		"PAGE" VARCHAR(200) DEFAULT NULL,
		"ELAPSED" DOUBLE DEFAULT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE TABLE "VBAC_UT"."TRACE_CONTROL" (
		"TRACE_CONTROL_TYPE" CHAR(20) DEFAULT NULL,
		"TRACE_CONTROL_VALUE" CHAR(40) DEFAULT NULL
	)
	in USERSPACE1
	DATA CAPTURE NONE;

CREATE INDEX "VBAC_UT"."IND_EMAIL"
	ON "VBAC_UT"."AUDIT"
	("EMAIL_ADDRESS"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "VBAC_UT"."PRIMARY_INDEX"
	ON "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES"
	("EMAIL_ADDRESS"		ASC,
	  "TITLE"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "VBAC_UT"."RECORD_TYPE"
	ON "VBAC_UT"."AUDIT"
	("TYPE"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE UNIQUE INDEX "VBAC_UT"."FEB_PRIMARY_INDEX"
	ON "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES"
	("EMAIL_ADDRESS"		ASC,
	  "TITLE"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE UNIQUE INDEX "VBAC_UT"."SD_SP_PK"
	ON "VBAC_UT"."STATIC_SUBPLATFORM"
	("WORK_STREAM_ID"		ASC,
	  "SUB_PLATFORM"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

ALTER TABLE "VBAC_UT"."AGILE_SQUAD_OLD" ADD CONSTRAINT "SQUAD_PK" PRIMARY KEY
	("SQUAD_NUMBER");

ALTER TABLE "VBAC_UT"."AGILE_TRIBE_OLD" ADD CONSTRAINT "TRIBE_PK" PRIMARY KEY
	("TRIBE_NUMBER");

ALTER TABLE "VBAC_UT"."ASSET_REQUESTS" ADD CONSTRAINT "PI_ASSET_REQUESTS_00001" PRIMARY KEY
	("REQUEST_REFERENCE");

ALTER TABLE "VBAC_UT"."DLP" ADD CONSTRAINT "DLPPrimary" PRIMARY KEY
	("HOSTNAME",
	 "CREATION_DATE");

ALTER TABLE "VBAC_UT"."PERSON" ADD CONSTRAINT "RF_BOOLH" CHECK ("RF_FLAG" in ('0','1'));

ALTER TABLE "VBAC_UT"."PERSON" ADD CONSTRAINT "SQL171213120532360" PRIMARY KEY
	("CNUM");

ALTER TABLE "VBAC_UT"."PES_TRACKER" ADD CONSTRAINT "TRACKER_KEY" PRIMARY KEY
	("CNUM");

ALTER TABLE "VBAC_UT"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");

ALTER TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "PI0001" PRIMARY KEY
	("COUNTRY_CODE");

ALTER TABLE "VBAC_UT"."STATIC_DOMAINS" ADD CONSTRAINT "Q_DOM_DOM00001_DOM00001_00001" PRIMARY KEY
	("DOMAIN_ID");

ALTER TABLE "VBAC_UT"."STATIC_GROUPS" ADD CONSTRAINT "Q_GROUP_GROU00001_00001" PRIMARY KEY
	("GROUP_ID");

ALTER TABLE "VBAC_UT"."STATIC_GROUPS_FOR_ROLES" ADD CONSTRAINT "Q_GROUP_GROU00002_00001" PRIMARY KEY
	("GROUP_ID",
	 "ROLE_ID");

ALTER TABLE "VBAC_UT"."STATIC_LOCATIONS" ADD CONSTRAINT "PI_LOCATION_00001" PRIMARY KEY
	("ADDRESS");

ALTER TABLE "VBAC_UT"."STATIC_ROLES" ADD CONSTRAINT "Q_REST_RESOU00001_RESOU00001_00001" PRIMARY KEY
	("ROLE_ID");

ALTER TABLE "VBAC_UT"."STATIC_WORKSTREAMS" ADD CONSTRAINT "Q_WORKS_WORK00001_00001" PRIMARY KEY
	("WORKSTREAM_ID");
	
	
	CREATE VIEW "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT", "CANCELLED_IN_ORDER_IT", "ORDER_IT_RESPONDED") AS
select request_reference as ref,
case when event = 'Created in vBAC' then occured else null end  as created_in_vbac,
case when event = 'Rejected in vBAC' then occured else null end as rejected_in_vbac,
case when event = 'Approved for Order IT' then occured else null end as approved_for_order_it,
case when event = 'Pre-req Created' then occured else null end as pre_req_created,
case when event = 'Pre-req Approved' then occured else null end as pre_req_approved,
case when event = 'Exported for Order IT' then occured else null end  as exported_for_order_it,
case when event = 'Raised in Order IT' then occured else null end  as raised_in_order_it,
case when event = 'Approved in Order IT' then occured else null end as approved_in_order_it,
case when event = 'Rejected in Order IT' then occured else null end as rejected_in_order_it,
case when event = 'Provisioned by Order IT' then occured else null end as provisioned_by_order_it,
case when event = 'Cancelled in Order IT' then occured else null end as cancelled_in_order_it,
case when event = 'orderIt Responded' then occured else null end as order_it_responded
from vbac_ut.asset_requests_events;

CREATE VIEW "VBAC_UT"."ASSET_REQUESTS_EVENTS_SUMMARY" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT", "CANCELLED_IN_ORDER_IT", "ORDER_IT_RESPONDED") AS
select ref, max(created_in_vbac) as created_in_vbac
, max(rejected_in_vbac) as rejected_in_vbac
, max(approved_for_order_it) as approved_for_order_it
, max(pre_req_created) as pre_req_created
, max(pre_req_approved) as pre_req_approved
, max(exported_for_order_it) as exported_for_order_it
, max(raised_in_order_it) as raised_in_order_it
, max(approved_in_order_it) as appproved_in_order_it
, max(rejected_in_order_it) as rejected_in_order_it
, max(provisioned_by_order_it) as provisioned_by_order_it
, max(cancelled_in_order_it) as cancelled_in_order_it
, max(order_it_responded) as order_it_responded
from vbac_ut.asset_requests_events_interim
group by ref;

CREATE VIEW "VBAC_UT"."ODC_ACCESS_LIVE" ("S_NO", "REQUEST_ID", "OWNER_CNUM_ID", "OWNER_NOTES_ID", "ACCESS_FOR", "SECURED_AREA_NAME", "REQUEST_TYPE", "START_DATE", "END_DATE", "REQUEST_STATUS", "WORK_FLOW_TYPE", "WORK_FLOW_STATUS", "CREATED_TMSP", "PEOPLE_MANAGERS_NOTES_ID", "SECURE_AREA_MANAGERS_NAME", "CREATED") AS
select * from  vbac_ut.ODC_ACCESS where REQUEST_STATUS = 'Active' and date(end_DATE) >= current date and date(start_date) <= current date;


CREATE VIEW "VBAC_UT"."ODC_ASSET_REMOVAL_LIVE" ("CNUM", "ASSET_SERIAL_NUMBER", "START_DATE", "END_DATE") AS
select * from  VBAC_UT.ODC_ASSET_REMOVAL where date(end_DATE) >= current date and date(start_date) <= current date;

GRANT CONTROL ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "ROBDANIEL";

GRANT DELETE ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

	
	
	
	

GRANT ALTER ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT CONTROL ON INDEX "VBAC_UT"."FEB_PRIMARY_INDEX" TO USER "ROBDANIEL";

GRANT CONTROL ON INDEX "VBAC_UT"."IND_EMAIL" TO USER "ROBDANIEL";

GRANT CONTROL ON INDEX "VBAC_UT"."PRIMARY_INDEX" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL";

GRANT DELETE ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."AUDIT_VC" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."DLP" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."FEB_TRAVEL_REQUEST_TEMPLATES" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."PERSON_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;


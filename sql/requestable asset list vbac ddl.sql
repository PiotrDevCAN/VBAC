--<ScriptOptions statementTerminator=";"/>

CREATE TABLE "VBAC"."REQUESTABLE_ASSET_LIST" (
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
		"ORDER_IT_TYPE" INTEGER NOT NULL DEFAULT 1,
		"ORDER_IT_REQUIRED" CHAR(1) 
	)
	DATA CAPTURE NONE;

ALTER TABLE "VBAC"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");
	

ALTER TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST"
 add column "ORDER_IT_REQUIRED" CHAR(1) 

   
CALL SYSPROC.ADMIN_CMD ('REORG TABLE  ROB_DEV.REQUESTABLE_ASSET_LIST');
DROP TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST";


CREATE TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST"(
		"ASSET_TITLE"  CHAR(60) NOT NULL,
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
		"LISTING_ENTRY_REMOVED_BY" CHAR(120)
		);

ALTER TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");

alter table "ROB_DEV"."REQUESTABLE_ASSET_LIST"
 alter column "ASSET_TITLE"
 	  set NOT NULL;

ALTER TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");


insert into "ROB_DEV"."REQUESTABLE_ASSET_LIST" ("ASSET_TITLE","APPLICABLE_ONSHORE",
			"APPLICABLE_OFFSHORE","LISTING_ENTRY_CREATED_BY","LISTING_ENTRY_CREATED")
values ('CT ID',1,1,'Rob.daniel@uk.ibm.com', current timestamp);


alter table "ROB_DEV"."REQUESTABLE_ASSET_LIST"
 add column "PROMPT"
 	  varchar(255) null ;






DROP TABLE "ROB_DEV"."ASSET_REQUESTS"

CREATE TABLE "ROB_DEV"."ASSET_REQUESTS" (
		"REQUEST_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"CNUM" CHAR(9) NOT NULL,
		"ASSET_TITLE" CHAR(60),
		"USER_LOCATION" VARCHAR(256),
		"PRIMARY_UID" CHAR(60),
		"SECONDARY_UID" CHAR(60),
		"DATE_ISSUED_TO_IBM" DATE,
		"DATE_ISSUED_TO_USER" DATE,
		"DATE_RETURNED" DATE,
		"BUSINESS_JUSTIFICATION" VARCHAR(256),
		"PRE_REQ_REQUEST" INTEGER,
		"REQUESTOR_EMAIL" CHAR(60),
		"REQUESTED" TIMESTAMP,
		"APPROVER_EMAIL" CHAR(60),
		"APPROVED" TIMESTAMP,
		"EDUCATION_CONFIRMED" CHAR(3),
		"STATUS" CHAR(30),
		"ORDERIT_VBAC_REF" CHAR(10),
		"ORDERIT_NUMBER" CHAR(20),
		"ORDERIT_STATUS" CHAR(50)
	)
	DATA CAPTURE NONE;

ALTER TABLE "ROB_DEV"."ASSET_REQUESTS" ADD CONSTRAINT "PI_ASSET_REQUESTS_00001" PRIMARY KEY
	("REQUEST_REFERENCE");

CREATE TABLE "ROB_DEV"."ORDER_IT"{
       "ORDER_IT_NUMBER" CHAR(7),
       "ORDER_IT_RAISED_DATE" DATE,
       "ORDER_IT_RAISED_BT" CHAR(60)
       );

ALTER TABLE "ROB_DEV"."ORDER_IT" ADD CONSTRAINT "PI_ORDER_IT_00001" PRIMARY KEY
	("ORDER_IT_NUMBER");


update ROB_DEV.REQUESTABLE_ASSET_LIST
set ASSET_PREREQUISITE = 'CT ID'
where ASSET_PREREQUISITE is null


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
		"PROMPT" VARCHAR(255),
		"ORDER_IT_TYPE" INTEGER
	)
	DATA CAPTURE NONE;

ALTER TABLE "VBAC"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");
	
	
ALTER TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST" 
 add column "ORDER_IT_TYPE" INTEGER not null default 1	


CREATE TABLE "ROB_DEV"."ASSET_REQUEST"(
		"ASSET_TITLE"  CHAR(60) NOT NULL,
		"CNUM" CHAR(9),
		"LOCATION" VARCHAR(240)
		"BUSINESS_JUSTIFICATION" VARCHAR(512),
		"APPROVING_FM" CHAR(60),
		"STATUS" CHAR(60),
		"ORDER_IT_NUMBER" CHAR(60),
		"ORDER_IT_STATUS" CHAR(60),



		);

ALTER TABLE "ROB_DEV"."ASSET_REQUEST" ADD CONSTRAINT "PI_ASSET_REQUEST_00001" PRIMARY KEY
	("ASSET_TITLE","CNUM");
	
ALTER TABLE "VBAC"."REQUESTABLE_ASSET_LIST"
 RENAME COLUMN "ORDER_IT_TYPE" to "ORDER_IT_TYPE"



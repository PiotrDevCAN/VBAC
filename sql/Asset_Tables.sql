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





DROP TABLE "ROB_DEV"."ASSET_REQUESTS"

CREATE TABLE "ROB_DEV"."ASSET_REQUESTS"(
       "REQUEST_IDENTIFIER" INTEGER GENERATED BY DEFAULT AS IDENTITY (
			START WITH 1 INCREMENT BY 1
			NO MINVALUE NO MAXVALUE
			NO CYCLE NO ORDER
			CACHE 20 ),
       "ASSET_TITLE" CHAR(60),
       "CNUM" CHAR(9),
       "ORDER_IT_NUMBER" CHAR(7),
       "ORDER_IT_STATUS" CHAR(30),
       "ORDER_IT_RESPONDED_DATE" DATE,
       "ASSET_PRIMARY_UID" CHAR(40),
       "ASSET_SECONDARY_UID" CHAR(40),
       "BUSINESS_JUSTIFICATION" VARCHAR(512),
       "DATE_ISSUED_TO_IBM" DATE,
	   "DATE_ISSUED_TO_USER" DATE,
	   "DATE_RETURNED" DATE
	   "REQUESTOR_CNUM" CHAR(60),
	   "REQUESTED_DATE" TIMESTAMP,
	   "APPROVER_CNUM" CHAR(60),
	   "APPROVED_DATE" TIMESTAMP
	   );

ALTER TABLE "ROB_DEV"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_ASSET_REQUESTS_00001" PRIMARY KEY
	("ASSET_TITLE");

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
		"LISTING_ENTRY_REMOVED_BY" CHAR(120)
	)
	DATA CAPTURE NONE;

ALTER TABLE "VBAC"."REQUESTABLE_ASSET_LIST" ADD CONSTRAINT "PI_REQUESTABLE_ASSET_LIST_00001" PRIMARY KEY
	("ASSET_TITLE");


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



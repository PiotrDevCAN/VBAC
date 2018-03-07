DROP TABLE "ROB_DEV"."ASSET_REQUESTS";


CREATE TABLE "ROB_DEV"."ASSET_REQUESTS"(
		"REQUEST_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"CNUM"  CHAR(9) NOT NULL,
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
		"REQUEST_BY_DEFAULT" CHAR(1),
		"APPROVER_EMAIL" CHAR(60),
		"APPROVED" TIMESTAMP,
		"EDUCATION_CONFIRMED" CHAR(3),
		"STATUS" CHAR(30),
		"ORDERIT_VARB_REF" CHAR(10),
		"ORDERIT_NUMBER" CHAR(20),
		"ORDERIT_STATUS" CHAR(50)
		);
		
ALTER TABLE "VBAC"."ASSET_REQUESTS" 	RENAME COLUMN ORDERIT_GROUP_REF to ORDERIT_VARB_REF;		
		

ALTER TABLE "ROB_DEV"."ASSET_REQUESTS" ADD CONSTRAINT "PI_ASSET_REQUESTS_00001" PRIMARY KEY
	("REQUEST_REFERENCE");
	
CREATE TABLE "VBAC"."ORDER_IT_VARB_TRACKER"(	
		"VARB" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"CREATED_DATE" TIMESTAMP default CURRENT TIMESTAMP,
		"CREATED_BY" CHAR(40) NOT NULL
		);
		

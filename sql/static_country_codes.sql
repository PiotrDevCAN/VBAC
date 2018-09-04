--<ScriptOptions statementTerminator=";"/>

DROP TABLE "ROB_DEV"."STATIC_COUNTRY_CODES";
DROP TABLE "VBAC_UT"."STATIC_COUNTRY_CODES";
DROP TABLE "VBAC"."STATIC_COUNTRY_CODES";

CREATE TABLE "ROB_DEV"."STATIC_COUNTRY_CODES" (
		"COUNTRY_CODE" CHAR(3) NOT NULL,
		"COUNTRY_NAME" CHAR(40) NOT NULL,
		"PES_EMAIL" CHAR(75) 
	)
	DATA CAPTURE NONE;


CREATE TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" (
		"COUNTRY_CODE" CHAR(3) NOT NULL,
		"COUNTRY_NAME" CHAR(40) NOT NULL
		"PES_EMAIL" CHAR(75) 
	)
	DATA CAPTURE NONE;

CREATE TABLE "VBAC"."STATIC_COUNTRY_CODES" (
		"COUNTRY_CODE" CHAR(3) NOT NULL,
		"COUNTRY_NAME" CHAR(40) NOT NULL
		"PES_EMAIL" CHAR(75) 
	)
	DATA CAPTURE NONE;


ALTER TABLE "ROB_DEV"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "PI0001" PRIMARY KEY
	("COUNTRY_CODE");
ALTER TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "PI0001" PRIMARY KEY
	("COUNTRY_CODE");
ALTER TABLE "VBAC"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "PI0001" PRIMARY KEY
	("COUNTRY_CODE");

	
ALTER TABLE "ROB_DEV"."STATIC_COUNTRY_CODES" 
ADD column "PES_EMAIL" CHAR(75) ;
	
	
ALTER TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" 
ADD column "PES_EMAIL" CHAR(75) ;

ALTER TABLE "VBAC"."STATIC_COUNTRY_CODES" 
ADD column "PES_EMAIL" CHAR(75) ;

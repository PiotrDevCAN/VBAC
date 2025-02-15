DROP TABLE "VBAC_UT"."PERSON";

CREATE TABLE "VBAC_UT"."PERSON" (
		"CNUM" char(9) not null,
		"OPEN_SEAT_NUMBER" CHAR(12) ,
		"FIRST_NAME" CHAR(30),
		"LAST_NAME" CHAR(40),
		"EMAIL_ADDRESS" CHAR(60),
		"NOTES_ID" CHAR(60),
		"LBG_EMAIL" CHAR(40),
		"EMPLOYEE_TYPE" CHAR(20),
		"FM_CNUM" char(9),
		"FM_MANAGER_FLAG" CHAR(3),
		"CTB_RTB" CHAR(10),
		"TT_BAU" CHAR(3),
		"LOB" CHAR(40),
		"ROLE_ON_THE_ACCOUNT" CHAR(60),
		"ROLE_TECHNOLOGY" CHAR(40),
		"START_DATE" DATE,
		"PROJECTED_END_DATE" DATE,
		"COUNTRY" CHAR(40),
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
		"CT_ID_ID_REQUIRED" CHAR(3),
		"CT_ID" CHAR(10),
		"CIO_ALIGNMENT" CHAR(30),
		"PRE_BOARDED" CHAR(9),
		"SECURITY_EDUCATION" CHAR(3) default 'No',
		"PES_DATE_EVIDENCE" DATE
	)
	DATA CAPTURE NONE;



alter table "VBAC_UT".PERSON
	 rename column "CONTRACTOR_ID_REQUIRED" to "CT_ID_REQUIRED"

alter table "VBAC_UT".PERSON
	 rename column "CONTRACTOR_ID" to "CT_ID"




ALTER TABLE "VBAC_UT"."PERSON" ADD CONSTRAINT "SQL171213120532360" PRIMARY KEY
	("CNUM");


alter table "ROB_DEV".PERSON
	 alter column "REVALIDATION_STATUS"
	  set data type  char(30) NULL;




alter table "VBAC_UT".PERSON
	 add column "PRE_BOARDED"
	   char(9);

alter table "VBAC".PERSON
	 add column "PRE_BOARDED"
	    char(9);

alter table "VBAC_UT".PERSON
	 add column "SECURITY_EDUCATION"
	   char(3) default 'No' NOT NULL;




alter table "ROB_DEV".STATIC_COUNTRY_CODES
	 alter column "COUNTRY_CODE"
	 SET DATA TYPE CHAR(40) NOT NULL;

	 alter table "VBAC_UT".STATIC_COUNTRY_CODES
	 alter column "COUNTRY_CODE"
	 SET DATA TYPE CHAR(40) NOT NULL;

	 alter table "VBAC".STATIC_COUNTRY_CODES
	 alter column "COUNTRY_CODE"
	 SET DATA TYPE CHAR(40) NOT NULL;



ALTER TABLE "ROB_DEV"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "SQL171210532360" PRIMARY KEY
	("COUNTRY_CODE");
ALTER TABLE "VBAC_UT"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "SQL171210532360" PRIMARY KEY
	("COUNTRY_CODE");
ALTER TABLE "VBAC"."STATIC_COUNTRY_CODES" ADD CONSTRAINT "SQL171210532360" PRIMARY KEY
	("COUNTRY_CODE");



	  UPDATE ROB_DEV.PERSON ct
       SET FM_CNUM = (SELECT cs.FM_CNUM
                    FROM VBAC_UT.TEMP_CNUM_2_FMCNUM  cs
                    WHERE cs.CNUM = ct.CNUM)
       WHERE ct.CNUM IN (SELECT cs.CNUM
                       FROM VBAC_UT.TEMP_CNUM_2_FMCNUM cs)


 update VBAC_UT.PERSON
  set FIRST_NAME ='Con', LAST_NAME='Kearns', EMAIL_ADDRESS = 'ckearns@vmware.com'
  where email_address = 'ckearns@cmware.com';
 update VBAC.PERSON
  set FIRST_NAME ='Con', LAST_NAME='Kearns', EMAIL_ADDRESS = 'ckearns@vmware.com'
  where email_address = 'ckearns@cmware.com';
   update ROB_DEV.PERSON
  set FIRST_NAME ='Con', LAST_NAME='Kearns', EMAIL_ADDRESS = 'ckearns@vmware.com'
  where email_address = 'ckearns@cmware.com';



  CREATE TABLE "ROB_DEV"."NEW_PEOPLE" (
		"CNUM" char(9) not null,
		"FIRST_NAME" CHAR(30),
		"LAST_NAME" CHAR(40),
		"EMAIL_ADDRESS" CHAR(60),
		"LBG_EMAIL" CHAR(40),
		"CTB_RTB" CHAR(10),
		"PROJECTED_END_DATE" DATE,
		"WORK_STREAM" CHAR(150),
		"OFFBOARDED_DATE" DATE,

	)
	DATA CAPTURE NONE;


ALTER TABLE "ROB_DEV"."NEW_PEOPLE" ADD CONSTRAINT "SQL171213120532360" PRIMARY KEY
	("CNUM");



alter table "VBAC".PERSON
rename column "CONTRACTOR_ID_REQUIRED" to "CT_ID_REQUIRED";

alter table "VBAC".PERSON
rename column "CONTRACTOR_ID" to "CT_ID";

alter table "VBAC".PERSON
add column "SECURITY_EDUCATION"
char(3) default 'No' NOT NULL;


alter table "VBAC".PERSON
rename column "CT_ID_REQUIRED" to "CONTRACTOR_ID_REQUIRED";

alter table "VBAC".PERSON
rename column "CT_ID" to "CONTRACTOR_ID";


alter table "VBAC".PERSON
	 add column "PES_DATE_EVIDENCE"
	   date;



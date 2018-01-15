DROP TABLE "VBAC_UT"."AUDIT";

CREATE TABLE "VBAC_UT"."AUDIT" (
		"TIMESTAMP" timestamp default current timestamp,
		"EMAIL_ADDRESS" CHAR(60) ,
		"DATA" CLOB(1024K)
		);



DROP TABLE "ROB_DEV"."AUDIT";

CREATE TABLE "ROB_DEV"."AUDIT" (
		"TIMESTAMP" timestamp default current timestamp,
		"EMAIL_ADDRESS" CHAR(60) ,
		"DATA" CLOB(1024K)
		);



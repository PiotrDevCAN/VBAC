DROP TABLE "VBAC-UT"."STATIC_GROUPS" ;

CREATE TABLE "VBAC_UT"."STATIC_GROUPS" (
		"GROUP_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"GROUP_NAME" CHAR(100) NOT NULL,
		"DOMAIN_NAME" CHAR(50) NOT NULL,
		"GROUP_DESCRIPTION" VARCHAR(255) NULL
	)
	DATA CAPTURE NONE;

ALTER TABLE "VBAC_UT"."STATIC_GROUPS" ADD CONSTRAINT "Q_GROUP_GROU00001_00001" PRIMARY KEY
	("GROUP_ID");
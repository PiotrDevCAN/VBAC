CREATE TABLE "ROB_DEV"."STATIC_GROUPS" (
		"GROUP_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"GROUP" CHAR(100) NOT NULL
	)
	DATA CAPTURE NONE;

ALTER TABLE "ROB_DEV"."STATIC_GROUPS" ADD CONSTRAINT "Q_GROUP_GROU00001_00001" PRIMARY KEY
	("GROUP_ID");
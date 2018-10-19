

DROP TABLE ROB_DEV.PES_TRACKER;

CREATE TABLE ROB_DEV.PES_TRACKER ( 
CNUM CHAR(9) NOT NULL WITH DEFAULT 'cnum'
, Passport_First_Name CHAR(200)
, Passport_Surname CHAR(200) 
, JML CHAR(20)
,  Consent CHAR(10)
, Right_to_work CHAR(10)
, Proof_of_Id CHAR(10)
, Proof_of_Residency CHAR(10)
, Credit_Check CHAR(10)
, Financial_Sanctions CHAR(10)
, Criminal_Records_Check CHAR(10)
, Proof_of_Activity CHAR(10)
, Processing_Status CHAR(20)
, Processing_Status_Changed TIMESTAMP(6)
, Date_Last_Chased DATE
, Comment CLOB(1024));

--<ScriptOptions statementTerminator=";"/>

DROP VIEW "ROB_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY";

CREATE VIEW "ROB_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "AWAITING_IAM", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT","ORDER_IT_RESPONDED") AS
select ref, max(created_in_vbac) as created_in_vbac
, max(rejected_in_vbac) as rejected_in_vbac
, max(awaiting_iam) as awaiting_iam
, max(approved_for_order_it) as approved_for_order_it
, max(pre_req_created) as pre_req_created
, max(pre_req_approved) as pre_req_approved
, max(exported_for_order_it) as exported_for_order_it
, max(raised_in_order_it) as raised_in_order_it
, max(approved_in_order_it) as appproved_in_order_it
, max(rejected_in_order_it) as rejected_in_order_it
, max(provisioned_by_order_it) as provisioned_by_order_it
, max(order_it_responded) as order_it_responded
from rob_dev.asset_requests_events_interim
group by ref;

GRANT CONTROL ON TABLE "ROB_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "BLUADMIN";

GRANT SELECT ON TABLE "ROB_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "BLUADMIN" WITH GRANT OPTION;


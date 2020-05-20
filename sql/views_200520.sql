--<ScriptOptions statementTerminator=";"/>

CREATE VIEW "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT", "CANCELLED_IN_ORDER_IT", "ORDER_IT_RESPONDED") AS
select request_reference as ref,
case when event = 'Created in vBAC' then occured else null end  as created_in_vbac,
case when event = 'Rejected in vBAC' then occured else null end as rejected_in_vbac,
case when event = 'Approved for Order IT' then occured else null end as approved_for_order_it,
case when event = 'Pre-req Created' then occured else null end as pre_req_created,
case when event = 'Pre-req Approved' then occured else null end as pre_req_approved,
case when event = 'Exported for Order IT' then occured else null end  as exported_for_order_it,
case when event = 'Raised in Order IT' then occured else null end  as raised_in_order_it,
case when event = 'Approved in Order IT' then occured else null end as approved_in_order_it,
case when event = 'Rejected in Order IT' then occured else null end as rejected_in_order_it,
case when event = 'Provisioned by Order IT' then occured else null end as provisioned_by_order_it,
case when event = 'Cancelled in Order IT' then occured else null end as cancelled_in_order_it,
case when event = 'orderIt Responded' then occured else null end as order_it_responded
from vbac.asset_requests_events;

CREATE VIEW "VBAC_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT", "CANCELLED_IN_ORDER_IT", "ORDER_IT_RESPONDED") AS
select ref, max(created_in_vbac) as created_in_vbac
, max(rejected_in_vbac) as rejected_in_vbac
, max(approved_for_order_it) as approved_for_order_it
, max(pre_req_created) as pre_req_created
, max(pre_req_approved) as pre_req_approved
, max(exported_for_order_it) as exported_for_order_it
, max(raised_in_order_it) as raised_in_order_it
, max(approved_in_order_it) as appproved_in_order_it
, max(rejected_in_order_it) as rejected_in_order_it
, max(provisioned_by_order_it) as provisioned_by_order_it
, max(cancelled_in_order_it) as cancelled_in_order_it
, max(order_it_responded) as order_it_responded
from vbac.asset_requests_events_interim
group by ref;

CREATE VIEW "VBAC_DEV"."ODC_ACCESS_LIVE" ("S_NO", "REQUEST_ID", "OWNER_CNUM_ID", "OWNER_NOTES_ID", "ACCESS_FOR", "SECURED_AREA_NAME", "REQUEST_TYPE", "START_DATE", "END_DATE", "REQUEST_STATUS", "WORK_FLOW_TYPE", "WORK_FLOW_STATUS", "CREATED_TMSP", "PEOPLE_MANAGERS_NOTES_ID", "SECURE_AREA_MANAGERS_NAME", "CREATED") AS
select * from  vbac.ODC_ACCESS where REQUEST_STATUS = 'Active' and date(end_DATE) >= current date and date(start_date) <= current date;

CREATE VIEW "VBAC_DEV"."ODC_ASSET_REMOVAL_LIVE" ("CNUM", "ASSET_SERIAL_NUMBER", "START_DATE", "END_DATE", "SYSTEM_START_TIME", "SYSTEM_END_TIME", "TRANS_ID") AS
select * from  VBAC.ODC_ASSET_REMOVAL where date(end_DATE) >= current date and date(start_date) <= current date;

GRANT CONTROL ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "ROBDANIEL";

GRANT DELETE ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_SUMMARY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_DEV"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "ROBDANIEL" WITH GRANT OPTION;


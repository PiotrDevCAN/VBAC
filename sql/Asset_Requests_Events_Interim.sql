--<ScriptOptions statementTerminator=";"/>

DROP VIEW "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM";

CREATE VIEW "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" ("REF", "CREATED_IN_VBAC", "REJECTED_IN_VBAC", "APPROVED_FOR_ORDER_IT", "PRE_REQ_CREATED", "PRE_REQ_APPROVED", "EXPORTED_FOR_ORDER_IT", "RAISED_IN_ORDER_IT", "APPROVED_IN_ORDER_IT", "REJECTED_IN_ORDER_IT", "PROVISIONED_BY_ORDER_IT","ORDER_IT_RESPONDED") AS
select request_reference as ref,
case when event = 'Created in vBAC' then occured else null end  as created_in_vbac
,case when event = 'Rejected in vBAC' then occured else null end as rejected_in_vbac
,case when event = 'Approved for Order IT' then occured else null end as approved_for_order_it
,case when event = 'Pre-req Created' then occured else null end as pre_req_created
,case when event = 'Pre-req Approved' then occured else null end as pre_req_approved
,case when event = 'Exported for Order IT' then occured else null end  as exported_for_order_it
,case when event = 'Raised in Order IT' then occured else null end  as raised_in_order_it
,case when event = 'Approved in Order IT' then occured else null end as approved_in_order_it
,case when event = 'Rejected in Order IT' then occured else null end as rejected_in_order_it
,case when event = 'Provisioned by Order IT' then occured else null end as provisioned_by_order_it
,case when event = 'orderIt Responded ' then occured else null end as order_it_responded
from rob_dev.asset_requests_events;

GRANT CONTROL ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "BLUADMIN";

GRANT DELETE ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "BLUADMIN" WITH GRANT OPTION;

GRANT INSERT ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "BLUADMIN" WITH GRANT OPTION;

GRANT SELECT ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "BLUADMIN" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "VBAC_UT"."ASSET_REQUESTS_EVENTS_INTERIM" TO USER "BLUADMIN" WITH GRANT OPTION;


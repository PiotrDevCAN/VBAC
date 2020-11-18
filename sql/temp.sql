select DATA
from VBAC.AUDIT
where date(timestamp) = current date and email_address='Scheduled Job'
and data like 'PES Status set for%'
order by timestamp desc;

fetch first 10 rows only
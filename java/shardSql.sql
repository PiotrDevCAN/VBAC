select log_entry, lastupdated, elapsed, thread_id from SHARD.TRACE where thread_id = 'AGY15'order by thread_id, lastupdated desc

select distinct received_ts, filename  from SHARd.RECEPTION order by 1 desc


select * from DPULSE.PULSE_FORMS
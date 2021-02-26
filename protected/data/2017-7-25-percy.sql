alter table swo_email_queue add column request_dt datetime default now() after id;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatus //
CREATE FUNCTION RequestStatus(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(2)
BEGIN
	DECLARE status char(2);
	SET status = (
		SELECT d.code
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		order by a.id desc
		LIMIT 1
	);
	RETURN status;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusEx //
CREATE FUNCTION RequestStatusEx(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(2)
BEGIN
	DECLARE status char(2);
	
	SET status = (
		SELECT IF(d.code<>'ED',d.code,f.code) as status
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e, wf_state f 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and a.current_state = e.new_state
		and e.old_state = f.id
		order by a.id desc, e.id desc 
		LIMIT 1
	);
	RETURN status;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusDesc //
CREATE FUNCTION RequestStatusDesc(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(255)
BEGIN
	DECLARE status_desc char(255);
	
	SET status_desc = (
		SELECT IF(d.code<>'ED',d.name,CONCAT(f.name,' (',d.name,')')) as status_desc
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e, wf_state f 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and a.current_state = e.new_state
		and e.old_state = f.id
		order by a.id desc, e.id desc 
		LIMIT 1
	);
	RETURN status_desc;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusDate //
CREATE FUNCTION RequestStatusDate(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime, p_code char(2)) RETURNS datetime
BEGIN
	DECLARE status_dt datetime;
	SET status_dt = (
		SELECT e.lcd
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and e.new_state = d.id
		and d.code = p_code 
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		order by a.id desc, e.id desc 
		LIMIT 1
	);
	RETURN status_dt;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS ActionPerson //
CREATE FUNCTION ActionPerson(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime, p_code char(2)) RETURNS varchar(30)
BEGIN
	DECLARE action_user varchar(30);
	SET action_user = (
		SELECT e.username
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_resp_user e
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and e.current_state = d.id
		and d.code = p_code 
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and e.status = 'C'
		order by a.id desc, e.id desc 
		LIMIT 1
	);
	RETURN action_user;
END //
DELIMITER ;

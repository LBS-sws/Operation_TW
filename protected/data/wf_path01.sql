alter table wf_request_resp_user add column remarks varchar(5000) after action_id;

INSERT INTO wf_process(id, code, name) values(2, 'OPRPT', 'Operation Report Process');

INSERT INTO wf_process_version(id, process_id, start_dt, end_dt) values(3, 2, '2016-01-01', '2099-12-31');

INSERT INTO wf_action(id, proc_ver_id, code, name) values
(101,3,'APPROVE','总部批准报表'),
(102,3,'DENY','总部拒绝报表'),
(103,3,'SUBMIT','提交报表'),
(104,3,'RESUBMIT','再提交报表'),
(105,3,'HDAPPROVE','主管批准报表'),
(106,3,'HDDENY','主管拒绝报表')
;

INSERT INTO wf_task(id, proc_ver_id, name, function_call, param) values
(101,3,'Send Email','sendEmail',''),
(102,3,'Status=Pending for HQ Approval','transit','PA'),
(103,3,'Status=HQ Approved','transit','A'),
(104,3,'Status=HQ Denied','transit','D'),
(105,3,'Route to Approver','routeToApprover',''),
(106,3,'Route to Requestor','routeToRequestor',''),
(107,3,'Status=End','transit','ED'),
(108,3,'Clear All Pending','clearAllPending',''),
(109,3,'Status=Pending for Resubmit','transit','PS'),
(110,3,'Route to Manager','routeToManager',''),
(111,3,'Status=Pending for Approval','transit','PH'),
(112,3,'Status=Manager Approved','transit','AH'),
(113,3,'Status=Manager Denied','transit','DH')
;

INSERT INTO wf_action_task(action_id, task_id, seq_no) values
(101,103,1),
(101,101,2),
(101,108,3),
(101,107,4),
(102,104,1),
(102,101,2),
(102,108,3),
(102,109,4),
(102,106,5),
(103,111,1),
(103,110,2),
(103,101,3),
(104,108,1),
(104,111,2),
(104,110,3),
(104,101,4),
(105,112,1),
(105,108,2),
(105,101,3),
(105,102,4),
(105,105,5),
(105,101,6),
(106,113,1),
(106,108,2),
(106,101,3),
(106,109,4),
(106,106,5)
;

INSERT INTO wf_state(id, proc_ver_id, code, name) VALUES
(101,3,'ST','开始'),
(102,3,'ED','结束'),
(103,3,'PA','有待总部审核'),
(104,3,'PS','有待再提交'),
(105,3,'A','总部已批准'),
(106,3,'D','总部已拒绝'),
(107,3,'PH','有待主管审核'),
(108,3,'AH','主管已批准'),
(109,3,'DH','主管已拒绝')
;

INSERT INTO wf_transition(proc_ver_id, current_state, next_state) VALUES
(3,101,107),
(3,107,108),
(3,107,109),
(3,108,103),
(3,109,104),
(3,104,107),
(3,103,105),
(3,103,106),
(3,105,102),
(3,106,104)
;


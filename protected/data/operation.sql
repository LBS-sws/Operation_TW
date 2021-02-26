CREATE DATABASE operation CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON operation.* TO 'swuser'@'localhost' IDENTIFIED BY 'swisher168';

use operation;

DROP TABLE IF EXISTS opr_monthly_hdr;
CREATE TABLE opr_monthly_hdr (
	id int unsigned auto_increment NOT NULL primary key,
	city char(5) NOT NULL,
	year_no smallint unsigned NOT NULL,
	month_no tinyint unsigned NOT NULL,
	status char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into opr_monthly_hdr(id, city, year_no, month_no, status, lcu, luu) values
(1,'SZ',2017,1,'Y','admin','admin'),
(2,'SZ',2017,2,'Y','admin','admin'),
(3,'SZ',2017,3,'Y','admin','admin'),
(4,'SH',2017,1,'Y','admin','admin'),
(5,'SH',2017,2,'Y','admin','admin'),
(6,'SH',2017,3,'Y','admin','admin')
;

DROP TABLE IF EXISTS opr_monthly_dtl;
CREATE TABLE opr_monthly_dtl (
	id int unsigned auto_increment NOT NULL primary key,
	hdr_id int unsigned NOT NULL,
	data_field char(5) NOT NULL,
	data_value varchar(100),
	manual_input char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into opr_monthly_dtl(hdr_id, data_field, data_value, manual_input, lcu, luu) values
(1,'10001','1000','Y','admin','admin'),
(1,'10002','2000','Y','admin','admin'),
(1,'10003','3000','Y','admin','admin'),
(1,'10004','4000','Y','admin','admin'),
(1,'10005','5000','Y','admin','admin'),
(1,'10006','6000','Y','admin','admin'),
(2,'10001','1000','Y','admin','admin'),
(2,'10002','3000','Y','admin','admin'),
(2,'10003','5000','Y','admin','admin'),
(2,'10004','6000','Y','admin','admin'),
(2,'10005','7000','Y','admin','admin'),
(2,'10006','9000','Y','admin','admin'),
(3,'10001','2000','Y','admin','admin'),
(3,'10002','3000','Y','admin','admin'),
(3,'10003','5000','Y','admin','admin'),
(3,'10004','6000','Y','admin','admin'),
(3,'10005','8000','Y','admin','admin'),
(3,'10006','9000','Y','admin','admin'),
(4,'10001','2000','Y','admin','admin'),
(4,'10002','4000','Y','admin','admin'),
(4,'10003','6000','Y','admin','admin'),
(4,'10004','8000','Y','admin','admin'),
(4,'10005','10000','Y','admin','admin'),
(4,'10006','12000','Y','admin','admin'),
(5,'10001','1000','Y','admin','admin'),
(5,'10002','2000','Y','admin','admin'),
(5,'10003','3000','Y','admin','admin'),
(5,'10004','4000','Y','admin','admin'),
(5,'10005','5000','Y','admin','admin'),
(5,'10006','6000','Y','admin','admin'),
(6,'10001','1000','Y','admin','admin'),
(6,'10002','3000','Y','admin','admin'),
(6,'10003','5000','Y','admin','admin'),
(6,'10004','6000','Y','admin','admin'),
(6,'10005','7000','Y','admin','admin'),
(6,'10006','9000','Y','admin','admin')
;

DROP TABLE IF EXISTS opr_monthly_field;
CREATE TABLE `opr_monthly_field` (
  `code` char(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `upd_type` char(1) NOT NULL DEFAULT 'M',
  `field_type` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) DEFAULT 'Y',
  `function_name` varchar(200) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `opr_monthly_field` VALUES ('10001','清洁收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10002','灭虫收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10003','杂项及其他销售收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10004','飘盈香收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10005','甲醛收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10006','纸品销售收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10007','服务费收入合共','Y','N','Y',NULL,'admin','admin','2017-05-31 01:59:48','2017-05-31 02:00:34'),('10008','服务专利费','Y','N','Y',NULL,'admin','admin','2017-05-31 01:59:48','2017-05-31 02:00:37'),('10009','纸品专利费','Y','N','Y',NULL,'admin','admin','2017-05-31 02:01:46','2017-05-31 02:01:46'),('10010','专利费合共','Y','N','Y',NULL,'admin','admin','2017-05-31 02:01:46','2017-05-31 02:01:46');

DROP TABLE IF EXISTS opr_queue;
CREATE TABLE opr_queue (
	id int unsigned NOT NULL auto_increment primary key,
	rpt_desc varchar(250) NOT NULL,
	req_dt datetime,
	fin_dt datetime,
	username varchar(30) NOT NULL,
	status char(1) NOT NULL,
	rpt_type varchar(10) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	rpt_content longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS opr_queue_param;
CREATE TABLE opr_queue_param (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	param_field varchar(50) NOT NULL,
	param_value varchar(500),
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS opr_queue_user;
CREATE TABLE opr_queue_user (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	username varchar(30) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


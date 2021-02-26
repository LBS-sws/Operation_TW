create table opr_monthly_rate(
  city char(5) NOT NULL,
  rate varchar(1000) NOT NULL,
  lcu varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  luu varchar(30) DEFAULT NULL,
  lcd timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  lud timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
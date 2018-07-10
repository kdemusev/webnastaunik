CREATE TABLE IF NOT EXISTS bsagreement (
  id INT AUTO_INCREMENT,
  school_id INT,
  bsanumber VARCHAR(16),
  bsaname VARCHAR(255),
  bsafrom FLOAT,
  bsato FLOAT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(school_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bsgroups (
  id INT AUTO_INCREMENT,
  school_id INT,
  bsgname VARCHAR(255),
  bsgpriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(school_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bsemployes (
  id INT AUTO_INCREMENT,
  bsgroup_id INT,
  bsename VARCHAR(255),
  bseplace VARCHAR(255),
  user_id INT,
  bsepriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(bsgroup_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bsperiods (
  id INT AUTO_INCREMENT,
  school_id INT,
  bspdate INT,
  bspbasevalue DECIMAL(10,2),
  bsppursebonus DECIMAL(13,2),
  bsppurseeconomy DECIMAL(13,2),
  bsppurseextra DECIMAL(13,2),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(school_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bspaybonus (
  id INT AUTO_INCREMENT,
  bsemployee_id INT,
  bsperiod_id INT,
  bsagreement_id INT,
  bsvalue DECIMAL(4,2),
  PRIMARY KEY(id),
  INDEX(bsemployee_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bspayeconomy (
  id INT AUTO_INCREMENT,
  bsemployee_id INT,
  bsperiod_id INT,
  bsagreement_id INT,
  bsvalue DECIMAL(4,2),
  PRIMARY KEY(id),
  INDEX(bsemployee_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bspayextra (
  id INT AUTO_INCREMENT,
  bsemployee_id INT,
  bsperiod_id INT,
  bspesum DECIMAL(13,2),
  bspepercent INT,
  bsreason VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(bsemployee_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bspays (
  id INT AUTO_INCREMENT,
  bsemployee_id INT,
  bsperiod_id INT,
  bspbonus DECIMAL(13,2),
  bspeconomy DECIMAL(13,2),
  PRIMARY KEY(id),
  INDEX(bsemployee_id)
) DEFAULT CHARACTER SET utf8;

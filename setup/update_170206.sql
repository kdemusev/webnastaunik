ALTER TABLE subjects ADD column sbiselective TINYINT DEFAULT 0;

DROP TABLE ktp_typical_info;
DROP TABLE ktp_typical;

CREATE TABLE IF NOT EXISTS ktp_typical_info (
  id INT AUTO_INCREMENT,
  teacher_id INT,
  kttiname VARCHAR(255),
  kttiform INT,
  kttidesc TEXT,
  PRIMARY KEY(id),
  INDEX(id),
  INdEX(teacher_id)
)  DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS ktp_typical (
  id INT AUTO_INCREMENT,
  ktp_typical_info_id INT,
  kttnumber INT,
  ktttopic VARCHAR(255),
  kttrequirements TEXT,
  kttaim TEXT,
  kttcolor TINYINT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(ktp_typical_info_id)
)  DEFAULT CHARACTER SET utf8;

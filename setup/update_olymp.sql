DROP table olymp_forms;
drop table olymp_pupils;
drop table olymp_teachers;
drop table olymp_years;
drop table olymp_subjects;
drop table olymp_schools;
drop table olymp;

CREATE TABLE IF NOT EXISTS olymp_forms(
  id INT AUTO_INCREMENT,
  district_id INT,
  ofname VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp_pupils(
    id INT AUTO_INCREMENT,
    opname VARCHAR(255),
    olymp_school_id INT,
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(olymp_school_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp_teachers(
    id INT AUTO_INCREMENT,
    otname VARCHAR(255),
    olymp_school_id INT,
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(olymp_school_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp_years(
    id INT AUTO_INCREMENT,
    district_id INT,
    oyname VARCHAR(255),
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp_subjects(
    id INT AUTO_INCREMENT,
    district_id INT,
    osname VARCHAR(255),
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp_schools(
  id INT AUTO_INCREMENT,
  district_id INT,
  oscname VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS olymp(
    id INT AUTO_INCREMENT,
    district_id INT,
    olymptype TINYINT,
    olymp_pupil_id INT,
    olymp_form_id INT,
    olymp_year_id INT,
    olymp_subject_id INT,
    olymp_teacher_id INT,
    olmaxpoints INT,
    olpoints FLOAT,
    olpercent FLOAT,
    olrating INT,
    oldiploma TINYINT DEFAULT 0,
    olabsend TINYINT,
    olnopassport TINYINT,
    olnoinapplication TINYINT,
    olisregion TINYINT,
    olregrating INT,
    olregdiploma TINYINT,
    olregabsend TINYINT,
    olisrepublic TINYINT,
    olreprating INT,
    olrepdiploma TINYINT,
    olrepabsend TINYINT,
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(district_id),
    INDEX(olymp_form_id),
    INDEX(olymp_year_id),
    INDEX(olymp_pupil_id),
    INDEX(olymp_subject_id),
    INDEX(olymp_teacher_id)
) DEFAULT CHARACTER SET utf8;

DROP TABLE concurs_types;
DROP TABLE concurs_pupils;
DROP TABLE concurs;
DROP TABLE concurs_sections;

CREATE TABLE IF NOT EXISTS concurs_types(
  id INT AUTO_INCREMENT,
  district_id INT,
  ctname VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS concurs_sections(
  id INT AUTO_INCREMENT,
  district_id INT,
  concurs_type_id INT,
  csname VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(district_id),
  INDEX(concurs_type_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS concurs_pupils(
  id INT AUTO_INCREMENT,
  olymp_pupil_id INT,
  olymp_form_id INT,
  concurs_id INT,
  PRIMARY KEY(id),
  INDEX(olymp_pupil_id),
  INDEX(olymp_form_id),
  INDEX(concurs_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS concurs(
    id INT AUTO_INCREMENT,
    district_id INT,
    concurstype TINYINT,
    olymp_year_id INT,
    concurs_section_id INT,
    olymp_teacher_id INT,
    olymp_subject_id INT,
    cnname VARCHAR(255),
    ctdiploma TINYINT DEFAULT 0,
    ctismore TINYINT,
    PRIMARY KEY(id),
    INDEX(id),
    INDEX(district_id),
    INDEX(olymp_year_id),
    INDEX(concurs_section_id),
    INDEX(olymp_subject_id),
    INDEX(olymp_teacher_id)
) DEFAULT CHARACTER SET utf8;

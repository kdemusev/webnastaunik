CREATE TABLE IF NOT EXISTS regions(
    id INT AUTO_INCREMENT,
    rgname VARCHAR(255),
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS districts(
    id INT AUTO_INCREMENT,
    dtname VARCHAR(255),
    region_id INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS schools(
    id INT AUTO_INCREMENT,
    scname VARCHAR(255),
    sctype TINYINT,
    district_id INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT,
    tcname VARCHAR(255),
    school_id INT,
    tcpriority INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT,
    usname VARCHAR(255),
    uslogin VARCHAR(255),
    uspassword VARCHAR(255),
    school_id INT,
    usemail VARCHAR(255),
    usphone VARCHAR(32),
    ustype TINYINT,
    usrights TINYINT NOT NULL DEFAULT 0,
    usplace VARCHAR(255),
    teacher_id INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS specializations (
    id INT AUTO_INCREMENT,
    spname VARCHAR(255),
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS usertospec (
    id INT AUTO_INCREMENT,
    user_id INT,
    specialization_id INT,
    PRIMARY KEY(id),
    INDEX(user_id),
    INDEX(specialization_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS ktpdayoff (
  id INT AUTO_INCREMENT,
  kdodate INT,
  kdotype SMALLINT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS ktptransposition (
  id INT AUTO_INCREMENT,
  ktdatefrom INT,
  ktdateto INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS rooms (
  id INT AUTO_INCREMENT,
  rmnumber VARCHAR(16),
  rmname VARCHAR(255),
  school_id INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS forms (
  id INT AUTO_INCREMENT,
  fmname VARCHAR(16),
  school_id INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT,
  form_id INT,
  sbname VARCHAR(64),
  sbhours TINYINT,
  sbrating TINYINT,
  teacher_id INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS pupils (
  id INT AUTO_INCREMENT,
  ppname VARCHAR(255),
  ppsurname VARCHAR(255),
  ppsex TINYINT,
  ppbirth INT,
  ppmother VARCHAR(255),
  ppfather VARCHAR(255),
  ppmotherplace VARCHAR(255),
  ppfatherplace VARCHAR(255),
  ppphone VARCHAR(64),
  ppmotherphone VARCHAR(64),
  ppfatherphone VARCHAR(64),
  pphomephone VARCHAR(64),
  ppaddress VARCHAR(255),
  pphealth TINYINT,
  ppphyz TINYINT,
  ppnotes TEXT,
  pppriority INT,
  form_id INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS timetable (
  id INT AUTO_INCREMENT,
  form_id INT,
  ttday INT,
  ttnumber INT,
  ttstart INT,
  ttend INT,
  subject_id INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS ktp (
    id INT AUTO_INCREMENT,
    subject_id INT,
    ktnum SMALLINT,
    kttopic TEXT,
    kthomework TEXT,
    ktdate INT,
    PRIMARY KEY(id),
    INDEX (id),
    INDEX (subject_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS journal_tmp (
  id INT AUTO_INCREMENT,
  subject_id INT,
  pupil_id INT,
  jrdate INT,
  jrmark INT,
  PRIMARY KEY(id),
  INDEX (id),
  INDEX (subject_id),
  INDEX (jrdate)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT,
  user_id INT,
  tsname VARCHAR(255),
  tsnotes TEXT,
  tsdate INT,
  tsweek TINYINT,
  tsremind INT,
  tspriority INT,
  tsdone TINYINT DEFAULT 0,
  tscolor TINYINT DEFAULT 0,
  PRIMARY KEY(id),
  INDEX (id),
  INDEX (user_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS fmsections (
  id INT AUTO_INCREMENT,
  fmscname VARCHAR(255),
  fmscdesc TEXT,
  fmscpriority INT,
  PRIMARY KEY(id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS fmtopics (
  id INT AUTO_INCREMENT,
  fmsection_id INT,
  fmtpname VARCHAR(255),
  fmtpdesc TEXT,
  fmtpposts INT DEFAULT 0,
  fmtpviews INT DEFAULT 0,
  fmtplast INT DEFAULT 0,
  fmtptime INT,
  fmtpanonym TINYINT,
  user_id INT,
  fmtppinned TINYINT DEFAULT 0,
  PRIMARY KEY(id),
  INDEX (id),
  INDEX (fmsection_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS fmposts (
  id INT AUTO_INCREMENT,
  fmtopic_id INT,
  fmtext TEXT,
  user_id INT,
  fmpttime INT DEFAULT 0,
  fmtpanonym TINYINT,
  fmptrating INT DEFAULT 0,
  PRIMARY KEY (id),
  INDEX (id),
  INDEX (fmtopic_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS forumfiles (
  id INT AUTO_INCREMENT,
  fmpost_id INT,
  fmflname VARCHAR(255),
  fmflsource VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(fmpost_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS forumtopicfiles (
  id INT AUTO_INCREMENT,
  fmtopic_id INT,
  fmflname VARCHAR(255),
  fmflsource VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(fmtopic_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinars (
  id INT AUTO_INCREMENT,
  user_id INT,
  district_id INT DEFAULT 0,
  wbname VARCHAR(255),
  wbtype TINYINT,
  wbstart INT,
  wbend INT,
  wbdesc TEXT,
  wbviews INT DEFAULT 0,
  wbposts INT DEFAULT 0,
  PRIMARY KEY(id),
  INDEX (id),
  INDEX (district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinarspecs (
  id INT AUTO_INCREMENT,
  webinar_id INT,
  specialization_id INT,
  PRIMARY KEY(id),
  INDEX(webinar_id),
  INDEX(specialization_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinarsections (
  id INT AUTO_INCREMENT,
  webinar_id INT,
  wbscname VARCHAR(255),
  wbscdesc TEXT,
  wbscpriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(webinar_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinarmembers (
  id INT AUTO_INCREMENT,
  webinar_id INT,
  wbmember_id INT,
  wbmemberinfo VARCHAR(255),
  wbmbpriority INT,
  wbmbtopic VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(webinar_id),
  INDEX(wbmember_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinarmessages (
  id INT AUTO_INCREMENT,
  wbsection_id INT,
  user_id INT,
  wbmstext TEXT,
  wbmstime INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(wbsection_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS webinarfiles (
  id INT AUTO_INCREMENT,
  wbmessage_id INT,
  wbflname VARCHAR(255),
  wbflsource VARCHAR(255),
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(wbmessage_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT,
  nsname VARCHAR(255),
  district_id INT DEFAULT 0,
  nstype TINYINT,
  nstext TEXT,
  nstime INT,
  user_id INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(district_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mlsubjects (
	id INT AUTO_INCREMENT,
	msname VARCHAR(128),
  tcht_id INT,
  mspriority INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

/* mgtype = 0 for quad and mgtype = 1 for year */

CREATE TABLE IF NOT EXISTS mlgroups (
	id INT AUTO_INCREMENT,
	mgname VARCHAR(128),
	mgtype INT,
  mgpriority INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mlist (
	id INT AUTO_INCREMENT,
	pupil_id INT,
	mlmark INT,
	mlinfo_id INT,
  PRIMARY KEY (id),
  INDEX (id),
	INDEX (mlinfo_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mlinfo (
	id INT AUTO_INCREMENT,
	teacher_id INT,
	mlhours INT,
	mlgroup_id INT,
	mlsubject_id INT,
  PRIMARY KEY(id),
  INDEX (id),
	INDEX (mlgroup_id),
	INDEX (mlsubject_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT,
  pupil_id INT,
  atmonth TINYINT,
  atyear SMALLINT,
  atday TINYINT,
  atmark TINYINT,
  PRIMARY KEY(id),
  INDEX(pupil_id),
  INDEX(atmonth),
  INDEX(atyear)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT,
  user_id INT,
  sender_id INT,
  mstopic VARCHAR(255),
  mstext TEXT,
  msreaded TINYINT,
  mstime INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(user_id),
  INDEX(sender_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT,
  user_id INT,
  nttopic VARCHAR(255),
  ntlink VARCHAR(255),
  nttime INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(user_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS lessons (
  id INT AUTO_INCREMENT,
  lptext TEXT,
  lphometask TEXT,
  lpnotes TEXT,
  ktp_id INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(ktp_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS journal (
  id INT AUTO_INCREMENT,
  pupil_id INT,
  ktp_id INT,
  jrmark VARCHAR(2),
  PRIMARY KEY(id),
  INDEX(pupil_id),
  INDEX(ktp_id),
  INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS ratingcriteria (
  id INT AUTO_INCREMENT,
  rcname VARCHAR(255),
  rcrating TINYINT,
  subject_id INT,
  rcpriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(subject_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS rating (
  id INT AUTO_INCREMENT,
  pupil_id INT,
  rc_id INT,
  rating TINYINT,
  PRIMARY KEY(id),
  INDEX(pupil_id),
  INDEX(rc_id),
  INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bellsgroups (
  id INT AUTO_INCREMENT,
  bgname VARCHAR(255),
  school_id INT,
  PRIMARY KEY(id),
  INDEX(school_id),
  INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS bells (
  id INT AUTO_INCREMENT,
  bellsgroup_id INT,
  blnumber TINYINT,
  blstart INT,
  blend INT,
  PRIMARY KEY(id),
  INDEX(bellsgroup_id),
  INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS methodblogs (
  id INT AUTO_INCREMENT,
  region_id INT DEFAULT 0,
  mbname VARCHAR(255),
  mbdesc TEXT,
  mbtype TINYINT,
  user_id INT,
  PRIMARY KEY(id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mbspecs (
  id INT AUTO_INCREMENT,
  methodblog_id INT,
  specialization_id INT,
  PRIMARY KEY(id),
  INDEX(methodblog_id),
  INDEX(specialization_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mbnews (
  id INT AUTO_INCREMENT,
  user_id INT,
  methodblog_id INT,
  mbntime INT,
  mbnname VARCHAR(255),
  mbntext TEXT,
  PRIMARY KEY(id),
  INDEX(methodblog_id),
  INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mbdialog (
  id INT AUTO_INCREMENT,
  user_id INT,
  methodblog_id INT,
  mbdialog_id INT NOT NULL DEFAULT 0,
  mbdtime INT,
  mbdtext TEXT,
  PRIMARY KEY(id),
  INDEX(methodblog_id),
  INDEX(id),
  INDEX(mbdialog_id)
) DEFAULT CHARACTER SET utf8;

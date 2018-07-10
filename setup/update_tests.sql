CREATE TABLE IF NOT EXISTS tests(
  id INT AUTO_INCREMENT,
  user_id INT,
  tsname VARCHAR(255),
  tsdesc TEXT,
  tscode INT,
  tstime INT,
  tsqnum INT DEFAULT 0,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(user_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS testtasks(
  id INT AUTO_INCREMENT,
  tttask TEXT,
  tttype INT,
  test_id INT,
  ttpriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(test_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS testvars(
  id INT AUTO_INCREMENT,
  testtask_id INT,
  tvvar TEXT,
  tvtrue SMALLINT DEFAULT 0,
  tvpriority INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(testtask_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS testaccords(
  id INT AUTO_INCREMENT,
  testvar_id INT,
  taaccord TEXT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(testvar_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS testresults(
  id INT AUTO_INCREMENT,
  user_id INT,
  test_id INT,
  trcount INT,
  trpercent INT,
  trtime INT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(user_id),
  INDEX(test_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS testresultdetails(
  id INT AUTO_INCREMENT,
  testresult_id INT,
  testtask_id INT,
  testvar_id INT,
  trdright SMALLINT,
  PRIMARY KEY(id),
  INDEX(id),
  INDEX(testresult_id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS mbauthors (
  id INT AUTO_INCREMENT,
  methodblog_id INT,
  user_id INT,
  PRIMARY KEY(id),
  INDEX(methodblog_id),
  INDEX(user_id)
) DEFAULT CHARACTER SET utf8;

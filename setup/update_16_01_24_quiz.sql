CREATE TABLE IF NOT EXISTS quizes(
    id INT AUTO_INCREMENT,
    qztype TINYINT,
    qzname VARCHAR(255),
    qzdesc TEXT,
    qzpage_id INT,
    qzonce TINYINT,
    qzshowresults TINYINT,
    qzthank TEXT,
    qztime INT,
    user_id INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS qzquestions(
    id INT AUTO_INCREMENT,
    quiz_id INT,
    qqtext TEXT,
    qqtype TINYINT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS qzanswers(
    id INT AUTO_INCREMENT,
    qzquestion_id INT,
    qatext VARCHAR(255),
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS qzresults(
    id INT AUTO_INCREMENT,
    qzanswer_id INT,
    user_id INT,
    PRIMARY KEY(id),
    INDEX(id)
) DEFAULT CHARACTER SET utf8;

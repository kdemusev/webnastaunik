DROP TABLE mlsubjects;

CREATE TABLE IF NOT EXISTS mlsubjects (
	id INT AUTO_INCREMENT,
	msname VARCHAR(128),
  tcht_id INT,
  mspriority INT,
  PRIMARY KEY (id),
  INDEX (id)
) DEFAULT CHARACTER SET utf8;


INSERT INTO mlsubjects(msname, tcht_id) VALUES
	('Белорусский язык', 1),
	('Белорусская литература', 1),
	('Русский язык', 2),
	('Русская литература', 2),
	('Иностранный язык', 3),
	('Математика', 4),
	('Информатика', 5),
	('История Беларуси', 6),
	('Всемирная история', 6),
	('Человек и мир', 7),
	('География', 7),
	('Биология', 7),
	('Физика', 8),
	('Химия', 8),
	('Астрономия', 9),
	('Черчение', 9),
	('Физическая культура и здоровье', 10),
	('Трудовое обучение', 10),
	('Изобразительное искусство', 11),
	('Поведение', 11);
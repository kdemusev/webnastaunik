ALTER TABLE webinars ADD COLUMN wbfreestart INT DEFAULT 0;
ALTER TABLE webinars ADD COLUMN wbfreeend INT DEFAULT 0;
ALTER TABLE webinarmessages ADD COLUMN wbmsusername VARCHAR(255) DEFAULT '';
ALTER TABLE methodblogs ADD COLUMN mbfreestart INT DEFAULT 0;
ALTER TABLE methodblogs ADD COLUMN mbfreeend INT DEFAULT 0;
ALTER TABLE mbdialog ADD COLUMN mbdusername VARCHAR(255) DEFAULT '';

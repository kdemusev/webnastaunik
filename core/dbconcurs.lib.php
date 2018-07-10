<?php

include ('base.lib.php');

class CDBConcurs extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('results', 'add', 'save', 'getsections',
                             'delconcurs', 'editconcurs', 'changeconcurs',
                             'addpupil', 'savepupil', 'editfio', 'changefio', 'delfio',
                             'schoolsdb', 'reports', 'reportrating', 'reportdiplomas',
                             'reportpartsome', 'reportdynamic', 'reportquit',
                             'reportscrating', 'reportscdiplomas', 'reportschistory',
                             'reportnopart',
                             'reporttcrating', 'reporttcdiplomas', 'reporttchistory',
                             'concursesdb', 'sectionsdb');

      $this->_district_id = $this->db->scalar("SELECT district_id FROM schools WHERE id = '{$this->getSchoolId()}'");
  }

  function welcome() {
    $this->results();
  }

  /*
   * Отображение сводной таблицы результатов
   *
   */
  function results() {
    $this->allow(87);

    $_concurstype = $this->prepConcurstypeSelect();
    $_olymp_year_id = $this->prepYearSelect();

    $whereadd = $_concurstype > 0 ? "concurstype = '$_concurstype'" : '1 = 1';

    $sql = "SELECT c.id AS ctid, cnname, ctname, oscname, csname
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  $whereadd
            ORDER BY oscname, ctname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT cp.id AS cpid, opname, ofname, olymp_pupil_id, concurs_id
            FROM concurs_pupils AS cp
            LEFT JOIN concurs AS c ON c.id = cp.concurs_id
            LEFT JOIN olymp_pupils AS op ON cp.olymp_pupil_id=op.id
            LEFT JOIN olymp_forms AS of ON cp.olymp_form_id=of.id
            WHERE c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  $whereadd
            ORDER BY ofname+0, ofname, opname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['concurs_id']][] = $rec;
    }

    $this->view->assign('data_pupils', $d3);
    $this->view->show('dbconcurs.results');
  }

  /*
   * Отображение формы добавления конкурса в БД
   *
   */
  function add() {
    $this->allow(87);

    $this->query('olymp_years', "SELECT * FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC");
    $this->query('concurs_types', "SELECT * FROM concurs_types WHERE district_id = '{$this->_district_id}' ORDER BY ctname");
    $this->query('olymp_forms', "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname+0, ofname");
    $this->query('olymp_schools', "SELECT * FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname");

    $this->view->show('dbconcurs.results.add');
  }

  /*
   * XML Ajax функция получения списка секций для списка
   * автозаполнения
   *
   */
  function getsections() {
    $this->allow(87);

    $_concurs_type_id = $this->db->safe($_GET['id']);
    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<answer>\n";
    $data = $this->db->query("SELECT id, csname FROM concurs_sections WHERE concurs_type_id = '$_concurs_type_id' ORDER BY csname");
    foreach($data as $rec) {
        print '<section id="'.$rec['id'].'">'.$rec['csname'].'</section>'."\n";
    }
    print "</answer>\n";
  }

  /*
   * Сохранение информации о конкурсе при добавлении
   *
   */
  function save() {
    $this->allow(87);

    $_concurstype = $this->db->safe($_POST['concurstype']);
    $_olymp_year_id = $this->getPostYear();
    $_concurs_type_id = $this->getPostConcurs();
    $_concurs_section_id = $this->getPostSection($_concurs_type_id);
    $_olymp_form_id = $this->getPostForm();
    $_olymp_school_id = $this->getPostSchool();
    $_olymp_pupil_id = $this->getPostPupil($_olymp_school_id);
    $_olymp_teacher_id = $this->getPostTeacher($_olymp_school_id);

    $_ctdiploma = $this->db->safe($_POST['ctdiploma']);
    $_ctismore = isset($_POST['ctismore']) ? 1 : 0;
    $_cnname = $this->db->safe($_POST['cnname']);

    $sql = "INSERT INTO concurs (district_id, concurstype, olymp_year_id, concurs_section_id, olymp_teacher_id, ctdiploma,
                                 ctismore, cnname, olymp_subject_id)
            VALUES ('{$this->_district_id}', '$_concurstype', '$_olymp_year_id', '$_concurs_section_id', '$_olymp_teacher_id',
                    '$_ctdiploma', '$_ctismore', '$_cnname', '$_olymp_subject_id')";

    $this->db->query($sql);

    $_concurs_id = $this->db->last_id();

    $sql = "INSERT INTO concurs_pupils (concurs_id, olymp_pupil_id, olymp_form_id) VALUES ('$_concurs_id', '$_olymp_pupil_id', '$_olymp_form_id')";
    $this->db->query($sql);

    $this->view->message('added');

    $_entermore = $_POST['entermore'];
    if($_entermore > 0) {
      header('Location: /dbconcurs/add');
    } else {
      header('Location: /dbconcurs/results');
    }
  }

  /*
   * Отображение формы изменения общей информации о конкурсе
   *
   */
  function editconcurs() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "SELECT c.id AS ctid, cnname, ctname, csname, oscname, otname,
                   olymp_school_id, concurs_type_id, ctismore, oyname,
                   olymp_teacher_id, concurs_section_id, ctdiploma, ctismore
            FROM concurs AS c
            LEFT JOIN olymp_years AS y ON c.olymp_year_id = y.id
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE c.district_id = '{$this->_district_id}' AND
                  c.id = '$_id'";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data[0]);

    $olymp_school_id = $data[0]['olymp_school_id'];
    $concurs_type_id = $data[0]['concurs_type_id'];

    $this->query('teachers', "SELECT * FROM olymp_teachers WHERE olymp_school_id='$olymp_school_id' ORDER BY otname");
    $this->query('sections', "SELECT * FROM concurs_sections WHERE concurs_type_id = '$concurs_type_id' ORDER BY csname");

    $this->view->show('dbconcurs.results.edit.concurs');
  }

  /*
   * Отображение формы добавления участника в конкурс
   *
   */
  function addpupil() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "SELECT c.id AS ctid, cnname, ctname, csname, oscname, otname,
                   olymp_school_id, oyname
            FROM concurs AS c
            LEFT JOIN olymp_years AS y ON c.olymp_year_id = y.id
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE c.district_id = '{$this->_district_id}' AND
                  c.id = '$_id'";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data[0]);

    $olymp_school_id = $data[0]['olymp_school_id'];
    $concurs_id = $data[0]['ctid'];

    $this->query('pupils', "SELECT * FROM olymp_pupils WHERE olymp_school_id='$olymp_school_id' ORDER BY opname");
    $this->query('forms', "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname+0, ofname");

    $sql = "SELECT opname, ofname FROM concurs_pupils AS c
            LEFT JOIN olymp_pupils AS p ON p.id = c.olymp_pupil_id
            LEFT JOIN olymp_forms AS f ON f.id = c.olymp_form_id
            WHERE concurs_id = '$concurs_id' ORDER BY ofname+0, ofname, opname";
    $data = $this->db->query($sql);
    $this->view->assign('data_pupils', $data);

    $this->view->show('dbconcurs.results.addpupil');
  }

  /*
   * Сохранение изменений при редактировании общих сведений о конкурсе
   *
   */
  function changeconcurs() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "SELECT olymp_school_id, concurs_type_id
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            WHERE c.id = '$_id' LIMIT 1";
    $data = $this->db->query($sql);
    $_olymp_school_id = $data[0]['olymp_school_id'];
    $_concurs_type_id = $data[0]['concurs_type_id'];

    $_concurs_section_id = $this->getPostSection($_concurs_type_id);
    $_olymp_teacher_id = $this->getPostTeacher($_olymp_school_id);

    $_ctdiploma = $this->db->safe($_POST['ctdiploma']);
    $_ctismore = isset($_POST['ctismore']) ? 1 : 0;
    $_cnname = $this->db->safe($_POST['cnname']);

    $sql = "UPDATE concurs SET ctdiploma = '$_ctdiploma', ctismore = '$_ctismore', cnname = '$_cnname',
                               olymp_teacher_id = '$_olymp_teacher_id', concurs_section_id = '$_concurs_section_id'
            WHERE id = '$_id'";
    $this->db->query($sql);

    $this->view->message('concurschanged');

    header('Location: /dbconcurs/results');
  }

  /*
   * Сохранение информации о добавленном участнике конкурса
   *
   */
  function savepupil() {
    $this->allow(87);

    $_concurs_id = $this->db->safe($_GET['id']);

    $olymp_school_id = $this->db->scalar("SELECT olymp_school_id FROM olymp_pupils AS op LEFT JOIN concurs_pupils AS p
                                          ON p.olymp_pupil_id = op.id WHERE p.concurs_id = '$_concurs_id'");

    $_olymp_form_id = $this->getPostForm();
    $_olymp_pupil_id = $this->getPostPupil($olymp_school_id);

    $sql = "INSERT INTO concurs_pupils (concurs_id, olymp_pupil_id, olymp_form_id) VALUES ('$_concurs_id', '$_olymp_pupil_id', '$_olymp_form_id')";
    $this->db->query($sql);

    $this->view->message('pupiladded');

    header('Location: /dbconcurs/results');
  }

  /*
   * Удаление участника из конкурса
   *
   */
  function delfio() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM concurs_pupils WHERE id = '$_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)
            AND id NOT IN (SELECT olymp_pupil_id FROM olymp)";
    $this->db->query($sql);

    $this->view->message('fiodeleted');
    header('Location: /dbconcurs/results');
  }

  /*
   * Изменение участника конкурса конкурса
   *
   */
  function editfio() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $olymp_school_id = $this->db->scalar("SELECT olymp_school_id FROM olymp_pupils AS op LEFT JOIN concurs_pupils AS p
                                          ON p.olymp_pupil_id = op.id WHERE p.id = '$_id'");

    $sql = "SELECT * FROM olymp_pupils AS p WHERE olymp_school_id='$olymp_school_id' ORDER BY opname";
    $data = $this->db->query($sql);
    $this->view->assign('pupils', $data);

    $sql = "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname+0, ofname";
    $data = $this->db->query($sql);
    $this->view->assign('forms', $data);

    $sql = "SELECT id, olymp_pupil_id, olymp_form_id FROM concurs_pupils
            WHERE id = '$_id'";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data[0]);

    $this->view->show('dbconcurs.results.editpupil');
  }

  /*
   * Сохранение изменений участника конкурса
   *
   */
  function changefio() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $olymp_school_id = $this->db->scalar("SELECT olymp_school_id FROM olymp_pupils AS op LEFT JOIN concurs_pupils AS p
                                          ON p.olymp_pupil_id = op.id WHERE p.id = '$_id'");

    $_olymp_form_id = $this->getPostForm();
    $_olymp_pupil_id = $this->getPostPupil($olymp_school_id);

    $sql = "UPDATE concurs_pupils SET olymp_pupil_id='$_olymp_pupil_id', olymp_form_id='$_olymp_form_id' WHERE id = '$_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM olymp)
            AND id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_forms WHERE id NOT IN (SELECT olymp_form_id FROM olymp)
            AND id NOT IN (SELECT olymp_form_id FROM concurs_pupils)";
    $this->db->query($sql);

    $this->view->message('fiochanged');

    header('Location: /dbconcurs/results');
  }

  /*
   * Удаление конкурса
   * ВНИМАНИЕ! При изменении данной функции соответствующие изменения проводить в dbolymp.lib.php в соовтетствующей функции
   */
  function delconcurs() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM concurs WHERE id = '$_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM concurs_pupils WHERE concurs_id = '$_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM olymp)
            AND id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)";
    $this->db->query($sql);

    $sql = "DELETE FROM concurs_sections WHERE id NOT IN (SELECT concurs_section_id FROM concurs)";
    $this->db->query($sql);

    $sql = "DELETE FROM concurs_types WHERE id NOT IN (SELECT concurs_type_id FROM concurs_sections)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_teachers WHERE id NOT IN (SELECT olymp_teacher_id FROM olymp)
            AND id NOT IN (SELECT olymp_teacher_id FROM concurs)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_forms WHERE id NOT IN (SELECT olymp_form_id FROM olymp)
            AND id NOT IN (SELECT olymp_form_id FROM concurs_pupils)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_years WHERE id NOT IN (SELECT olymp_year_id FROM olymp)
            AND id NOT IN (SELECT olymp_year_id FROM concurs)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_schools
            WHERE id NOT IN (SELECT olymp_school_id FROM olymp_teachers) AND
            id NOT IN (SELECT olymp_school_id FROM olymp_pupils)";
    $this->db->query($sql);

    $this->view->message('concursdeleted');

    header('Location: /dbconcurs/results');
  }

  function reports() {
    $this->allow(87);

    $this->view->show('dbconcurs.reports');
  }

  function reportrating() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurs_type_id = $this->prepConcursSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd = "";
    if($_concurs_type_id > 0) {
      $whereadd = "concurs_type_id = '$_concurs_type_id' AND";
    }
    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT c.id AS ctid, cnname, ctname, oscname, csname, otname, ctismore, ctdiploma, concurstype
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            ORDER BY CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, oscname, ctname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT opname, ofname, concurs_id
            FROM concurs_pupils AS cp
            LEFT JOIN concurs AS c ON c.id = cp.concurs_id
            LEFT JOIN olymp_pupils AS op ON cp.olymp_pupil_id=op.id
            LEFT JOIN olymp_forms AS of ON cp.olymp_form_id=of.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            WHERE $whereadd $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            ORDER BY ofname+0, ofname, opname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['concurs_id']][] = $rec;
    }

    $this->view->assign('data_pupils', $d3);


    $this->view->show('dbconcurs.reports.rating');
  }

  function reportdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurs_type_id = $this->prepConcursSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd = "";
    if($_concurs_type_id > 0) {
      $whereadd = "concurs_type_id = '$_concurs_type_id' AND";
    }
    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT c.id AS ctid, cnname, ctname, oscname, csname, otname, ctismore, ctdiploma, concurstype
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  ctdiploma > 0
            ORDER BY CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, oscname, ctname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT opname, ofname, concurs_id
            FROM concurs_pupils AS cp
            LEFT JOIN concurs AS c ON c.id = cp.concurs_id
            LEFT JOIN olymp_pupils AS op ON cp.olymp_pupil_id=op.id
            LEFT JOIN olymp_forms AS of ON cp.olymp_form_id=of.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            WHERE $whereadd $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  ctdiploma > 0
            ORDER BY ofname+0, ofname, opname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['concurs_id']][] = $rec;
    }

    $this->view->assign('data_pupils', $d3);


    $this->view->show('dbconcurs.reports.diplomas');
  }

  function reportpartsome() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();

    $sql = "SELECT opname, ofname, oscname, olymp_pupil_id, olymp_form_id, COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl,
                   COUNT(*) AS cnt
            FROM concurs_pupils AS cp
            LEFT JOIN olymp_pupils AS op ON op.id = cp.olymp_pupil_id
            LEFT JOIN olymp_forms AS of ON of.id = cp.olymp_form_id
            LEFT JOIN concurs AS c ON c.id = cp.concurs_id
            LEFT JOIN olymp_schools AS os ON os.id = op.olymp_school_id
            WHERE olymp_year_id = '$_olymp_year_id' AND
                  c.district_id = '{$this->_district_id}'
            GROUP BY olymp_pupil_id HAVING cnt > 1
            ORDER BY cntdipl DESC, ofname+0, ofname, opname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT ctname, ctdiploma, olymp_pupil_id
            FROM concurs AS c
            LEFT JOIN concurs_pupils AS p ON p.concurs_id = c.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS t ON t.id = cs.concurs_type_id
            WHERE c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            ORDER BY CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, ctname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['olymp_pupil_id']][] = $rec;
    }
    $this->view->assign('data_concurses', $d3);

    $this->view->show('dbconcurs.reports.partsome');
  }

  function reportdynamic() {
    $this->allow(87);

    $this->prepReportHeader();

    $sql = "SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC";
    $years = $this->db->query($sql);

    if(count($years) > 1) {
      $sql = "SELECT olymp_pupil_id, concurs_type_id
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON c.id = cp.concurs_id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              WHERE c.district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[0]['id']}'";
      $data = $this->db->query($sql);

      $d1 = [];
      foreach($data as $rec) {
        $d1[$rec['olymp_pupil_id']][] = $rec['concurs_type_id'];
      }

      $sql = "SELECT olymp_pupil_id, concurs_type_id
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON c.id = cp.concurs_id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              WHERE c.district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[1]['id']}'";
      $data = $this->db->query($sql);

      $d2 = [];
      foreach($data as $rec) {
        $d2[$rec['olymp_pupil_id']][] = $rec['concurs_type_id'];
      }

      // at least one subject was in the last year;
      $in = '(';
      foreach($d1 as $pup => $rec) {
        if(!isset($d2[$pup])) { continue; } // pupil was not in the last year
        $in .= $pup.',';
      }
      $in .= '0)';

      $sql = "SELECT opname, ofname, oscname, olymp_pupil_id
              FROM concurs_pupils AS cp
              LEFT JOIN concurs AS c ON c.id = cp.concurs_id
              LEFT JOIN olymp_pupils AS op ON op.id = cp.olymp_pupil_id
              LEFT JOIN olymp_schools AS os ON op.olymp_school_id = os.id
              LEFT JOIN olymp_forms AS of ON of.id = cp.olymp_form_id
              WHERE olymp_pupil_id IN $in AND olymp_year_id = '{$years[0]['id']}'
              GROUP BY opname
              ORDER BY oscname, ofname+0, ofname, opname";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $sql = "SELECT ctname, csname, ctdiploma, oyname, olymp_pupil_id, concurstype
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON cp.concurs_id = c.id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
              LEFT JOIN olymp_years AS oy ON c.olymp_year_id = oy.id
              WHERE olymp_pupil_id IN $in
              ORDER BY oyname DESC, concurstype DESC, CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, ctname, csname";
      $data = $this->db->query($sql);

      $d3 = [];
      foreach($data as $rec) {
        $d3[$rec['olymp_pupil_id']][] = $rec;
      }
      $this->view->assign('data_concurses', $d3);
    } else {
      $this->view->assign('data', array());
    }

    $this->view->show('dbconcurs.reports.dynamic');
  }

  function reportquit() {
    $this->allow(87);

    $this->prepReportHeader();

    $sql = "SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC";
    $years = $this->db->query($sql);

    if(count($years) > 1) {
      $sql = "SELECT olymp_pupil_id, concurs_type_id
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON c.id = cp.concurs_id
              LEFT JOIN olymp_forms AS of ON cp.olymp_form_id = of.id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              WHERE c.district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[0]['id']}'";
      $data = $this->db->query($sql);

      $d1 = [];
      foreach($data as $rec) {
        $d1[$rec['olymp_pupil_id']][] = $rec['concurs_type_id'];
      }

      $sql = "SELECT olymp_pupil_id, concurs_type_id
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON c.id = cp.concurs_id
              LEFT JOIN olymp_forms AS of ON cp.olymp_form_id = of.id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              WHERE c.district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[1]['id']}'
                    AND ofname NOT LIKE '%11%'";
      $data = $this->db->query($sql);

      $d2 = [];
      foreach($data as $rec) {
        $d2[$rec['olymp_pupil_id']][] = $rec['concurs_type_id'];
      }

      // no one subject was in the last year;
      $in = '(';
      foreach($d2 as $pup => $rec) {
        if(!isset($d1[$pup])) {
          $in .= $pup.',';
        }
      }
      $in .= '0)';

      $sql = "SELECT opname, ofname, oscname, olymp_pupil_id
              FROM concurs_pupils AS cp
              LEFT JOIN concurs AS c ON c.id = cp.concurs_id
              LEFT JOIN olymp_pupils AS op ON op.id = cp.olymp_pupil_id
              LEFT JOIN olymp_schools AS os ON op.olymp_school_id = os.id
              LEFT JOIN olymp_forms AS of ON of.id = cp.olymp_form_id
              WHERE olymp_pupil_id IN $in
              GROUP BY opname
              ORDER BY oscname, ofname+0, ofname, opname";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $sql = "SELECT ctname, csname, ctdiploma, oyname, olymp_pupil_id
              FROM concurs AS c
              LEFT JOIN concurs_pupils AS cp ON cp.concurs_id = c.id
              LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
              LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
              LEFT JOIN olymp_years AS oy ON c.olymp_year_id = oy.id
              WHERE olymp_pupil_id IN $in
              ORDER BY oyname DESC, CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, ctname, csname";
      $data = $this->db->query($sql);

      $d3 = [];
      foreach($data as $rec) {
        $d3[$rec['olymp_pupil_id']][] = $rec;
      }
      $this->view->assign('data_concurses', $d3);
    } else {
      $this->view->assign('data', array());
    }

    $this->view->show('dbconcurs.reports.quit');
  }

  function reportscrating() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT c.id AS ctid, ctname, concurs_type_id, oscname, olymp_school_id, concurstype, COUNT(*) as cnt,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY ctname, oscname
            ORDER BY cnt DESC, cntdipl DESC, ctname, oscname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT cnname, csname, ctdiploma, c.id AS ctid, concurs_type_id, olymp_school_id
            FROM concurs AS c
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            ORDER BY CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, csname, cnname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['concurs_type_id'].'_'.$rec['olymp_school_id']][] = $rec;
    }

    $this->view->assign('data_works', $d3);


    $this->view->show('dbconcurs.reports.scrating');
  }

  function reportscdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT c.id AS ctid, ctname, concurs_type_id, oscname, olymp_school_id, concurstype, COUNT(*) as cnt,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY ctname, oscname HAVING cntdipl > 0
            ORDER BY cnt DESC, cntdipl DESC, ctname, oscname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT cnname, csname, ctdiploma, c.id AS ctid, concurs_type_id, olymp_school_id
            FROM concurs AS c
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  ctdiploma > 0
            ORDER BY CASE WHEN ctdiploma = 0 THEN 999 WHEN ctdiploma > 0 THEN ctdiploma END, csname, cnname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['concurs_type_id'].'_'.$rec['olymp_school_id']][] = $rec;
    }

    $this->view->assign('data_works', $d3);


    $this->view->show('dbconcurs.reports.scdiplomas');
  }

  function reportschistory() {
    $this->allow(87);

    $this->prepReportHeader();

    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT olymp_school_id, oscname, olymp_year_id, oyname,
            COUNT(*) AS cnt,
            COUNT(IF(ctdiploma>0,ctdiploma,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_years AS oy ON c.olymp_year_id = oy.id
            LEFT JOIN olymp_teachers AS ot ON c.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd2
            c.district_id = '{$this->_district_id}'
            GROUP BY olymp_school_id, olymp_year_id
            ORDER BY oscname, oyname DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbconcurs.reports.schistory');
  }

  function reportnopart() {
    $this->allow(87);

    $_olymp_year_id = $this->prepYearSelect();
    $_concurs_type_id = $this->prepConcursSelect();

    $whereaddpre = "";
    $whereadd2 = "";
    if($_concurs_type_id > 0) {
      $whereaddpre = "id = '$_concurs_type_id' AND";
      $whereadd2 = "concurs_type_id = '$_concurs_type_id' AND";
    }

    $sql = "SELECT id, oscname FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname";
    $datasc = $this->db->query($sql);
    $this->view->assign('data', $datasc);

    $sql = "SELECT id, ctname FROM concurs_types WHERE $whereaddpre district_id = '{$this->_district_id}' ORDER BY ctname";
    $dataos = $this->db->query($sql);
    $this->view->assign('dataos', $dataos);

    $sql = "SELECT concurs_type_id, olymp_school_id
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS ot ON c.olymp_teacher_id = ot.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            WHERE $whereadd2
            olymp_year_id = '$_olymp_year_id' AND
            c.district_id = '{$this->_district_id}'";

    $data = $this->db->query($sql);
    $check = [];
    foreach($data as $rec) {
      $check[$rec['concurs_type_id']][] = $rec['olymp_school_id'];
    }

    $d2 = [];
    foreach($datasc as $rec) {
      foreach($dataos as $recos) {
        if(!isset($check[$recos['id']]) || !in_array($rec['id'], $check[$recos['id']])) {
          $d2[$rec['id']][] = $recos['ctname'];
        }
      }
    }
    $this->view->assign('datasubjects', $d2);

    $this->view->show('dbconcurs.reports.nopart');
  }

  function reporttcrating() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT otname, oscname, olymp_teacher_id, concurstype, COUNT(*) as cnt,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY otname
            ORDER BY cntdipl DESC, cnt DESC, otname, oscname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT ctname, COUNT(*) as cnt,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl,
                   olymp_teacher_id
            FROM concurs AS c
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY ctname, olymp_teacher_id
            ORDER BY cntdipl, cnt, ctname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['olymp_teacher_id']][] = $rec;
    }

    $this->view->assign('data_works', $d3);


    $this->view->show('dbconcurs.reports.tcrating');
  }

  function reporttcdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT otname, oscname, olymp_teacher_id, concurstype,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_teachers AS t ON c.olymp_teacher_id = t.id
            LEFT JOIN olymp_schools AS s ON t.olymp_school_id = s.id
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY otname HAVING cntdipl > 0
            ORDER BY cntdipl DESC, otname, oscname";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT ctname,
                   COUNT(IF(ctdiploma>0,1,NULL)) AS cntdipl,
                   olymp_teacher_id
            FROM concurs AS c
            LEFT JOIN concurs_sections AS cs ON c.concurs_section_id = cs.id
            LEFT JOIN concurs_types AS ct ON cs.concurs_type_id = ct.id
            WHERE $whereadd2
                  c.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            GROUP BY ctname, olymp_teacher_id HAVING cntdipl > 0
            ORDER BY cntdipl, ctname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['olymp_teacher_id']][] = $rec;
    }

    $this->view->assign('data_works', $d3);

    $this->view->show('dbconcurs.reports.tcdiplomas');
}

  function reporttchistory() {
    $this->allow(87);

    $this->prepReportHeader();

    $_concurstype = $this->prepConcurstypeSelect();

    $whereadd2 = "";
    if($_concurstype > 0) {
      $whereadd2 = "concurstype = '$_concurstype' AND";
    }

    $sql = "SELECT olymp_teacher_id, otname, oscname, olymp_year_id, oyname,
            COUNT(*) AS cnt,
            COUNT(IF(ctdiploma>0,ctdiploma,NULL)) AS cntdipl
            FROM concurs AS c
            LEFT JOIN olymp_years AS oy ON c.olymp_year_id = oy.id
            LEFT JOIN olymp_teachers AS ot ON c.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd2
            c.district_id = '{$this->_district_id}'
            GROUP BY olymp_teacher_id, olymp_year_id
            ORDER BY cntdipl DESC, cnt DESC, otname, oyname DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbconcurs.reports.tchistory');
  }

  function concursesdb() {
    $this->allow(87);

    if(isset($_POST['save'])) {
      if(isset($_POST['ctname'])) {
        foreach($_POST['ctname'] as $id => $ctname) {
          $id = $this->db->safe($id);
          $ctname = $this->db->safe($ctname);

          $sql = "UPDATE concurs_types SET ctname = '$ctname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM concurs_types WHERE id NOT IN (SELECT concurs_type_id FROM concurs_sections)";
    $this->db->query($sql);

    $this->query('data',
            "SELECT * FROM concurs_types WHERE district_id = '{$this->_district_id}'
            ORDER BY ctname");

    $this->view->show('dbconcurs.concurses');
  }

  function sectionsdb() {
    $this->allow(87);

    $sql = "SELECT * FROM concurs_types WHERE district_id = '{$this->_district_id}' ORDER BY ctname";
    $data = $this->db->query($sql);
    $this->view->assign('concurses', $data);

    if(isset($_POST['setconcurs_type_id'])) {
      $_SESSION['concurs_type_id'] = $_POST['setconcurs_type_id'];
    } else if(!isset($_SESSION['concurs_type_id'])) {
      $_SESSION['concurs_type_id'] = $this->db->scalar("SELECT id FROM concurs_types WHERE district_id = '{$this->_district_id}' ORDER BY ctname LIMIT 1");
    }
    $_concurs_type_id = $this->db->safe($_SESSION['concurs_type_id']);

    if(isset($_POST['save'])) {
      if(isset($_POST['csname'])) {
        foreach($_POST['csname'] as $id => $csname) {
          $id = $this->db->safe($id);
          $csname = $this->db->safe($csname);

          $sql = "UPDATE concurs_sections SET csname = '$csname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM concurs_sections WHERE id NOT IN (SELECT concurs_section_id FROM concurs)";
    $this->db->query($sql);

    $this->query('data',
            "SELECT * FROM concurs_sections WHERE concurs_type_id = '$_concurs_type_id'
            ORDER BY csname");

    $this->view->show('dbconcurs.sections');
  }

  // Get the name of the institution for the report header
  private function prepReportHeader() {
    $uoname = $this->db->scalar("SELECT scname FROM schools WHERE id = '{$this->getSchoolId()}'");
    $this->view->assign('uoname', $uoname);
  }

  // Get data for year select box
  private function prepYearSelect() {
    if(isset($_POST['setolymp_year_id'])) {
      $_SESSION['olymp_year_id'] = $_POST['setolymp_year_id'];
    } else if(!isset($_SESSION['olymp_year_id'])) {
      $_SESSION['olymp_year_id'] =
        $this->db->scalar("SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC LIMIT 1");
    }
    $_olymp_year_id = $this->db->safe($_SESSION['olymp_year_id']);

    $this->query('years',
      "SELECT * FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC");

    $this->view->assign('oyname', $this->db->scalar("SELECT oyname FROM olymp_years WHERE id = '$_olymp_year_id'"));

    return $_olymp_year_id;
  }

  // Get data for concurs select box
  private function prepConcursSelect() {
    if(isset($_POST['setconcurs_type_id'])) {
      $_SESSION['concurs_type_id'] = $_POST['setconcurs_type_id'];
    } else if(!isset($_SESSION['concurs_type_id'])) {
      $_SESSION['concurs_type_id'] = 0;
    }
    $_concurs_type_id = $this->db->safe($_SESSION['concurs_type_id']);

    $sql = "SELECT * FROM concurs_types WHERE district_id = '{$this->_district_id}' ORDER BY ctname";
    $data=  $this->db->query($sql);
    $this->view->assign('concurses', $data);

    return $_concurs_type_id;
  }

  private function prepConcurstypeSelect() {
    if(isset($_POST['setconcurstype'])) {
      $_SESSION['concurstype'] = $_POST['setconcurstype'];
    } else if(!isset($_SESSION['concurstype'])) {
      $_SESSION['concurstype'] = 1;
    }
    $_concurstype = $this->db->safe($_SESSION['concurstype']);

    return $_concurstype;
  }

  private function getPostForm() {
    $_olymp_form_id = $this->db->safe($_POST['form_id']);

    if($_olymp_form_id == -1) { // Add new form
      $_ofname = $this->db->safe($_POST['add_form_id']);
      $sql = "INSERT INTO olymp_forms (ofname, district_id) VALUES ('$_ofname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_form_id = $this->db->last_id();
    }
    return $_olymp_form_id;
  }

  private function getPostPupil($_olymp_school_id) {
    $_olymp_pupil_id = $this->db->safe($_POST['pupil_id']);

    if($_olymp_pupil_id == -1) {  // Add new pupil
      $_opname = $this->db->safe($_POST['add_pupil_id']);
      $sql = "INSERT INTO olymp_pupils (opname, olymp_school_id) VALUES ('$_opname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_pupil_id = $this->db->last_id();
    }
    return $_olymp_pupil_id;
  }

  private function getPostSection($_concurs_type_id) {
    $_concurs_section_id = $this->db->safe($_POST['section_id']);

    if($_concurs_section_id == -1) { // Add new subject
      $_csname = $this->db->safe($_POST['add_section_id']);
      $sql = "INSERT INTO concurs_sections (csname, concurs_type_id) VALUES ('$_csname', '$_concurs_type_id')";
      $this->db->query($sql);
      $_concurs_section_id = $this->db->last_id();
    }
    return $_concurs_section_id;
  }

  private function getPostTeacher($_olymp_school_id) {
    $_olymp_teacher_id = $this->db->safe($_POST['teacher_id']);

    if($_olymp_teacher_id == -1) {  // Add new teacher
      $_otname = $this->db->safe($_POST['add_teacher_id']);
      $sql = "INSERT INTO olymp_teachers (otname, olymp_school_id) VALUES ('$_otname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_teacher_id = $this->db->last_id();
    }
    return $_olymp_teacher_id;
  }

  private function getPostYear() {
    $_olymp_year_id = $this->db->safe($_POST['year_id']);

    if($_olymp_year_id == -1) { // Add new year
      $_olname = $this->db->safe($_POST['add_year_id']);
      $sql = "INSERT INTO olymp_years (oyname, district_id) VALUES ('$_olname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_year_id = $this->db->last_id();
    }
    return $_olymp_year_id;
  }

  private function getPostConcurs() {
    $_concurs_id = $this->db->safe($_POST['concurs_id']);

    if($_concurs_id == -1) { // Add new subject
      $_ctname = $this->db->safe($_POST['add_concurs_id']);
      $sql = "INSERT INTO concurs_types (ctname, district_id) VALUES ('$_ctname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_concurs_id = $this->db->last_id();
    }
    return $_concurs_id;
  }

  private function getPostSchool() {
    $_olymp_school_id = $this->db->safe($_POST['school_id']);

    if($_olymp_school_id == -1) { // Add new school
      $_oscname = $this->db->safe($_POST['add_school_id']);
      $sql =  "INSERT INTO olymp_schools (oscname, district_id) VALUES ('$_oscname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_school_id = $this->db->last_id();
    }
    return $_olymp_school_id;
  }

}

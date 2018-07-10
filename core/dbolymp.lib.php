<?php

include ('base.lib.php');

class CDBOlymp extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('results', 'add', 'save', 'getpupandteach',
                             'delsubj', 'editsubj', 'changesubj',
                             'editfio', 'changefio',
                             'schoolsdb', 'formsdb', 'pupilsdb', 'subjectsdb',
                             'reports',
                             'reportrating', 'reportdiplomas', 'reportpartsome',
                             'reportnosubject', 'reportdynamic', 'reportquit',
                             'reportscdiplomas', 'reportscresult',
                             'reporttcdiplomas', 'reporttcresult',
                             'reportpartreg', 'reportresreg',
                             'reportpartrep', 'reportresrep',
                             'reportschistory', 'reporttchistory',
                             'reportnopart', 'total',
                             'importcsv');

      $this->_district_id = $this->db->scalar("SELECT district_id FROM schools WHERE id = '{$this->getSchoolId()}'");
  }

  function welcome() {
    $this->results();
  }

  function schoolsdb() {
    $this->allow(87);

    if(isset($_POST['save'])) {
      if(isset($_POST['oscname'])) {
        foreach($_POST['oscname'] as $id => $oscname) {
          $id = $this->db->safe($id);
          $oscname = $this->db->safe($oscname);

          $sql = "UPDATE olymp_schools SET oscname = '$oscname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM olymp_schools
            WHERE id NOT IN (SELECT olymp_school_id FROM olymp_teachers) AND
            id NOT IN (SELECT olymp_school_id FROM olymp_pupils)";
    $this->db->query($sql);

    $sql = "SELECT * FROM districts WHERE id = '{$this->_district_id}'";
    $data = $this->db->query($sql);
    $this->view->assign('districtdata', $data[0]);

    $this->query('data',
            "SELECT * FROM olymp_schools WHERE district_id = '{$this->_district_id}'
            ORDER BY oscname");

    $this->view->show('dbolymp.schools');
  }

  function formsdb() {
    $this->allow(87);

    if(isset($_POST['save'])) {
      if(isset($_POST['ofname'])) {
        foreach($_POST['ofname'] as $id => $ofname) {
          $id = $this->db->safe($id);
          $ofname = $this->db->safe($ofname);

          $sql = "UPDATE olymp_forms SET ofname = '$ofname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM olymp_forms WHERE id NOT IN (SELECT olymp_form_id FROM olymp)
            AND id NOT IN (SELECT olymp_form_id FROM concurs_pupils)";
    $this->db->query($sql);

    $this->query('data',
            "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}'
            ORDER BY ofname+0, ofname");

    $this->view->show('dbolymp.forms');
  }

  function pupilsdb() {
    $this->allow(87);

    $sql = "SELECT * FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname";
    $data = $this->db->query($sql);
    $this->view->assign('schools', $data);

    if(isset($_POST['setolymp_school_id'])) {
      $_SESSION['olymp_school_id'] = $_POST['setolymp_school_id'];
    } else if(!isset($_SESSION['olymp_school_id'])) {
      $_SESSION['olymp_school_id'] = $this->db->scalar("SELECT id FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname LIMIT 1");
    }
    $_olymp_school_id = $this->db->safe($_SESSION['olymp_school_id']);

    if(isset($_POST['save'])) {
      if(isset($_POST['opname'])) {
        foreach($_POST['opname'] as $id => $opname) {
          $id = $this->db->safe($id);
          $opname = $this->db->safe($opname);

          $sql = "UPDATE olymp_pupils SET opname = '$opname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM olymp)
            AND id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)";
    $this->db->query($sql);

    $this->query('data',
            "SELECT * FROM olymp_pupils WHERE olymp_school_id = '$_olymp_school_id'
            ORDER BY opname");

    $this->view->show('dbolymp.pupils');
  }

  function subjectsdb() {
    $this->allow(87);

    if(isset($_POST['save'])) {
      if(isset($_POST['osname'])) {
        foreach($_POST['osname'] as $id => $osname) {
          $id = $this->db->safe($id);
          $osname = $this->db->safe($osname);

          $sql = "UPDATE olymp_subjects SET osname = '$osname'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }
      $this->view->message('changed');
    }

    $sql = "DELETE FROM olymp_subjects WHERE id NOT IN (SELECT olymp_subject_id FROM olymp)";
    $this->db->query($sql);

    $this->query('data',
            "SELECT * FROM olymp_subjects WHERE district_id = '{$this->_district_id}'
            ORDER BY osname");

    $this->view->show('dbolymp.subjects');
  }

  /*
   * Функция отображения общей таблицы результатов проведения 2 этапа олимпиады
   *
   */
  function results() {
    $this->allow(87);

    $_olymptype = $this->prepOlymptypeSelect();
    $_olymp_year_id = $this->prepYearSelect();

    $sql = "SELECT COUNT(olymp_subject_id) AS subjcount, olymp_pupil_id, opname,
                   oscname, ofname, o.id AS olymp_id
            FROM olymp AS o
            LEFT JOIN olymp_pupils AS p ON o.olymp_pupil_id = p.id
            LEFT JOIN olymp_forms AS f ON o.olymp_form_id = f.id
            LEFT JOIN olymp_schools AS s ON p.olymp_school_id = s.id
            WHERE o.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id' AND
                  olymptype = '$_olymptype'
            GROUP BY olymp_pupil_id
            ORDER BY oscname, ofname, opname";
    $data = $this->db->query($sql);
    $this->view->assign('olymp_data', $data);

    $sql = "SELECT o.id AS olymp_id, olymp_pupil_id, osname, otname
            FROM olymp AS o
            LEFT JOIN olymp_subjects AS s ON o.olymp_subject_id = s.id
            LEFT JOIN olymp_teachers AS t ON olymp_teacher_id = t.id
            WHERE o.district_id = '{$this->_district_id}' AND
                  olymp_year_id = '$_olymp_year_id'
            ORDER BY olymp_pupil_id, osname";
    $data = $this->db->query($sql);
    $this->view->assign('olymp_subjects', $data);

    $this->view->show('dbolymp.results');
  }

  /*
   * Функция удаления предмета из списка олимпиадных предметов участника
   *
   */
  function delsubj() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM olymp WHERE id = '$_id' AND district_id = '{$this->_district_id}'";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM olymp)
            AND id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_subjects WHERE id NOT IN (SELECT olymp_subject_id FROM olymp)";
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


    $this->view->message('subjdeleted');

    header('Location: /dbolymp/results');
  }

  /*
   * Функция отображения формы изменения результата участия по предмету
   *
   */
  function editsubj() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "SELECT o.id AS olymp_id, olymp_teacher_id, olymp_school_id, oyname, opname,
                   oscname, ofname, osname, olmaxpoints, olpoints, olpercent, olrating,
                   oldiploma, olabsend, olnopassport, olnoinapplication, olisregion, olregrating,
                   olregdiploma, olregabsend, olisrepublic, olreprating, olrepdiploma,
                   olrepabsend
            FROM olymp AS o
            LEFT JOIN olymp_years AS y ON o.olymp_year_id = y.id
            LEFT JOIN olymp_pupils AS p ON o.olymp_pupil_id = p.id
            LEFT JOIN olymp_subjects AS sb ON o.olymp_subject_id = sb.id
            LEFT JOIN olymp_forms AS f ON o.olymp_form_id = f.id
            LEFT JOIN olymp_schools AS s ON p.olymp_school_id = s.id
            WHERE o.district_id = '{$this->_district_id}' AND
                  o.id = '$_id'";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data[0]);

    $olymp_school_id = $data[0]['olymp_school_id'];

    $sql = "SELECT * FROM olymp_teachers WHERE olymp_school_id='$olymp_school_id'";
    $data = $this->db->query($sql);
    $this->view->assign('teachers', $data);

    $this->view->show('dbolymp.result.edit.subject');
  }

  /*
   * Функция сохранения изменений результата участия по предмету
   *
   */
  function changesubj() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $sql = "SELECT olymp_school_id FROM olymp AS o
            LEFT JOIN olymp_teachers AS t ON t.id = o.olymp_teacher_id
            WHERE o.id = '$_id' AND
            o.district_id = '{$this->_district_id}'";
    $_olymp_school_id = $this->db->scalar($sql);

    $_olymp_teacher_id = $this->db->safe($_POST['teacher_id']);

    if($_olymp_teacher_id == -1) {  // Add new teacher
      $_otname = $this->db->safe($_POST['add_teacher_id']);
      $sql = "INSERT INTO olymp_teachers (otname, olymp_school_id) VALUES ('$_otname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_teacher_id = $this->db->last_id();
    }

    $_olmaxpoints = $this->db->safe($_POST['olmaxpoints']);
    $_olpoints = str_replace(',', '.', $this->db->safe($_POST['olpoints']));
    $_olpercent = str_replace(',', '.', $this->db->safe($_POST['olpercent']));
    $_olrating = $this->db->safe($_POST['olrating']);
    $_oldiploma = $this->db->safe($_POST['oldiploma']);
    $_olabsend = isset($_POST['olabsend']) ? 1 : 0;
    $_olnopassport = isset($_POST['olnopassport']) ? 1 : 0;
    $_olnoinapplication = isset($_POST['olnoinapplication']) ? 1 : 0;
    $_olisregion = isset($_POST['olisregion']) ? 1 : 0;
    $_olregrating = $this->db->safe($_POST['olregrating']);
    $_olregdiploma = $this->db->safe($_POST['olregdiploma']);
    $_olregabsend = isset($_POST['olabsend']) ? 1 : 0;
    $_olisrepublic = isset($_POST['olisrepublic']) ? 1 : 0;
    $_olreprating = $this->db->safe($_POST['olreprating']);
    $_olrepdiploma = $this->db->safe($_POST['olrepdiploma']);
    $_olrepabsend = isset($_POST['olabsend']) ? 1 : 0;

    $sql = "UPDATE olymp SET olymp_teacher_id = '$_olymp_teacher_id',
            olmaxpoints = '$_olmaxpoints', olpoints = '$_olpoints',
            olpercent = '$_olpercent', olrating = '$_olrating',
            oldiploma = '$_oldiploma', olabsend = '$_olabsend',
            olnopassport = '$_olnopassport', olisregion = '$_olisregion',
            olnoinapplication = '$_olnoinapplication',
            olregrating = '$_olregrating', olregdiploma = '$_olregdiploma',
            olregabsend = '$_olregabsend', olisrepublic = '$_olisrepublic',
            olreprating = '$_olreprating', olrepdiploma = '$_olrepdiploma',
            olrepabsend = '$_olrepabsend' WHERE id = '$_id' AND
            district_id = '{$this->_district_id}'";
    $this->db->query($sql);

    $this->view->message('subjchanged');

    header('Location: /dbolymp/results');
  }

  /*
   * Функция отображения формы редактирования фамилии, имени, отчества участника
   *
   */
  function editfio() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $olymp_school_id = $this->db->scalar("SELECT olymp_school_id FROM olymp AS o
                                          LEFT JOIN olymp_pupils AS op ON op.id = o.olymp_pupil_id
                                          WHERE o.id = '$_id'");

    $sql = "SELECT * FROM olymp_pupils AS p WHERE olymp_school_id='$olymp_school_id' ORDER BY opname";
    $data = $this->db->query($sql);
    $this->view->assign('pupils', $data);

    $sql = "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname+0, ofname";
    $data = $this->db->query($sql);
    $this->view->assign('forms', $data);

    $sql = "SELECT id, olymp_pupil_id, olymp_form_id FROM olymp
            WHERE id = '$_id'";
    $data = $this->db->query($sql);

    $this->view->assign('data', $data[0]);

    $this->view->show('dbolymp.results.edit.name');
  }

  /*
   * Функция сохранения изменений фамилии, имени, отчества участника
   *
   */
  function changefio() {
    $this->allow(87);

    $_id = $this->db->safe($_GET['id']);

    $_olymp_form_id = $this->db->safe($_POST['form_id']);

    if($_olymp_form_id == -1) { // Add new form
      $_ofname = $this->db->safe($_POST['add_form_id']);
      $sql = "INSERT INTO olymp_forms (ofname, district_id) VALUES ('$_ofname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_form_id = $this->db->last_id();
    }

    $_olymp_pupil_id = $this->db->safe($_POST['pupil_id']);

    if($_olymp_pupil_id == -1) {  // Add new pupil
      $_opname = $this->db->safe($_POST['add_pupil_id']);
      $sql = "INSERT INTO olymp_pupils (opname, olymp_school_id) VALUES ('$_opname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_pupil_id = $this->db->last_id();
    }

    $sql = "SELECT olymp_pupil_id, olymp_form_id, olymp_year_id FROM olymp WHERE id = '$_id'";
    $data = $this->db->query($sql);
    $_old_olymp_pupil_id = $data[0]['olymp_pupil_id'];
    $_old_olymp_form_id = $data[0]['olymp_form_id'];
    $_old_olymp_year_id = $data[0]['olymp_year_id'];

    $sql = "UPDATE olymp SET olymp_pupil_id='$_olymp_pupil_id', olymp_form_id='$_olymp_form_id' WHERE
            olymp_pupil_id = '$_old_olymp_pupil_id' AND olymp_form_id = '$_old_olymp_form_id'
            AND olymp_year_id = '$_old_olymp_year_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_pupils WHERE id NOT IN (SELECT olymp_pupil_id FROM olymp)
            AND id NOT IN (SELECT olymp_pupil_id FROM concurs_pupils)";
    $this->db->query($sql);

    $sql = "DELETE FROM olymp_forms WHERE id NOT IN (SELECT olymp_form_id FROM olymp)
            AND id NOT IN (SELECT olymp_form_id FROM concurs_pupils)";
    $this->db->query($sql);

    $this->view->message('fiochanged');

    header('Location: /dbolymp/results');
  }

  /*
   * XML Ajax функция получения списка учащихся и педагогов учреждения для списка
   * автозаполнения
   *
   */
  function getpupandteach() {
    $this->allow(87);

    $_olymp_school_id = $this->db->safe($_GET['id']);
    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<answer>\n";
    $data = $this->db->query("SELECT id, opname FROM olymp_pupils WHERE olymp_school_id = '$_olymp_school_id' ORDER BY opname");
    foreach($data as $rec) {
        print '<pupil id="'.$rec['id'].'">'.$rec['opname'].'</pupil>'."\n";
    }
    $data = $this->db->query("SELECT id, otname FROM olymp_teachers WHERE olymp_school_id = '$_olymp_school_id' ORDER BY otname");
    foreach($data as $rec) {
        print '<teacher id="'.$rec['id'].'">'.$rec['otname'].'</teacher>'."\n";
    }
    print "</answer>\n";
  }

  /*
   * Функция отображения формы добавления результата участия
   *
   */
  function add() {
    $this->allow(87);

    $this->query('olymp_years', "SELECT * FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC");
    $this->query('olymp_subjects', "SELECT * FROM olymp_subjects WHERE district_id = '{$this->_district_id}' ORDER BY osname");
    $this->query('olymp_forms', "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname");
    $this->query('olymp_schools', "SELECT * FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname");

    $this->view->show('dbolymp.results.add');
  }

  /*
   * Функция отображения формы добавления результата участия
   *
   */
  function save() {
    $this->allow(87);

    $_olymptype = $this->db->safe($_POST['olymptype']);
    $_olymp_year_id = $this->db->safe($_POST['year_id']);

    if($_olymp_year_id == -1) { // Add new year
      $_olname = $this->db->safe($_POST['add_year_id']);
      $sql = "INSERT INTO olymp_years (oyname, district_id) VALUES ('$_olname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_year_id = $this->db->last_id();
    }

    $_olymp_subject_id = $this->db->safe($_POST['subject_id']);

    if($_olymp_subject_id == -1) { // Add new subject
      $_osname = $this->db->safe($_POST['add_subject_id']);
      $sql = "INSERT INTO olymp_subjects (osname, district_id) VALUES ('$_osname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_subject_id = $this->db->last_id();
    }

    $_olymp_form_id = $this->db->safe($_POST['form_id']);

    if($_olymp_form_id == -1) { // Add new form
      $_ofname = $this->db->safe($_POST['add_form_id']);
      $sql = "INSERT INTO olymp_forms (ofname, district_id) VALUES ('$_ofname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_form_id = $this->db->last_id();
    }

    $_olymp_school_id = $this->db->safe($_POST['school_id']);

    if($_olymp_school_id == -1) { // Add new school
      $_oscname = $this->db->safe($_POST['add_school_id']);
      $sql =  "INSERT INTO olymp_schools (oscname, district_id) VALUES ('$_oscname', '{$this->_district_id}')";
      $this->db->query($sql);
      $_olymp_school_id = $this->db->last_id();
    }

    $_olymp_pupil_id = $this->db->safe($_POST['pupil_id']);

    if($_olymp_pupil_id == -1) {  // Add new pupil
      $_opname = $this->db->safe($_POST['add_pupil_id']);
      $sql = "INSERT INTO olymp_pupils (opname, olymp_school_id) VALUES ('$_opname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_pupil_id = $this->db->last_id();
    }

    $_olymp_teacher_id = $this->db->safe($_POST['teacher_id']);

    if($_olymp_teacher_id == -1) {  // Add new teacher
      $_otname = $this->db->safe($_POST['add_teacher_id']);
      $sql = "INSERT INTO olymp_teachers (otname, olymp_school_id) VALUES ('$_otname', '$_olymp_school_id')";
      $this->db->query($sql);
      $_olymp_teacher_id = $this->db->last_id();
    }

    $_olmaxpoints = $this->db->safe($_POST['olmaxpoints']);
    $_olpoints = str_replace(',', '.', $this->db->safe($_POST['olpoints']));
    $_olpercent = str_replace(',', '.', $this->db->safe($_POST['olpercent']));
    $_olrating = $this->db->safe($_POST['olrating']);
    $_oldiploma = $this->db->safe($_POST['oldiploma']);
    $_olabsend = isset($_POST['olabsend']) ? 1 : 0;
    $_olnopassport = isset($_POST['olnopassport']) ? 1 : 0;
    $_olnoinapplication = isset($_POST['olnoinapplication']) ? 1 : 0;
    $_olisregion = isset($_POST['olisregion']) ? 1 : 0;
    $_olregrating = $this->db->safe($_POST['olregrating']);
    $_olregdiploma = $this->db->safe($_POST['olregdiploma']);
    $_olregabsend = isset($_POST['olabsend']) ? 1 : 0;
    $_olisrepublic = isset($_POST['olisrepublic']) ? 1 : 0;
    $_olreprating = $this->db->safe($_POST['olreprating']);
    $_olrepdiploma = $this->db->safe($_POST['olrepdiploma']);
    $_olrepabsend = isset($_POST['olabsend']) ? 1 : 0;

    $sql = "INSERT INTO olymp (district_id, olymptype, olymp_pupil_id, olymp_form_id, olymp_year_id, olymp_subject_id,
                               olymp_teacher_id, olmaxpoints, olpoints, olpercent, olrating, oldiploma, olabsend, olnopassport, olnoinapplication,
                               olisregion, olregrating, olregdiploma, olregabsend, olisrepublic, olreprating, olrepdiploma, olrepabsend)
                       VALUES ('{$this->_district_id}', '$_olymptype', '$_olymp_pupil_id', '$_olymp_form_id', '$_olymp_year_id', '$_olymp_subject_id',
                               '$_olymp_teacher_id', '$_olmaxpoints', '$_olpoints', '$_olpercent', '$_olrating', '$_oldiploma', '$_olabsend', '$_olnopassport', '$_olnoinapplication',
                               '$_olisregion', '$_olregrating', '$_olregdiploma', '$_olregabsend', '$_olisrepublic', '$_olreprating', '$_olrepdiploma', '$_olrepabsend')";
    $this->db->query($sql);

    $this->view->message('added');

    $_entermore = $_POST['entermore'];
    if($_entermore > 0) {
      header('Location: /dbolymp/add');
    } else {
      header('Location: /dbolymp/results');
    }
  }

  /*
   * Функция отображения меню сводной информации
   *
   */
  function reports() {
    $this->allow(87);

    $this->view->show('dbolymp.reports');
  }

  /*
   * Функция отображения сводной информации "Рейтинг"
   *
   */
  function reportrating() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();
    $_olymp_form_id = $this->prepFormSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd .= " olymp_subject_id = '$_olymp_subject_id' AND";
    }
    if($_olymp_form_id > 0) {
      $whereadd .= " olymp_form_id = '$_olymp_form_id' AND";
    }
    if($_olymptype > 0) {
      $whereadd .= " olymptype = '$_olymptype' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, olmaxpoints,
            olpoints, olpercent, olrating, oldiploma, olnopassport, olabsend, olnoinapplication
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}'
            ORDER BY osname, ofname + 1, CASE WHEN olrating = 0 THEN 999 WHEN olrating > 0 THEN olrating END,
            opname, olabsend DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.rating');
  }

  /*
   * Функция отображения сводной информации "Дипломы"
   *
   */
  function reportdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();
    $_olymp_form_id = $this->prepFormSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd .= "olymp_subject_id = '$_olymp_subject_id' AND";
    }
    if($_olymp_form_id > 0) {
      $whereadd .= " olymp_form_id = '$_olymp_form_id' AND";
    }
    if($_olymptype > 0) {
      $whereadd .= " olymptype = '$_olymptype' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, olmaxpoints,
            olpoints, olpercent, olrating, oldiploma, olnopassport, olabsend,
            olymp_subject_id
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}' AND oldiploma > 0
            ORDER BY osname, oldiploma, ofname+0, ofname,
            CASE WHEN olrating = 0 THEN 1000 WHEN olrating > 0 THEN olrating END";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.diplomas');
  }

  /*
   * Функция отображения сводной информации "Участники по нескольким предметам"
   *
   */
  function reportpartsome() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $sql = "SELECT COUNT(olymp_subject_id) AS cnt, olymp_pupil_id, oyname, opname,
                   ofname, oscname
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id WHERE
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}'
            GROUP BY olymp_pupil_id HAVING cnt > 1
            ORDER BY cnt DESC, oscname, ofname+0, ofname, opname";
    $data = $this->db->query($sql);

    $sql = "SELECT osname, olymp_subject_id, olymp_pupil_id, oldiploma
            FROM olymp AS o LEFT JOIN olymp_subjects AS s ON o.olymp_subject_id = s.id
            WHERE olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}'
            ORDER BY CASE WHEN oldiploma = 0 THEN 999 WHEN oldiploma > 0 THEN oldiploma END";
    $data2 = $this->db->query($sql);
    $data3 = [];
    foreach($data2 as $rec) {
      $data3[$rec['olymp_pupil_id']][] = $rec;
    }

    $this->view->assign('data', $data);
    $this->view->assign('datasubjects', $data3);

    $this->view->show('dbolymp.reports.partsome');
  }

  /*
   * Функция отображения сводной информации "Участники меняющие предметы"
   *
   */
  function reportnosubject() {
    $this->allow(87);

    $this->prepReportHeader();

    $sql = "SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC";
    $years = $this->db->query($sql);

    if(count($years) > 1) {
      $sql = "SELECT olymp_pupil_id, olymp_subject_id
              FROM olymp
              WHERE district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[0]['id']}' AND olrating > 0";
      $data = $this->db->query($sql);

      $d1 = [];
      foreach($data as $rec) {
        $d1[$rec['olymp_pupil_id']][] = $rec['olymp_subject_id'];
      }

      $sql = "SELECT olymp_pupil_id, olymp_subject_id
              FROM olymp
              WHERE district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[1]['id']}' AND olrating > 0";
      $data = $this->db->query($sql);

      $d2 = [];
      foreach($data as $rec) {
        $d2[$rec['olymp_pupil_id']][] = $rec['olymp_subject_id'];
      }

      // at least one subject was in the last year;
      $in = '';
      foreach($d1 as $pup => $rec) {
        if(!isset($d2[$pup])) { continue; } // pupil was not in the last year
        $noone = true;
        foreach($rec as $subj) {
          if(in_array($subj, $d2[$pup])) {
            $noone = false;
          }
        }
        if($noone) {
          $in .= $pup.',';
        }
      }
      $in .= '0';

      $sql = "SELECT olymp_pupil_id, opname, ofname, oscname
              FROM olymp AS o
              LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
              LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
              LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
              LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
              WHERE olymp_pupil_id IN ($in)
              GROUP BY olymp_pupil_id
              ORDER BY oscname, ofname+0, ofname, opname";

      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $sql = "SELECT olymp_pupil_id, oyname, osname, olrating, oldiploma FROM olymp AS o
              LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
              LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
              WHERE olymp_pupil_id IN ($in)
              ORDER BY oyname DESC";
      $data = $this->db->query($sql);

      $data3 = [];
      foreach($data as $rec) {
        $data3[$rec['olymp_pupil_id']][] = $rec;
      }

      $this->view->assign('datasubjects', $data3);
    } else {
      $this->view->assign('data', array());
    }

    $this->view->show('dbolymp.reports.nosubject');
  }

  /*
   * Функция отображения сводной информации "Динамика по годам"
   *
   */
  function reportdynamic() {
    $this->allow(87);

    $this->prepReportHeader();

    $sql = "SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC";
    $years = $this->db->query($sql);

    if(count($years) > 1) {
      $sql = "SELECT olymp_pupil_id, olymp_subject_id
              FROM olymp
              WHERE district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[0]['id']}' AND olrating > 0";
      $data = $this->db->query($sql);

      $d1 = [];
      foreach($data as $rec) {
        $d1[$rec['olymp_pupil_id']][] = $rec['olymp_subject_id'];
      }

      $sql = "SELECT olymp_pupil_id, olymp_subject_id
              FROM olymp
              WHERE district_id = '{$this->_district_id}' AND olymp_year_id = '{$years[1]['id']}' AND olrating > 0";
      $data = $this->db->query($sql);

      $d2 = [];
      foreach($data as $rec) {
        $d2[$rec['olymp_pupil_id']][] = $rec['olymp_subject_id'];
      }

      // at least one subject was in the last year;
      $in = '';
      foreach($d1 as $pup => $rec) {
        if(!isset($d2[$pup])) { continue; } // pupil was not in the last year
        $noone = true;
        foreach($rec as $subj) {
          if(in_array($subj, $d2[$pup])) {
            $noone = false;
          }
        }
        if(!$noone) {
          $in .= $pup.',';
        }
      }
      $in .= '0';

      $sql = "SELECT olymp_pupil_id, opname, ofname, oscname
              FROM olymp AS o
              LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
              LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
              LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
              LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
              WHERE olymp_pupil_id IN ($in)
              GROUP BY olymp_pupil_id
              ORDER BY oscname, ofname+0, ofname, opname";

      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $sql = "SELECT olymp_pupil_id, oyname, osname, olrating, oldiploma FROM olymp AS o
              LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
              LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
              WHERE olymp_pupil_id IN ($in)
              ORDER BY oyname DESC";
      $data = $this->db->query($sql);

      $data3 = [];
      foreach($data as $rec) {
        $data3[$rec['olymp_pupil_id']][] = $rec;
      }

      $this->view->assign('datasubjects', $data3);
    } else {
      $this->view->assign('data', array());
    }

    $this->view->show('dbolymp.reports.dynamic');
  }

  /*
   * Функция отображения сводной информации "Вышли из олимпиадного движения"
   *
   */
  function reportquit() {
    $this->allow(87);

    $this->prepReportHeader();

    $sql = "SELECT id FROM olymp_years WHERE district_id = '{$this->_district_id}' ORDER BY oyname DESC";
    $years = $this->db->query($sql);

    if(count($years) > 1) {
      $sql = "SELECT olymp_pupil_id, opname, ofname, oscname
              FROM olymp AS o
              LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
              LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
              LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
              LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
              WHERE o.district_id = '{$this->_district_id}' AND
              olymp_year_id = '{$years[1]['id']}' AND olrating > 0 AND
              ofname NOT LIKE '%11%' AND
              olymp_pupil_id NOT IN (SELECT olymp_pupil_id FROM olymp WHERE olymp_year_id = '{$years[0]['id']}')
              GROUP BY olymp_pupil_id
              ORDER BY oscname, ofname+0, ofname, opname";

      $data = $this->db->query($sql);

      $in = '(';
      foreach($data as $rec) {
        $in .= $rec['olymp_pupil_id'].',';
      }
      $in .= '0)';

      $this->view->assign('data', $data);

      $sql = "SELECT olymp_pupil_id, oyname, osname, olrating, oldiploma FROM olymp AS o
              LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
              LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
              WHERE olymp_pupil_id IN $in
              ORDER BY oyname DESC";
      $data = $this->db->query($sql);

      $data3 = [];
      foreach($data as $rec) {
        $data3[$rec['olymp_pupil_id']][] = $rec;
      }

      $this->view->assign('datasubjects', $data3);
    } else {
      $this->view->assign('data', array());
    }
    $this->view->show('dbolymp.reports.quit');
  }

  /*
   * Функция отображения сводной информации "По учреждениям - По количеству дипломов"
   *
   */
  function reportscdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT MIN(osname), oscname, oyname, osname,
            COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma='1',oldiploma,NULL)) AS cnt1,
            COUNT(IF(oldiploma='2',oldiploma,NULL)) AS cnt2,
            COUNT(IF(oldiploma='3',oldiploma,NULL)) AS cnt3, olymp_school_id
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            oldiploma > 0 AND o.district_id = '{$this->_district_id}'
            GROUP BY olymp_school_id
            ORDER BY cnt DESC";

    // Выбор дополнительной таблицы педагогов
    $sql2 = "SELECT otname, olymp_school_id, COUNT(IF(oldiploma>0,oldiploma,NULL)) AS moldiploma
             FROM olymp AS o
             LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
             LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
             LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
             LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
             WHERE $whereadd
             olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
             oldiploma > 0 AND o.district_id = '{$this->_district_id}'
             GROUP BY otname, olymp_school_id
             ORDER BY olymp_school_id, moldiploma DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $data = $this->db->query($sql2);
    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['olymp_school_id']][] = $rec;
    }

    $this->view->assign('datateachers', $d3);

    $this->view->show('dbolymp.reports.scdiplomas');
  }

  /*
   * Функция отображения сводной информации "По учреждениям - По результативности"
   *
   */
  function reportscresult() {
    $this->allow(87);
    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, oscname,
            COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma='1',oldiploma,NULL)) AS cnt1,
            COUNT(IF(oldiploma='2',oldiploma,NULL)) AS cnt2,
            COUNT(IF(oldiploma='3',oldiploma,NULL)) AS cnt3,
            COUNT(o.id) AS pupcnt,
            (COUNT(IF(oldiploma>0,oldiploma,NULL)))/COUNT(o.id)*100 AS percent,
            olymp_school_id
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND o.district_id = '{$this->_district_id}'
            GROUP BY olymp_school_id
            ORDER BY cnt DESC, percent DESC, pupcnt DESC, oscname";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.scresult');
  }

  /*
   * Функция отображения сводной информации "По педагогам - По количеству дипломов"
   *
   */
  function reporttcdiplomas() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT o.id AS olymp_id, osname, oscname, otname, oyname, COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma='1',oldiploma,NULL)) AS cnt1,
            COUNT(IF(oldiploma='2',oldiploma,NULL)) AS cnt2,
            COUNT(IF(oldiploma='3',oldiploma,NULL)) AS cnt3,
            COUNT(olymp_pupil_id) AS pupcnt, olymp_teacher_id
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND o.district_id = '{$this->_district_id}'
            GROUP BY olymp_teacher_id HAVING cnt > 0
            ORDER BY cnt DESC, otname";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $sql = "SELECT olymp_teacher_id, osname, COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt
             FROM olymp AS o
             LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
             WHERE $whereadd
             olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND o.district_id = '{$this->_district_id}'
             GROUP BY olymp_teacher_id, osname HAVING cnt > 0
             ORDER BY cnt DESC, osname";
    $data = $this->db->query($sql);

    $d3 = [];
    foreach($data as $rec) {
      $d3[$rec['olymp_teacher_id']][] = $rec;
    }

    $this->view->assign('datasubjects', $d3);

    $this->view->show('dbolymp.reports.tcdiplomas');
  }

  /*
   * Функция отображения сводной информации "По педагогам - По результативности"
   *
   */
  function reporttcresult() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, otname, oscname,
            COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma='1',oldiploma,NULL)) AS cnt1,
            COUNT(IF(oldiploma='2',oldiploma,NULL)) AS cnt2,
            COUNT(IF(oldiploma='3',oldiploma,NULL)) AS cnt3,
            COUNT(o.id) AS pupcnt, (COUNT(IF(oldiploma>0,oldiploma,NULL)))/COUNT(o.id)*100 AS percent, olymp_teacher_id
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND o.district_id = '{$this->_district_id}'
            GROUP BY olymp_teacher_id
            ORDER BY cnt DESC, percent DESC, pupcnt DESC, oscname, otname";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.tcresult');
  }

  /*
   * Функция отображения сводной информации "Участники III этапа"
   *
   */
  function reportpartreg() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, oldiploma, olnopassport, olrating
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}' AND olisregion = 1
            ORDER BY osname, ofname+0, ofname, oscname, opname";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.partreg');
  }

  /*
   * Функция отображения сводной информации "Результаты III этапа"
   *
   */
  function reportresreg() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, olregrating, olregdiploma, olregabsend
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}' AND olisregion = 1
            ORDER BY osname,
            CASE WHEN olregdiploma = 0 THEN 1000 WHEN olregdiploma>0 THEN olregdiploma END,
            CASE WHEN olregrating = 0 THEN 1000 WHEN olregrating>0 THEN olregrating END,
            oscname, osname, olregabsend";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.resreg');
  }

  /*
   * Функция отображения сводной информации "Участники заключительного этапа"
   *
   */
  function reportpartrep() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = 1;

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, olregdiploma, olregrating
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}' AND olisrepublic = 1
            ORDER BY osname, ofname+0, ofname, oscname, opname";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.partrep');
  }

  /*
   * Функция отображения сводной информации "Результаты заключительного этапа"
   *
   */
  function reportresrep() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_year_id = $this->prepYearSelect();
    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = 1;

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT oyname, osname, opname, ofname, oscname, otname, olreprating, olrepdiploma, olrepabsend
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_pupils AS op ON o.olymp_pupil_id = op.id
            LEFT JOIN olymp_forms AS ofm ON o.olymp_form_id = ofm.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}' AND olisrepublic = 1
            ORDER BY osname,
            CASE WHEN olrepdiploma = 0 THEN 1000 WHEN olrepdiploma>0 THEN olrepdiploma END,
            CASE WHEN olreprating = 0 THEN 1000 WHEN olreprating>0 THEN olreprating END,
            oscname, osname, olrepabsend";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.resrep');
  }

  /*
   * Функция отображения сводной информации "Олимпиадная история учреждения"
   *
   */
  function reportschistory() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT olymp_school_id, oscname, olymp_year_id, oyname, osname,
            COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma>0,oldiploma,NULL))/COUNT(olymp_pupil_id)*100 AS percent
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND
            o.district_id = '{$this->_district_id}'
            GROUP BY olymp_school_id, olymp_year_id
            ORDER BY oscname, oyname DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.schistory');
  }

  /*
   * Функция отображения сводной информации "Олимпиадная история педагога"
   *
   */
  function reporttchistory() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT otname, olymp_teacher_id, oscname, olymp_year_id, oyname, osname,
            COUNT(IF(oldiploma>0,oldiploma,NULL)) AS cnt,
            COUNT(IF(oldiploma>0,oldiploma,NULL))/COUNT(olymp_pupil_id)*100 AS percent
            FROM olymp AS o
            LEFT JOIN olymp_years AS oy ON o.olymp_year_id = oy.id
            LEFT JOIN olymp_subjects AS os ON o.olymp_subject_id = os.id
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            LEFT JOIN olymp_schools AS s ON ot.olymp_school_id = s.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND
            o.district_id = '{$this->_district_id}'
            GROUP BY olymp_teacher_id, olymp_year_id
            ORDER BY oscname, otname, oyname DESC";

    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $this->view->show('dbolymp.reports.tchistory');
  }

  /*
   * Функция отображения сводной информации "Отсутствуют участники"
   *
   */
  function reportnopart() {
    $this->allow(87);

    $this->prepReportHeader();

    $_olymp_subject_id = $this->prepSubjectSelect();
    $_olymp_year_id = $this->prepYearSelect();
    $_olymptype = $this->prepOlymptypeSelect();

    $whereaddpre = "";
    $whereadd = "";
    if($_olymp_subject_id > 0) {
      $whereaddpre = "id = '$_olymp_subject_id' AND";
      $whereadd = "olymp_subject_id = '$_olymp_subject_id' AND";
    }

    $sql = "SELECT id, oscname FROM olymp_schools WHERE district_id = '{$this->_district_id}' ORDER BY oscname";
    $datasc = $this->db->query($sql);
    $this->view->assign('data', $datasc);

    $sql = "SELECT id, osname FROM olymp_subjects WHERE $whereaddpre district_id = '{$this->_district_id}' ORDER BY osname";
    $dataos = $this->db->query($sql);
    $this->view->assign('dataos', $dataos);

    $sql = "SELECT olymp_subject_id, olymp_school_id
            FROM olymp AS o
            LEFT JOIN olymp_teachers AS ot ON o.olymp_teacher_id = ot.id
            WHERE $whereadd
            olymptype = '$_olymptype' AND olymp_year_id = '$_olymp_year_id' AND
            o.district_id = '{$this->_district_id}'";

    $data = $this->db->query($sql);
    $check = [];
    foreach($data as $rec) {
      $check[$rec['olymp_subject_id']][] = $rec['olymp_school_id'];
    }

    $d2 = [];
    foreach($datasc as $rec) {
      foreach($dataos as $recos) {
        if(!isset($check[$recos['id']]) || !in_array($rec['id'], $check[$recos['id']])) {
          $d2[$rec['id']][] = $recos['osname'];
        }
      }
    }
    $this->view->assign('datasubjects', $d2);

    $this->view->show('dbolymp.reports.nopart');
  }

  function total() {
    $this->allow(1);



    $this->view->show('dbolymp.reports.total');
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

  // Get data for subject select box
  private function prepSubjectSelect() {
    if(isset($_POST['setolymp_subject_id'])) {
      $_SESSION['olymp_subject_id'] = $_POST['setolymp_subject_id'];
    } else if(!isset($_SESSION['olymp_subject_id'])) {
      $_SESSION['olymp_subject_id'] = 0;
    }
    $_olymp_subject_id = $this->db->safe($_SESSION['olymp_subject_id']);

    $this->query('subjects',
      "SELECT * FROM olymp_subjects WHERE district_id = '{$this->_district_id}' ORDER BY osname");

    $this->view->assign('osname', $this->db->scalar("SELECT osname FROM olymp_subjects WHERE id = '$_olymp_subject_id'"));

    return $_olymp_subject_id;
  }

  // Get data for subject select box
  private function prepFormSelect() {
    if(isset($_POST['setolymp_form_id'])) {
      $_SESSION['olymp_form_id'] = $_POST['setolymp_form_id'];
    } else if(!isset($_SESSION['olymp_form_id'])) {
      $_SESSION['olymp_form_id'] = 0;
    }
    $_olymp_form_id = $this->db->safe($_SESSION['olymp_form_id']);

    $this->query('forms',
      "SELECT * FROM olymp_forms WHERE district_id = '{$this->_district_id}' ORDER BY ofname");

    $this->view->assign('ofname', $this->db->scalar("SELECT ofname FROM olymp_forms WHERE id = '$_olymp_form_id'"));

    return $_olymp_form_id;
  }

  private function prepOlymptypeSelect() {
    if(isset($_POST['setolymptype'])) {
      $_SESSION['olymptype'] = $_POST['setolymptype'];
    } else if(!isset($_SESSION['olymptype'])) {
      $_SESSION['olymptype'] = 1;
    }
    $_olymptype = $this->db->safe($_SESSION['olymptype']);

    return $_olymptype;
  }

  // not public pages
  function importcsv() {
    ini_set('max_execution_time', 300);
    $file = fopen('olympres2.txt', 'r');
    $str = fgets($file);
    print $str.'<br>';
    while(!feof($file)) {
      $str = fgets($file);
      print $str.'<br>';
      $str = iconv("windows-1251", "utf-8", $str);
      if(trim($str)=='') {
        continue;
      }
      $e = explode("\t", $str);
      $olymptype = trim($e[0]) == 'Областная' ? 2 : 1;
      $olabsend = trim($e[1]) == 'не явился' ? 1 : 0;
      $olnopassport = trim($e[3]) == 'нет' ? 1 : 0;
      $olrating = trim($e[13]);
      $oyname = '2016/2017';
      $osname = trim($e[5]);
      $opname = trim($e[6]);
      $ofname = trim($e[7]);
      $oscname = trim($e[8]);
      $otname = trim($e[9]);
      $olmaxpoints = trim($e[10]);
      $olpoints = str_replace(',','.',trim($e[11]));
      $olpercent = str_replace(',','.',trim($e[12]));
      $oldiploma = (trim($e[13]) == '1 степени') ? 1 : ((trim($e[13]) == '2 степени') ? 2 : ((trim($e[13]) == '3 степени') ? 3 : 0));
      $olisregion = trim($e[14]) == 'да' ? 1 : 0;
      $olregdiploma = (trim($e[15]) == '1 степени') ? 1 : ((trim($e[15]) == '2 степени') ? 2 : ((trim($e[15]) == '3 степени') ? 3 : 0));
      $olisrepublic = trim($e[16]) == 'да' ? 1 : 0;
      $olrepdiploma = (trim($e[17]) == '1 степени') ? 1 : ((trim($e[17]) == '2 степени') ? 2 : ((trim($e[17]) == '3 степени') ? 3 : 0));


      $olymp_school_id = $this->db->scalar("SELECT id FROM olymp_schools WHERE oscname = '$oscname'");
      if($olymp_school_id == NULL) {
        $this->db->query("INSERT INTO olymp_schools (oscname, district_id) VALUES ('$oscname', '{$this->_district_id}')");
        $olymp_school_id = $this->db->last_id();
      }

      $olymp_year_id = $this->db->scalar("SELECT id FROM olymp_years WHERE oyname = '$oyname'");
      if($olymp_year_id == NULL) { // Add new year
        $this->db->query("INSERT INTO olymp_years (oyname, district_id) VALUES ('$oyname', '{$this->_district_id}')");
        $olymp_year_id = $this->db->last_id();
      }

      $olymp_subject_id = $this->db->scalar("SELECT id FROM olymp_subjects WHERE osname = '$osname'");
      if($olymp_subject_id == NULL) { // Add new subject
        $this->db->query("INSERT INTO olymp_subjects (osname, district_id) VALUES ('$osname', '{$this->_district_id}')");
        $olymp_subject_id = $this->db->last_id();
      }

      $olymp_form_id = $this->db->scalar("SELECT id FROM olymp_forms WHERE ofname = '$ofname'");
      if($olymp_form_id == NULL) { // Add new form
        $this->db->query("INSERT INTO olymp_forms (ofname, district_id) VALUES ('$ofname', '{$this->_district_id}')");
        $olymp_form_id = $this->db->last_id();
      }

      $olymp_pupil_id = $this->db->scalar("SELECT id FROM olymp_pupils WHERE opname = '$opname'");
      if($olymp_pupil_id == NULL) {  // Add new pupil
        $this->db->query("INSERT INTO olymp_pupils (opname, olymp_school_id) VALUES ('$opname', '$olymp_school_id')");
        $olymp_pupil_id = $this->db->last_id();
      }

      $olymp_teacher_id = $this->db->scalar("SELECT id FROM olymp_teachers WHERE otname = '$otname'");
      if($olymp_teacher_id == NULL) {  // Add new teacher
        $this->db->query("INSERT INTO olymp_teachers (otname, olymp_school_id) VALUES ('$otname', '$olymp_school_id')");
        $olymp_teacher_id = $this->db->last_id();
      }

      $sql = "INSERT INTO olymp (district_id, olymptype, olymp_pupil_id, olymp_form_id, olymp_year_id, olymp_subject_id,
                                 olymp_teacher_id, olmaxpoints, olpoints, olpercent, olrating, oldiploma, olabsend, olnopassport, olnoinapplication,
                                 olisregion, olregrating, olregdiploma, olregabsend, olisrepublic, olreprating, olrepdiploma, olrepabsend)
                         VALUES ('{$this->_district_id}', '$olymptype', '$olymp_pupil_id', '$olymp_form_id', '$olymp_year_id', '$olymp_subject_id',
                                 '$olymp_teacher_id', '$olmaxpoints', '$olpoints', '$olpercent', '$olrating', '$oldiploma', '$olabsend', '$olnopassport', '0',
                                 '$olisregion', '', '$olregdiploma', '0', '$olisrepublic', '', '$olrepdiploma', '0')";
      $this->db->query($sql);

    }
    fclose($file);
  }
}

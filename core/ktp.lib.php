<?php

include ('base.lib.php');

class CKtp extends CBase {

    function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('setup',
                             'dates', 'vacations', 'setvacations',
                             'fill', 'view',
                             'createtypical', 'viewtypical',
                             'typicallist', 'edittypical', 'deltypical',
                             'import');
    }

    function view() {
      $this->allow(1);

      $user_id = $this->user_id;
      $teacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");

      $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                 (isset($_POST['subject_id']) ?
                 $this->db->scalar("SELECT form_id FROM subjects WHERE id = '".
                                   $this->db->safe($_POST['subject_id'])."'") :
                 $this->db->scalar("SELECT form_id FROM subjects LEFT JOIN forms ON
                                    forms.id = subjects.form_id WHERE teacher_id = '$teacher_id'
                                    ORDER BY fmname+0 LIMIT 1"));
      $this->view->assign('form_id', $form_id);

      $subject_id = isset($_POST['subject_id']) ? $this->db->safe($_POST['subject_id']) :
                    $this->db->scalar("SELECT id FROM subjects WHERE teacher_id = '$teacher_id'
                                       AND form_id = '$form_id'
                                       ORDER BY sbname LIMIT 1");
      $this->view->assign('subject_id', $subject_id);

      $sql = "SELECT form_id AS id, fmname FROM subjects AS s LEFT JOIN forms AS f
              ON s.form_id = f.id WHERE teacher_id = '$teacher_id' GROUP BY form_id
              ORDER BY fmname+0";
      $data = $this->db->query($sql);
      $this->view->assign('_forms', $data);

      $sql = "SELECT id, sbname FROM subjects WHERE teacher_id = '$teacher_id' AND
              form_id = '$form_id' ORDER BY sbname";
      $data = $this->db->query($sql);
      $this->view->assign('_subjects', $data);

      $sql = "SELECT * FROM ktpdayoff";
      $data = $this->db->query($sql);
      $this->view->assign('_dayoffs', $data);

      $sql = "SELECT * FROM ktp WHERE subject_id = '$subject_id' ORDER BY ktdate";
      $data = $this->db->query($sql);
      $this->view->assign('_ktp', $data);
      if(count($data) == 0) {
        $this->view->message('auto');
      }

      // get days of the week
      $sql = "SELECT ttday FROM timetable WHERE subject_id = '$subject_id'";
      $data = $this->db->query($sql);
      $this->view->assign('_days', $data);


      $school_id = $this->getSchoolId();
      $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
      $this->view->assign('scname', $scname);

      $sbname = $this->db->scalar("SELECT sbname FROM subjects WHERE id = '$subject_id'");
      $this->view->assign('sbname', $sbname);

      $fmname = $this->db->scalar("SELECT fmname FROM forms WHERE id = '$form_id'");
      $this->view->assign('fmname', $fmname);

      $this->view->show('ktp.view');
    }

    function typicallist() {
      $this->allow(1);

      $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) : '1';
      $this->view->assign('form_id', $form_id);

      $_forms = [];
      for($i = 1; $i <= 11; $i++) {
        $_forms[$i-1]['id'] = $i;
        $_forms[$i-1]['fmname'] = $i;
      }
      $this->view->assign('_forms', $_forms);

      $this->q('data',
        "SELECT k.id AS kttiid, kttiname, kttiform, tcname, scname, dtname, rgname
         FROM ktp_typical_info AS k
         INNER JOIN teachers AS t ON k.teacher_id = t.id
         INNER JOIN schools AS s ON t.school_id = s.id
         INNER JOIN districts AS d ON s.district_id = d.id
         INNER JOIN regions AS r ON d.region_id = r.id
         WHERE kttiform = '$form_id'
         ORDER BY kttiform+1, kttiform, kttiname"
      );

      $this->view->show('ktp.typical.list');
    }

    function viewtypical() {
      $this->allow(1);

      $id = $this->g('id');

      $school_id = $this->getSchoolId();
      $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
      $this->view->assign('scname', $scname);

      $this->q('info',
        "SELECT id, kttiname, kttiform, kttidesc FROM ktp_typical_info WHERE id = '$id'"
      );

      $this->q('_ktp',
        "SELECT * FROM ktp_typical WHERE ktp_typical_info_id = '$id'"
      );

      $teacher_id = $this->db->scalar("SELECT teacher_id FROM ktp_typical_info WHERE id = '$id'");
      if($teacher_id == $this->getTeacherId()) {
        $this->view->assign('canedit', '1');
      } else {
        $this->view->assign('canedit', '0');
      }

      $this->view->show('ktp.typical.view');
    }

    function deltypical() {
      $this->allow(1);

      $id = $this->g('id');

      $teacher_id = $this->db->scalar("SELECT teacher_id FROM ktp_typical_info WHERE id = '$id'");
      if($teacher_id != $this->getTeacherId()) {
        $this-page404();
      }

      $this->db->query(
        "DELETE FROM ktp_typical_info WHERE id = '$id'"
      );

      $this->db->query(
        "DELETE FROM ktp_typical WHERE ktp_typical_info_id = '$id'"
      );

      $this->view->message('deleted');

      $this->typicallist();
    }

    function edittypical() {
      $this->allow(1);

      $ktp_typical_info_id = $this->g('id');

      $teacher_id = $this->db->scalar("SELECT teacher_id FROM ktp_typical_info WHERE id = '$ktp_typical_info_id'");
      if($teacher_id != $this->getTeacherId()) {
        $this-page404();
      }

      if(isset($_POST['save'])) {
        $kttiname = $this->p('kttiname');
        $kttiform = $this->p('kttiform');
        $kttidesc = $this->p('kttidesc');
        $this->db->query(
          "UPDATE ktp_typical_info SET kttiname = '$kttiname', kttiform = '$kttiform',
           kttidesc = '$kttidesc' WHERE id = '$ktp_typical_info_id'"
        );

        if(isset($_POST['ktttopic']) && count($_POST['ktttopic']) > 0) {
          foreach($_POST['ktttopic'] as $n => $rec) {
            $id = $this->db->safe($n);
            if(trim($rec)=="") {
              $this->db->query(
                "DELETE FROM ktp_typical WHERE id = '$id'"
              );
              continue;
            }
            $kttnumber = $this->db->safe($_POST['kttnumber'][$n]);
            $ktttopic = $this->db->safe($rec);
            $kttcolor = $this->db->safe($_POST['kttcolor'][$n]);

            $sql = "UPDATE ktp_typical SET kttnumber = '$kttnumber',
                    ktttopic = '$ktttopic', kttcolor = '$kttcolor'
                    WHERE id = '$id'";
            $this->db->query($sql);
          }
        }
        if(isset($_POST['newktttopic']) && count($_POST['newktttopic']) > 0) {
          foreach($_POST['newktttopic'] as $n => $rec) {
            if(trim($rec)=="") { continue; }
            $kttnumber++;
            $kttnumber = $this->db->safe($_POST['newkttnumber'][$n]);
            $ktttopic = $this->db->safe($rec);
            $kttcolor = $this->db->safe($_POST['newkttcolor'][$n]);

            $sql = "INSERT INTO ktp_typical (ktp_typical_info_id, kttnumber, ktttopic, kttcolor)
                    VALUES ('$ktp_typical_info_id', '$kttnumber', '$ktttopic', '$kttcolor')";
            $this->db->query($sql);
          }
        }

        $this->view->message('updated');
        $this->viewtypical();
        return;
      }

      $this->q('info',
        "SELECT id, kttiname, kttiform, kttidesc FROM ktp_typical_info WHERE id = '$ktp_typical_info_id'"
      );

      $this->query('_ktp',
        "SELECT * FROM ktp_typical WHERE ktp_typical_info_id = '$ktp_typical_info_id'
        ORDER BY kttnumber"
      );

      $this->view->show('ktp.typical.edit');
    }

    function createtypical() {
      $this->allow(1);

      if(isset($_POST['save']) && !isset($_POST['saveastypical'])) {
        $kttiname = $this->p('kttiname');
        $kttiform = $this->p('kttiform');
        $kttidesc = $this->p('kttidesc');
        $teacher_id = $this->getTeacherId();
        $this->db->query(
          "INSERT INTO ktp_typical_info(kttiname, kttiform, kttidesc, teacher_id)
           VALUES ('$kttiname', '$kttiform', '$kttidesc', '$teacher_id')"
        );
        $ktp_typical_info_id = $this->db->last_id();

        if(isset($_POST['kttopic']) && count($_POST['kttopic']) > 0) {
          $kttnumber = 0;
          foreach($_POST['kttopic'] as $n => $rec) {
            if(trim($rec)=="") { continue; }
            $kttnumber++;
            $kttopic = $this->db->safe($rec);
            $id = $this->db->safe($n);
            $ktcolor = $this->db->safe($_POST['ktcolor'][$n]);

            $sql = "INSERT INTO ktp_typical (ktp_typical_info_id, kttnumber, ktttopic, kttcolor)
                    VALUES ('$ktp_typical_info_id', '$kttnumber', '$kttopic', '$ktcolor')";
            $this->db->query($sql);
          }
        }
        if(isset($_POST['newkttopic']) && count($_POST['newkttopic']) > 0) {
          foreach($_POST['newkttopic'] as $n => $rec) {
            if(trim($rec)=="") { continue; }
            $kttnumber++;
            $kttopic = $this->db->safe($rec);
            $id = $this->db->safe($n);
            $ktcolor = $this->db->safe($_POST['newktcolor'][$n]);

            $sql = "INSERT INTO ktp_typical (ktp_typical_info_id, kttnumber, ktttopic, kttcolor)
                    VALUES ('$ktp_typical_info_id', '$kttnumber', '$kttopic', '$ktcolor')";
            $this->db->query($sql);
          }
        }

        $this->view->message('updated');
        header("Location: /ktp/viewtypical/$ktp_typical_info_id");
        return;
      }

      $subject_id = $this->db->safe($_POST['subject_id']);
      $form_id = $this->db->safe($_POST['form_id']);

      $sbname = $this->db->scalar(
        "SELECT sbname FROM subjects WHERE id = '$subject_id'"
      );
      $this->view->assign('sbname', $sbname);

      $fmname = (int)$this->db->scalar(
        "SELECT fmname FROM forms WHERE id = '$form_id'"
      );
      $this->view->assign('fmname', $fmname);

      $this->query('_ktp',
        "SELECT * FROM ktp WHERE subject_id = '$subject_id' ORDER BY ktdate"
      );

      $this->view->show('ktp.typical.create');
    }

    function fill($imported = false) {
      $this->allow(1);

        $user_id = $this->user_id;
        $teacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");

        $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                   (isset($_POST['subject_id']) ?
                   $this->db->scalar("SELECT form_id FROM subjects WHERE id = '".
                                     $this->db->safe($_POST['subject_id'])."'") :
                   $this->db->scalar("SELECT form_id FROM subjects LEFT JOIN forms ON
                                      forms.id = subjects.form_id WHERE teacher_id = '$teacher_id'
                                      ORDER BY fmname+0 LIMIT 1"));
        $this->view->assign('form_id', $form_id);

        $subject_id = isset($_POST['subject_id']) ? $this->db->safe($_POST['subject_id']) :
                      $this->db->scalar("SELECT id FROM subjects WHERE teacher_id = '$teacher_id'
                                         AND form_id = '$form_id'
                                         ORDER BY sbname LIMIT 1");
        $this->view->assign('subject_id', $subject_id);

        if(isset($_POST['save']) || isset($_POST['saveastypical'])) {
          if(isset($_POST['kttopic']) && count($_POST['kttopic']) > 0) {
              foreach($_POST['kttopic'] as $n => $rec) {
                  $kttopic = $this->db->safe($rec);
                  $id = $this->db->safe($n);
                  $ktcolor = $this->db->safe($_POST['ktcolor'][$n]);

                  if(trim($_POST['ktdate'][$n])=='') {
                    $sql = "DELETE FROM ktp WHERE id = '$id'";
                  } else {
                    $ktdate = CUtilities::dateToTimestamp($_POST['ktdate'][$n]);
                    $sql = "UPDATE ktp SET kttopic = '$kttopic', ktdate = '$ktdate', ktcolor = '$ktcolor'
                            WHERE id = '$id'";
                  }
                  $this->db->query($sql);
              }
          }
            if(isset($_POST['newkttopic']) && count($_POST['newkttopic']) > 0) {
                foreach($_POST['newkttopic'] as $n => $rec) {
                    $kttopic = $this->db->safe($rec);
                    $ktcolor = $this->db->safe($_POST['newktcolor'][$n]);

                    $ktdate = CUtilities::dateToTimestamp($_POST['newktdate'][$n]);

                    $sql = "INSERT INTO ktp (kttopic, ktdate, subject_id, ktcolor)
                            VALUES ('$kttopic', '$ktdate', '$subject_id', '$ktcolor')";
                    $this->db->query($sql);
                }
            }

            if(!isset($_POST['saveastypical'])) {
              $this->view->message('updated');
            }
        }

        if(isset($_POST['saveastypical'])) {
          $this->createtypical();
          return;
        }

        $sql = "SELECT form_id AS id, fmname FROM subjects AS s LEFT JOIN forms AS f
                ON s.form_id = f.id WHERE teacher_id = '$teacher_id' GROUP BY form_id
                ORDER BY fmname+0";
        $data = $this->db->query($sql);
        $this->view->assign('_forms', $data);

        $sql = "SELECT id, sbname FROM subjects WHERE teacher_id = '$teacher_id' AND
                form_id = '$form_id' ORDER BY sbname";
        $data = $this->db->query($sql);
        $this->view->assign('_subjects', $data);

        $sql = "SELECT * FROM ktpdayoff";
        $data = $this->db->query($sql);
        $this->view->assign('_dayoffs', $data);

        $sql = "SELECT * FROM ktp WHERE subject_id = '$subject_id' ORDER BY ktdate";
        $data = $this->db->query($sql);
        $this->view->assign('_ktp', $data);
        if(count($data) == 0 && !$imported) {
          $this->view->message('auto');
        }

        // get days of the week
        $sql = "SELECT ttday FROM timetable WHERE subject_id = '$subject_id'";
        $data = $this->db->query($sql);
        $this->view->assign('_days', $data);

        $this->q('ktptypical',
          "SELECT c.id AS kttiid, kttiform, kttiname, tcname FROM ktp_typical_info AS c
           INNER JOIN teachers AS t ON t.id = c.teacher_id
           ORDER BY kttiform+0,kttiform,kttiname,tcname"
        );

        $this->view->show('ktp.fill');
    }

    function import() {
      $this->allow(1);

      if(!isset($_POST['ktp_typical_info_id'])) { // after importing to usual fill
        print '<form action="/ktp/fill" method="post">';
        if(isset($_POST['form_id'])) {
          print '<input type="hidden" name="form_id" value="'.$_POST['form_id'].'" />';
        }
        if(isset($_POST['subject_id'])) {
          print '<input type="hidden" name="subject_id" value="'.$_POST['subject_id'].'" />';
        }
        print '</form>';
        print '<script> document.forms[0].submit(); </script>';
        return;
      }

      $user_id = $this->user_id;
      $teacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");

      $form_id = $this->p('form_id');
      $subject_id = $this->p('subject_id');
      $ktp_typical_info_id = $this->p('ktp_typical_info_id');

      $this->view->assign('ktp_import', 1);

      $this->q('impktp',
        "SELECT ktttopic, kttcolor FROM ktp_typical WHERE ktp_typical_info_id = '$ktp_typical_info_id'
         ORDER BY kttnumber"
      );

      $this->view->message('imported');
      $this->fill(true);
    }

    function setup() {
      $this->allow(99);

        if(!isset($_SESSION['ktpfrommonth'])) { $_SESSION['ktpfrommonth'] = 8; }
        if(!isset($_SESSION['ktpfromyear'])) { $_SESSION['ktpfromyear'] = date('m') >= 8 ? date('Y') : date('Y') - 1; }
        if(!isset($_SESSION['ktptomonth'])) { $_SESSION['ktptomonth'] = 4; }
        if(!isset($_SESSION['ktptoyear'])) { $_SESSION['ktptoyear'] = date('m') >= 8 ? date('Y')+1 : date('Y'); }

        if(isset($_POST['frommonth'])) {
            $_SESSION['ktpfrommonth'] = $_POST['frommonth'];
            $_SESSION['ktpfromyear'] = $_POST['fromyear'];
            $_SESSION['ktptomonth'] = $_POST['tomonth'];
            $_SESSION['ktptoyear'] = $_POST['toyear'];
        }

        $datestart = mktime(0,0,0,$_SESSION['ktpfrommonth'], 1, $_SESSION['ktpfromyear']);
        $dateend = mktime(0,0,0,$_SESSION['ktptomonth'], 1, $_SESSION['ktptoyear']);

        if(isset($_POST['vac']) && count($_POST['vac'])>0) {
            $this->db->query("DELETE FROM ktpdayoff WHERE kdotype = 1 AND kdodate >= $datestart AND kdodate <= $dateend");
            $sql = "INSERT INTO ktpdayoff (kdodate, kdotype) VALUES ";
            foreach($_POST['vac'] as $rec) {
                $sql .= "('$rec', 1),";
            }
            $sql = rtrim($sql, ',');
            $this->db->query("$sql");
        }
        if(isset($_POST['off']) && count($_POST['off'])>0) {
            $this->db->query("DELETE FROM ktpdayoff WHERE kdotype = 0 AND kdodate >= $datestart AND kdodate <= $dateend");
            $sql = "INSERT INTO ktpdayoff (kdodate, kdotype) VALUES ";
            $was = false;
            foreach($_POST['off'] as $rec) {
                if(date('N', (int)$rec) < 7) {
                    $sql .= "('$rec', 0),";
                    $was = true;
                }
            }
            $sql = rtrim($sql, ',');
            if($was) {
                $this->db->query("$sql");
            }
        }

        $sql = "SELECT * FROM ktpdayoff";
        $data = $this->db->query("$sql");

        $this->view->assign('_data', $data);
        $this->view->show('ktp.setup');
    }

    function dates() {
      $this->allow(1);

        if(!isset($_SESSION['ktpfrommonth'])) { $_SESSION['ktpfrommonth'] = 8; }
        if(!isset($_SESSION['ktpfromyear'])) { $_SESSION['ktpfromyear'] = date('m') >= 8 ? date('Y') : date('Y') - 1; }
        if(!isset($_SESSION['ktptomonth'])) { $_SESSION['ktptomonth'] = 4; }
        if(!isset($_SESSION['ktptoyear'])) { $_SESSION['ktptoyear'] = date('m') >= 8 ? date('Y')+1 : date('Y'); }

        if(isset($_POST['frommonth'])) {
            $_SESSION['ktpfrommonth'] = $_POST['frommonth'];
            $_SESSION['ktpfromyear'] = $_POST['fromyear'];
            $_SESSION['ktptomonth'] = $_POST['tomonth'];
            $_SESSION['ktptoyear'] = $_POST['toyear'];
        }

        $sql = "SELECT * FROM ktpdayoff";
        $data = $this->db->query($sql);

        $this->view->assign('_data', $data);
        $this->view->show('ktp.dates');
    }

    function vacations() {
      $this->allow(99);

      $sql = 'SELECT * FROM ktp_vacations';
      $data = $this->db->query($sql);
      $this->view->assign("vacations", isset($data[0]) ? $data[0] : null);

      $this->view->show('ktp.vacations');
    }

    function setvacations() {
      $this->allow(99);

      $fststart = $this->p('fststart');
      $fstend = $this->p('fstend');
      $secstart = $this->p('secstart');
      $secend = $this->p('secend');
      $thrstart = $this->p('thrstart');
      $thrend = $this->p('thrend');
      $foustart = $this->p('foustart');
      $fouend = $this->p('fouend');

      // vacations update
      $sql = "DELETE FROM ktp_vacations";
      $this->db->query($sql);

      $sql = "INSERT INTO ktp_vacations (fststart, fstend, secstart, secend, thrstart, thrend, foustart, fouend)
              VALUES ('$fststart', '$fstend', '$secstart', '$secend', '$thrstart', '$thrend', '$foustart', '$fouend')";
      $this->db->query($sql);

      // calendar update
      for($i = $fstend + 86400; $i < $secstart; $i+=86400) {
        $this->db->query("DELETE FROM ktpdayoff WHERE kdodate='$i'");
        $this->db->query("INSERT INTO ktpdayoff (kdodate, kdotype) VALUES ('$i', '1')");
      }

      for($i = $secend + 86400; $i < $thrstart; $i+=86400) {
        $this->db->query("DELETE FROM ktpdayoff WHERE kdodate='$i'");
        $this->db->query("INSERT INTO ktpdayoff (kdodate, kdotype) VALUES ('$i', '1')");
      }

      for($i = $thrend + 86400; $i < $foustart; $i+=86400) {
        $this->db->query("DELETE FROM ktpdayoff WHERE kdodate='$i'");
        $this->db->query("INSERT INTO ktpdayoff (kdodate, kdotype) VALUES ('$i', '1')");
      }

      $this->view->message('saved');
      $this->view->go('/ktp/vacations');
    }
}

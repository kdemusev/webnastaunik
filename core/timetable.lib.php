<?php

include ('base.lib.php');

class CTimetable extends CBase {

    function __construct($db, $view) {
        parent::__construct($db, $view);

        $this->actions = array('compose',
                               'viewbyteacher',
                               'view',
                               'clean',
                               'approve');
    }

    function viewbyteacher() {
      $this->allow(1);

        $school_id = $this->getSchoolId();
        $user_id = $this->user_id;
        $teacher_id = isset($_POST['teacher_id']) ? $this->db->safe($_POST['teacher_id']) : 0;

        $iam_teacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");
        $teacher_id = ($teacher_id == 0 && $iam_teacher_id) ? $iam_teacher_id : $teacher_id;

        $this->view->assign('teacher_id', $teacher_id);

        $sql = "SELECT id, tcname FROM teachers WHERE school_id = '$school_id'
                ORDER BY tcname";
        $data = $this->db->query($sql);
        $this->view->assign('_teachers', $data);

        if($teacher_id > 0) {
            $data = $this->db->query("SELECT sbname, fmname, ttday, ttnumber FROM
                                      timetable AS tt LEFT JOIN forms AS f ON
                                      tt.form_id = f.id LEFT JOIN subjects AS s
                                      ON s.id = subject_id WHERE teacher_id = '$teacher_id'");
            $this->view->assign('_timetable', $data);
        }

        // detect daypart
        $sql = "SELECT MIN(ttnumber) FROM timetable AS tt
                INNER JOIN subjects AS s ON tt.subject_id = s.id
                WHERE teacher_id = '$teacher_id'";
        $minnumber = $this->db->scalar($sql) < 5 ? 0 : 5;
        $sql = "SELECT MAX(ttnumber) FROM timetable AS tt
                INNER JOIN subjects AS s ON tt.subject_id = s.id
                WHERE teacher_id = '$teacher_id'";
        $maxnumber = $this->db->scalar($sql) > 6 ? 12 : 7;

        $this->view->assign('minnumber', $minnumber);
        $this->view->assign('maxnumber', $maxnumber);

        $this->view->show('timetable.viewbyteacher');
    }

    function view() {
      $this->allow(1);

      $school_id = $this->getSchoolId();
      $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
      $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                         ORDER BY fmname+0 LIMIT 1");
      $this->view->assign('form_id', $form_id);

      $daypart = $this->db->scalar("SELECT MAX(ttnumber) FROM timetable WHERE form_id = '$form_id'") > 6 ? 2 : 1;
      $this->view->assign('daypart', $daypart);

      $data = $this->db->query("SELECT id, fmname FROM forms WHERE school_id = '$school_id'
                                ORDER BY fmname+0");
      array_unshift($data, array('id' => 0, 'fmname' => 'Все классы'));
      $this->view->assign('_forms', $data);

      if($form_id > 0) {
        $data = $this->db->query("SELECT sbname, fmname, ttday, ttnumber, tcname, sbrating FROM
                                  timetable AS tt LEFT JOIN forms AS f ON
                                  tt.form_id = f.id LEFT JOIN subjects AS s
                                  ON s.id = subject_id LEFT JOIN teachers AS t ON
                                  s.teacher_id = t.id WHERE s.form_id = '$form_id'");
        $this->view->assign('_timetable', $data);
      } else {
        $timetable = array();
        $sql = "SELECT id, fmname FROM forms WHERE school_id = '$school_id'
                ORDER BY fmname+0, fmname";
        $forms = $this->db->query($sql);

        foreach($forms as $rec) {
          $ttform_id = $rec['id'];
          $daypart = $this->db->scalar("SELECT MAX(ttnumber) FROM timetable WHERE form_id = '$ttform_id'") > 6 ? 2 : 1;
          $data = $this->db->query("SELECT sbname, sbrating, fmname, ttday, ttnumber, tcname FROM
                                  timetable AS tt LEFT JOIN forms AS f ON
                                  tt.form_id = f.id LEFT JOIN subjects AS s
                                  ON s.id = subject_id LEFT JOIN teachers AS t ON
                                  s.teacher_id = t.id WHERE s.form_id = '$ttform_id'
                                  ORDER BY ttday, ttnumber");
          foreach($data as $r2) {
            if($daypart == 2) {
              $r2['ttnumber'] -= 5;
            }
            $timetable[$ttform_id][$r2['ttday']][$r2['ttnumber']][] = $r2;
          }
        }
        $this->view->assign('_timetable', $timetable);
        $this->view->assign('_ttforms', $forms);
      }

      $this->view->show('timetable.view');

    }

    function clean() {
      $this->allow(87);

      $school_id = $this->getSchoolId();

      $sql = "DELETE FROM timetable WHERE form_id IN (SELECT id FROM forms WHERE school_id = '$school_id')";
      $this->db->query($sql);

      $this->view->message('cleaned');

      $this->compose();
    }

    function compose() {
      $this->allow(87);

        $school_id = $this->getSchoolId();
        $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
        $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                           ORDER BY fmname+0 LIMIT 1");
        $this->view->assign('form_id', $form_id);

        $daypart_id = isset($_POST['daypart_id']) ? $this->db->safe($_POST['daypart_id']) :
          ($this->db->scalar("SELECT MAX(ttnumber) FROM timetable WHERE form_id = '$form_id'") > 6 ? 2 : 1);
        $this->view->assign('daypart_id', $daypart_id);

        if(isset($_POST['save'])) {
            $sql = "DELETE FROM timetable WHERE form_id = '$form_id'";
            $this->db->query($sql);

            if(isset($_POST['timetable']) && count($_POST['timetable']) > 0) {
                foreach($_POST['timetable'] as $day => $arr) {
                    foreach($arr as $n => $subjects_id) {
                      foreach($subjects_id as $subject_id) {
                        $subject_id = $this->db->safe($subject_id);
                        $ttday = $this->db->safe($day);
                        $ttnumber = $this->db->safe($n);

                        $sql = "INSERT INTO timetable (form_id, ttday, ttnumber, subject_id)
                                VALUES ('$form_id', '$ttday', '$ttnumber', '$subject_id')";
                        $this->db->query($sql);
                      }
                    }
                }
            }
            if(isset($_POST['ttremove'])&& count($_POST['ttremove']) > 0) {
                foreach($_POST['ttremove'] as $id) {
                    $id = $this->db->safe($id);

                    $sql = "DELETE FROM timetable WHERE id = '$id'";
                    $this->db->query($sql);
                }
            }

            // change ktp and notify about changes
            $sql = "SELECT DISTINCT subject_id, u.id AS user_id FROM timetable AS t
                    INNER JOIN subjects AS s ON t.subject_id = s.id LEFT JOIN users as u ON s.teacher_id = u.teacher_id
                    WHERE t.form_id = '$form_id' ORDER BY subject_id";
            $data = $this->db->query($sql);
            foreach($data as $rec) {
              if($rec['user_id'] && $rec['user_id'] != '') {
                $ktp_user_id = $rec['user_id'];
                $subject_id = $rec['subject_id'];

                $curyear = date('n') >= 8 ? date('Y') : (date('Y')-1);
                $date = time();
                $datestart = mktime(0,0,0,9,1,$curyear);
                if($date > $datestart) {
                  $datestart = mktime(0, 0, 0,
                                    date('n', $date), date('j', $date), date('Y', $date));
                }

                $dateend = mktime(0,0,0,5,31,$curyear+1);

                // this teaching year
                $sql = "(SELECT * FROM ktp WHERE subject_id = '$subject_id'
                        AND ktdate >= $datestart
                        ORDER BY ktdate)
                        UNION
                        (SELECT * FROM ktp WHERE subject_id = '$subject_id'
                        AND ktdate = 0
                        ORDER BY id)";
                $ktp = $this->db->query($sql);
                if(count($ktp) == 0) {
                  // new teaching year
                  $sql = "(SELECT * FROM ktp WHERE subject_id = '$subject_id'
                          AND ktdate != 0 ORDER BY ktdate)
                          UNION
                          (SELECT * FROM ktp WHERE subject_id = '$subject_id'
                          AND ktdate = 0
                          ORDER BY id)";
                  $ktp = $this->db->query($sql);
                  if(count($ktp) == 0) {
                    $this->notify($ktp_user_id, 'Ваше расписание занятий изменено', '/timetable/viewbyteacher');
                    continue;
                  }
                }

                $sql = "SELECT ttday FROM timetable WHERE subject_id = '$subject_id'";
                $data_days = $this->db->query($sql);

                $sql = "SELECT * FROM ktpdayoff WHERE kdodate >= $datestart AND kdodate <= $dateend";
                $dayoffs = $this->db->query($sql);

                $offs = array();
                foreach($dayoffs as $rec) {
                  $offs[] = $rec['kdodate'];
                }

                $days = array();
                foreach($data_days as $rec) {
                  $days[] = $rec['ttday'] + 1;
                }
                $countindays = array_count_values($days);
                $ktp_i = 0;
                $begincountinday = 0;
                while((date('n', $datestart)+date('Y', $datestart)*100) <= (date('n', $dateend)+date('Y', $dateend)*100)) {
                  if(!in_array($datestart, $offs) && in_array(date('w',$datestart), $days)) {
                    if(isset($ktp[$ktp_i])) {
                      $ktp_id = $ktp[$ktp_i]['id'];
                      $sql = "UPDATE ktp SET ktdate = '$datestart' WHERE id = '$ktp_id'";
                    } else {
                      $sql = "INSERT INTO ktp (ktdate, subject_id) VALUES ('$datestart', '$subject_id')";
                    }
                    $this->db->query($sql);
                    $ktp_i++;
                  }
                  $begincountinday++;
                  if(!isset($countindays[date('w', $datestart)]) ||
                    $begincountinday >= $countindays[date('w', $datestart)]) {
                    $datestart += 86400;
                    $begincountinday=0;
                  }
                }

                while(isset($ktp[$ktp_i])) {

                  $ktp_id = $ktp[$ktp_i]['id'];
                  $sql = "SELECT kttopic FROM ktp WHERE id = '$ktp_id'";
                  if(trim($this->db->scalar($sql)) == '') {
                    $sql = "DELETE FROM ktp WHERE id = '$ktp_id'";
                  } else {
                    $sql = "UPDATE ktp SET ktdate = '$datestart' WHERE id = '$ktp_id'";
                  }
                  $this->db->query($sql);
                  $ktp_i++;
                  $datestart += 86400;
                }

                $this->notify($ktp_user_id, 'Ваше расписание занятий и соответствующее планирование изменено с сегодняшнего дня', '/timetable/viewbyteacher');

              }

            }

            $this->view->message('updated');
        }

        $data = $this->db->query("SELECT * FROM forms WHERE school_id = '$school_id'
                                  ORDER BY fmname+0");
        $this->view->assign('_forms', $data);

        $data = array();
        $data[0]['id'] = '1';
        $data[0]['dpname'] = 'Первая смена';
        $data[2]['id'] = '2';
        $data[2]['dpname'] = 'Вторая смена';
        $this->view->assign('_dayparts', $data);


        $data = $this->db->query("SELECT id, tcname FROM teachers WHERE school_id = '$school_id'");
        $this->view->assign('_teachers', $data);

        $data = $this->db->query("SELECT s.id, sbname, sbhours, sbrating, tcname, teacher_id
                                  FROM subjects AS s LEFT JOIN teachers AS t
                                  ON t.id = s.teacher_id WHERE form_id = '$form_id'");
        $this->view->assign('_subjects', $data);

        // teachers timetable in other forms
        $data = $this->db->query("SELECT tt.id AS id, teacher_id, fmname,
                                  ttday, ttnumber FROM
                                  timetable AS tt LEFT JOIN forms AS f ON
                                  tt.form_id = f.id LEFT JOIN subjects AS s
                                  ON s.id = subject_id WHERE school_id = '$school_id' AND
                                  tt.form_id != '$form_id'");

        $this->view->assign('_teacherstt', $data);

        $data = $this->db->query("SELECT * FROM timetable WHERE form_id = '$form_id'");
        $this->view->assign('_timetable', $data);

        $this->view->show('timetable.compose');
    }

    function approve() {
      $this->allow(87);

      $school_id = $this->getSchoolId();
      $sql = "SELECT id FROM forms WHERE school_id = '$school_id'";
      $forms_data = $this->db->query($sql);

      foreach($forms_data as $rec) {
        $form_id = $rec['id'];

        // change ktp and notify about changes
        $sql = "SELECT DISTINCT subject_id, u.id AS user_id FROM timetable AS t
                INNER JOIN subjects AS s ON t.subject_id = s.id LEFT JOIN users as u ON s.teacher_id = u.teacher_id
                WHERE t.form_id = '$form_id' ORDER BY subject_id";
        $data = $this->db->query($sql);
        foreach($data as $rec) {
          if($rec['user_id'] && $rec['user_id'] != '') {
            $ktp_user_id = $rec['user_id'];
            $subject_id = $rec['subject_id'];

            $curyear = date('Y');
            if(date('n') >= 8) {
              $datestart = mktime(0,0,0,9,1,$curyear);
              $dateend = mktime(0,0,0,5,31,$curyear+1);
            } else {
              $datestart = mktime(0,0,0,1,1,$curyear);
              $dateend = mktime(0,0,0,5,31,$curyear);
            }

            // this teaching year
            $sql = "(SELECT * FROM ktp WHERE subject_id = '$subject_id'
                    AND ktdate >= $datestart
                    ORDER BY ktdate)
                    UNION
                    (SELECT * FROM ktp WHERE subject_id = '$subject_id'
                    AND ktdate = 0
                    ORDER BY id)";
            $ktp = $this->db->query($sql);
            if(count($ktp) == 0) {
              // new teaching year
              $sql = "(SELECT * FROM ktp WHERE subject_id = '$subject_id'
                      AND ktdate != 0 ORDER BY ktdate)
                      UNION
                      (SELECT * FROM ktp WHERE subject_id = '$subject_id'
                      AND ktdate = 0
                      ORDER BY id)";
              $ktp = $this->db->query($sql);
              if(count($ktp) == 0) {
                $this->notify($ktp_user_id, 'Ваше расписание занятий изменено', '/timetable/viewbyteacher');
                continue;
              }
            }

            $sql = "SELECT ttday FROM timetable WHERE subject_id = '$subject_id'";
            $data_days = $this->db->query($sql);

            $sql = "SELECT * FROM ktpdayoff WHERE kdodate >= $datestart AND kdodate <= $dateend";
            $dayoffs = $this->db->query($sql);

            $offs = array();
            foreach($dayoffs as $rec) {
              $offs[] = $rec['kdodate'];
            }

            $days = array();
            foreach($data_days as $rec) {
              $days[] = $rec['ttday'] + 1;
            }
            $countindays = array_count_values($days);
            $ktp_i = 0;
            $begincountinday = 0;
            while((date('n', $datestart)+date('Y', $datestart)*100) <= (date('n', $dateend)+date('Y', $dateend)*100)) {
              if(!in_array($datestart, $offs) && in_array(date('w',$datestart), $days)) {
                if(isset($ktp[$ktp_i])) {
                  $ktp_id = $ktp[$ktp_i]['id'];
                  $sql = "UPDATE ktp SET ktdate = '$datestart' WHERE id = '$ktp_id'";
                } else {
                  $sql = "INSERT INTO ktp (ktdate, subject_id) VALUES ('$datestart', '$subject_id')";
                }
                $this->db->query($sql);
                $ktp_i++;
              }
              $begincountinday++;
              if(!isset($countindays[date('w', $datestart)]) ||
                $begincountinday >= $countindays[date('w', $datestart)]) {
                $datestart += 86400;
                $begincountinday=0;
              }
            }

            while(isset($ktp[$ktp_i])) {

              $ktp_id = $ktp[$ktp_i]['id'];
              $sql = "SELECT kttopic FROM ktp WHERE id = '$ktp_id'";
              if(trim($this->db->scalar($sql)) == '') {
                $sql = "DELETE FROM ktp WHERE id = '$ktp_id'";
              } else {
                $sql = "UPDATE ktp SET ktdate = '$datestart' WHERE id = '$ktp_id'";
              }
              $this->db->query($sql);
              $ktp_i++;
              $datestart += 86400;
            }

            $this->notify($ktp_user_id, 'Ваше расписание занятий и соответствующее планирование изменено с начала текущего полугодия', '/timetable/viewbyteacher');

          }

        }
      }

    $this->compose();

    }
}

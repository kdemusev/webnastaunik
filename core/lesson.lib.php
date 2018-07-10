<?php

include ('base.lib.php');

class CLesson extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('choose', 'confirm', 'plan', 'confirmplan');
  }

  function index($curlesson = 0) {
    $this->allow(1);

    if($curlesson == 0) {
      $teacher_id = $this->getTeacherId();
      $now = mktime(date('G'), date('i'), date('s'), 1, 1, 1970);
      $day = date('w') - 1;
      $sql = "SELECT subject_id FROM timetable AS tt INNER JOIN subjects AS s
              ON tt.subject_id = s.id WHERE s.teacher_id = '$teacher_id' AND
              ttend >= $now AND ttday = '$day' ORDER BY ttstart LIMIT 1";
      $data = $this->db->query($sql);
      if(count($data) > 0) {
        $today = mktime(0,0,0);
        $sql = "SELECT id FROM ktp WHERE subject_id = '".$data[0]['subject_id']."' AND ktdate = '$today'";
        $data = $this->db->query($sql);
        if(count($data) > 0) {
          $ktp_id = $data[0]['id'];
        } else {
          print '<form id="postform" action="/lesson/choose"><input type="hidden" name="subject_id" value="'.$data[0]['subject_id'].'"/>';
          print '<script>document.getElementById(\'postform\').submit()</script>';
          return;
        }
      } else {
        $this->choose();
        return;
      }
    } else {
      $ktp_id = $curlesson;
    }

    $subject_id = $this->db->scalar("SELECT subject_id FROM ktp WHERE id = '$ktp_id'");

    $sql = "SELECT id AS ktp_id, kttopic, ktnum, ktdate FROM ktp WHERE id = '$ktp_id'";
    $data = $this->db->query($sql);
    $ktdate = $data[0]['ktdate'];
    $this->view->assign('_ktp', $data[0]);

    $sql = "SELECT id AS ktp_id, kttopic, ktnum, ktdate FROM ktp
            WHERE subject_id = '$subject_id' AND ktdate > '$ktdate' ORDER BY ktdate LIMIT 1";
    $data = $this->db->query($sql);
    $nextktp_id = $data[0]['ktp_id'];
    $this->view->assign('_ktpnext', $data[0]);

    $sql = "SELECT s.id AS id, sbname, fmname, form_id FROM subjects AS s LEFT JOIN forms AS f
            ON s.form_id = f.id WHERE s.id = '$subject_id'";
    $data = $this->db->query($sql);
    $form_id = $data[0]['form_id'];
    $this->view->assign('_subject', $data[0]);

    $sql = "SELECT p.id AS pupil_id, ppname, pppriority, jrmark FROM pupils AS p LEFT JOIN
            (SELECT jrmark, pupil_id FROM journal WHERE ktp_id = '$ktp_id') AS j
            ON p.id = j.pupil_id
            WHERE form_id = '$form_id'
            ORDER BY pppriority";
    $data = $this->db->query($sql);
    $pupil_in = '';
    foreach($data as $rec) {
      $pupil_in .= $rec['pupil_id'].',';
    }
    $pupil_in .= '0';
    $this->view->assign('pupils', $data);

    $sql = "SELECT * FROM lessons WHERE ktp_id = '$ktp_id'";
    $data = $this->db->query($sql);
    if(count($data) > 0) {
      $this->view->assign('lesson', $data[0]);
    } else {
      $this->view->assign('lesson', array('lptext'=>'','lphometask'=>'','lpnotes'=>''));
    }

    $sql = "SELECT * FROM lessons WHERE ktp_id = '$nextktp_id'";
    $data = $this->db->query($sql);
    if(count($data) > 0) {
      $this->view->assign('nextlesson', $data[0]);
    } else {
      $this->view->assign('nextlesson', array('lptext'=>'','lphometask'=>'','lpnotes'=>''));
    }

    // rating
    $sql = "SELECT * FROM ratingcriteria WHERE subject_id = '$subject_id'
            ORDER BY rcpriority";
    $data = $this->db->query($sql);
    //make in
    $rc_in = '';
    $maxrating = 0;
    foreach($data as $rec) {
      $rc_in .= $rec['id'].',';
      $maxrating += $rec['rcrating'];
    }
    $rc_in .= '0';
    $this->view->assign('rc', $data);
    $this->view->assign('maxrating', $maxrating);

    $sql = "SELECT * FROM rating WHERE rc_id IN ($rc_in) AND pupil_id IN ($pupil_in)";
    $data = $this->db->query($sql);
    $rating = array();
    foreach($data as $rec) {
      $rating[$rec['pupil_id']][$rec['rc_id']] = $rec['rating'];
    }
    $this->view->assign('rating', $rating);

    // time last to the lesson's over
    $now = mktime(date('G'), date('i'), date('s'), 1, 1, 1970);
    $day = date('w') - 1;
    $sql = "SELECT ttstart, ttend FROM timetable AS tt
            WHERE tt.subject_id = '$subject_id' AND
            ttstart <= $now AND ttend >= $now AND ttday = '$day'";
    $data = $this->db->query($sql);
    if(count($data) > 0) {
      $timerLast = $data[0]['ttend'] - $now;
      $this->view->assign('timerLast', $timerLast);
      $this->view->assign('_now', true);
    } else {
      $this->view->assign('_now', false);
    }

    $this->view->show('lesson');
  }

  function choose($forplan = false) {
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

    $sql = "SELECT * FROM ktp WHERE subject_id = '$subject_id'";
    $data = $this->db->query($sql);
    $this->view->assign('_ktp', $data);

    $this->view->assign('forplan', $forplan);

    $this->view->show('lesson.choose');
  }

  function plan() {
    $this->allow(1);

    $this->choose(true);
  }

  function confirm() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $this->index($id);
  }

  function confirmplan() {
    $this->allow(1);

    $ktp_id = $this->db->safe($_GET['id']);

    $subject_id = $this->db->scalar("SELECT subject_id FROM ktp WHERE id = '$ktp_id'");

    $sql = "SELECT id AS ktp_id, kttopic, ktnum, ktdate FROM ktp WHERE id = '$ktp_id'";
    $data = $this->db->query($sql);
    $this->view->assign('_ktpnext', $data[0]);

    $sql = "SELECT sbname, fmname, form_id FROM subjects AS s LEFT JOIN forms AS f
            ON s.form_id = f.id WHERE s.id = '$subject_id'";
    $data = $this->db->query($sql);
    $this->view->assign('_subject', $data[0]);

    $sql = "SELECT * FROM lessons WHERE ktp_id = '$ktp_id'";
    $data = $this->db->query($sql);
    if(count($data) > 0) {
      $this->view->assign('nextlesson', $data[0]);
    } else {
      $this->view->assign('nextlesson', array('lptext'=>'','lphometask'=>'','lpnotes'=>''));
    }

    $this->view->show('lesson.plan');
  }

}

<?php

include ('base.lib.php');

class CJournal extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('view',
                             'attendance',
                             'makerating',
                             'rating', 'ratingover', 'deleterating',
                             'ratingoverconfirm');
  }

  function view() {
    $this->allow(1);

    $user_id = $this->user_id;
    $sql = "SELECT teacher_id FROM users WHERE id = '$user_id' LIMIT 1";
    $teacher_id = $this->db->scalar($sql);

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

    $sql = "SELECT id, ppname FROM pupils WHERE form_id = '$form_id'";
    $data = $this->db->query($sql);
    $this->view->assign('_pupils', $data);

    $sql = "SELECT * FROM ktp_vacations";
    $data = $this->db->query($sql);
    $this->view->assign('_vacations', isset($data[0]) ? $data[0] : null);

    $now = time();
    $sql = "SELECT id, ktdate, kttopic, ktcolor FROM ktp WHERE subject_id = '$subject_id' AND
            ktdate < $now ORDER BY ktdate";
    $data = $this->db->query($sql);
    $this->view->assign('_ktp', $data);

    $sql = "SELECT jrmark, pupil_id, k.id AS ktp_id FROM journal AS j INNER JOIN ktp AS k ON k.id = j.ktp_id
            WHERE subject_id = '$subject_id' AND ktdate < $now";
    $data = $this->db->query($sql);
    $journal = array();
    foreach($data as $rec) {
      $pupil_id = $rec['pupil_id'];
      $ktp_id = $rec['ktp_id'];
      $journal[$pupil_id][$ktp_id] = $rec['jrmark'];
    }
    $this->view->assign('_journal', $journal);

    $this->view->show('journal.view');
  }

  function attendance() {
    $this->allow(1);

    $teacher_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$teacher_id'");

    $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
        $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                           ORDER BY fmname+0, fmname LIMIT 1");
    $this->view->assign('form_id', $form_id);

    $month = isset($_POST['month']) ? $this->db->safe($_POST['month']) :
          date('n');
    $this->view->assign('month', $month);

    $year = isset($_POST['year']) ? $this->db->safe($_POST['year']) :
          date('Y');
    $this->view->assign('year', $year);

    if(isset($_POST['save'])) {
      $sql = "DELETE FROM attendance WHERE pupil_id IN
              (SELECT id FROM pupils WHERE form_id = '$form_id')
              AND atmonth = '$month' AND atyear = '$year'";
      $this->db->query($sql);

      if(isset($_POST['att'])) {
        foreach($_POST['att'] as $pupil_id => $rec) {
          foreach($rec as $atday => $atmark) {
            $pupil_id = $this->db->safe($pupil_id);
            $atmark = $this->db->safe($atmark);
            $atday = $this->db->safe($atday);

            $sql = "INSERT INTO attendance (pupil_id, atmark, atday, atmonth, atyear)
                    VALUES ('$pupil_id', '$atmark', '$atday', '$month', '$year')";
            $this->db->query($sql);
          }
        }
      }

      $this->view->message('saved');
    }

    $sql = "SELECT pupil_id, atmark, atday FROM attendance WHERE pupil_id IN
            (SELECT id FROM pupils WHERE form_id = '$form_id')
            AND atmonth = '$month' AND atyear = '$year'";
    $data = $this->db->query($sql);
    $attendance = array();
    foreach($data as $rec) {
      $pupil_id = $rec['pupil_id'];
      $mark = $rec['atmark'];
      $day = $rec['atday'];
      $attendance[$pupil_id][$day] = $mark;
    }
    $this->view->assign('att', $attendance);

    $sql = "SELECT id, fmname FROM forms
            WHERE school_id = '$school_id'
            ORDER BY fmname+0, fmname";
    $data = $this->db->query($sql);
    $this->view->assign('_forms', $data);

    $sql = "SELECT id, ppname FROM pupils WHERE form_id = '$form_id'
            ORDER BY pppriority";
    $data = $this->db->query($sql);
    $this->view->assign('pupils', $data);

    $sql = "SELECT kdodate, kdotype FROM ktpdayoff";
    $data = $this->db->query($sql);
    $ktpdayoff = array();
    foreach($data as $rec) {
      $ktpdayoff[$rec['kdodate']] = $rec['kdotype'];
    }
    $this->view->assign('ktpdayoff', $ktpdayoff);

    $sql = "SELECT scname FROM schools WHERE id = '$school_id'";
    $this->view->assign('scname', $this->db->scalar($sql));

    $sql = "SELECT fmname FROM forms WHERE id = '$form_id'";
    $this->view->assign('form', $this->db->scalar($sql));

    $this->view->show('journal.attendance');
  }

  function makerating() {
    $this->allow(1);

    $user_id = $this->user_id;
    $sql = "SELECT teacher_id FROM users WHERE id = '$user_id' LIMIT 1";
    $teacher_id = $this->db->scalar($sql);

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

    if(isset($_POST['save'])) {
      if(isset($_POST['rcname'])) {
        foreach($_POST['rcname'] as $id => $rcname) {
          $id = $this->db->safe($id);
          $rcname = $this->db->safe($rcname);

          if(trim($rcname) == '') {
            $sql = "DELETE FROM ratingcriteria WHERE id = '$id'";
            $this->db->query($sql);
            continue;
          }

          $rcrating = $this->db->safe($_POST['rcrating'][$id]);
          $rcpriority = $this->db->safe($_POST['rcpriority'][$id]);

          $sql = "UPDATE ratingcriteria SET rcname = '$rcname', rcrating = '$rcrating',
                  rcpriority = '$rcpriority'
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }

      foreach($_POST['newrcname'] as $k => $rcname) {
        $rcname = $this->db->safe($rcname);

        if(trim($rcname) == '') {
          continue;
        }

        $rcrating = $this->db->safe($_POST['newrcrating'][$k]);
        $rcpriority = $this->db->safe($_POST['newrcpriority'][$k]);

        $sql = "INSERT INTO ratingcriteria(rcname, rcrating, rcpriority, subject_id) VALUES
                ('$rcname', '$rcrating', '$rcpriority', '$subject_id')";
        $this->db->query($sql);
      }

      $this->view->message('updated');
    }

    $sql = "SELECT s.id AS subject_id, sbname, fmname FROM subjects AS s
            INNER JOIN forms AS f ON s.form_id = f.id WHERE teacher_id = '$teacher_id'
            AND s.id != '$subject_id'
            ORDER BY fmname+0, fmname, sbname";
    $data = $this->db->query($sql);
    $this->view->assign('copyfrom', $data);

    if(isset($_POST['copyfrom_id']) && $_POST['copyfrom_id'] > 0) {
      $ch_subject_id = $this->db->safe($_POST['copyfrom_id']);
      $this->view->assign('copied', 1);
    } else {
      $ch_subject_id = $subject_id;
    }

    $sql = "SELECT * FROM ratingcriteria WHERE subject_id = '$ch_subject_id'
            ORDER BY rcpriority";
    $data = $this->db->query($sql);
    $this->view->assign('rc', $data);

    $this->view->show('journal.rating.make');
  }

  function rating() {
    $this->allow(1);

    $user_id = $this->user_id;
    $sql = "SELECT teacher_id FROM users WHERE id = '$user_id' LIMIT 1";
    $teacher_id = $this->db->scalar($sql);
    $postsbj = isset($_POST['subject_id']) ? $this->db->safe($_POST['subject_id']) : (isset($_SESSION['rosubject_id']) ? $_SESSION['rosubject_id'] : 0);

    $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
               ($postsbj > 0 ?
               $this->db->scalar("SELECT form_id FROM subjects WHERE id = '".
                                 $postsbj."'") :
               $this->db->scalar("SELECT form_id FROM subjects LEFT JOIN forms ON
                                  forms.id = subjects.form_id WHERE teacher_id = '$teacher_id'
                                  ORDER BY fmname+0 LIMIT 1"));
    $this->view->assign('form_id', $form_id);

    $subject_id = isset($_POST['subject_id']) ? $this->db->safe($_POST['subject_id']) :
                  (isset($_SESSION['rosubject_id'])? $_SESSION['rosubject_id'] :
                  $this->db->scalar("SELECT id FROM subjects WHERE teacher_id = '$teacher_id'
                                     AND form_id = '$form_id'
                                     ORDER BY sbname LIMIT 1"));
    $this->view->assign('subject_id', $subject_id);

    unset($_SESSION['rusubject_id']);

    $sql = "SELECT form_id AS id, fmname FROM subjects AS s LEFT JOIN forms AS f
            ON s.form_id = f.id WHERE teacher_id = '$teacher_id' GROUP BY form_id
            ORDER BY fmname+0";
    $data = $this->db->query($sql);
    $this->view->assign('_forms', $data);

    $sql = "SELECT id, sbname FROM subjects WHERE teacher_id = '$teacher_id' AND
            form_id = '$form_id' ORDER BY sbname";
    $data = $this->db->query($sql);
    $this->view->assign('_subjects', $data);

    $sql = "SELECT id, ppname, pppriority FROM pupils WHERE form_id = '$form_id'
            ORDER BY pppriority";
    $data = $this->db->query($sql);
    //make in
    $pupil_in = '';
    foreach($data as $rec) {
      $pupil_in .= $rec['id'].',';
    }
    $pupil_in .= '0';
    $this->view->assign('pupils', $data);

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

    $this->view->show('journal.rating');
  }

  function deleterating() {
    $this->allow(1);

    // TODO safe
    $subject_id = $this->db->safe($_GET['id']);
    $_SESSION['rosubject_id'] = $subject_id;

    $sql = "DELETE FROM rating WHERE rc_id IN (SELECT id FROM ratingcriteria WHERE subject_id = '$subject_id')";
    $this->db->query($sql);

    $this->view->message('deleted');

    $this->view->go('/journal/rating');
  }

  function ratingover() {
    $this->allow(1);

    // TODO safe
    $subject_id = $this->db->safe($_GET['id']);
    $_SESSION['rosubject_id'] = $subject_id;

    $sql = "SELECT k.id AS id, ktdate,kttopic,COUNT(j.id) AS cnt FROM ktp AS k LEFT JOIN journal AS j ON j.ktp_id = k.id
            WHERE subject_id = '$subject_id' GROUP BY(ktdate) ORDER BY ktdate";
    $data = $this->db->query($sql);
    $this->view->assign('_ktp', $data);

    $this->view->show('journal.rating.choose');
  }

  function ratingoverconfirm() {
    $this->allow(1);

    $ktp_id = $this->db->safe($_GET['id']);
    $subject_id = $_SESSION['rosubject_id'];

    $maxrating = $this->db->scalar("SELECT SUM(rcrating) FROM ratingcriteria WHERE subject_id = '$subject_id'");

    $sql = "SELECT SUM(rating) AS sumrating, pupil_id FROM rating WHERE rc_id IN (SELECT id FROM ratingcriteria WHERE subject_id = '$subject_id')
            GROUP BY pupil_id";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      $jrmark = round($rec['sumrating'] / $maxrating * 10);
      $pupil_id = $rec['pupil_id'];
      $sql = "INSERT INTO journal(pupil_id, jrmark, ktp_id) VALUE ('$pupil_id', '$jrmark', '$ktp_id')";
      $this->db->query($sql);
    }

    $sql = "DELETE FROM rating WHERE rc_id IN (SELECT id FROM ratingcriteria WHERE subject_id = '$subject_id')";
    $this->db->query($sql);

    $this->view->message('marked');
    $this->view->go('/journal/rating');
  }
}

?>

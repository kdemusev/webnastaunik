<?php

include ('base.lib.php');

class CQuiz extends CBase {

  function __construct($db, $view) {
    parent::__construct($db, $view);

    $this->actions = array('showlist', 'show', 'answer', 'results', 'add',
                           'save', 'edit', 'change', 'delete', 'showiframe', 'answeriframe');
  }

  function showlist() {
    $this->allow(87);
    $user_id = $this->user_id;

    $sql = "SELECT id, qzname, qzdesc, qzonce, user_id FROM quizes WHERE user_id = '$user_id'
            ORDER BY qztime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('quizdata', $data);

    $this->view->show('quiz.list');
  }

  function showiframe() {
    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM quizes WHERE id = '$id'";
    $data = $this->db->query($sql);

    if(count($data) == 0) {
      return;
    }

    if($data[0]['qzonce'] == 0) {
      if(isset($_COOKIE['quizonce'][$id])) {
        $this->answeriframe(0);
        return;
      }
    }

    $quizdata = $data;

    $sql = "SELECT * FROM qzquestions WHERE quiz_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $i => $rec) {
      $qzquestion_id = $rec['id'];

      $sql = "SELECT COUNT(*) AS total FROM qzresults AS qr LEFT JOIN qzanswers AS qa
              ON qa.id = qr.qzanswer_id WHERE qzquestion_id = '$qzquestion_id'";
      $total = $this->db->scalar($sql);

      $sql = "SELECT qa.id AS qaid, qatext, COUNT(qr.user_id) AS cnt FROM qzanswers AS qa
              LEFT JOIN qzresults AS qr ON qa.id = qr.qzanswer_id
              WHERE qzquestion_id = '$qzquestion_id'
              GROUP BY qaid ORDER BY qa.id";

      $data2 = $this->db->query($sql);
      foreach($data2 as $j=> $rec2) {
        if($total > 0) {
          $data2[$j]['total'] = round($rec2['cnt']/$total*100);
        } else {
          $data2[$j]['total'] = 0;
        }
      }
      $data[$i]['answers'] = $data2;
    }

    $qzquestions = $data;

    include('view/quiz.showiframe.php');
  }

  function answeriframe($tosave = 1) {

    if($tosave == 1) {
      $id = $this->db->safe($_POST['quiz_id']);
      $user_id = $this->user_id;

      foreach($_POST['results'] AS $qzquestion_id => $qzanswer_id) {
        $qzanswer_id = $this->db->safe($qzanswer_id);
        $sql = "INSERT INTO qzresults (qzanswer_id, user_id) VALUES
                ('$qzanswer_id', '$user_id')";
        $this->db->query($sql);
      }

      $this->view->message('quizaccepted');
    } else {
      $id = $this->db->safe($_GET['id']);
    }

    $sql = "SELECT * FROM quizes WHERE id = '$id'";
    $quizdata = $this->db->query($sql);

    if(count($quizdata)==0) {
      // TODO set empty page for client site (and in the showiframe too)
      return;
    }

    if($quizdata[0]['qzonce'] == 0) {
      setcookie('quizonce['.$id.']', '1', time()+86400*365, '/');
    }

    $sql = "SELECT * FROM qzquestions WHERE quiz_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $i => $rec) {
      $qzquestion_id = $rec['id'];

      $sql = "SELECT COUNT(*) AS total FROM qzresults AS qr LEFT JOIN qzanswers AS qa
              ON qa.id = qr.qzanswer_id WHERE qzquestion_id = '$qzquestion_id'";
      $total = $this->db->scalar($sql);

      $sql = "SELECT qa.id AS qaid, qatext, COUNT(qr.user_id) AS cnt FROM qzanswers AS qa
              LEFT JOIN qzresults AS qr ON qa.id = qr.qzanswer_id
              WHERE qzquestion_id = '$qzquestion_id'
              GROUP BY qaid ORDER BY qa.id";

      $data2 = $this->db->query($sql);
      foreach($data2 as $j=> $rec2) {
        if($total > 0) {
          $data2[$j]['total'] = round($rec2['cnt']/$total*100);
        } else {
          $data2[$j]['total'] = 0;
        }
      }
      $data[$i]['answers'] = $data2;
    }

    $qzquestions = $data;

    include('view/quiz.answeriframe.php');
  }

  function show() {
    $this->allow(87);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM quizes WHERE id = '$id'";
    $data = $this->db->query($sql);

    if($data[0]['user_id'] != $this->user_id) {
      $this->view->page404();
      return;
    }

    $this->view->assign('quizdata', $data);

    $sql = "SELECT * FROM qzquestions WHERE quiz_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $i => $rec) {
      $qzquestion_id = $rec['id'];

      $sql = "SELECT COUNT(*) AS total FROM qzresults AS qr LEFT JOIN qzanswers AS qa
              ON qa.id = qr.qzanswer_id WHERE qzquestion_id = '$qzquestion_id'";
      $total = $this->db->scalar($sql);

      $sql = "SELECT qa.id AS qaid, qatext, COUNT(qr.user_id) AS cnt FROM qzanswers AS qa
              LEFT JOIN qzresults AS qr ON qa.id = qr.qzanswer_id
              WHERE qzquestion_id = '$qzquestion_id'
              GROUP BY qaid ORDER BY qa.id";

      $data2 = $this->db->query($sql);
      foreach($data2 as $j=> $rec2) {
        if($total > 0) {
          $data2[$j]['total'] = round($rec2['cnt']/$total*100);
        } else {
          $data2[$j]['total'] = 0;
        }
      }
      $data[$i]['answers'] = $data2;
    }

    $this->view->assign('qzquestions', $data);

    $this->view->show('quiz.show');
  }

  function answer() {
    $this->allow(87);

    $id = $this->db->safe($_POST['quiz_id']);
    $user_id = $this->user_id;

    foreach($_POST['results'] AS $qzquestion_id => $qzanswer_id) {
      $qzanswer_id = $this->db->safe($qzanswer_id);
      $sql = "INSERT INTO qzresults (qzanswer_id, user_id) VALUES
              ('$qzanswer_id', '$user_id')";
      $this->db->query($sql);
    }

    $this->view->message('quizaccepted');
    $this->view->go('/quiz/show/'.$_POST['quiz_id']);
  }

  function add() {
    $this->allow(87);

    $this->view->show('quiz.add');
  }

  function save() {
    $this->allow(87);

    $qzname = $this->db->safe($_POST['qzname']);
    $qzdesc = $this->db->safe($_POST['qzdesc']);
    $qzonce = $this->db->safe($_POST['qzonce']);
    $qzshowresults = $this->db->safe($_POST['qzshowresults']);
    $qzthank = $this->db->safe($_POST['qzthank']);
    $user_id = $this->user_id;
    $qztime = time();

    $sql = "INSERT INTO quizes(qzname, qzdesc, qzonce, qzshowresults, qzthank, user_id, qztime)
            VALUES ('$qzname', '$qzdesc', '$qzonce', '$qzshowresults', '$qzthank', '$user_id', '$qztime')";
    $this->db->query($sql);

    $quiz_id = $this->db->last_id();

    foreach($_POST['qqtext'] as $i => $qqtext) {
      $qqtext = $this->db->safe($qqtext);
      if(trim($qqtext) == '') {
        continue;
      }
      $sql = "INSERT INTO qzquestions(quiz_id, qqtext) VALUES ('$quiz_id', '$qqtext')";
      $this->db->query($sql);

      $qzquestion_id = $this->db->last_id();

      foreach($_POST['qatext'][$i] as $qatext) {
        $qatext = $this->db->safe($qatext);
        if(trim($qatext) == '') {
          continue;
        }
        $sql = "INSERT INTO qzanswers(qzquestion_id, qatext) VALUES ('$qzquestion_id', '$qatext')";
        $this->db->query($sql);
      }
    }

    $this->view->message('quizadded');
    $this->view->go('/quiz/showlist');
  }

  function edit() {
    $this->allow(87);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM quizes WHERE id = '$id'";
    $data = $this->db->query($sql);

    if($data[0]['user_id'] != $this->user_id) {
      $this->view->page404();
      return;
    }

    $this->view->assign('quizdata', $data);

    $sql = "SELECT * FROM qzquestions WHERE quiz_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $i => $rec) {
      $qzquestion_id = $rec['id'];

      $sql = "SELECT * FROM qzanswers WHERE qzquestion_id = '$qzquestion_id'";
      $data2 = $this->db->query($sql);
      $data[$i]['answers'] = $data2;
    }

    $this->view->assign('qzquestions', $data);

    $this->view->show('quiz.edit');
  }

  function change() {
    $this->allow(87);

    $id = $this->db->safe($_POST['quiz_id']);

    $sql = "SELECT user_id FROM quizes WHERE id = '$id'";
    $data = $this->db->query($sql);

    if($data[0]['user_id'] != $this->user_id) {
      $this->view->page404();
      return;
    }

    $qzname = $this->db->safe($_POST['qzname']);
    $qzdesc = $this->db->safe($_POST['qzdesc']);
    $qzonce = $this->db->safe($_POST['qzonce']);
    $qzshowresults = $this->db->safe($_POST['qzshowresults']);
    $qzthank = $this->db->safe($_POST['qzthank']);

    $sql = "UPDATE quizes SET qzname='$qzname', qzdesc='$qzdesc', qzonce='$qzonce',
            qzshowresults='$qzshowresults', qzthank='$qzthank' WHERE id = '$id'";
    $this->db->query($sql);

    foreach($_POST['qqtext'] as $i => $qqtext) {
      $qqtext = $this->db->safe($qqtext);
      if(trim($qqtext) == '') {
        $sql = "DELETE FROM qzquestions WHERE id = '$i'";
        $this->db->query($sql);
        $sql = "DELETE FROM qzanswers WHERE qzquestion_id = '$i'";
        $this->db->query($sql);
        continue;
      }
      $sql = "UPDATE qzquestions SET qqtext='$qqtext' WHERE id = '$i'";
      $this->db->query($sql);

      foreach($_POST['qatext'][$i] as $j => $qatext) {
        $qatext = $this->db->safe($qatext);
        if(trim($qatext) == '') {
          $sql = "DELETE FROM qzanswers WHERE id = '$j'";
          $this->db->query($sql);
          continue;
        }
        $sql = "UPDATE qzanswers SET qatext='$qatext' WHERE id = '$j'";
        $this->db->query($sql);
      }

      foreach($_POST['qatextnew'][$i] as $qatext) {
        $qatext = $this->db->safe($qatext);
        if(trim($qatext) == '') {
          continue;
        }
        $sql = "INSERT INTO qzanswers(qzquestion_id, qatext) VALUES ('$i', '$qatext')";
        $this->db->query($sql);
      }
    }

    foreach($_POST['qqtextnew'] as $i => $qqtext) {
      $qqtext = $this->db->safe($qqtext);
      if(trim($qqtext) == '') {
        continue;
      }
      $sql = "INSERT INTO qzquestions(quiz_id, qqtext) VALUES ('$id', '$qqtext')";
      $this->db->query($sql);

      $qzquestion_id = $this->db->last_id();

      foreach($_POST['qatextnew'][$i] as $qatext) {
        $qatext = $this->db->safe($qatext);
        if(trim($qatext) == '') {
          continue;
        }
        $sql = "INSERT INTO qzanswers(qzquestion_id, qatext) VALUES ('$qzquestion_id', '$qatext')";
        $this->db->query($sql);
      }
    }

    $this->view->message('quizchanged');
    $this->view->go('/quiz/showlist');
  }

  function delete() {
    $this->allow(87);
    
    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT user_id FROM quizes WHERE id = '$id'";
    $user_id = $this->db->scalar($sql);

    if($user_id != $this->user_id) {
      $this->view->page404();
      return;
    }

    $sql = "SELECT id FROM qzquestions WHERE quiz_id = '$id'";
    $data = $this->db->query($sql);

    foreach($data as $rec) {
      $qzquestion_id = $rec['id'];

      $sql = "SELECT id FROM qzanswers WHERE qzquestion_id = '$qzquestion_id'";
      $data2 = $this->db->query($sql);
      foreach($data2 as $rec2) {
        $qzanswer_id = $rec2['id'];

        $sql = "DELETE FROM qzresults WHERE qzanswer_id = '$qzanswer_id'";
        $this->db->query($sql);
      }

      $sql = "DELETE FROM qzanswers WHERE qzquestion_id = '$qzquestion_id'";
      $this->db->query($sql);
    }

    $sql=  "DELETE FROM qzquestions WHERE quiz_id = '$id'";
    $this->db->query($sql);

    $sql = "DELETE FROM quizes WHERE id = '$id'";
    $this->db->query($sql);

    $this->view->message('quizdeleted');

    $this->view->go('/quiz/showlist');
  }


}

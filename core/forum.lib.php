<?php

include ('base.lib.php');

class CForum extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('topics', 'newtopic', 'posts',
                             'newpost', 'file', 'filetopic',
                             'delpost', 'deltopic',
                             'edittopic', 'savetopic',
                             'editpost', 'savepost');
  }

  function index() {
    $this->allow(1);

    $sql = "SELECT * FROM fmsections ORDER BY fmscpriority";
    $data = $this->db->query($sql);
    $this->view->assign('fmsections', $data);

    $this->view->show('forum');
  }

  function newtopic() {
    $this->allow(1);

    $fmsection_id = $this->db->safe($_POST['fmsection_id']);
    $fmtpname = $this->db->safe($_POST['fmtpname']);
    $fmtpdesc = $this->db->safe($_POST['edText']);
    $fmtplast = time();
    $fmtpanonym = $this->db->safe($_POST['isanonym']);
    $user_id = $this->user_id;

    if(trim($fmtpname)=='') {
      $this->view->page404();
      return;
    }

    $sql = "INSERT INTO fmtopics (fmsection_id, fmtpname, fmtpdesc,
            fmtplast, fmtptime, user_id, fmtpanonym) VALUES ('$fmsection_id', '$fmtpname',
            '$fmtpdesc', '$fmtplast', '$fmtplast', '$user_id', '$fmtpanonym')";
    $this->db->query($sql);

    $ftpost_id = $this->db->last_id();
    if(isset($_FILES['newpostFile'])) {
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $fmflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(time());

          move_uploaded_file($tmp_name, "forumtopicfiles/$fileid");
          $sql = "INSERT INTO forumtopicfiles (fmtopic_id, fmflname, fmflsource)
                  VALUES ('$fmpost_id', '$fileid', '$fmflname')";
          $this->db->query($sql);
        }
      }
    }

    // notify every user
    $sql = "SELECT id FROM users WHERE uslogin != '01737del17'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      $user_id = $rec['id'];
      $this->notify($user_id, 'В областном педагогическом форуме появился новый вопрос', "/forum/posts/$ftpost_id");
    }

    $this->view->message('added');
    header("Location: /forum/topics/$fmsection_id");
  }

  function topics() {
    $this->allow(1);

    $fmsection_id = $this->db->safe($_GET['id']);
    $user_id = $this->user_id;

    // section info
    $sql = "SELECT * FROM fmsections WHERE id = '$fmsection_id'";
    $data = $this->db->query($sql);
    $this->view->assign('fmsection', $data[0]);

    // topics
    $sql = "SELECT *, f.id AS fmtopic_id
            FROM fmtopics AS f LEFT JOIN users AS u
            ON f.user_id = u.id WHERE fmsection_id = '$fmsection_id'
            ORDER BY fmtplast DESC";
    $data = $this->db->query($sql);
    $this->view->assign('fmtopics', $data);

    // user info for post box
    $sql = "SELECT usname FROM users WHERE id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('usinfo', $data);

    $this->view->show('forum.topics');
  }

  function newpost() {
    $this->allow(1);

    $fmtopic_id = $this->db->safe($_POST['fmtopic_id']);
    $fmtext = $this->db->safe($_POST['edText']);
    $fmpttime = time();
    $fmptanonym = $this->db->safe($_POST['isanonym']);
    $user_id = $this->user_id;

    $sql = "INSERT INTO fmposts (fmtopic_id, fmtext, user_id,
            fmpttime, fmptanonym) VALUES ('$fmtopic_id', '$fmtext', '$user_id',
            '$fmpttime', '$fmptanonym')";
    $this->db->query($sql);

    if(isset($_FILES['newpostFile'])) {
      $fmpost_id = $this->db->last_id();
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $fmflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(time());

          move_uploaded_file($tmp_name, "forumfiles/$fileid");
          $sql = "INSERT INTO forumfiles (fmpost_id, fmflname, fmflsource)
                  VALUES ('$fmpost_id', '$fileid', '$fmflname')";
          $this->db->query($sql);
        }
      }
    }

    $sql = "UPDATE fmtopics SET fmtpposts = fmtpposts + 1,
            fmtplast = '$fmpttime' WHERE id = '$fmtopic_id'";
    $this->db->query($sql);

    $this->view->message('added');

    header("Location: /forum/posts/$fmtopic_id");
  }

  function posts() {
    $this->allow(1);

    $user_id = $this->user_id;
    $fmtopic_id = $this->db->safe($_GET['id']);

    // get section info
    $sql = "SELECT fmsection_id FROM fmtopics WHERE id = '$fmtopic_id'";
    $fmsection_id = $this->db->scalar($sql);
    $sql = "SELECT * FROM fmsections WHERE id = '$fmsection_id'";
    $data = $this->db->query($sql);
    $this->view->assign('fmsection', $data);

    // update views count
    if(!isset($_SESSION['fmviewedtopic']) ||
       !in_array($fmtopic_id, $_SESSION['fmviewedtopic'])) {
      $sql = "UPDATE fmtopics SET fmtpviews = fmtpviews + 1
              WHERE id = '$fmtopic_id'";
      $this->db->query($sql);
      $_SESSION['fmviewedtopic'][] = $fmtopic_id;
    }

    // get header post
    $sql = "SELECT f.id AS fmtopic_id, fmtpname, fmtpdesc, fmtptime, scname,
            usplace, usname, user_id, fmtpanonym
            FROM fmtopics AS f LEFT JOIN users AS u
            ON f.user_id = u.id LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE f.id = '$fmtopic_id'";
    $data = $this->db->query($sql);
    $this->view->assign('fmtopic', $data);

    // files
    $sql = "SELECT id, fmflsource FROM forumtopicfiles
            WHERE fmtopic_id = '$fmtopic_id'
            ORDER BY id, fmflsource";
    $data = $this->db->query($sql);
    $this->view->assign('ftfiles', $data);

    // get other posts
    $sql = "SELECT f.id AS fmpost_id, fmtext, fmpttime, usname, usplace, scname,
            user_id, fmptanonym
            FROM fmposts AS f LEFT JOIN users AS u
            ON f.user_id = u.id LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE fmtopic_id = '$fmtopic_id'
            ORDER BY fmpttime";
    $data = $this->db->query($sql);
    $this->view->assign('fmposts', $data);

    // form ids of messages for IN clause
    $in = '';
    foreach($data as $rec) {
      $in .= $rec['fmpost_id'].',';
    }
    $in .= '0';

    // files
    $sql = "SELECT id, fmflsource, fmpost_id FROM forumfiles
            WHERE fmpost_id IN ($in)
            ORDER BY id, fmflsource";
    $data = $this->db->query($sql);
    $fmfiles = array();
    foreach($data as $rec) {
      $fmfiles[$rec['fmpost_id']][] = $rec;
    }
    $this->view->assign('fmfiles', $fmfiles);

    // user info for post box
    $sql = "SELECT usname FROM users WHERE id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('usinfo', $data);

    $this->view->show('forum.posts');
  }

  function file() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM forumfiles WHERE id = '$id'";
    $data = $this->db->query($sql);

    if(count($data) != 1) {
      $this->view->page404();
    }

    include('files.lib.php');


    if(!CFiles::download('forumfiles/'.$data[0]['fmflname'], $data[0]['fmflsource'])) {
      $this->view->page404();
    }
  }

  function filetopic() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM forumtopicfiles WHERE id = '$id'";
    $data = $this->db->query($sql);

    if(count($data) != 1) {
      $this->view->page404();
    }

    include('files.lib.php');

    if(!CFiles::download('forumtopicfiles/'.$data[0]['fmflname'], $data[0]['fmflsource'])) {
      $this->view->page404();
    }
  }

  function justDelPost($id) {
    $this->allow(1);

    $sql = "DELETE FROM fmposts WHERE id = '$id'";
    $this->db->query($sql);

    $sql = "SELECT fmflname FROM forumfiles WHERE fmpost_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      @unlink('forumfiles/'.$rec['fmflname']);
    }
    $sql = "DELETE FROM forumfiles WHERE fmpost_id = '$id'";
    $this->db->query($sql);
  }

  function delpost() {
    $this->allow(89);

    $id = $this->db->safe($_GET['id']);

    // -- fmposts
    $sql = "SELECT fmtopic_id FROM fmposts WHERE id = '$id'";
    $fmtopic_id = $this->db->scalar($sql);
    $sql = "UPDATE fmtopics SET fmtpposts = fmtpposts - 1 WHERE id = '$fmtopic_id'";
    $this->db->query($sql);

    $this->justDelPost($id);

    $this->view->message('postdeleted');
    header('Location: /forum/posts/'.$fmtopic_id);
  }

  function deltopic() {
    $this->allow(89);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT fmsection_id FROM fmtopics WHERE id = '$id'";
    $fmsection_id = $this->db->scalar($sql);

    $sql = "SELECT id FROM fmposts WHERE fmtopic_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      $this->justDelPost($rec['id']);
    }

    $sql = "SELECT fmflname FROM forumtopicfiles WHERE fmtopic_id = '$id'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      @unlink('forumtopicfiles/'.$rec['fmflname']);
    }
    $sql = "DELETE FROM forumtopicfiles WHERE fmtopic_id = '$id'";
    $this->db->query($sql);

    $sql = "DELETE FROM fmtopics WHERE id = '$id'";
    $this->db->query($sql);

    $this->view->message('topicdeleted');
    header('Location: /forum/topics/'.$fmsection_id);
  }

  function editpost() {
    $this->allow(89);

    $id = $this->db->safe($_GET['id']);

    // post info
    $sql = "SELECT f.id AS fmpost_id, fmtext, usname, fmptanonym FROM fmposts AS f
            LEFT JOIN users AS u ON f.user_id = u.id WHERE f.id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('fmpost', $data[0]);

    // files
    $sql = "SELECT id, fmflsource, fmpost_id FROM forumfiles
            WHERE fmpost_id = '$id'
            ORDER BY id, fmflsource";
    $data = $this->db->query($sql);
    $this->view->assign('fmfiles', $data);

    $this->view->show('forum.post.edit');
  }

  function savepost() {
    $this->allow(89);

    $id = $this->db->safe($_POST['fmpost_id']);
    $fmtext = $this->db->safe($_POST['edText']);
    $fmptanonym = $this->db->safe($_POST['isanonym']);

    $sql = "UPDATE fmposts SET fmtext = '$fmtext', fmptanonym = '$fmptanonym'
            WHERE id = '$id'";
    $this->db->query($sql);

    // file can't be edited just can be deleted
    if(isset($_POST['postFile'])) {
      foreach($_POST['postFile'] as $k => $rec) {
        if(trim($rec) == '') {
          $wbflid = $this->db->safe($k);
          $sql = "SELECT fmflname FROM forumfiles WHERE id = '$wbflid'";
          $fname = $this->db->scalar($sql);

          @unlink('forumfiles/$flname');

          $sql = "DELETE FROM forumfiles WHERE id = '$wbflid'";
          $this->db->query($sql);
        }
      }
    }

    if(isset($_FILES['newpostFile'])) {
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $wbflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(time());

          move_uploaded_file($tmp_name, "forumfiles/$fileid");
          $sql = "INSERT INTO forumfiles (fmpost_id, fmflname, fmflsource)
                  VALUES ('$id', '$fileid', '$wbflname')";
          $this->db->query($sql);
        }
      }
    }

    $sql = "SELECT fmtopic_id FROM fmposts WHERE id = '$id'";
    $fmtopic_id = $this->db->scalar($sql);

    $this->view->message('postedited');
    header('Location: /forum/posts/'.$fmtopic_id);
  }

  function edittopic() {
    $this->allow(89);

    $id = $this->db->safe($_GET['id']);

    // topic info
    $sql = "SELECT f.id AS fmtopic_id, fmtpname, fmtpdesc, usname, fmtpanonym FROM fmtopics AS f
            LEFT JOIN users AS u ON f.user_id = u.id WHERE f.id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('fmtopic', $data[0]);

    // files
    $sql = "SELECT id, fmflsource, fmtopic_id FROM forumtopicfiles
            WHERE fmtopic_id = '$id'
            ORDER BY id, fmflsource";
    $data = $this->db->query($sql);
    $this->view->assign('fmfiles', $data);

    $this->view->show('forum.topic.edit');
  }

  function savetopic() {
    $this->allow(89);

    $id = $this->db->safe($_POST['fmtopic_id']);
    $fmtpname = $this->db->safe($_POST['fmtpname']);
    $fmtpdesc = $this->db->safe($_POST['edText']);
    $fmtpanonym = $this->db->safe($_POST['isanonym']);

    $sql = "UPDATE fmtopics SET fmtpname = '$fmtpname', fmtpdesc = '$fmtpdesc',
            fmtpanonym = '$fmtpanonym'
            WHERE id = '$id'";
    $this->db->query($sql);

    // file can't be edited just can be deleted
    if(isset($_POST['postFile'])) {
      foreach($_POST['postFile'] as $k => $rec) {
        if(trim($rec) == '') {
          $wbflid = $this->db->safe($k);
          $sql = "SELECT fmflname FROM forumtopicfiles WHERE id = '$wbflid'";
          $fname = $this->db->scalar($sql);

          @unlink('forumtopicfiles/$flname');

          $sql = "DELETE FROM forumfiles WHERE id = '$wbflid'";
          $this->db->query($sql);
        }
      }
    }

    if(isset($_FILES['newpostFile'])) {
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $wbflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(time());

          move_uploaded_file($tmp_name, "forumtopicfiles/$fileid");
          $sql = "INSERT INTO forumtopicfiles (fmtopic_id, fmflname, fmflsource)
                  VALUES ('$id', '$fileid', '$wbflname')";
          $this->db->query($sql);
        }
      }
    }

    $this->view->message('topicedited');
    header('Location: /forum/posts/'.$id);
  }


}

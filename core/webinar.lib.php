<?php

include ('base.lib.php');

class CWebinar extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('open', 'add', 'edit', 'save', 'delete', 'district',
                             'region', 'wblist', 'show', 'showsection', 'post',
                             'file', 'deletepost', 'editpost', 'savepost');
  }

  function add() {

    $this->allow(88);

    $user_id = $this->user_id;
    $wbname = $this->db->safe($_POST['wbname']);
    $wbtype = $this->db->safe($_POST['wbtype']);
    $wbstart = $this->db->safe($_POST['wbstart']);
    $wbend = $this->db->safe($_POST['wbend']);
    $wbfreestart = $this->db->safe($_POST['wbfreestart']);
    $wbfreeend = $this->db->safe($_POST['wbfreeend']);
    $wbdesc = $this->db->safe($_POST['wbdesc']);
    $district_id = 0;

    if($wbtype == 0) {
      $sql = "SELECT district_id FROM schools WHERE id IN
              (SELECT school_id FROM users WHERE id = '$user_id')";
      $district_id = $this->db->scalar($sql);
    }

    $sql = "INSERT INTO webinars (user_id, district_id, wbname, wbtype, wbstart,
            wbend, wbfreestart, wbfreeend, wbdesc)
            VALUES ('$user_id', '$district_id', '$wbname', '$wbtype',
            '$wbstart', '$wbend', '$wbfreestart', '$wbfreeend', '$wbdesc')";

    $this->db->query($sql);
    $webinar_id = $this->db->last_id();

    foreach($_POST['specialization_id'] as $rec) {
      $specialization_id = $this->db->safe($rec);
      $sql = "INSERT INTO webinarspecs (webinar_id, specialization_id)
              VALUES ('$webinar_id', '$specialization_id')";
      $this->db->query($sql);
    }

    if(isset($_POST['newwbscname'])) {
      foreach($_POST['newwbscname'] as $k => $rec) {
        if(trim($rec) == '') {
          continue;
        }
        $wbscname = $this->db->safe($rec);
        $wbscdesc = $this->db->safe($_POST['newwbscdesc'][$k]);
        $wbscpriority = $this->db->safe($_POST['newwbscpriority'][$k]);

        $sql = "INSERT INTO webinarsections (webinar_id, wbscname, wbscdesc, wbscpriority)
                VALUES ('$webinar_id', '$wbscname', '$wbscdesc', '$wbscpriority')";

        $this->db->query($sql);
      }
    }

    if(isset($_POST['newwbmbtopic'])) {
      foreach($_POST['newwbmbtopic'] as $k => $rec) {
        if(trim($rec)=='') {
          continue;
        }
        $wbmember_id = 0;
        $wbmemberinfo = '';
        if(is_numeric($_POST['newwbmembers'][$k])) {
          $wbmember_id = $this->db->safe($_POST['newwbmembers'][$k]);
        } else {
          $wbmemberinfo = $this->db->safe($_POST['newwbmembers'][$k]);
        }
        $wbmbpriority = $this->db->safe($_POST['newwbmbpriority'][$k]);
        $wbmbtopic = $this->db->safe($_POST['newwbmbtopic'][$k]);

        $sql = "INSERT INTO webinarmembers (webinar_id, wbmember_id, wbmemberinfo,
                wbmbpriority, wbmbtopic)
                VALUES ('$webinar_id', '$wbmember_id', '$wbmemberinfo',
                '$wbmbpriority', '$wbmbtopic')";

        $this->db->query($sql);
      }
    }

    $this->view->message('added');

    if($wbtype == 0) {
      header('Location: /webinar/district');
    } else {
      header('Location: /webinar/region');
    }
  }

  function save() {
    $this->allow(88);

    $webinar_id = $this->db->safe($_POST['webinar_id']);
    $user_id = $this->user_id;
    $wbname = $this->db->safe($_POST['wbname']);
    $wbtype = $this->db->safe($_POST['wbtype']);
    $wbstart = $this->db->safe($_POST['wbstart']);
    $wbend = $this->db->safe($_POST['wbend']);
    $wbfreestart = $this->db->safe($_POST['wbfreestart']);
    $wbfreeend = $this->db->safe($_POST['wbfreeend']);
    $wbdesc = $this->db->safe($_POST['wbdesc']);
    $district_id = 0;

    if($wbtype == 0) {
      $sql = "SELECT district_id FROM schools WHERE id IN
              (SELECT school_id FROM users WHERE id = '$user_id')";
      $district_id = $this->db->scalar($sql);
    }

    $sql = "UPDATE webinars SET district_id='$district_id', wbname='$wbname',
            wbtype='$wbtype', wbstart='$wbstart', wbend='$wbend', wbfreestart='$wbfreestart',
            wbfreeend = '$wbfreeend', wbdesc='$wbdesc'
            WHERE id = '$webinar_id'";

    $this->db->query($sql);

    $sql = "DELETE FROM webinarspecs WHERE webinar_id = '$webinar_id'";
    $this->db->query($sql);
    foreach($_POST['specialization_id'] as $rec) {
      $specialization_id = $this->db->safe($rec);
      $sql = "INSERT INTO webinarspecs (webinar_id, specialization_id)
              VALUES ('$webinar_id', '$specialization_id')";
      $this->db->query($sql);
    }

    if(isset($_POST['wbscname'])) {
      foreach($_POST['wbscname'] as $k => $rec) {
        $wbscid = $this->db->safe($k);
        if(trim($rec) == '') {
          $sql = "DELETE FROM webinarsections WHERE id = '$wbscid'";
          $this->db->query($sql);
          continue;
        }
        $wbscname = $this->db->safe($rec);
        $wbscdesc = $this->db->safe($_POST['wbscdesc'][$k]);
        $wbscpriority = $this->db->safe($_POST['wbscpriority'][$k]);

        $sql = "UPDATE webinarsections SET wbscname='$wbscname', wbscdesc='$wbscdesc',
                wbscpriority='$wbscpriority' WHERE id = '$wbscid'";
        $this->db->query($sql);
      }
    }

    if(isset($_POST['newwbscname'])) {
      foreach($_POST['newwbscname'] as $k => $rec) {
        if(trim($rec) == '') {
          continue;
        }
        $wbscname = $this->db->safe($rec);
        $wbscdesc = $this->db->safe($_POST['newwbscdesc'][$k]);
        $wbscpriority = $this->db->safe($_POST['newwbscpriority'][$k]);

        $sql = "INSERT INTO webinarsections (webinar_id, wbscname, wbscdesc, wbscpriority)
                VALUES ('$webinar_id', '$wbscname', '$wbscdesc', '$wbscpriority')";

        $this->db->query($sql);
      }
    }

    if(isset($_POST['wbmbtopic'])) {
      foreach($_POST['wbmbtopic'] as $k => $rec) {
        $wbmbid = $this->db->safe($k);
        if(trim($rec)=='') {
          $sql = "DELETE FROM webinarmembers WHERE id = '$wbmbid'";
          $this->db->query($sql);
          continue;
        }

        $wbmember_id = 0;
        $wbmemberinfo = '';
        if(is_numeric($_POST['wbmembers'][$k]) && $_POST['wbmembers'][$k] > 0) {
          $wbmember_id = $this->db->safe($_POST['wbmembers'][$k]);
        } else {
          $wbmemberinfo = $this->db->safe($_POST['wbmembers'][$k]);
        }
        $wbmbpriority = $this->db->safe($_POST['wbmbpriority'][$k]);
        $wbmbtopic = $this->db->safe($_POST['wbmbtopic'][$k]);

        $sql = "UPDATE webinarmembers SET wbmember_id='$wbmember_id',
                wbmemberinfo='$wbmemberinfo', wbmbpriority='$wbmbpriority',
                wbmbtopic='$wbmbtopic' WHERE id = '$wbmbid'";
        $this->db->query($sql);
      }
    }


    if(isset($_POST['newwbmbtopic'])) {
      foreach($_POST['newwbmbtopic'] as $k => $rec) {
        if(trim($rec)=='') {
          continue;
        }
        $wbmember_id = 0;
        $wbmemberinfo = '';
        if(is_numeric($_POST['newwbmembers'][$k])) {
          $wbmember_id = $this->db->safe($_POST['newwbmembers'][$k]);
        } else {
          $wbmemberinfo = $this->db->safe($_POST['newwbmembers'][$k]);
        }
        $wbmbpriority = $this->db->safe($_POST['newwbmbpriority'][$k]);
        $wbmbtopic = $this->db->safe($_POST['newwbmbtopic'][$k]);

        $sql = "INSERT INTO webinarmembers (webinar_id, wbmember_id, wbmemberinfo,
                wbmbpriority, wbmbtopic)
                VALUES ('$webinar_id', '$wbmember_id', '$wbmemberinfo',
                '$wbmbpriority', '$wbmbtopic')";

        $this->db->query($sql);
      }
    }

    $this->view->message('edited');

    header('Location: /webinar/show/'.$webinar_id);
  }

  function edit() {
    $this->allow(88);

    $webinar_id = $this->db->safe($_GET['id']);
    // webinar info
    $sql = "SELECT * FROM webinars WHERE id = '$webinar_id'";
    $data = $this->db->query($sql);
    $this->view->assign('webinar', $data[0]);

    // webinar specializations
    $sql = "SELECT * FROM webinarspecs WHERE webinar_id = '$webinar_id'";
    $data = $this->db->query($sql);
    $specs = array();
    foreach($data as $rec) {
      $specs[$rec['specialization_id']] = 1;
    }
    $this->view->assign('webinarspecs', $specs);

    // webinar members
    $sql = "SELECT w.id AS wid, wbmember_id, wbmemberinfo, wbmbtopic, usname, usplace, scname
            FROM webinarmembers AS w
            LEFT JOIN users AS u ON w.wbmember_id = u.id LEFT JOIN
            schools AS s ON u.school_id = s.id WHERE webinar_id = '$webinar_id'
            ORDER BY wbmbpriority";
    $data = $this->db->query($sql);
    $this->view->assign('webinarmembers', $data);

    // webinar sections
    $sql =  "SELECT * FROM webinarsections WHERE webinar_id = '$webinar_id'";
    $data = $this->db->query($sql);
    $this->view->assign('webinarsections', $data);

    $this->open(true);
  }

  function delete() {
    $this->allow(88);

    $webinar_id = $this->db->safe($_GET['id']);

    $sql = "SELECT wbflname FROM webinarfiles WHERE wbmessage_id IN
            (SELECT id FROM webinarmessages WHERE wbsection_id IN
            (SELECT id FROM webinarsections WHERE webinar_id = '$webinar_id'))";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      unlink('webinarfiles/'.$rec['wbflname']);
    }

    $sql = "DELETE FROM webinarfiles WHERE wbmessage_id IN
            (SELECT id FROM webinarmessages WHERE wbsection_id IN
            (SELECT id FROM webinarsections WHERE webinar_id = '$webinar_id'))";
    $this->db->query($sql);

    $sql = "DELETE FROM webinarmessages WHERE wbsection_id IN
            (SELECT id FROM webinarsections WHERE webinar_id = '$webinar_id')";
    $this->db->query($sql);

    $sql = "DELETE FROM webinarmembers WHERE webinar_id = '$webinar_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM webinarsections WHERE webinar_id = '$webinar_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM webinarspecs WHERE webinar_id = '$webinar_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM webinars WHERE id = '$webinar_id'";
    $this->db->query($sql);

    $this->view->message('deleted');

    $this->view->go('/webinar/wblist');
  }

  function open($_edit = false) {
    $this->allow(88);

    $data = $this->db->query("SELECT id, rgname FROM regions ORDER BY rgname");
    $this->view->assign('_regions', $data);

    $data = $this->db->query("SELECT id, spname FROM specializations ORDER BY id");
    $this->view->assign('_specializations', $data);

    $this->view->assign('_edit', $_edit);
    $this->view->show('webinar.open');
  }

  function district() {
    $this->allow(1);

    $this->wblist(0);
  }

  function region() {
    $this->allow(1);

    $this->wblist(1);
  }

  function wblist($wbtype = 2) {
    $this->allow(1);

    if(isset($_POST['wbtype'])) {
      $wbtype = $_POST['wbtype'];
    }
    $user_id = $this->user_id;
    $sql = "SELECT district_id FROM schools WHERE id IN
            (SELECT school_id FROM users WHERE id = '$user_id')";
    $district_id = $this->db->scalar($sql);

    $sql = '';
    if($wbtype == 0) {
      $sql = "SELECT DISTINCT w.id AS wid, wbname, wbstart, wbend, wbdesc, wbviews,
              wbposts FROM webinars AS w INNER JOIN webinarspecs AS s ON
              w.id = s.webinar_id WHERE district_id = '$district_id' AND
              (specialization_id IN (SELECT specialization_id FROM
              usertospec WHERE user_id = '$user_id') OR specialization_id = 0) ORDER BY wbend DESC";
    } else if($wbtype == 1) {
      $sql = "SELECT DISTINCT w.id AS wid, wbname, wbstart, wbend, wbdesc, wbviews,
              wbposts FROM webinars AS w INNER JOIN webinarspecs AS s ON
              w.id = s.webinar_id WHERE wbtype = 1 AND
              (specialization_id IN (SELECT specialization_id FROM
              usertospec WHERE user_id = '$user_id') OR specialization_id = 0) ORDER BY wbend DESC";
    } else {
      $sql = "SELECT DISTINCT w.id AS wid, wbname, wbstart, wbend, wbdesc, wbviews,
              wbposts FROM webinars AS w INNER JOIN webinarspecs AS s ON
              w.id = s.webinar_id WHERE (wbtype = 1 OR district_id = '$district_id')
              AND (specialization_id IN (SELECT specialization_id FROM
              usertospec WHERE user_id = '$user_id') OR specialization_id = 0) ORDER BY wbend DESC";
    }
    $data = $this->db->query($sql);
    $this->view->assign('webinars', $data);

    $wbin = '';
    foreach($data as $rec) {
      $wbin .= $rec['wid'].',';
    }
    $wbin .= '0';

    // webinar specializations
    $sql = "SELECT webinar_id, spname FROM webinarspecs AS w INNER JOIN specializations AS s
            ON w.specialization_id = s.id AND w.webinar_id IN ($wbin)";
    $data = $this->db->query($sql);
    $specs = array();
    foreach($data as $rec) {
      $specs[$rec['webinar_id']][] = $rec['spname'];
    }
    $this->view->assign('specializations', $specs);

    $this->view->assign('wbtype', $wbtype);
    $this->view->show('webinar.list');
  }

  function selectwbmessages($section_id) {
    // messages
    $sql = "SELECT w.id AS wid, wbmstext, wbmstime, wbmsusername, usname, usplace, scname, user_id
            FROM webinarmessages AS w LEFT JOIN users AS u ON w.user_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE wbsection_id = '$section_id'
            ORDER BY wbmstime";
    $wbmessages = $this->db->query($sql);
    $this->view->assign('wbmessages', $wbmessages);

    // form ids of messages for IN clause
    $in = '';
    foreach($wbmessages as $rec) {
      $in .= $rec['wid'].',';
    }
    $in .= '0';

    // files
    $sql = "SELECT id, wbflsource, wbmessage_id FROM webinarfiles
            WHERE wbmessage_id IN ($in)
            ORDER BY id, wbflsource";
    $data = $this->db->query($sql);
    $wbfiles = array();
    foreach($data as $rec) {
      $wbfiles[$rec['wbmessage_id']][] = $rec;
    }
    $this->view->assign('wbfiles', $wbfiles);
  }

  function show($section_id = 0) {

    $user_id = $this->user_id;
    $webinar_id = $this->db->safe($_GET['id']);

    $this->isOwner($webinar_id);

    // ++ wbviews
    if(!isset($_SESSION['wbviewed']) ||
       !in_array($webinar_id, $_SESSION['wbviewed'])) {
       $sql = "UPDATE webinars SET wbviews = wbviews + 1 WHERE id = '$webinar_id'";
       $this->db->query($sql);
      $_SESSION['wbviewed'][] = $webinar_id;
    }

    // webinar info
    $sql = "SELECT w.id AS wid, user_id, wbname, wbtype, wbstart, wbend,
            wbfreestart, wbfreeend, wbdesc,
            usname, user_id, usplace, scname,
            dtname FROM webinars AS w LEFT JOIN users AS u ON w.user_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id LEFT JOIN districts AS d
            ON w.district_id = d.id WHERE w.id = '$webinar_id'";
    $data = $this->db->query($sql);
    $this->view->assign('webinar', $data[0]);

    // debug
    //$t = time();
    //print "{$data[0]['wbfreestart']} < {$t} || {$data[0]['wbfreeend']} > {$t}";

    if(($data[0]['wbfreestart'] == 0 || $data[0]['wbfreestart'] > time()) || $data[0]['wbfreeend'] < time()) {
      $this->allow(1);
    }

    // webinar specializations
    $sql = "SELECT spname FROM webinarspecs AS w INNER JOIN specializations AS s
            ON w.specialization_id = s.id AND w.webinar_id = '$webinar_id'";
    $data = $this->db->query($sql);
    $this->view->assign('specializations', $data);

    // webinar members
    $sql = "SELECT wbmember_id, wbmemberinfo, wbmbtopic, usname, usplace, scname
            FROM webinarmembers AS w
            LEFT JOIN users AS u ON w.wbmember_id = u.id LEFT JOIN
            schools AS s ON u.school_id = s.id WHERE webinar_id = '$webinar_id'
            ORDER BY wbmbpriority";
    $data = $this->db->query($sql);
    $this->view->assign('webinarmembers', $data);

    // default section is first
    if($section_id == 0) {
      $sql = "SELECT id FROM webinarsections WHERE webinar_id = '$webinar_id'
              ORDER BY wbscpriority LIMIT 1";
      $section_id = $this->db->scalar($sql);
    }

    // current section info
    $sql = "SELECT * FROM webinarsections WHERE id = '$section_id'";
    $data = $this->db->query($sql);
    $this->view->assign('wbsection', $data);

    // webinar sections
    $sql =  "SELECT * FROM webinarsections WHERE webinar_id = '$webinar_id'
             ORDER BY wbscpriority";
    $data = $this->db->query($sql);
    $this->view->assign('webinarsections', $data);

    // user info for post box
    $sql = "SELECT usname FROM users WHERE id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('usinfo', $data);

    $this->selectwbmessages($section_id);

    $this->view->show('webinar.show');
  }

  function showsection() {
    $section_id = $this->db->safe($_GET['id']);
    $sql = "SELECT webinar_id FROM webinarsections WHERE id = '$section_id'";
    $webinar_id = $this->db->scalar($sql);
    $_GET['id'] = $webinar_id;
    $this->show($section_id);
  }

  function post() {
    $user_id = $this->user_id;

    $wbsection_id = $this->db->safe($_POST['wbsection_id']);
    $wbmstext = $this->db->safe($_POST['edText']);
    $wbmstime = time();
    $wbmsusername = $user_id == 0 ? $this->db->safe($_POST['usfreename']) : '';

    $data = $this->db->query("SELECT DISTINCT wbfreestart, wbfreeend FROM webinars INNER JOIN webinarsections AS ws ON webinar_id = webinars.id WHERE ws.id='$wbsection_id'");
    if(($data[0]['wbfreestart'] == 0 || $data[0]['wbfreestart'] > time()) || $data[0]['wbfreeend'] < time()) {
      $this->allow(1);
    }

    // ++ wbposts
    $sql = "SELECT webinar_id FROM webinarsections WHERE id = '$wbsection_id'";
    $webinar_id = $this->db->scalar($sql);
    $sql = "UPDATE webinars SET wbposts = wbposts + 1 WHERE id = '$webinar_id'";
    $this->db->query($sql);

    $sql = "INSERT INTO webinarmessages (wbsection_id, user_id, wbmstext, wbmstime, wbmsusername)
            VALUES ('$wbsection_id', '$user_id', '$wbmstext', '$wbmstime', '$wbmsusername')";
    $this->db->query($sql);

    if(isset($_FILES['newpostFile'])) {
      $wbmessage_id = $this->db->last_id();
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $wbflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(uniqid());

          move_uploaded_file($tmp_name, "webinarfiles/$fileid");
          $sql = "INSERT INTO webinarfiles (wbmessage_id, wbflname, wbflsource)
                  VALUES ('$wbmessage_id', '$fileid', '$wbflname')";
          $this->db->query($sql);
        }
      }
    }

    $this->view->message('posted');
    header('Location: /webinar/showsection/'.$wbsection_id);

  }

  function file() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM webinarfiles WHERE id = '$id'";
    $data = $this->db->query($sql);

    if(count($data) != 1) {
      $this->view->page404();
    }

    include('files.lib.php');

    if(!CFiles::download('webinarfiles/'.$data[0]['wbflname'], $data[0]['wbflsource'])) {
      $this->view->page404();
    }
  }

  function deletepost() {
    $this->allow(88);

    $wbmsid = $this->db->safe($_GET['id']);

    $sql = "SELECT wbsection_id FROM webinarmessages WHERE id = '$wbmsid'";
    $section_id = $this->db->scalar($sql);

    // -- wbposts
    $sql = "SELECT webinar_id FROM webinarsections WHERE id = '$section_id'";
    $webinar_id = $this->db->scalar($sql);
    $sql = "UPDATE webinars SET wbposts = wbposts - 1 WHERE id = '$webinar_id'";
    $this->db->query($sql);

    $sql = "DELETE FROM webinarmessages WHERE id = '$wbmsid'";
    $this->db->query($sql);

    // remove files
    $sql = "SELECT fileid FROM webinarfiles WHERE wbmessage_id = '$wbmsid'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      unlink('webinarfiles/'.$rec['fileid']);
    }

    $sql = "DELETE FROM webinarfiles WHERE wbmessage_id = '$wbmsid'";
    $this->db->query($sql);

    $this->view->message('postdeleted');

    header("Location: /webinar/showsection/$section_id");
  }

  function editpost() {
    $this->allow(88);

    $wbmsid = $this->db->safe($_GET['id']);

    // messages
    $sql = "SELECT w.id AS wid, wbmstext, wbmstime, usname, usplace, scname
            FROM webinarmessages AS w LEFT JOIN users AS u ON w.user_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE w.id = '$wbmsid'";
    $wbmessages = $this->db->query($sql);
    $this->view->assign('wbmessages', $wbmessages[0]);

    // files
    $sql = "SELECT id, wbflsource FROM webinarfiles WHERE wbmessage_id = '$wbmsid'
            ORDER BY wbflsource";
    $data = $this->db->query($sql);
    $this->view->assign('wbfiles', $data);

    $this->view->show('webinar.post.edit');
  }

  function savepost() {
    $this->allow(88);

    $wbmsid = $this->db->safe($_POST['wbmsid']);
    $wbmstext = $this->db->safe($_POST['edText']);

    $sql = "UPDATE webinarmessages SET wbmstext = '$wbmstext' WHERE id ='$wbmsid'";
    $this->db->query($sql);

    // file can't be edited just can be deleted
    if(isset($_POST['postFile'])) {
      foreach($_POST['postFile'] as $k => $rec) {
        if(trim($rec) == '') {
          $wbflid = $this->db->safe($k);
          $sql = "SELECT fileid FROM webinarfiles WHERE id = '$wbflid'";
          $flname = $this->db->scalar($sql);

          unlink("webinarfiles/$flname");

          $sql = "DELETE FROM webinarfiles WHERE id = '$wbflid'";
          $this->db->query($sql);
        }
      }
    }


    if(isset($_FILES['newpostFile'])) {
      foreach ($_FILES["newpostFile"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["newpostFile"]["tmp_name"][$key];
          $wbflname = $_FILES["newpostFile"]["name"][$key];
          $fileid = md5(uniqid());

          move_uploaded_file($tmp_name, "webinarfiles/$fileid");
          $sql = "INSERT INTO webinarfiles (wbmessage_id, wbflname, wbflsource)
                  VALUES ('$wbmsid', '$fileid', '$wbflname')";
          $this->db->query($sql);
        }
      }
    }

    $sql = "SELECT wbsection_id FROM webinarmessages WHERE id = '$wbmsid'";
    $wbsection_id = $this->db->scalar($sql);

    $this->view->message('postedited');
    header('Location: /webinar/showsection/'.$wbsection_id);
  }

  function isOwner($webinar_id) {
    // assume mblog_id is safe
    if($this->ustype == 99 || $this->getWebinarOwnerId($webinar_id) == $this->user_id) {
      $this->view->assign('webinar_owner', '1');
      return true;
    } else {
      $this->view->assign('webinar_owner', '0');
      return false;
    }
  }

  private function getWebinarOwnerId($webinar_id) {
    // assume webinar_id is safe

    $sql = "SELECT user_id FROM webinars WHERE id = '$webinar_id'";
    $user_id = $this->db->scalar($sql);

    return $user_id;
  }

}

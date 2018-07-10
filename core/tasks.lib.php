<?php

include ('base.lib.php');

class CTasks extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('view', 'add', 'setdone', 'del', 'setpriority',
                             'change', 'edit');
  }

  function edit() {
    $this->allow(1);

    $this->add(true);
  }

  function change() {
    $this->allow(1);

    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM tasks WHERE id = '$id' AND user_id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('data', $data);

    $today = mktime(0,0,0);
    $thisweek = $today-(date('N')-1)*86400;
    $this->view->assign('today', $today);
    $this->view->assign('thisweek', $thisweek);

    $todate = 0;
    if($data[0]['tsdate']!=$today &&
       $data[0]['tsdate']!=$today+86400 &&
       $data[0]['tsdate']!=$thisweek &&
       $data[0]['tsdate']!=$thisweek+86400*7 &&
       $data[0]['tsdate']!=0) {
      $todate = 1;
    }
    $this->view->assign('todate', $todate);

    $this->view->show('tasks.edit');
  }

  function setdone() {
    $this->allow(1);

    $id = $this->db->safe($_POST['id']);
    $tsdone = $this->db->safe($_POST['tsdone']);

    $sql = "UPDATE tasks SET tsdone = '$tsdone' WHERE id = '$id'";
    $this->db->query($sql);
  }

  function del() {
    $this->allow(1);

    $id = $this->db->safe($_POST['id']);

    $sql = "DELETE FROM tasks WHERE id = '$id'";
    $this->db->query($sql);
  }

  function setpriority() {
    $this->allow(1);

    foreach($_POST['tspriority'] as $id => $rec) {
      $id = $this->db->safe($id);
      $rec = $this->db->safe($rec);

      $sql = "UPDATE tasks SET tspriority = '$rec' WHERE id = '$id'";
      $this->db->query($sql);
    }
  }

  function view() {
    $this->allow(1);

    $user_id = $this->user_id;
    $limit = 3;

    $_tsdate = 0;
    $_tsweek = 0;
    $today = mktime(0,0,0);
    if(!isset($_POST['showwhen'])) {
      $_POST['showwhen'] = 'today';
    }
    switch($_POST['showwhen']) {
      case 'today': case 'date': $_tsdate = $today; break;
      case 'tomorrow': $_tsdate = $today+86400; break;
      case 'thisweek':
        $_tsweek = 1;
        $_tsdate = $today-(date('N')-1)*86400;
        break;
      case 'nextweek':
        $_tsweek = 1;
        $_tsdate = $today-(date('N')-1)*86400+86400*7;
        break;
      case 'whenever': $_tsdate = 0; break;
      default:
        $_tsdate = $this->db->safe((int)$_POST['showwhen']);
        break;
    }

    switch($_POST['showwhen']) {
      case 'today': case 'date':
        // overdue
        $_tsdatemonday = $today-(date('N')-1)*86400;
        $_tsdateplusthree = $today+86400*3;
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND tsdone = 0 AND
                (tsdate < $_tsdate AND tsweek = 0 AND tsdate != 0) OR
                (tsdate < $_tsdatemonday AND tsweek = 1)
                ORDER BY tspriority";
        $data = $this->db->query($sql);
        $this->view->assign('overdue', $data);

        // today
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND
                tsdate = $_tsdate AND tsweek = 0
                ORDER BY tspriority";
        $data = $this->db->query($sql);
        $this->view->assign('main', $data);

        // this week
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND tsdone = 0 AND
                tsdate = $_tsdatemonday AND tsweek = '1'
                ORDER BY tspriority LIMIT $limit";
        $data = $this->db->query($sql);
        $this->view->assign('thisweek', $data);

        // soon
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND tsdone = 0 AND
                tsdate > $_tsdate AND tsdate <= $_tsdateplusthree AND
                tsweek = '0' ORDER BY tspriority LIMIT $limit";
        $data = $this->db->query($sql);
        $this->view->assign('soon', $data);

      break;
      case 'tomorrow':
      default:
        // for day
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND
                tsdate = $_tsdate AND tsweek = 0 ORDER BY tspriority";
        $data = $this->db->query($sql);
        $this->view->assign('main', $data);
        if($_POST['showwhen']!='tomorrow' && $_POST['showwhen']!='whenever') {
          $this->view->assign('showdate', 1);
        }
        break;
      case 'thisweek':
      case 'nextweek':
        $_tsdatenextmonday = $today-(date('N')-1)*86400+86400*7;
        // for week
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND
                tsdate = $_tsdate AND tsweek = 1 ORDER BY tspriority";
        $data = $this->db->query($sql);
        $this->view->assign('main', $data);

        // for week with dates
        $sql = "SELECT * FROM tasks WHERE user_id = '$user_id' AND tsdone = 0 AND
                tsdate > $_tsdate AND tsdate < $_tsdatenextmonday AND
                tsweek = 0 ORDER BY tspriority LIMIT $limit";
        $data = $this->db->query($sql);
        $this->view->assign('thisweek', $data);

        break;
    }

    $this->view->show('tasks.view');
  }

  function add($edit = false) {
    $this->allow(1);

    $user_id = $this->user_id;
    $_tsname = $this->db->safe($_POST['tsname']);
    if(trim($_tsname)=='') {
      //$this->view();
      return;
      //print 'oh here';
      //die();
    }
    $_tsnotes = $this->db->safe($_POST['tsnotes']);
    $_tsdate = 0;
    $_tsweek = 0;
    $today = mktime(0,0,0);
    switch($_POST['when']) {
      case 'today': case 'date': $_tsdate = $today; break;
      case 'tomorrow': $_tsdate = $today+86400; break;
      case 'thisweek':
        $_tsweek = 1;
        $_tsdate = $today-(date('N')-1)*86400;
        break;
      case 'nextweek':
        $_tsweek = 1;
        $_tsdate = $today-(date('N')-1)*86400+86400*7;
        break;
      case 'whenever': $_tsdate = 0; break;
      default:
        $_tsdate = $this->db->safe($_POST['when']);
        break;
    }
    $_tsremind = 0;
    switch($_POST['bell']) {
      case 'noremind': case 'time': $_tsremind = 0; break;
      default: $_tsremind = $this->db->safe($_POST['bell']); break;
    }
    $_tscolor = $_POST['tscolor'];

    if(!$edit) {
      $sql = "INSERT INTO tasks (user_id, tsname, tsnotes, tsdate, tsweek, tsremind, tscolor)
              VALUES ('$user_id', '$_tsname', '$_tsnotes', '$_tsdate',
                      '$_tsweek', '$_tsremind', '$_tscolor')";
      $this->db->query($sql);
      $this->view->message('added');
    } else {
      $_id = $this->db->safe($_GET['id']);
      $sql = "UPDATE tasks SET tsname = '$_tsname', tsnotes = '$_tsnotes', tsdate = '$_tsdate',
              tsweek = '$_tsweek', tsremind = '$_tsremind', tscolor = '$_tscolor'
              WHERE id = '$_id' AND user_id = '$user_id'";
      $this->db->query($sql);
      $this->view->message('edited');
    }

    $this->view();
  }

}

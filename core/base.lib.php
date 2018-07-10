<?php

function s_q($str) {
  $str1 = str_replace("'", "\\x27", $str);
  return str_replace("\"", "\\x22", $str1);
}

include('utils.lib.php');

class CBase {
    protected $db;
    protected $view;
    protected $actions;
    protected $user_id;

    function __construct($db, $view) {
        $this->db = $db;
        $this->view = $view;
        $this->user_id = 0;
        $this->ustype = 0;
        $this->school_id = 0;

        $this->authorize();
        $this->autologin();
        $this->view->assign('user_id', $this->user_id);
        $this->view->assign('user_rights', $this->ustype);

        $this->checkNew();
    }

    function autologin() {
      if($this->user_id > 0) {
        return;
      }

      if(!isset($_GET['autologin']) ||
         !isset($_GET['passkey'])) {
        return;
      }

      $uslogin = $this->db->safe($_GET['autologin']);
      $sql = "SELECT id, uspassword, ustype, usname, school_id FROM users WHERE uslogin = '$uslogin'";
      $data = $this->db->query($sql);

      if(count($data) > 0 &&
         md5($data[0]['uspassword']) == $_GET['passkey']) {

        $md5 = md5(time());
        $hash = substr($md5, 0, 4).$data[0]['id'].'a'.substr($md5, 6+strlen($data[0]['id']));
        setcookie('sc2session', $hash, 0, '/');

        preg_match('/^[^\s]+\s([^\s]+)\s.+/', $data[0]['usname'], $mname);
        if(count($mname) > 1) {
          $this->view->assign('g_usname', $mname[1]);
        } else {
          $this->view->assign('g_usname', 'Пользователь');
        }
        $this->ustype = $data[0]['ustype'];
        $this->school_id = $data[0]['school_id'];

        $this->user_id = $data[0]['id'];

        // set last time logged in
        $id = $data[0]['id'];
        $uslasttime = time();
        $this->db->query("UPDATE users SET uslasttime = '$uslasttime' WHERE id = '$id'");

      }
    }

    /**
     * function to check if user is logged in and saves user id in memory
     */
    function authorize() {
      // algorithm is targeted to prohibit login throught out renumbering
      // if the cookies was stolen it is the user's problem
      //
      if(!isset($_COOKIE['sc2session']) ||
         $_COOKIE['sc2session'] == '0') {
        return;
      }

      $hash = $_COOKIE['sc2session'];
      preg_match('/^(\d+?)a/', substr($hash, 4), $matches);

      if(count($matches) < 2 || !is_numeric($matches[1])) {
        setcookie('sc2session', '0', 1);
        $this->page404();
        return;
      }

      $data = $this->db->query("SELECT usname, ustype, school_id FROM users WHERE id = '{$matches[1]}'");
      if(count($data) < 1) {
        setcookie('sc2session', '0', 1);
        $this->page404();
        return;
      }

      preg_match('/^[^\s]+\s([^\s]+)\s.+/', $data[0]['usname'], $mname);
      if(count($mname) > 1) {
        $this->view->assign('g_usname', $mname[1]);
      } else {
        $this->view->assign('g_usname', 'Пользователь');
      }
      $this->ustype = $data[0]['ustype'];
      $this->school_id = $data[0]['school_id'];

      $this->user_id = $matches[1];

      // set last time logged in
      $id = $this->user_id;
      $uslasttime = time();
      $this->db->query("UPDATE users SET uslasttime = '$uslasttime' WHERE id = '$id'");
    }

    function checkNew() {
      $notcnt = $this->db->scalar("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = '{$this->user_id}'");
      $mescnt = $this->db->scalar("SELECT COUNT(*) AS cnt FROM messages WHERE user_id = '{$this->user_id}' AND msreaded = 0");

      $this->view->assign('newnots', $notcnt);
      $this->view->assign('newmsgs', $mescnt);
    }

    function getSchoolId() {
      return $this->school_id;
    }

    function getTeacherId() {
      return $this->db->scalar("SELECT teacher_id FROM users WHERE id = '{$this->user_id}'");
    }

    function run() {
      if(!isset($_GET['action']) &&
         method_exists($this, 'index')) {
        $this->index();
        return;
      }
      if(!isset($_GET['action']) ||
         !in_array($_GET['action'],$this->actions)) {
        $this->view->page404();
        return;
      }
      $this->$_GET['action']();
    }

    function page404() {
      $this->view->page404();
    }

    function allow($rights) {
      if($this->ustype < $rights) {
        $this->view->page404();
      }
    }

    function notify($user_id, $nttopic, $ntlink) {
      $nttime = time();
      $nttopic = $this->db->safe($nttopic);
      $ntlink = $this->db->safe($ntlink);
      $user_id = $this->db->safe($user_id);

      $sql = "INSERT INTO notifications (nttopic, ntlink, nttime, user_id)
              VALUES ('$nttopic', '$ntlink', '$nttime', '$user_id')";
      $this->db->query($sql);
    }

    function notifyadmin($nttopic, $ntlink) {
      $sql = "SELECT id FROM users WHERE ustype = 99";
      $user_id = $this->db->scalar($sql);

      $this->notify($user_id, $nttopic, $ntlink);
    }

    function query($varname, $sql) {
      $data = $this->db->query($sql);
      $this->view->assign($varname, $data);
    }

    function q($varname, $sql) {
      $this->query($varname, $sql);
    }

    function g($varname) {
      return $this->db->safe($_GET[$varname]);
    }

    function p($varname) {
      return $this->db->safe($_POST[$varname]);
    }
}

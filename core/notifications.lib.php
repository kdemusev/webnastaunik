<?php

include ('base.lib.php');

class CNotifications extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('goforit', 'delete', 'deleteall', 'delivery', 'senddelivery');
  }

  function index() {
    $this->allow(1);

    $user_id = $this->user_id;

    $sql = "SELECT id, nttopic, nttime, ntlink
            FROM notifications
            WHERE user_id = '$user_id' ORDER BY nttime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('notifications', $data);

    $this->view->show('notifications');
  }

  function goforit() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $link = $this->db->scalar("SELECT ntlink FROM notifications WHERE id = '$id'");

    $sql = "DELETE FROM notifications WHERE id = '$id'";
    $this->db->query($sql);

    $this->view->go($link);
  }

  function delete() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM notifications WHERE id = '$id'";
    $this->db->query($sql);

    $this->view->message('deleted');
    $this->view->go('/notifications');
  }

  function deleteall() {
    $this->allow(1);

    $user_id = $this->user_id;

    $sql = "DELETE FROM notifications WHERE user_id = '$user_id'";
    $this->db->query($sql);

    $this->view->message('deletedall');
    $this->view->go('/notifications');
  }

  function delivery() {
    $this->view->show('notifications.delivery');
  }

  function senddelivery() {
    $nttopic = $this->db->safe($_POST['nttopic']);
    $ntlink = $this->db->safe($_POST['ntlink']);
    $nttime = time();

    $sql = "SELECT id FROM users WHERE uslogin != '01737del17'";
    $data = $this->db->query($sql);
    foreach($data as $rec) {
      $user_id = $rec['id'];

      $sql = "INSERT INTO notifications (nttopic, ntlink, nttime, user_id)
              VALUES ('$nttopic', '$ntlink', '$nttime', '$user_id')";
      $this->db->query($sql);
    }
    
    $this->view->message('sended');
    $this->view->go('/notifications/delivery');
  }

  static function make($user_id, $nttopic, $ntlink) {
    $nttime = time();
    $nttopic = $this->db->safe($nttopic);
    $ntlink = $this->db->safe($ntlink);
    $user_id = $this->db->safe($user_id);

    $sql = "INSERT INTO notifications (nttopic, ntlink, nttime, user_id)
            VALUES ('$nttopic', '$ntlink', '$nttime', '$user_id')";
  }

  static function makeadmin($nttopic, $ntlink) {
    $sql = "SELECT id FROM users WHERE ustype == 99";
    $user_id = $this->db->scalar($sql);

    self::make($user_id, $nttopic, $ntlink);
  }
}

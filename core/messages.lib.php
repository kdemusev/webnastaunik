<?php

include ('base.lib.php');

class CMessages extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('compose', 'post', 'delete', 'show', 'answer',
                             'justnew', 'sended', 'sendedshow', 'feedback',
                             'sendfeedback');
  }

  function index() {
    $this->allow(1);

    $user_id = $this->user_id;

    $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname
            FROM messages AS m LEFT JOIN users AS u ON m.sender_id = u.id
            WHERE m.user_id = '$user_id' ORDER BY mstime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('messages', $data);

    $this->view->assign('justnew', false);

    $this->view->show('messages');
  }

  function justnew() {
    $this->allow(1);

    $user_id = $this->user_id;

    $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname
            FROM messages AS m LEFT JOIN users AS u ON m.sender_id = u.id
            WHERE m.user_id = '$user_id' AND msreaded = 0 ORDER BY mstime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('messages', $data);

    $this->view->assign('justnew', true);

    $this->view->show('messages');
  }

  function sended() {
    $this->allow(1);

    $user_id = $this->user_id;

    $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname
            FROM messages AS m LEFT JOIN users AS u ON m.user_id = u.id
            WHERE m.sender_id = '$user_id' ORDER BY mstime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('messages', $data);

    $this->view->show('messages.sended');
  }

  function sendedshow() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname,
            usplace, scname
            FROM messages AS m LEFT JOIN users AS u ON m.user_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE m.id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('message', $data[0]);

    $this->view->show('messages.sended.show');
  }

  function show() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "UPDATE messages SET msreaded = 1 WHERE id = '$id'";
    $this->db->query($sql);

    $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname,
            usplace, scname
            FROM messages AS m LEFT JOIN users AS u ON m.sender_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id
            WHERE m.id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('message', $data[0]);

    $this->view->show('messages.show');
  }

  function delete() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM messages WHERE id = '$id'";
    $this->db->query($sql);

    $this->view->message('deleted');

    $this->view->go('/messages');
  }

  function post() {
    $this->allow(1);

    $user_id = $this->db->safe($_POST['user_id']);
    $sender_id = $this->user_id;
    $mstopic = $this->db->safe($_POST['mstopic']);
    $mstext = $this->db->safe($_POST['mstext']);
    $mstime = time();
    $msreaded = 0;

    $sql = "INSERT INTO messages (user_id, sender_id, mstopic, mstext,
            mstime, msreaded)
            VALUE ('$user_id', '$sender_id', '$mstopic', '$mstext',
            '$mstime', '$msreaded')";
    $this->db->query($sql);

    $this->view->message('added');
    $this->view->go('/messages');
  }

  function answer() {
    $this->allow(1);

    $this->compose(true);
  }

  function compose($answer = false) {
    $this->allow(1);

    if($answer) {
      $id = $this->db->safe($_GET['id']);

      $sql = "SELECT m.id AS message_id, mstopic, mstext, mstime, msreaded, usname,
              sender_id
              FROM messages AS m LEFT JOIN users AS u ON m.sender_id = u.id
              WHERE m.id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('message', $data[0]);
    }

    $this->view->assign('answer', $answer);

    $sql = "SELECT * FROM regions ORDER BY rgname";
    $data = $this->db->query($sql);
    $this->view->assign('_regions', $data);

    $this->view->show('messages.compose');
  }

  function feedback() {
    $this->view->show('messages.feedback');
  }

  function sendfeedback() {
    $sql = "SELECT id FROM users WHERE ustype = 99";
    $user_id = $this->db->scalar($sql);

    $sender_id = $this->user_id;
    $mstopic = $this->db->safe($_POST['mstopic']);
    $mstext = $this->db->safe($_POST['mstext']);
    $mstime = time();
    $msreaded = 0;

    $sql = "INSERT INTO messages (user_id, sender_id, mstopic, mstext,
            mstime, msreaded)
            VALUE ('$user_id', '$sender_id', '$mstopic', '$mstext',
            '$mstime', '$msreaded')";
    $this->db->query($sql);
    
    $this->view->message('sended');
    $this->view->go('/messages/feedback');
  }

}

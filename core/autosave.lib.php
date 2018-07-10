<?php

include ('base.lib.php');

class CAutosave extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array();
  }

  function index() {
    header('Content-type: text/xml');

    $table = $this->db->safe($_POST['table']);
    $field = $this->db->safe($_POST['field']);
    $where = $this->db->safe($_POST['where']);
    $cmp_val = $this->db->safe($_POST['cmp_val']);
    $where2 = $this->db->safe($_POST['where2']);
    $cmp_val2 = $this->db->safe($_POST['cmp_val2']);
    $allowdelete = isset($_POST['allowdelete']) ? $this->db->safe($_POST['allowdelete']) : 0;

    $val = $this->db->safe($_POST['val']);

    if($where2 == 'undefined') {
      if($allowdelete == 1 && trim($val) == '') {
        $sql = "DELETE FROM $table WHERE $where = '$cmp_val'";
        $this->db->query($sql);
      } else {
        $count = $this->db->scalar("SELECT COUNT(*) AS cnt FROM $table WHERE $where = '$cmp_val'");
        if($count > 0) {
          $sql = "UPDATE $table SET $field = '$val' WHERE $where = '$cmp_val'";
          $this->db->query($sql);
        } else if($where != 'id') {
          $sql = "INSERT INTO $table ($field, $where) VALUES ('$val', '$cmp_val')";
          $this->db->query($sql);
        } else {
          $sql = "INSERT INTO $table ($field) VALUES ('$val')";
          $this->db->query($sql);
        }
      }
    } else {
      if($allowdelete == 1 && trim($val) == '') {
        $sql = "DELETE FROM $table WHERE $where = '$cmp_val' AND $where2 = '$cmp_val2'";
        $this->db->query($sql);
      } else {
        $count = $this->db->scalar("SELECT COUNT(*) AS cnt FROM $table WHERE $where = '$cmp_val' AND $where2 = '$cmp_val2'");
        if($count > 0) {
          $sql = "UPDATE $table SET $field = '$val' WHERE $where = '$cmp_val' AND $where2 = '$cmp_val2'";
          $this->db->query($sql);
        } else {
          $sql = "INSERT INTO $table ($field, $where, $where2) VALUES ('$val', '$cmp_val', '$cmp_val2')";
          $this->db->query($sql);
        }
      }
    }


    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<result>ok</result>";
  }
}

?>

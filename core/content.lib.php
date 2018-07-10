<?php

include ('base.lib.php');

class CContent extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('news', 'addnews', 'editnews', 'savenews', 'delnews',
                             'shownews');
  }

  function news() {
    $this->allow(1);

    $sql = "SELECT * FROM news ORDER BY nstime DESC";
    $data = $this->db->query($sql);
    foreach($data as $k => $rec) {
      $data[$k]['nstext'] = CUtilities::truncate($rec['nstext'], 255);
    }
    $this->view->assign('news', $data);

    $this->view->show('news');
  }

  function shownews() {
    $this->allow(1);

    $id = $this->db->safe($_GET['id']);
    $sql = "SELECT n.id AS news_id, nstext, nsname, nstype, nstime, usname, user_id,
            usplace, scname FROM news AS n LEFT JOIN users AS u ON n.user_id = u.id
            LEFT JOIN schools AS s ON u.school_id = s.id WHERE n.id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('news', $data[0]);

    $this->view->show('news.show');
  }

  function addnews() {
    $this->allow(88);

    $user_id = $this->user_id;
    $nsname = $this->db->safe($_POST['nsname']);
    $nstext = $this->db->safe($_POST['edText']);
    $nstype = $this->db->safe($_POST['nstype']);
    $nstime = time();
    $district_id = 0;

    if($nstype == 0) {
      $sql = "SELECT district_id FROM users AS u INNER JOIN schools AS s
              ON u.school_id = s.id WHERE u.id = '$user_id'";
      $district_id = $this->db->scalar($sql);
    }

    $sql = "INSERT INTO news (nsname, district_id, nstype, nstext, nstime, user_id) VALUES
            ('$nsname', '$district_id', '$nstype', '$nstext', '$nstime', '$user_id')";
    $this->db->query($sql);

    $id = $this->db->last_id();

    if(isset($_FILES['mainimage'])) {
      $error = $_FILES["mainimage"]["error"];
      if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["mainimage"]["tmp_name"];

        if(is_uploaded_file($tmp_name)) {
          include('image.lib.php');
          $img = new CImage($tmp_name);
          $img->scale(800, 600);
          $img->save_as("newsfiles/$id");

          $img = new CImage($tmp_name);
          $img->scale_and_crop(135, 110);
          $img->save_as("newsfiles/sm/$id");

          unlink($tmp_name);
        }
      }
    }

    $this->view->message('added');

    $this->view->go('/content/news');
  }

  function editnews() {
    $this->allow(88);

    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM news WHERE id = '$id'";
    $data = $this->db->query($sql);
    $this->view->assign('news', $data[0]);

    $this->view->show('news.edit');
  }

  function delnews() {
    $this->allow(88);

    $id = $this->db->safe($_GET['id']);

    $sql = "DELETE FROM news WHERE id = '$id'";
    $this->db->query($sql);

    @unlink("newsfiles/$id");
    @unlink("newsfiles/sm/$id");

    $this->view->message('deleted');

    $this->view->go('/content/news');
  }

  function savenews() {
    $this->allow(88);

    $user_id = $this->user_id;
    $id = $this->db->safe($_POST['news_id']);
    $nsname = $this->db->safe($_POST['nsname']);
    $nstext = $this->db->safe($_POST['edText']);
    $nstype = $this->db->safe($_POST['nstype']);
    $district_id = 0;

    if($nstype == 0) {
      $sql = "SELECT district_id FROM users AS u INNER JOIN schools AS s
              ON u.school_id = s.id WHERE u.id = '$user_id'";
      $district_id = $this->db->scalar($sql);
    }

    $sql = "UPDATE news SET nsname = '$nsname', district_id = '$district_id',
            nstype = '$nstype', nstext = '$nstext' WHERE id = '$id'";
    $this->db->query($sql);

    if(isset($_FILES['mainimage'])) {
      $error = $_FILES["mainimage"]["error"];
      if ($error == UPLOAD_ERR_OK) {
        @unlink("newsfiles/$id");
        @unlink("newsfiles/sm/$id");

        $tmp_name = $_FILES["mainimage"]["tmp_name"];

        if(is_uploaded_file($tmp_name)) {
          include('image.lib.php');
          $img = new CImage($tmp_name);
          $img->scale(800, 600);
          $img->save_as("newsfiles/$id");

          $img = new CImage($tmp_name);
          $img->scale_and_crop(135, 110);
          $img->save_as("newsfiles/sm/$id");

          unlink($tmp_name);
        }
      }
    }

    $this->view->message('edited');

    $this->view->go('/content/shownews/'.$id);
  }

}

?>

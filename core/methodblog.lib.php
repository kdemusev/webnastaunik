<?php

include ('base.lib.php');

class CMethodblog extends CBase {

    function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('showlist', 'show', 'add', 'save', 'edit', 'change', 'delete',
                             'news', 'addnews', 'editnews', 'savenews', 'changenews', 'deletenews',
                             'dialog', 'addmessage', 'editmessage', 'changemessage', 'deletemessage',
                             'settings', 'savesettings');
    }

    function index() {
      $this->allow(1);

      $this->showlist();
    }

    function show() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);
      $this->isOwner($id);
      $this->isAuthor($id);

      $sql = "SELECT * FROM methodblogs WHERE id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('methodblog', $data[0]);

      $sql = "SELECT mbn.id AS mbn_id, mbnname, mbntime, mbntext, usname,
              usplace, scname, user_id FROM mbnews AS mbn LEFT JOIN users AS u ON u.id = mbn.user_id
              LEFT JOIN schools AS s ON s.id = u.school_id WHERE methodblog_id = '$id'
              ORDER BY mbntime DESC";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $this->view->show('methodblog');
    }

    function showlist() {
      $this->allow(1);

      $sql = "SELECT specialization_id FROM usertospec WHERE user_id = '{$this->user_id}'";
      $specialization_id = $this->db->scalar($sql);

      if($this->ustype < 88) {  // учитель видит только районные блоги и открытые областные
        $sql = "SELECT district_id FROM schools WHERE id IN
                (SELECT school_id FROM users WHERE id = '{$this->user_id}')";
        $region_id = $this->db->scalar($sql);

        $sql = "SELECT id, mbname, mbdesc, mbtype, user_id FROM methodblogs WHERE region_id = '$region_id' AND
                id IN (SELECT methodblog_id FROM mbspecs WHERE specialization_id IN
                (SELECT specialization_id FROM usertospec WHERE user_id = '{$this->user_id}')) AND
                mbtype = 0
                ORDER BY mbname";
        $data = $this->db->query($sql);

        // добавить к списку областные открытые
        $sql = "SELECT region_id FROM districts WHERE id IN
                (SELECT district_id FROM schools WHERE id IN
                (SELECT school_id FROM users WHERE id = '{$this->user_id}'))";
        $region_id = $this->db->scalar($sql);

        $sql = "SELECT id, mbname, mbdesc, mbtype, user_id FROM methodblogs WHERE region_id = '$region_id' AND
                id IN (SELECT methodblog_id FROM mbspecs WHERE specialization_id IN
                (SELECT specialization_id FROM usertospec WHERE user_id = '{$this->user_id}')) AND
                mbtype = 2
                ORDER BY mbname";
        $data2 = $this->db->query($sql);
        $data = array_merge($data, $data2);
      } else {
        $sql = "SELECT region_id FROM districts WHERE id IN
                (SELECT district_id FROM schools WHERE id IN
                (SELECT school_id FROM users WHERE id = '{$this->user_id}'))";
        $region_id = $this->db->scalar($sql);

        if($this->ustype == 88 || $this->ustype == 99) { // методист района или администратор видит все блоги
          $sql = "SELECT district_id FROM schools WHERE id IN
                  (SELECT school_id FROM users WHERE id = '{$this->user_id}')";
          $district_id = $this->db->scalar($sql);

          $sql = "SELECT id, mbname, mbdesc, mbtype, user_id FROM methodblogs WHERE (region_id = '$region_id'
                  OR region_id = '$district_id') AND
                  id IN (SELECT methodblog_id FROM mbspecs WHERE specialization_id IN
                  (SELECT specialization_id FROM usertospec WHERE user_id = '{$this->user_id}'))
                  ORDER BY mbname";
        } else {  // методист области видит только областные блоги и открытые областные блоги
          $sql = "SELECT id, mbname, mbdesc, mbtype, user_id FROM methodblogs WHERE region_id = '$region_id' AND
                  id IN (SELECT methodblog_id FROM mbspecs WHERE specialization_id IN
                  (SELECT specialization_id FROM usertospec WHERE user_id = '{$this->user_id}')) AND
                  (mbtype = 1 OR mbtype = 2)
                  ORDER BY mbname";
        }
        $data = $this->db->query($sql);
      }

      $specializations = array();
      foreach($data as $rec) {
        $methodblog_id = $rec['id'];
        $sql = "SELECT spname FROM mbspecs AS ms INNER JOIN specializations AS s
                ON s.id = ms.specialization_id WHERE methodblog_id = '$methodblog_id'";
        $specializations[$methodblog_id] = $this->db->query($sql);
      }

      $this->view->assign('methodblogs', $data);
      $this->view->assign('specializations', $specializations);

      $this->view->show('methodblog.list');
    }

    function add() {
      $this->allow(88);

      $sql = "SELECT * FROM specializations ORDER BY id";
      $data = $this->db->query($sql);
      $this->view->assign('_specializations', $data);

      $this->view->show('methodblog.add');
    }

    // save when adding
    function save() {
      $this->allow(88);

      $mbname = $this->db->safe($_POST['mbname']);
      $mbdesc = $this->db->safe($_POST['mbdesc']);
      $user_id = $this->user_id;
      $mbtype =  $this->db->safe($_POST['mbtype']);

      if($mbtype == 1 || $mbtype == 2) {
        $sql = "SELECT region_id FROM districts WHERE id IN
                (SELECT district_id FROM schools WHERE id IN
                (SELECT school_id FROM users WHERE id = '{$this->user_id}'))";
        $region_id = $this->db->scalar($sql);
      } else {
        $sql = "SELECT district_id FROM schools WHERE id IN
                (SELECT school_id FROM users WHERE id = '{$this->user_id}')";
        $region_id = $this->db->scalar($sql);
      }

      $sql = "INSERT INTO methodblogs (mbname, mbdesc, region_id, mbtype, user_id) VALUES
              ('$mbname', '$mbdesc', '$region_id', '$mbtype', '$user_id')";
      $this->db->query($sql);
      $methodblog_id = $this->db->last_id();

      foreach($_POST['specialization_id'] as $rec) {
        $specialization_id = $this->db->safe($rec);

        $sql = "INSERT INTO mbspecs (methodblog_id, specialization_id)
                VALUES ('$methodblog_id', '$specialization_id')";
        $this->db->query($sql);
      }

      $this->view->message('added');

      $this->view->go('/methodblog');
    }

    function delete() {
      $this->allow(88);

      $id = $this->db->safe($_GET['id']);
      if(!$this->isOwner($id)) {
        $this->view->page404();
      }

      $sql = "DELETE FROM methodblogs WHERE id = '$id'";
      $this->db->query($sql);

      $sql = "DELETE FROM mbspecs WHERE methodblog_id = '$id'";
      $this->db->query($sql);

      $sql = "DELETE FROM mbnews WHERE methodblog_id = '$id'";
      $this->db->query($sql);

      $sql = "DELETE FROM mbdialog WHERE methodblog_id = '$id'";
      $this->db->query($sql);

      $this->view->message('deleted');

      $this->view->go('/methodblog');
    }

    function edit() {
      $this->allow(88);

      $id = $this->db->safe($_GET['id']);
      if(!$this->isOwner($id)) {
        $this->view->page404();
      }

      $sql = "SELECT * FROM methodblogs WHERE id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data[0]);

      $specs = array();
      $sql = "SELECT specialization_id FROM mbspecs WHERE methodblog_id = '$id'";
      $data = $this->db->query($sql);
      foreach($data as $rec) {
        $specs[$rec['specialization_id']] = 1;
      }
      $this->view->assign('specs', $specs);

      $sql = "SELECT * FROM specializations ORDER BY id";
      $data = $this->db->query($sql);
      $this->view->assign('_specializations', $data);

      $this->view->show('methodblog.edit');
    }

    // save when editing
    function change() {
      $this->allow(88);

      $id = $this->db->safe($_POST['id']);
      if(!$this->isOwner($id)) {
        $this->view->page404();
      }

      $mbtype = $this->db->safe($_POST['mbtype']);
      $mbname = $this->db->safe($_POST['mbname']);
      $mbdesc = $this->db->safe($_POST['mbdesc']);

      $sql = "UPDATE methodblogs SET mbname = '$mbname', mbdesc = '$mbdesc',
              mbtype = '$mbtype' WHERE id = '$id'";
      $this->db->query($sql);

      $sql = "DELETE FROM mbspecs WHERE methodblog_id = '$id'";
      $this->db->query($sql);

      foreach($_POST['specialization_id'] as $rec) {
        $specialization_id = $this->db->safe($rec);

        $sql = "INSERT INTO mbspecs (methodblog_id, specialization_id)
                VALUES ('$id', '$specialization_id')";
        $this->db->query($sql);
      }

      $this->view->message('changed');

      $this->view->go('/methodblog');
    }

    function addnews() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);
      if(!$this->isOwner($id) && !$this->isAuthor($id)) {
        $this->view->page404();
      }

      $sql = "SELECT * FROM methodblogs WHERE id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('methodblog', $data[0]);

      $this->view->show('methodblog.news.add');
    }

    function savenews() {
      $this->allow(1);

      $methodblog_id = $this->db->safe($_POST['methodblog_id']);
      if(!$this->isOwner($methodblog_id) && !$this->isAuthor($methodblog_id)) {
        $this->view->page404();
      }
      $mbnname = $this->db->safe($_POST['mbnname']);
      $mbntext = $this->db->safe($_POST['mbntext']);
      $mbntime = time();
      $user_id = $this->user_id;

      $sql = "INSERT INTO mbnews (mbnname, mbntime, mbntext, methodblog_id,
              user_id) VALUES ('$mbnname', '$mbntime', '$mbntext', '$methodblog_id',
              '$user_id')";
      $this->db->query($sql);

      $this->view->message('newsadded');

      $this->view->go("/methodblog/show/$methodblog_id");
    }

    function deletenews() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);

      $sql = "SELECT methodblog_id FROM mbnews WHERE id = '$id'";
      $methodblog_id = $this->db->scalar($sql);
      if(!$this->isOwner($methodblog_id) && !$this->isAuthor($methodblog_id)) {
        $this->view->page404();
      }

      $sql = "DELETE FROM mbnews WHERE id = '$id'";
      $this->db->query($sql);

      $this->view->message('newsdeleted');

      $this->view->go("/methodblog/show/$methodblog_id");
    }

    function editnews() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);

      $sql = "SELECT * FROM methodblogs WHERE id IN (SELECT methodblog_id FROM mbnews WHERE id = '$id')";
      $data = $this->db->query($sql);
      $this->view->assign('mbdata', $data[0]);

      $sql = "SELECT * FROM mbnews WHERE id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data[0]);

      $this->view->show('methodblog.news.edit');
    }

    function changenews() {
      $this->allow(1);

      $id = $this->db->safe($_POST['id']);

      $sql = "SELECT methodblog_id FROM mbnews WHERE id = '$id'";
      $methodblog_id = $this->db->scalar($sql);

      if(!$this->isOwner($methodblog_id) && !$this->isAuthor($methodblog_id)) {
        $this->view->page404();
      }

      $mbnname = $this->db->safe($_POST['mbnname']);
      $mbntext = $this->db->safe($_POST['mbntext']);
      $user_id = $this->user_id;

      $sql = "UPDATE mbnews SET mbnname = '$mbnname', mbntext = '$mbntext',
              user_id = '$user_id' WHERE id = '$id'";
      $this->db->query($sql);

      $this->view->message('newschanged');

      $this->view->go("/methodblog/show/$methodblog_id");
    }

    function dialog() {
      //$this->allow(1);

      $id = $this->db->safe($_GET['id']);
      $this->isOwner($id);
      $this->isAuthor($id);

      $sql = "SELECT usname FROM users WHERE id = '{$this->user_id}'";
      $data = $this->db->query($sql);
      $this->view->assign('usinfo', $data);

      $sql = "SELECT * FROM methodblogs WHERE id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('mbdata', $data[0]);

      $sql = "SELECT mbd.id AS mbd_id, user_id, mbdtext, mbdtime, mbdanonym, usname, usplace, scname, mbdusername
              FROM mbdialog AS mbd LEFT JOIN users AS u ON u.id = mbd.user_id
              LEFT JOIN schools AS s ON s.id = u.school_id WHERE methodblog_id = '$id'
              ORDER BY mbdtime DESC";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data);

      $this->view->show('methodblog.dialog');
    }

    function addmessage() {
      // because no separate page with adding form
      $this->savemessage();
    }

    function savemessage() {
      //$this->allow(1);

      $methodblog_id = $this->db->safe($_POST['methodblog_id']);
      $mbdtext = $this->db->safe($_POST['edText']);
      $mbdtime = time();
      $mbdanonym = $this->db->safe($_POST['isanonym']);
      $mbdusername = $this->db->safe($_POST['usfreename']);
      $user_id = $this->user_id;

      $sql = "INSERT INTO mbdialog (mbdtext, mbdtime, user_id, methodblog_id, mbdanonym, mbdusername)
              VALUES ('$mbdtext', '$mbdtime', '$user_id', '$methodblog_id', '$mbdanonym', '$mbdusername')";
      $this->db->query($sql);

      $this->view->message('messageadded');

      $this->view->go("/methodblog/dialog/$methodblog_id");
    }

    function deletemessage() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);

      $sql = "SELECT methodblog_id FROM mbdialog WHERE id = '$id'";
      $methodblog_id = $this->db->scalar($sql);
      if(!$this->isOwner($methodblog_id) && !$this->isAuthor($methodblog_id)) {
        $this->view->page404();
      }

      $sql = "DELETE FROM mbdialog WHERE id = '$id'";
      $this->db->query($sql);

      $this->view->message('messagedeleted');

      $this->view->go("/methodblog/dialog/$methodblog_id");
    }

    function editmessage() {
      $this->allow(1);

      $id = $this->db->safe($_GET['id']);

      $sql = "SELECT * FROM methodblogs WHERE id IN (SELECT methodblog_id FROM mbdialog WHERE id = '$id')";
      $data = $this->db->query($sql);
      $this->view->assign('mbdata', $data[0]);

      $sql = "SELECT mbd.id AS mbd_id, mbdtext, mbdtime, mbdanonym, usname, usplace, scname
              FROM mbdialog AS mbd LEFT JOIN users AS u ON u.id = mbd.user_id
              LEFT JOIN schools AS s ON s.id = u.school_id WHERE mbd.id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('data', $data[0]);

      $this->view->show('methodblog.dialog.edit');
    }

    function changemessage() {
      $this->allow(1);

      $id = $this->db->safe($_POST['id']);
      $methodblog_id = $this->db->safe($_POST['methodblog_id']);
      if(!$this->isOwner($methodblog_id) && !$this->isAuthor($methodblog_id)) {
        $this->view->page404();
      }
      $mbdtext = $this->db->safe($_POST['edText']);
      $mbdanonym = $this->db->safe($_POST['isanonym']);

      $sql = "UPDATE mbdialog SET mbdtext = '$mbdtext', mbdanonym = '$mbdanonym'
              WHERE id = '$id'";
      $this->db->query($sql);

      $this->view->message('messagechanged');

      $this->view->go("/methodblog/dialog/$methodblog_id");

    }

    function settings() {
      $this->allow(88);

      $id = $this->db->safe($_GET['id']);
      if(!$this->isOwner($id)) {
        $this->view->page404();
      }

      $sql = "SELECT mb.id AS mbid, mbname, usname, usplace, scname, s.id AS scid FROM methodblogs AS mb
              LEFT JOIN users AS u ON mb.user_id = u.id LEFT JOIN schools AS s ON s.id = u.school_id
              WHERE mb.id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('methodblog', $data[0]);

      $school_id = $data[0]['scid'];
      $sql = "SELECT id, scname FROM schools WHERE district_id IN
              (SELECT district_id FROM schools WHERE id = '$school_id') ORDER BY scname";
      $data = $this->db->query($sql);
      $this->view->assign('schools', $data);

      $sql = "SELECT user_id, usname FROM mbauthors AS ma LEFT JOIN users AS u ON ma.user_id = u.id WHERE
              methodblog_id = '$id'";
      $data = $this->db->query($sql);
      $this->view->assign('mbauthors', $data);

      $this->view->show('methodblog.settings');
    }

    function savesettings() {
      $this->allow(88);

      $id = $this->db->safe($_POST['methodblog_id']);
      if(!$this->isOwner($id)) {
        $this->view->page404();
      }

      foreach($_POST['mbauthor_info'] AS $k => $rec) {
        if(trim($rec)=='') {
          $user_id = $this->db->safe($_POST['mbauthors'][$k]);

          $sql = "DELETE FROM mbauthors WHERE user_id = '$user_id' AND methodblog_id = '$id'";
          $this->db->query($sql);
        }
      }

      foreach($_POST['newmbauthor_info'] AS $k => $rec) {
        if(trim($rec)=='') {
          continue;
        }

        $user_id = $this->db->safe($_POST['newmbauthors'][$k]);

        $sql = "INSERT INTO mbauthors (user_id, methodblog_id) VALUES
                ('$user_id', '$id')";
        $this->db->query($sql);
      }

      $this->view->message('changed');
      $this->view->go('/methodblog/settings/'.$id);
    }

    function isOwner($mblog_id) {
      // assume mblog_id is safe
      if($this->ustype == 99 || $this->getBlogOwnerId($mblog_id) == $this->user_id) {
        $this->view->assign('blog_owner', '1');
        return true;
      } else {
        $this->view->assign('blog_owner', '0');
        return false;
      }
    }

    function isAuthor($mblog_id) {
      $user_id = $this->user_id;

      $sql = "SELECT COUNT(*) AS cnt FROM mbauthors WHERE
              methodblog_id = '$mblog_id' AND user_id = '$user_id'";

      $cnt = $this->db->scalar($sql);

      $this->view->assign('blog_author', $cnt);
      return $cnt;
    }

    private function getBlogOwnerId($mblog_id) {
      // assume mblog_id is safe

      $sql = "SELECT user_id FROM methodblogs WHERE id = '$mblog_id'";
      $user_id = $this->db->scalar($sql);

      return $user_id;
    }
}

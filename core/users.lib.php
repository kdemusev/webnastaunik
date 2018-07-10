<?php

include ('base.lib.php');

class CUsers extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('register', 'login', 'logout', 'show',
                             'getdistricts', 'getschools', 'getteachers',
                             'checklogin', 'findteacher', 'getlogin',
                             'settings', 'edit', 'adminedit');
  }

  function index() {
    if($this->user_id == 0) {
      $sql = "SELECT * FROM regions ORDER BY rgname";
      $data = $this->db->query($sql);
      $this->view->assign('_regions', $data);

      // show freewebinars
      $time = time();
      $sql = "SELECT id, wbname, wbfreestart, wbfreeend FROM webinars WHERE wbfreestart > 0
              AND wbfreestart < $time AND wbfreeend > $time ORDER BY wbfreestart DESC";
      $data = $this->db->query($sql);
      $this->view->assign('freewebinars', $data);

      $this->view->show('welcome');
      return;
    }


    // start guide
    $usrights = $this->db->scalar("SELECT usrights FROM users WHERE id = '{$this->user_id}'");
    $ustype = $this->ustype;
    $school_id = $this->getSchoolId();

    $sql = "SELECT teacher_id FROM users WHERE id = '{$this->user_id}'";
    $teacher_id = $this->db->scalar($sql);

    if($usrights > 1) {
      $this->view->assign('startguide', 'wait');
    } else {
      if($teacher_id < 1) {
        $this->view->assign('startguide', 'teacher');
      } else {
        $sql = "SELECT COUNT(*) AS cnt FROM ktp WHERE subject_id IN
                (SELECT id FROM subjects WHERE teacher_id = '$teacher_id')";
        $ktpcnt = $this->db->scalar($sql);
        if($ktpcnt == 0) {
          $this->view->assign('startguide', 'ktp');
        }
      }
    }

    // lesson
    $now = mktime(date('G'),date('i'),0,1,1,1970);
    $ttday = date('N') - 1;

    $sql = "SELECT sbname, fmname, ttstart, ttend, ttnumber FROM timetable AS t INNER JOIN subjects AS s
            ON t.subject_id = s.id INNER JOIN forms AS f ON t.form_id = f.id
            WHERE school_id = '$school_id' AND s.teacher_id = '$teacher_id' AND
            t.ttday = '$ttday' AND (ttstart >= $now OR (ttstart <= $now AND ttend > $now))
            ORDER BY ttnumber";
    $data = $this->db->query($sql);
    $this->view->assign('lesson', $data);
    $this->view->assign('timenow', $now);

    $today = mktime(0,0,0);
    // tasks
    $sql = "SELECT * FROM tasks WHERE user_id = '{$this->user_id}' AND
            tsdate = $today AND tsweek = 0
            ORDER BY tspriority LIMIT 3";
    $data = $this->db->query($sql);
    $this->view->assign('tasks', $data);

    // news
    $sql = "SELECT * FROM news ORDER BY nstime DESC LIMIT 2";
    $data = $this->db->query($sql);
    $this->view->assign('news', $data);

    // messages
    $sql = "SELECT mstopic
            FROM messages AS m
            WHERE m.user_id = '{$this->user_id}' AND msreaded = 0 ORDER BY mstime DESC LIMIT 5";
    $data = $this->db->query($sql);
    $this->view->assign('messages', $data);

    // forum
    $sql = "SELECT fmtpname
            FROM fmtopics
            ORDER BY fmtpposts, fmtptime DESC LIMIT 3";
    $data = $this->db->query($sql);
    $this->view->assign('forum', $data);

    // webinar
    $timenow = time();
    $sql = "SELECT wbname FROM webinars AS w INNER JOIN webinarspecs AS s ON
            w.id = s.webinar_id WHERE (wbtype = 1 OR district_id IN (SELECT district_id FROM schools WHERE id IN (SELECT school_id FROM users WHERE id = '{$this->user_id}')))
            AND specialization_id IN (SELECT specialization_id FROM
            usertospec WHERE user_id = '{$this->user_id}') AND wbstart <= $timenow AND wbend >= $timenow
            ORDER BY wbend";
    $data = $this->db->query($sql);
    $this->view->assign('webinars', $data);

    // timetable
    $ttday = date('N') - 1;
    $sql = "SELECT ttnumber, sbname, fmname FROM timetable AS t LEFT JOIN subjects AS s
            ON t.subject_id = s.id LEFT JOIN forms AS f ON t.form_id = f.id
            WHERE teacher_id = '$teacher_id' AND
            ttday = '$ttday'
            ORDER BY ttnumber";
    $data = $this->db->query($sql);
    $this->view->assign('timetable', $data);

    // notifications
    $sql = "SELECT nttopic FROM notifications WHERE user_id = '{$this->user_id}'
            ORDER BY nttime LIMIT 5";
    $data = $this->db->query($sql);
    $this->view->assign('notifications', $data);

    $this->view->show('now');
  }

  function register() {
      if(isset($_POST['uslogin'])) {
          $school_id = $this->db->safe($_POST['school_id']);
          $usname = $this->db->safe($_POST['usname']);
          $uslogin = $this->db->safe($_POST['uslogin']);
          $uspassword = $this->db->safe($_POST['uspass']);
          $usrights = $this->db->safe($_POST['usrights']);
          $usphone = $this->db->safe($_POST['usphone']);
          $usemail = $this->db->safe($_POST['usemail']);
          $usplace = $this->db->safe($_POST['usplace']);

          // insert new school if it is neccessary
          if($school_id==-1) {
              $scname = $this->db->safe($_POST['scname']);
              $district_id = $this->db->safe($_POST['district_id']);
              $this->db->query("INSERT INTO schools (district_id, scname) VALUES
                                ('$district_id', '$scname')");
              $school_id = $this->db->last_id();
          }

          // place resolver
          switch($usplace) {
            case '1': $usplace = 'учитель'; break;
            case '2': $usplace = 'заместитель директора'; break;
            case '3': $usplace = 'директор'; break;
            case '4': $usplace = 'методист'; break;
            default: $usplace = $this->db->safe($_POST['newusplace']); break;
          }

          // insert teacher
          $this->db->query("INSERT INTO users (school_id, usname, uslogin, uspassword,
                            ustype, usphone, usemail, usplace, usrights) VALUES ('$school_id', '$usname',
                            '$uslogin', '$uspassword', '1', '$usphone', '$usemail', '$usplace', '$usrights')");

          // insert teacher specializations
          $teacher_id = $this->db->last_id();
          $db = '';
          foreach($_POST['specialization_id'] as $rec) {
              $rec = $this->db->safe($rec);
              $db .= "('$teacher_id', '$rec'),";
          }
          $db = rtrim($db, ',');
          $this->db->query("INSERT INTO usertospec (user_id, specialization_id)
                            VALUES $db");

          // end up
          if($usrights == 1) {
            $this->view->message('registered');
            $this->notifyadmin('Зарегистрировался новый пользователь', '/users/edit/'.$teacher_id);
          } else {
            $this->view->message('registeredadmin');
            $this->notifyadmin('Зарегистрировался новый пользователь. Запрошены права', '/users/edit/'.$teacher_id);
          }

          $schooladmin = $this->db->query("SELECT id FROM users WHERE ustype > 1 AND school_id = '$school_id'");
          foreach($schooladmin as $rec) {
            $adminid = $rec['id'];
            $this->notify($adminid, "Зарегистрирован новый пользователь $usname. Определите его принадлежность к педагогу", '/school/teachers');
          }

          header('Location: /');
          return;
      }
      $data = $this->db->query("SELECT id, rgname FROM regions ORDER BY rgname");
      $this->view->assign('_regions', $data);

      $data = $this->db->query("SELECT id, spname FROM specializations ORDER BY id");
      $this->view->assign('_specializations', $data);

      $this->view->show('register');
  }

  function settings() {
    $this->allow(1);

    $user_id = $this->user_id;

    if(isset($_POST['usname'])) {
        $usname = $this->db->safe($_POST['usname']);
        $uspassword = $this->db->safe($_POST['uspass']);
        $usrights = $this->db->safe($_POST['usrights']);
        $usphone = $this->db->safe($_POST['usphone']);
        $usemail = $this->db->safe($_POST['usemail']);
        $usplace = $this->db->safe($_POST['usplace']);

        // place resolver
        switch($usplace) {
          case '1': $usplace = 'учитель'; break;
          case '2': $usplace = 'заместитель директора'; break;
          case '3': $usplace = 'директор'; break;
          case '4': $usplace = 'методист'; break;
          default: $usplace = $this->db->safe($_POST['newusplace']); break;
        }

        // insert teacher
        $this->db->query("UPDATE users SET usname = '$usname', usphone = '$usphone', usemail = '$usemail',
                          usplace='$usplace' WHERE id = '$user_id'");

        // insert teacher specializations
        $sql = "DELETE FROM usertospec WHERE user_id = '$user_id'";
        $this->db->query($sql);
        $db = '';
        foreach($_POST['specialization_id'] as $rec) {
            $rec = $this->db->safe($rec);
            $db .= "('$user_id', '$rec'),";
        }
        $db = rtrim($db, ',');
        $this->db->query("INSERT INTO usertospec (user_id, specialization_id)
                          VALUES $db");

        // change user type
        if($usrights != $this->ustype) {
          if($usrights == 1) {
            $sql = "UPDATE users SET ustype = '1' WHERE id = '$user_id'";
            $this->db->query($sql);
          }
          $sql = "UPDATE users SET usrights = '$usrights' WHERE id = '$user_id'";
          $this->db->query($sql);
        }

        // change password
        if($uspassword!='') {
          $sql = "UPDATE users SET uspassword = '$uspassword' WHERE id = '$user_id'";
          $this->db->query($sql);
        }

        // end up
        if($usrights == 1 || $usrights == $this->ustype) {
          $this->view->message('saved');
        } else {
          $this->view->message('savedadmin');
          $this->notifyadmin('Запрошены права', '/users/edit/'.$user_id);
        }
        header('Location: /user/settings');
        return;
    }

    $sql = "SELECT * FROM users WHERE id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('user', $data[0]);

    $data = $this->db->query("SELECT id, spname FROM specializations ORDER BY id");
    $this->view->assign('_specializations', $data);

    $sql = "SELECT specialization_id FROM usertospec WHERE user_id = '$user_id'";
    $data = $this->db->query($sql);
    $specs = array();
    foreach($data as $rec) {
      $specs[$rec['specialization_id']] = 1;
    }
    $this->view->assign('specs', $specs);

    $this->view->show('user.settings');
  }

  function login() {
    $uslogin = $this->db->safe($_POST['uslogin']);
    $uspassword = $this->db->safe($_POST['uspassword']);
    $usremember = isset($_POST['usremember']) ? 1 : 0;

    $data = $this->db->query("SELECT id, uspassword, usname, school_id
                              FROM users WHERE
                              uslogin = '$uslogin'");
    if(count($data) == 0) {
        $this->view->message('wronglogin');
        header('Location: /');
        return;
    } else if($data[0]['uspassword'] != $uspassword) {
        $this->view->message('wrongpass');
        header('Location: /');
        return;
    }

    $md5 = md5(time());
    $hash = substr($md5, 0, 4).$data[0]['id'].'a'.substr($md5, 6+strlen($data[0]['id']));
    if($usremember) {
      setcookie('sc2session', $hash, time()+86400*30, '/');
    } else {
      setcookie('sc2session', $hash, 0, '/');
    }

    $this->view->go('/');
  }

  function logout() {
    setcookie('sc2session', '0', 1, '/');
    $this->view->go('/');
  }

  function getdistricts() {
    $id = $this->db->safe($_GET['id']);
    $data = $this->db->query("SELECT id, dtname FROM districts WHERE region_id = $id ORDER BY dtname");
    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<regions>\n";
    foreach($data as $rec) {
        print '<region id="'.$rec['id'].'">'.$rec['dtname'].'</region>'."\n";
    }
    print "</regions>\n";
  }

  function getschools() {
    $id = $this->db->safe($_GET['id']);
    $data = $this->db->query("SELECT id, scname FROM schools WHERE district_id = $id ORDER BY scname");
    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<schools>\n";
    foreach($data as $rec) {
        print '<school id="'.$rec['id'].'">'.$rec['scname'].'</school>'."\n";
    }
    print "</schools>\n";
    return;
  }

  function getteachers() {
    $id = $this->db->safe($_GET['id']);
    $sql = "SELECT id, usname FROM users WHERE school_id = $id
            ORDER BY usname";
    $data = $this->db->query($sql);
    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<teachers>\n";
    foreach($data as $rec) {
        print '<teacher id="'.$rec['id'].'">'.$rec['usname'].'</teacher>'."\n";
    }
    print "</teachers>\n";
    return;
  }

  function findteacher() {
    $id = $this->db->safe($_GET['id']);

    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<result>\n";

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$id'");
    $sql = "SELECT id, usname FROM users WHERE school_id = '$school_id'
            ORDER BY usname";
    $data = $this->db->query($sql);

    foreach($data as $rec) {
        print '<teacher id="'.$rec['id'].'">'.$rec['usname'].'</teacher>'."\n";
    }

    $district_id = $this->db->scalar("SELECT district_id FROM schools WHERE id = '$school_id'");
    $data = $this->db->query("SELECT id, scname FROM schools WHERE district_id = '$district_id' ORDER BY scname");
    foreach($data as $rec) {
        print '<school id="'.$rec['id'].'">'.$rec['scname'].'</school>'."\n";
    }

    $region_id = $this->db->scalar("SELECT region_id FROM districts WHERE id = '$district_id'");
    $data = $this->db->query("SELECT id, dtname FROM districts WHERE region_id = '$region_id' ORDER BY dtname");
    foreach($data as $rec) {
        print '<region id="'.$rec['id'].'">'.$rec['dtname'].'</region>'."\n";
    }

    print '<selected school_id="'.$school_id.'" district_id="'.$district_id.'" region_id="'.$region_id.'"></selected>';

    print "</result>\n";
    return;
  }

  function checklogin() {
    $uslogin = $this->db->safe($_POST['uslogin']);

    $sql = "SELECT id, usname FROM users WHERE uslogin = '$uslogin'";
    $data = $this->db->query($sql);

    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    if(count($data) > 0) {
      print "<result isexists=\"true\">\n";
      print "<id>".$data[0]['id']."</id>\n";
      print "<usname>".$data[0]['usname']."</usname>\n";
      print "</result>\n";
    } else {
      print "<result isexists=\"false\">\n";
      print "</result>\n";
    }
  }

  function getlogin() {
    $id = $this->db->safe($_GET['id']);

    $sql = "SELECT uslogin FROM users WHERE id = '$id'";
    $data = $this->db->query($sql);

    header('Content-type: text/xml');
    print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    print "<result>".$data[0]['uslogin']."</result>\n";
  }

  function show() {
    $this->allow(99);

    if(isset($_POST['school_id']) && $_POST['school_id']!=0) {
      $school_id = $this->db->safe($_POST['school_id']);
      $this->view->assign('school_id', $school_id);
    } else {
      $this->view->assign('school_id', 0);
    }
    if(isset($_POST['district_id']) && $_POST['district_id']!=0) {
      $district_id = $this->db->safe($_POST['district_id']);
      $this->view->assign('district_id', $district_id);
      $sql = "SELECT id, scname FROM schools WHERE district_id = '$district_id'
              ORDER BY scname";
      $data = $this->db->query($sql);
      $this->view->assign('_schools', $data);
    } else {
      $this->view->assign('district_id', 0);
    }
    if(isset($_POST['region_id']) && $_POST['region_id']!=0) {
      $region_id = $this->db->safe($_POST['region_id']);
      $this->view->assign('region_id', $region_id);
      $sql = "SELECT id, dtname FROM districts WHERE region_id = '$region_id'
              ORDER BY dtname";
      $data = $this->db->query($sql);
      $this->view->assign('_districts', $data);
    } else {
      $this->view->assign('region_id', 0);
    }
    $data = $this->db->query("SELECT id, rgname FROM regions ORDER BY rgname");
    $this->view->assign('_regions', $data);

    if(isset($_POST['save'])) {
      foreach($_POST['usname'] AS $id => $rec) {
        $id = $this->db->safe($id);

        if(trim($rec) == '') {
          $sql = "UPDATE users SET uslogin = '01737del17', uspassword = 'protected', usname='   ',
                  school_id = 0, teacher_id = 0
                  WHERE id = '$id'";
          $this->db->query($sql);
        }
      }

      $this->view->message('edited');
    }

    if(isset($school_id)) {
      $sql = "SELECT *, u.id AS user_id FROM users AS u LEFT JOIN schools AS s ON u.school_id = s.id
              WHERE school_id = '$school_id' AND uslogin != '01737del17' ORDER BY scname, usname";
    } else if(isset($district_id)) {
      $sql = "SELECT *, u.id AS user_id FROM users AS u LEFT JOIN schools AS s ON u.school_id = s.id
              WHERE school_id IN
              (SELECT id FROM schools WHERE district_id = '$district_id') AND uslogin != '01737del17'
              ORDER BY scname, usname";
    } else if(isset($region_id)) {
      $sql = "SELECT *, u.id AS user_id FROM users AS u LEFT JOIN schools AS s ON u.school_id = s.id
              WHERE school_id IN
              (SELECT id FROM schools WHERE district_id IN
              (SELECT id FROM districts WHERE region_id = '$region_id')) AND uslogin != '01737del17'
              ORDER BY scname, usname";
    } else {
      $sql = "SELECT *, u.id AS user_id FROM users AS u LEFT JOIN schools AS s ON u.school_id = s.id
              WHERE uslogin != '01737del17' ORDER BY scname, usname";
    }
    $data = $this->db->query($sql);
    $this->view->assign('users', $data);

    $this->view->show('users.show');
  }

  function edit() {
    $this->allow(99);

    $user_id = $this->db->safe($_GET['id']);

    $sql = "SELECT * FROM users WHERE id = '$user_id'";
    $data = $this->db->query($sql);
    $this->view->assign('user', $data[0]);

    $data = $this->db->query("SELECT id, spname FROM specializations ORDER BY id");
    $this->view->assign('_specializations', $data);

    $sql = "SELECT specialization_id FROM usertospec WHERE user_id = '$user_id'";
    $data = $this->db->query($sql);
    $specs = array();
    foreach($data as $rec) {
      $specs[$rec['specialization_id']] = 1;
    }
    $this->view->assign('specs', $specs);

    $this->view->show('users.edit');
  }

  function adminedit() {
    $this->allow(99);

    $user_id = $this->db->safe($_POST['user_id']);
    $usname = $this->db->safe($_POST['usname']);
    $uspassword = $this->db->safe($_POST['uspass']);
    $ustype = $this->db->safe($_POST['ustype']);
    $usphone = $this->db->safe($_POST['usphone']);
    $usemail = $this->db->safe($_POST['usemail']);
    $usplace = $this->db->safe($_POST['usplace']);

    // place resolver
    switch($usplace) {
      case '1': $usplace = 'учитель'; break;
      case '2': $usplace = 'заместитель директора'; break;
      case '3': $usplace = 'директор'; break;
      case '4': $usplace = 'методист'; break;
      default: $usplace = $this->db->safe($_POST['newusplace']); break;
    }

    // get old ustype
    $oldtype = $this->db->scalar("SELECT ustype FROM users WHERE id = '$user_id'");
    if($oldtype != $ustype) {
      if($ustype > 1) {
        $this->notify($user_id, 'Вам предоставлены расширенные права доступа', '/');
      } else {
        $this->notify($user_id, 'Расширенные права доступа к порталу отменены', '/');
      }
    }

    // insert teacher
    $this->db->query("UPDATE users SET usname = '$usname', usphone = '$usphone', usemail = '$usemail',
                      usplace='$usplace', ustype='$ustype', usrights='0', uspassword = '$uspassword' WHERE id = '$user_id'");

    // insert teacher specializations
    $sql = "DELETE FROM usertospec WHERE user_id = '$user_id'";
    $this->db->query($sql);
    $db = '';
    foreach($_POST['specialization_id'] as $rec) {
        $rec = $this->db->safe($rec);
        $db .= "('$user_id', '$rec'),";
    }
    $db = rtrim($db, ',');
    $this->db->query("INSERT INTO usertospec (user_id, specialization_id)
                      VALUES $db");

    // end up
    $this->view->message('edited');

    $sql = "SELECT region_id, district_id, school_id FROM users AS u
            LEFT JOIN schools AS s ON u.school_id = s.id
            LEFT JOIN districts AS d ON s.district_id = d.id
            WHERE u.id = '$user_id'";
    $data = $this->db->query($sql);
    $region_id = $data[0]['region_id'];
    $district_id = $data[0]['district_id'];
    $school_id = $data[0]['school_id'];

    print '<form method="post" action="/users/show" style="display: none">';
    print '<input type="hidden" name="region_id" value="'.$region_id.'">';
    print '<input type="hidden" name="district_id" value="'.$district_id.'">';
    print '<input type="hidden" name="school_id" value="'.$school_id.'">';
    print '</form>';
    print '<script> document.forms[0].submit(); </script>';
  }
}

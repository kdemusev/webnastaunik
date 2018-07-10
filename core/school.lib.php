<?php

// <!-- checked 4 -->

include ('base.lib.php');

class CSchool extends CBase{

    function __construct($db, $view) {
        parent::__construct($db, $view);

        $this->actions = array('rooms',
                               'forms',
                               'teachers',
                               'subjects', 'plan',
                               'bellsgroups', 'bells', 'assignbells',
                               'pupils', 'showpupils', 'show');
    }

    function rooms() {
      $this->allow(87);

      $school_id = $this->getSchoolId();

      if(isset($_POST['save'])) {
        if(isset($_POST['rmnumber'])) {
          foreach($_POST['rmnumber'] as $id => $rmnumber) {
            $id = $this->db->safe($id);
            $rmnumber = $this->db->safe($rmnumber);

            if(trim($rmnumber) == '') {
              $sql = "DELETE FROM rooms WHERE id = '$id'";
              $this->db->query($sql);
              continue;
            }

            $rmname = $this->db->safe($_POST['rmname'][$id]);

            $sql = "UPDATE rooms SET rmnumber = '$rmnumber', rmname = '$rmname'
                    WHERE id = '$id'";
            $this->db->query($sql);
          }
        }

        foreach($_POST['newrmnumber'] as $k => $rmnumber) {
          $rmnumber = $this->db->safe($rmnumber);

          if(trim($rmnumber) == '') {
            continue;
          }

          $rmname = $this->db->safe($_POST['newrmname'][$k]);

          $sql = "INSERT INTO rooms(rmnumber, rmname, school_id) VALUES
                  ('$rmnumber', '$rmname', '$school_id')";
          $this->db->query($sql);
        }

        $this->view->message('updated');
      }

      $data = $this->db->query("SELECT * FROM rooms WHERE school_id = '$school_id'
                                ORDER BY LEFT(rmnumber, 1), LENGTH(rmnumber), rmnumber");

      $this->view->assign('_data', $data);
      $this->view->show('school.rooms');
    }

    function forms() {
      $this->allow(87);

      $school_id =  $this->getSchoolId();

      if(isset($_POST['save'])) {
        if(isset($_POST['fmname'])) {
          foreach($_POST['fmname'] as $id => $fmname) {
            $id = $this->db->safe($id);
            $fmname = $this->db->safe($fmname);

            if(trim($fmname) == '') {
              $sql = "DELETE FROM forms WHERE id = '$id'";
              $this->db->query($sql);
              continue;
            }

            $sql = "UPDATE forms SET fmname = '$fmname'
                    WHERE id = '$id'";
            $this->db->query($sql);
          }
        }

        foreach($_POST['newfmname'] as $k => $fmname) {
          $fmname = $this->db->safe($fmname);

          if(trim($fmname) == '') {
            continue;
          }

          $sql = "INSERT INTO forms(fmname, school_id) VALUES
                  ('$fmname', '$school_id')";
          $this->db->query($sql);
        }

        $this->view->message('updated');
      }

      $data = $this->db->query("SELECT * FROM forms WHERE school_id = '$school_id'
                                ORDER BY fmname+0");

      $this->view->assign('_data', $data);
      $this->view->show('school.forms');
    }

    function teachers() {
      $this->allow(87);

      $user_id = $this->user_id;
      $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$user_id'");

      if(isset($_POST['save'])) {
        if(isset($_POST['tcname'])) {
          foreach($_POST['tcname'] as $id => $tcname) {
            $id = $this->db->safe($id);
            $tcname = trim($this->db->safe($tcname));

            if($tcname == '') {
                $sql = "DELETE FROM teachers WHERE id = '$id' AND
                        school_id = '$school_id'";
                $this->db->query($sql);
                continue;
            }

            $tcpriority = $this->db->safe($_POST['tcpriority'][$id]);

            $sql = "UPDATE teachers SET tcname = '$tcname', tcpriority = '$tcpriority'
                    WHERE id = '$id' AND school_id = '$school_id'";
            $this->db->query($sql);

            $user_id = isset($_POST['user_id'][$id]) ? $this->db->safe($_POST['user_id'][$id]) : 0;
            if($user_id > 0) {
              $oldteacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");
              if($oldteacher_id!=$id) {
                $sql = "UPDATE users SET teacher_id = '$id' WHERE id = '$user_id'";
                $this->db->query($sql);
                $this->notify($user_id, 'Определена Ваша принадлежнасть к педагогу. Вам доступны все возможности портала', '/');
              }
            }
          }
        }

        foreach($_POST['newtcname'] as $k => $tcname) {
          $tcname = trim($this->db->safe($tcname));
          $tcpriority = $this->db->safe($_POST['newtcpriority'][$k]);

          if($tcname != '') {
            $sql = "INSERT INTO teachers(tcname, tcpriority, school_id) VALUES
                    ('$tcname', '$tcpriority', '$school_id')";
            $this->db->query($sql);
            $id = $this->db->last_id();

            $user_id = $this->db->safe($_POST['newuser_id'][$k]);
            if($user_id > 0) {
              $oldteacher_id = $this->db->scalar("SELECT teacher_id FROM users WHERE id = '$user_id'");
              if($oldteacher_id!=$id) {
                $sql = "UPDATE users SET teacher_id = '$id' WHERE id = '$user_id'";
                $this->db->query($sql);
                $this->notify($user_id, 'Определена Ваша принадлежнасть к педагогу. Вам доступны все возможности портала', '/');
              }
            }
          }
        }

        $this->view->message('updated');
      }

      $sql = "SELECT id, usname FROM users WHERE school_id = '$school_id'";
      $data = $this->db->query($sql);
      array_unshift($data, array('id'=>0,'usname'=>''));
      $this->view->assign('_users', $data);

      $sql = "SELECT t.id AS tc_id, tcname, tcpriority, u.id AS user_id
              FROM teachers AS t LEFT JOIN users AS u ON u.teacher_id = t.id
              WHERE t.school_id = '$school_id'
              ORDER BY tcpriority";
      $data = $this->db->query($sql);
      $this->view->assign('_data', $data);

      $this->view->show('school.teachers');
    }

    function plan() {
      $this->allow(87);

      $user_id = $this->user_id;
      $school_id = $this->getSchoolId();

      $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                 $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                                    ORDER BY fmname+0 LIMIT 1");
      $this->view->assign('form_id', $form_id);

      $data = $this->db->query("SELECT * FROM forms WHERE school_id = '$school_id'
                                ORDER BY fmname+0");
      $this->view->assign('_forms', $data);

      $data = $this->db->query("SELECT * FROM subjects WHERE form_id = '$form_id' ORDER BY sbpriority");
      $this->view->assign('_data', $data);

      $this->view->show('school.plan');
    }

    function subjects() {
      $this->allow(87);

        $user_id = $this->user_id;
        $school_id = $this->getSchoolId();

        $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                   $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                                      ORDER BY fmname+0 LIMIT 1");
        $this->view->assign('form_id', $form_id);

        if(isset($_POST['save'])) {
            if(isset($_POST['sbname'])) {
                foreach($_POST['sbname'] as $id => $sbname) {
                    $id = $this->db->safe($id);
                    $sbname = trim($this->db->safe($sbname));

                    if($sbname == '') {
                        $sql = "DELETE FROM subjects WHERE id = '$id'";
                        $this->db->query($sql);
                        $sql = "DELETE FROM timetable WHERE subject_id = '$id'";
                        $this->db->query($sql);
                        continue;
                    }

                    $sbhours = trim($this->db->safe($_POST['sbhours'][$id]));
                    $sbrating = trim($this->db->safe($_POST['sbrating'][$id]));
                    $teacher_id = $this->db->safe($_POST['teacher_id'][$id]);
                    $sbpriority = $this->db->safe($_POST['sbpriority'][$id]);

                    $sql = "UPDATE subjects SET sbname = '$sbname',
                            sbhours = '$sbhours',
                            sbrating = '$sbrating',
                            teacher_id = '$teacher_id',
                            sbpriority = '$sbpriority',
                            sbiselective = '0'
                            WHERE id = '$id'";
                    $this->db->query($sql);
                }
            }

            foreach($_POST['newsbname'] as $id => $sbname) {
                $id = $this->db->safe($id);
                $sbname = trim($this->db->safe($sbname));

                if($sbname != '') {
                    $sbhours = trim($this->db->safe($_POST['newsbhours'][$id]));
                    $sbrating = trim($this->db->safe($_POST['newsbrating'][$id]));
                    $teacher_id = $this->db->safe($_POST['newteacher_id'][$id]);
                    $sbpriority = $this->db->safe($_POST['newsbpriority'][$id]);
                    $sql = "INSERT INTO subjects(sbname, sbhours, sbrating, teacher_id, form_id, sbpriority, sbiselective) VALUES
                            ('$sbname', '$sbhours', '$sbrating', '$teacher_id', '$form_id', '$sbpriority', '0')";
                    $this->db->query($sql);
                }
            }

            // elective courses
            if(isset($_POST['esbname'])) {
                foreach($_POST['esbname'] as $id => $sbname) {
                    $id = $this->db->safe($id);
                    $sbname = trim($this->db->safe($sbname));

                    if($sbname == '') {
                        $sql = "DELETE FROM subjects WHERE id = '$id'";
                        $this->db->query($sql);
                        $sql = "DELETE FROM timetable WHERE subject_id = '$id'";
                        $this->db->query($sql);
                        continue;
                    }

                    $sbhours = trim($this->db->safe($_POST['esbhours'][$id]));
                    $sbrating = 0;
                    $teacher_id = $this->db->safe($_POST['eteacher_id'][$id]);
                    $sbpriority = $this->db->safe($_POST['esbpriority'][$id]);

                    $sql = "UPDATE subjects SET sbname = '$sbname',
                            sbhours = '$sbhours',
                            sbrating = '$sbrating',
                            teacher_id = '$teacher_id',
                            sbpriority = '$sbpriority',
                            sbiselective = '1'
                            WHERE id = '$id'";
                    $this->db->query($sql);
                }
            }

            foreach($_POST['newesbname'] as $id => $sbname) {
                $id = $this->db->safe($id);
                $sbname = trim($this->db->safe($sbname));

                if($sbname != '') {
                    $sbhours = trim($this->db->safe($_POST['newesbhours'][$id]));
                    $sbrating = 0;
                    $teacher_id = $this->db->safe($_POST['neweteacher_id'][$id]);
                    $sbpriority = $this->db->safe($_POST['newesbpriority'][$id]);
                    $sql = "INSERT INTO subjects(sbname, sbhours, sbrating, teacher_id, form_id, sbpriority, sbiselective) VALUES
                            ('$sbname', '$sbhours', '$sbrating', '$teacher_id', '$form_id', '$sbpriority', '1')";
                    $this->db->query($sql);
                }
            }

            $this->view->message('updated');
        }

        $this->query('_teachers',
          "SELECT id, tcname FROM teachers WHERE school_id = '$school_id'
           ORDER BY tcname");

        $this->query('_forms',
          "SELECT * FROM forms WHERE school_id = '$school_id'
           ORDER BY fmname+0, fmname");

        $this->query('_data',
          "SELECT * FROM subjects WHERE form_id = '$form_id' 
           ORDER BY sbpriority, id");
  //      print nl2br(print_r($this->db->query("SELECT * FROM subjects WHERE form_id = '$form_id'
    //     ORDER BY sbpriority, id"), true));
//die();
        $this->view->show('school.subjects');
    }

    function bellsgroups() {
      $this->allow(87);

      $school_id = $this->getSchoolId();

      if(isset($_POST['save'])) {
        if(isset($_POST['bgname'])) {
          foreach($_POST['bgname'] as $id => $bgname) {
            $id = $this->db->safe($id);
            $bgname = $this->db->safe($bgname);

            if(trim($bgname) == '') {
              $sql = "DELETE FROM bellsgroups WHERE id = '$id'";
              $this->db->query($sql);

              $sql = "DELETE FROM bells WHERE bellsgroup_id = '$id'";
              $this->db->query($sql);
              continue;
            }

            $sql = "UPDATE bellsgroups SET bgname = '$bgname'
                    WHERE id = '$id'";
            $this->db->query($sql);
          }
        }

        foreach($_POST['newbgname'] as $k => $bgname) {
          $bgname = $this->db->safe($bgname);

          if(trim($bgname) == '') {
            continue;
          }

          $sql = "INSERT INTO bellsgroups(bgname, school_id) VALUES
                  ('$bgname', '$school_id')";
          $this->db->query($sql);
        }

        $this->view->message('updated');
      }

      $sql = "SELECT * FROM bellsgroups WHERE school_id = '$school_id'
              ORDER BY bgname";
      $data = $this->db->query($sql);
      $this->view->assign('bellsgroups', $data);
      $this->view->show('school.bells.groups');
    }

    function bells() {
      $this->allow(87);

      $bellsgroup_id = $this->db->safe($_GET['id']);

      $sql = "SELECT * FROM bellsgroups WHERE id = '$bellsgroup_id'";
      $data = $this->db->query($sql);
      $this->view->assign('bellsgroup', $data[0]);

      if(isset($_POST['save'])) {
        if(isset($_POST['shour'])) {
          foreach($_POST['shour'] as $id => $shour) {
            $id = $this->db->safe($id);
            $shour = $this->db->safe($shour);

            if(trim($shour) == '') {
              $sql = "DELETE FROM bells WHERE id = '$id'";
              $this->db->query($sql);
              continue;
            }

            $blnumber = $this->db->safe($_POST['blnumber'][$id]);
            $smin = $this->db->safe($_POST['smin'][$id]);
            $ehour = $this->db->safe($_POST['ehour'][$id]);
            $emin = $this->db->safe($_POST['emin'][$id]);

            $blstart = mktime($shour, $smin, 0, 1, 1, 1970);
            $blend = mktime($ehour, $emin, 0, 1, 1, 1970);

            $sql = "UPDATE bells SET blstart = '$blstart', blend = '$blend',
                    blnumber = '$blnumber'
                    WHERE id = '$id'";
            $this->db->query($sql);
          }
        }

        foreach($_POST['newshour'] as $k => $shour) {
          $shour = $this->db->safe($shour);

          if(trim($shour) == '') {
            continue;
          }

          $blnumber = $this->db->safe($_POST['newblnumber'][$k]);
          $smin = $this->db->safe($_POST['newsmin'][$k]);
          $ehour = $this->db->safe($_POST['newehour'][$k]);
          $emin = $this->db->safe($_POST['newemin'][$k]);

          $blstart = mktime($shour, $smin, 0, 1, 1, 1970);
          $blend = mktime($ehour, $emin, 0, 1, 1, 1970);

          $sql = "INSERT INTO bells (blstart, blend, blnumber, bellsgroup_id)
                  VALUES ('$blstart', '$blend', '$blnumber', '$bellsgroup_id')";
          $this->db->query($sql);
        }

        $this->view->message('updated');
      }

      $sql = "SELECT * FROM bells WHERE bellsgroup_id = '$bellsgroup_id'";
      $data = $this->db->query($sql);
      $bells = array();
      foreach($data as $k => $rec) {
        $bells[$k]['blnumber'] = $rec['blnumber'];
        $bells[$k]['id'] = $rec['id'];
        $bells[$k]['shour'] = date('G',$rec['blstart']);
        $bells[$k]['smin'] = date('i',$rec['blstart']);
        $bells[$k]['ehour'] = date('G',$rec['blend']);
        $bells[$k]['emin'] = date('i',$rec['blend']);
      }
      $this->view->assign('bells', $bells);

      $this->view->show('school.bells');
    }

    function assignbells() {
      $this->allow(87);

      $school_id = $this->getSchoolId();
      $bellsgroup_id = $this->db->safe($_GET['id']);

      $sql = "SELECT * FROM bellsgroups WHERE id = '$bellsgroup_id'";
      $data = $this->db->query($sql);
      $this->view->assign('bellsgroup', $data[0]);

      if(isset($_POST['save'])) {
        $sql = "SELECT * FROM bells WHERE bellsgroup_id = '$bellsgroup_id'";
        $bells = $this->db->query($sql);

        foreach($_POST['bells'] as $form => $rec) {
          foreach($rec as $day => $r2) {
            $form = $this->db->safe($form);
            $day = $this->db->safe($day);

            $sql = "SELECT id, ttnumber FROM timetable WHERE form_id = '$form'
                    AND ttday = '$day'";
            $data = $this->db->query($sql);
            $tt = array();
            foreach($data as $d) {
              $tt[$d['ttnumber']] = $d['id'];
            }

            foreach($bells as $bell) {
              $ttnumber = $bell['blnumber'] - 1;
              $ttstart = $bell['blstart'];
              $ttend = $bell['blend'];

              if(isset($tt[$ttnumber])) {
                $tt_id = $tt[$ttnumber];

                $sql = "UPDATE timetable SET ttstart = '$ttstart', ttend = '$ttend'
                        WHERE form_id = '$form' AND ttday = '$day' AND ttnumber = '$ttnumber'";
                $this->db->query($sql);
              }

            }
          }
        }

        $this->view->message('assigned');
        header('Location: /school/bells/'.$bellsgroup_id);
        return;
      }

      $sql = "SELECT * FROM forms WHERE school_id = '$school_id'
              ORDER BY fmname+0, fmname";
      $data = $this->db->query($sql);
      $this->view->assign('forms', $data);

      $this->view->show('school.bells.assign');
    }

    function pupils() {
      $this->allow(1);
        $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");
        $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                   $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                                      ORDER BY fmname+0 LIMIT 1");
        $this->view->assign('form_id', $form_id);

        if(isset($_POST['save'])) {
            if(isset($_POST['ppname']) && count($_POST['ppname']) > 0) {
                foreach($_POST['ppname'] as $id => $ppname) {
                    $id = $this->db->safe($id);
                    $ppname = trim($this->db->safe($ppname));
                    $ppsurname = trim($this->db->safe($_POST['ppsurname'][$id]));
                    $ppsex = trim($this->db->safe($_POST['ppsex'][$id]));
                    $ppbirth = trim($this->db->safe($_POST['ppbirth'][$id]));
                    $ppaddress = trim($this->db->safe($_POST['ppaddress'][$id]));
                    $ppmother = trim($this->db->safe($_POST['ppmother'][$id]));
                    $ppfather = trim($this->db->safe($_POST['ppfather'][$id]));
                    $ppmotherplace = trim($this->db->safe($_POST['ppmotherplace'][$id]));
                    $ppfatherplace = trim($this->db->safe($_POST['ppfatherplace'][$id]));
                    $ppphone = trim($this->db->safe($_POST['ppphone'][$id]));
                    $ppmotherphone = trim($this->db->safe($_POST['ppmotherphone'][$id]));
                    $ppfatherphone = trim($this->db->safe($_POST['ppfatherphone'][$id]));
                    $pphomephone = trim($this->db->safe($_POST['pphomephone'][$id]));
                    $pphealth = trim($this->db->safe($_POST['pphealth'][$id]));
                    $ppphyz = trim($this->db->safe($_POST['ppphyz'][$id]));
                    $ppnotes = trim($this->db->safe($_POST['ppnotes'][$id]));

                    if($ppname == '') {
                        $sql = "DELETE FROM pupils WHERE id = '$id'";
                        $this->db->query($sql);
                        continue;
                    }

                    $pppriority = $this->db->safe($_POST['pppriority'][$id]);

                    $sql = "UPDATE pupils SET ppname = '$ppname', pppriority = '$pppriority',
                            ppsurname = '$ppsurname', ppsex = '$ppsex', ppbirth = '$ppbirth',
                            ppaddress = '$ppaddress', ppmother = '$ppmother', ppfather = '$ppfather',
                            ppmotherplace = '$ppmotherplace', ppfatherplace = '$ppfatherplace',
                            ppphone = '$ppphone', ppmotherphone = '$ppmotherphone',
                            ppfatherphone = '$ppfatherphone', pphomephone = '$pphomephone',
                            pphealth = '$pphealth', ppphyz = '$ppphyz', ppnotes = '$ppnotes'
                            WHERE id = '$id'";
                    $this->db->query($sql);
                }
            }
            foreach($_POST['newppname'] as $k => $ppname) {
                $ppname = trim($this->db->safe($ppname));
                if($ppname == '') { continue; }
                $pppriority = $this->db->safe($_POST['newpppriority'][$k]);
                $ppsurname = trim($this->db->safe($_POST['newppsurname'][$k]));
                $ppsex = trim($this->db->safe($_POST['newppsex'][$k]));
                $ppbirth = trim($this->db->safe($_POST['newppbirth'][$k]));
                $ppaddress = trim($this->db->safe($_POST['newppaddress'][$k]));
                $ppmother = trim($this->db->safe($_POST['newppmother'][$k]));
                $ppfather = trim($this->db->safe($_POST['newppfather'][$k]));
                $ppmotherplace = trim($this->db->safe($_POST['newppmotherplace'][$k]));
                $ppfatherplace = trim($this->db->safe($_POST['newppfatherplace'][$k]));
                $ppphone = trim($this->db->safe($_POST['newppphone'][$k]));
                $ppmotherphone = trim($this->db->safe($_POST['newppmotherphone'][$k]));
                $ppfatherphone = trim($this->db->safe($_POST['newppfatherphone'][$k]));
                $pphomephone = trim($this->db->safe($_POST['newpphomephone'][$k]));
                $pphealth = trim($this->db->safe($_POST['newpphealth'][$k]));
                $ppphyz = trim($this->db->safe($_POST['newppphyz'][$k]));
                $ppnotes = trim($this->db->safe($_POST['newppnotes'][$k]));

                $sql = "INSERT INTO pupils(ppname, pppriority, form_id, ppsurname,
                        ppsex, ppbirth, ppaddress, ppmother, ppfather, ppmotherplace,
                        ppfatherplace, ppphone, ppmotherphone, ppfatherphone,
                        pphomephone, pphealth, ppphyz, ppnotes) VALUES
                        ('$ppname', '$pppriority', '$form_id', '$ppsurname',
                        '$ppsex', '$ppbirth', '$ppaddress', '$ppmother', '$ppfather',
                        '$ppmotherplace', '$ppfatherplace', '$ppphone', '$ppmotherphone',
                        '$ppfatherphone', '$pphomephone', '$pphealth', '$ppphyz',
                        '$ppnotes')";
                $this->db->query($sql);
            }
            $this->view->message('updated');
        }

        $data = $this->db->query("SELECT * FROM forms WHERE school_id = '$school_id'
                                  ORDER BY fmname+0");
        $this->view->assign('_forms', $data);

        $data = $this->db->query("SELECT * FROM pupils WHERE form_id = '$form_id'
                                  ORDER BY pppriority, ppname");

        $this->view->assign('_data', $data);
        $this->view->show('school.pupils');
    }

    function showpupils() {
      $this->allow(1);

      $school_id = $this->getSchoolId();

      $form_id = isset($_POST['form_id']) ? $this->db->safe($_POST['form_id']) :
                 $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id'
                                    ORDER BY fmname+0 LIMIT 1");
      $this->view->assign('form_id', $form_id);

      if($form_id > 0) {
        $data = $this->db->query("SELECT * FROM pupils WHERE form_id = '$form_id'
                                  ORDER BY pppriority, ppname");
      } else {
        $sql = "SELECT ppname, fmname, ppsurname, pppriority,
                ppsex, ppbirth, ppaddress, ppmother, ppfather, ppmotherplace,
                ppfatherplace, ppphone, ppmotherphone, ppfatherphone,
                pphomephone, pphealth, ppphyz, ppnotes FROM pupils AS p
                INNER JOIN forms AS f ON p.form_id = f.id WHERE school_id = '$school_id'
                ORDER BY fmname+0, fmname, pppriority";
        $data = $this->db->query($sql);
      }

      $this->view->assign('pupils', $data);

      $data = $this->db->query("SELECT id, fmname FROM forms WHERE school_id = '$school_id'
                                ORDER BY fmname+0");
      array_unshift($data, array('id' => 0, 'fmname' => 'Все классы'));
      $this->view->assign('_forms', $data);

      $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
      $this->view->assign('scname', $scname);
      $fmname = $this->db->scalar("SELECT fmname FROM forms WHERE id = '$form_id'");
      $this->view->assign('form', $fmname);

      $this->view->show('school.pupils.show');
    }

    function show() {
      $this->allow(99);

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
        foreach($_POST['scname'] AS $id => $rec) {
          $id = $this->db->safe($id);
          $scname = $this->db->safe($rec);

          if(trim($rec) == '') {
            $sql = "DELETE FROM schools WHERE id = '$id'";
            $this->db->query($sql);
            continue;
          }

          $sql = "UPDATE schools SET scname = '$scname' WHERE id = '$id'";
          $this->db->query($sql);
        }

        if(isset($_POST['newscname'])) {
          foreach($_POST['newscname'] AS $id => $rec) {
            $id = $this->db->safe($id);
            $scname = $this->db->safe($rec);

            if(trim($rec) == '') {
              continue;
            }

            $sql = "INSERT INTO schools (scname, district_id) VALUES ('$scname', '$district_id')";
            $this->db->query($sql);
          }
        }

        $this->view->message('edited');
      }

      if(isset($district_id) && $district_id > 0) {
        $sql = "SELECT s.id AS school_id, scname, dtname FROM schools AS s LEFT JOIN districts AS d ON d.id = s.district_id WHERE district_id = '$district_id' ORDER BY scname";
      } else if(isset($region_id) && $region_id > 0) {
        $sql = "SELECT s.id AS school_id, scname, dtname FROM schools AS s LEFT JOIN districts AS d ON d.id = s.district_id
                WHERE d.region_id = '$region_id' ORDER BY dtname, scname";
      } else {
        $sql = "SELECT s.id AS school_id, scname, dtname FROM schools AS s LEFT JOIN districts AS d ON d.id = s.district_id
                ORDER BY dtname, scname";
      }
      $data = $this->db->query($sql);
      $this->view->assign('schools', $data);

      $this->view->show('schools.show');
    }

}

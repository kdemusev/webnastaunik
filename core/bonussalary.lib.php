<?php

include ('base.lib.php');

class CBonussalary extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('agreement', 'groups', 'employes',
                             'bonus', 'economy', 'extra',
                             'savebonus', 'saveeconomy', 'saveextra', 'printbonus',
                             'view');
  }

  function view() {
    $this->allow(1);

    $user_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $bspdate = isset($_POST['bspdate']) ? $this->db->safe($_POST['bspdate']) : mktime(0,0,0,date('n'),1,date('Y'));
    $this->view->assign('bspdate', $bspdate);
    $sql = "SELECT MAX(bspdate) FROM bsperiods WHERE school_id = '$school_id'";
    $maxbspdate = $this->db->scalar($sql);
    $this->view->assign('maxbspdate', $maxbspdate);

    $sql = "SELECT * FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $data = $this->db->query($sql);
    $this->view->assign('_bspdata', $data);

    $bsperiod_id = count($data) > 0 ? $data[0]['id'] : 0;
    $baseval = count($data) > 0 ? $data[0]['bspbasevalue'] : 0;

    $sql = "SELECT * FROM bsemployes WHERE user_id = '$user_id'";
    $demp = $this->db->query($sql);
    $this->view->assign('_edata', $demp);

    if(count($demp > 0)) {
      foreach($demp as $rec) {
        $bsemployee_id = $rec['id'];

        $sql = "SELECT bsanumber, bsvalue, bsaname, bsafrom, bsato FROM bspaybonus AS b LEFT JOIN bsagreement AS a ON b.bsagreement_id = a.id
                WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id = '$bsemployee_id' ORDER BY bsanumber";
        $data1[$bsemployee_id] = $this->db->query($sql);
      }
      $this->view->assign('_bsbdata', $data1);

      foreach($demp as $rec) {
        $bsemployee_id = $rec['id'];

        $sql = "SELECT SUM(bsvalue) AS bval FROM bspaybonus AS b
                WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id = '$bsemployee_id'";
        $bval[$bsemployee_id] = $this->db->scalar($sql) * $baseval;
      }
      $this->view->assign('_bspaybonus', $bval );

      foreach($demp as $rec) {
        $bsemployee_id = $rec['id'];

        $sql = "SELECT bsanumber, bsvalue, bsaname, bsafrom, bsato FROM bspayeconomy AS b LEFT JOIN bsagreement AS a ON b.bsagreement_id = a.id
                WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id = '$bsemployee_id' ORDER BY bsanumber";
        $data2[$bsemployee_id] = $this->db->query($sql);
      }
      $this->view->assign('_bsedata', $data2);

      foreach($demp as $rec) {
        $bsemployee_id = $rec['id'];

        $sql = "SELECT SUM(bsvalue) AS bval FROM bspayeconomy AS b
                WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id = '$bsemployee_id'";
        $bval2[$bsemployee_id] = $this->db->scalar($sql) * $baseval;
      }
      $this->view->assign('_bspayeconomy', $bval2);

      foreach($demp as $rec) {
        $bsemployee_id = $rec['id'];

        $sql = "SELECT bspepercent, bspesum, bsreason FROM bspayextra
                WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id = '$bsemployee_id'";
        $data3[$bsemployee_id] = $this->db->query($sql);
      }
      $this->view->assign('_bsxdata', $data3);
    }

    $this->view->show('bonussalary.view');
  }

  function agreement() {
    $this->allow(87);

    $user_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$user_id'");

    if(isset($_POST['save'])) {
      if(isset($_POST['bsanumber'])) {
        foreach($_POST['bsanumber'] as $id => $bsanumber) {
          $id = $this->db->safe($id);
          $bsanumber = trim($this->db->safe($bsanumber));

          if($bsanumber == '') {
              $sql = "DELETE FROM bsagreement WHERE id = '$id' AND
                      school_id = '$school_id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspaybonus WHERE bsagreement_id = '$id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspayeconomy WHERE bsagreement_id = '$id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspayextra WHERE bsagreement_id = '$id'";
              $this->db->query($sql);
              continue;
          }

          $bsaname = $this->db->safe($_POST['bsaname'][$id]);
          $bsafrom = $this->db->safe($_POST['bsafrom'][$id]);
          $bsato = $this->db->safe($_POST['bsato'][$id]);

          $sql = "UPDATE bsagreement SET bsaname = '$bsaname',
                  bsanumber = '$bsanumber', bsafrom = '$bsafrom', bsato = '$bsato'
                  WHERE id = '$id' AND school_id = '$school_id'";
          $this->db->query($sql);
        }
      }

      foreach($_POST['newbsanumber'] as $k => $bsanumber) {
        $bsanumber = trim($this->db->safe($bsanumber));
        $bsaname = $this->db->safe($_POST['newbsaname'][$k]);
        $bsafrom = $this->db->safe($_POST['newbsafrom'][$k]);
        $bsato = $this->db->safe($_POST['newbsato'][$k]);

        if($bsanumber != '') {
          $sql = "INSERT INTO bsagreement(bsanumber, bsaname, bsafrom, bsato, school_id) VALUES
                  ('$bsanumber', '$bsaname', '$bsafrom', '$bsato', '$school_id')";
          $this->db->query($sql);
        }
      }

      $this->view->message('updated');
    }

    $sql = "SELECT * FROM bsagreement WHERE school_id = '$school_id' ORDER BY bsanumber";
    $data = $this->db->query($sql);
    $this->view->assign('_data', $data);

    $this->view->show('bonussalary.agreement');
  }

  function groups() {
    $this->allow(87);

    $user_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$user_id'");

    if(isset($_POST['save'])) {
      if(isset($_POST['bsgname'])) {
        foreach($_POST['bsgname'] as $id => $bsgname) {
          $id = $this->db->safe($id);
          $bsgname = trim($this->db->safe($bsgname));

          if($bsgname == '') {
              $sql = "DELETE FROM bsgroups WHERE id = '$id' AND
                      school_id = '$school_id'";
              $this->db->query($sql);
              $sql = "SELECT id FROM bsemployes WHERE bsgroup_id = '$id'";
              $data = $this->db->query($sql);
              foreach($data as $rec) {
                $id = $rec['id'];
                $sql = "DELETE FROM bsemployes WHERE id = '$id' AND
                        bsgroup_id = '$bsgroup_id'";
                $this->db->query($sql);
                $sql = "DELETE FROM bspaybonus WHERE bsemployee_id = '$id'";
                $this->db->query($sql);
                $sql = "DELETE FROM bspayeconomy WHERE bsemployee_id = '$id'";
                $this->db->query($sql);
                $sql = "DELETE FROM bspayextra WHERE bsemployee_id = '$id'";
                $this->db->query($sql);
              }

              continue;
          }

          $bsgpriority = $this->db->safe($_POST['bsgpriority'][$id]);

          $sql = "UPDATE bsgroups SET bsgname = '$bsgname',
                  bsgpriority = '$bsgpriority'
                  WHERE id = '$id' AND school_id = '$school_id'";
          $this->db->query($sql);
        }
      }

      foreach($_POST['newbsgname'] as $k => $bsgname) {
        $bsgname = trim($this->db->safe($bsgname));
        $bsgpriority = $this->db->safe($_POST['newbsgpriority'][$k]);

        if($bsgname != '') {
          $sql = "INSERT INTO bsgroups(bsgname, bsgpriority, school_id) VALUES
                  ('$bsgname', '$bsgpriority', '$school_id')";
          $this->db->query($sql);
        }
      }

      $this->view->message('updated');
    }

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('_data', $data);

    $this->view->show('bonussalary.groups');
  }

  function employes() {
    $this->allow(87);
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");
    $bsgroup_id = isset($_POST['bsgroup_id']) ? $this->db->safe($_POST['bsgroup_id']) :
                  $this->db->scalar("SELECT id FROM bsgroups WHERE school_id = '$school_id'
                                     ORDER BY bsgpriority LIMIT 1");
    $this->view->assign('bsgroup_id', $bsgroup_id);

    if(isset($_POST['save'])) {
      if(isset($_POST['bsename'])) {
        foreach($_POST['bsename'] as $id => $bsename) {
          $id = $this->db->safe($id);
          $bsename = trim($this->db->safe($bsename));

          if($bsename == '') {
              $sql = "DELETE FROM bsemployes WHERE id = '$id' AND
                      bsgroup_id = '$bsgroup_id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspaybonus WHERE bsemployee_id = '$id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspayeconomy WHERE bsemployee_id = '$id'";
              $this->db->query($sql);
              $sql = "DELETE FROM bspayextra WHERE bsemployee_id = '$id'";
              $this->db->query($sql);
              continue;
          }

          $bsepriority = $this->db->safe($_POST['bsepriority'][$id]);
          $bseplace = $this->db->safe($_POST['bseplace'][$id]);
          $bseuser_id = $this->db->safe($_POST['bseuser_id'][$id]);

          $sql = "UPDATE bsemployes SET bsename = '$bsename', bseplace = '$bseplace',
                  bsepriority = '$bsepriority', user_id = '$bseuser_id'
                  WHERE id = '$id' AND bsgroup_id = '$bsgroup_id'";
          $this->db->query($sql);
        }
      }

      foreach($_POST['newbsename'] as $k => $bsename) {
        $bsename = trim($this->db->safe($bsename));

        if($bsename == '') {
          continue;
        }

        $bsepriority = $this->db->safe($_POST['newbsepriority'][$k]);
        $bseplace = $this->db->safe($_POST['newbseplace'][$k]);
        $bseuser_id = $this->db->safe($_POST['newbseuser_id'][$k]);

        if($bsename != '') {
          $sql = "INSERT INTO bsemployes(bsename, bsepriority, bseplace, user_id, bsgroup_id) VALUES
                  ('$bsename', '$bsepriority', '$bseplace', '$bseuser_id', '$bsgroup_id')";
          $this->db->query($sql);
        }
      }

      $this->view->message('updated');
    }

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('_bsgroups', $data);

    $sql = "SELECT * FROM users WHERE school_id = '$school_id' ORDER BY usname";
    $data = $this->db->query($sql);
    $this->view->assign('_users', $data);

    $sql = "SELECT * FROM bsemployes WHERE bsgroup_id = '$bsgroup_id' ORDER BY bsepriority";
    $data = $this->db->query($sql);
    $this->view->assign('_data', $data);

    $this->view->show('bonussalary.employes');
  }

  function savebonus() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $bspdate = $this->db->safe($_POST['bspdate']);
    $bspbasevalue = $this->db->safe($_POST['bspbasevalue']);
    $bsppursebonus = $this->db->safe($_POST['bsppursebonus']);

    $sql = "SELECT COUNT(*) FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $isexists = $this->db->scalar($sql);
    if($isexists) {
      $sql = "SELECT id FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
      $bsperiod_id = $this->db->scalar($sql);

      $sql = "UPDATE bsperiods SET bspbasevalue = '$bspbasevalue', bsppursebonus = '$bsppursebonus' WHERE id = '$bsperiod_id'";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO bsperiods(bspdate, bspbasevalue, bsppursebonus, school_id) VALUES ('$bspdate', '$bspbasevalue', '$bsppursebonus', '$school_id')";
      $this->db->query($sql);
      $bsperiod_id = $this->db->last_id();
    }

    $sql = "DELETE FROM bspaybonus WHERE bsperiod_id = '$bsperiod_id'";
    $this->db->query($sql);

    foreach($_POST['bsvalue'] as $bsemployee_id => $bsvalue) {
      $bsemployee_id = $this->db->safe($bsemployee_id);
      if(count($bsvalue) > 0) {
        $sql = "INSERT INTO bspaybonus(bsemployee_id,bsperiod_id,bsagreement_id,bsvalue) VALUES ";
        $iswas = false;
        foreach($bsvalue as $bsagreement_id => $rec) {
          $bsagreement_id = $this->db->safe($bsagreement_id);
          $rec = $this->db->safe(str_replace(',','.',$rec));
          if(trim($rec)=='' || trim($rec) == '0' || trim($rec) =='0.00') {
            continue;
          }
          $iswas = true;
          $sql .= "('$bsemployee_id', '$bsperiod_id', '$bsagreement_id', '$rec'),";
        }
        if($iswas) {
          $sql = substr($sql, 0, -1);
          $this->db->query($sql);
        }
      }
    }

    $this->view->message('updated');
    print '<form method="post" action="/bonussalary/bonus"><input type="hidden" name="bspdate" value="'.$_POST['bspdate'].'"></form><script>document.forms[0].submit();</script>';
  }

  function bonus() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $sql = "SELECT * FROM bsagreement WHERE school_id = '$school_id' ORDER BY id";
    $data = $this->db->query($sql);
    $this->view->assign('_bsadata', $data);

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('_bsgdata', $data);

    $sql = "SELECT * FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id') ORDER BY bsepriority";
    $data = $this->db->query($sql);
    $bsedata = array();
    foreach($data as $rec) {
      $bsedata[$rec['bsgroup_id']][] = $rec;
    }
    $this->view->assign('_bsedata', $bsedata);

    $bspdate = isset($_POST['bspdate']) ? $this->db->safe($_POST['bspdate']) : mktime(0,0,0,date('n'),1,date('Y'));
    $this->view->assign('bspdate', $bspdate);
    $sql = "SELECT MAX(bspdate) FROM bsperiods WHERE school_id = '$school_id'";
    $maxbspdate = $this->db->scalar($sql);
    $this->view->assign('maxbspdate', $maxbspdate);

    $sql = "SELECT * FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $data = $this->db->query($sql);
    $this->view->assign('_bspdata', $data);

    $bsperiod_id = count($data) > 0 ? $data[0]['id'] : 0;

    $sql = "SELECT * FROM bspaybonus WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id IN
            (SELECT id FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id'))";
    $data = $this->db->query($sql);
    $bspaydata = array();
    foreach($data as $rec) {
      $bspaydata[$rec['bsemployee_id']][$rec['bsagreement_id']] = $rec['bsvalue'];
    }
    $this->view->assign('_bspaydata', $bspaydata);

    $this->view->show('bonussalary.bonus');
  }

  function saveeconomy() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $bspdate = $this->db->safe($_POST['bspdate']);
    $bspbasevalue = $this->db->safe($_POST['bspbasevalue']);
    $bsppurseeconomy = $this->db->safe($_POST['bsppurseeconomy']);

    $sql = "SELECT COUNT(*) FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $isexists = $this->db->scalar($sql);
    if($isexists) {
      $sql = "SELECT id FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
      $bsperiod_id = $this->db->scalar($sql);

      $sql = "UPDATE bsperiods SET bspbasevalue = '$bspbasevalue', bsppurseeconomy = '$bsppurseeconomy' WHERE id = '$bsperiod_id'";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO bsperiods(bspdate, bspbasevalue, bsppurseeconomy, school_id) VALUES ('$bspdate', '$bspbasevalue', '$bsppurseeconomy', '$school_id')";
      $this->db->query($sql);
      $bsperiod_id = $this->db->last_id();
    }

    $sql = "DELETE FROM bspayeconomy WHERE bsperiod_id = '$bsperiod_id'";
    $this->db->query($sql);

    foreach($_POST['bsvalue'] as $bsemployee_id => $bsvalue) {
      $bsemployee_id = $this->db->safe($bsemployee_id);
      if(count($bsvalue) > 0) {
        $sql = "INSERT INTO bspayeconomy(bsemployee_id,bsperiod_id,bsagreement_id,bsvalue) VALUES ";
        $iswas = false;
        foreach($bsvalue as $bsagreement_id => $rec) {
          $bsagreement_id = $this->db->safe($bsagreement_id);
          $rec = $this->db->safe(str_replace(',','.',$rec));
          if(trim($rec)=='' || trim($rec) == '0' || trim($rec) =='0.00') {
            continue;
          }
          $iswas = true;
          $sql .= "('$bsemployee_id', '$bsperiod_id', '$bsagreement_id', '$rec'),";
        }
        if($iswas) {
          $sql = substr($sql, 0, -1);
          $this->db->query($sql);
        }
      }
    }

    $this->view->message('updated');
    print '<form method="post" action="/bonussalary/economy"><input type="hidden" name="bspdate" value="'.$_POST['bspdate'].'"></form><script>document.forms[0].submit();</script>';
  }

  function economy() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $sql = "SELECT * FROM bsagreement WHERE school_id = '$school_id' ORDER BY id";
    $data = $this->db->query($sql);
    $this->view->assign('_bsadata', $data);

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('_bsgdata', $data);

    $sql = "SELECT * FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id') ORDER BY bsepriority";
    $data = $this->db->query($sql);
    $bsedata = array();
    foreach($data as $rec) {
      $bsedata[$rec['bsgroup_id']][] = $rec;
    }
    $this->view->assign('_bsedata', $bsedata);

    $bspdate = isset($_POST['bspdate']) ? $this->db->safe($_POST['bspdate']) : mktime(0,0,0,date('n'),1,date('Y'));
    $this->view->assign('bspdate', $bspdate);
    $sql = "SELECT MAX(bspdate) FROM bsperiods WHERE school_id = '$school_id'";
    $maxbspdate = $this->db->scalar($sql);
    $this->view->assign('maxbspdate', $maxbspdate);

    $sql = "SELECT * FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $data = $this->db->query($sql);
    $this->view->assign('_bspdata', $data);

    $bsperiod_id = count($data) > 0 ? $data[0]['id'] : 0;

    $sql = "SELECT * FROM bspayeconomy WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id IN
            (SELECT id FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id'))";
    $data = $this->db->query($sql);
    $bspaydata = array();
    foreach($data as $rec) {
      $bspaydata[$rec['bsemployee_id']][$rec['bsagreement_id']] = $rec['bsvalue'];
    }
    $this->view->assign('_bspaydata', $bspaydata);

    $this->view->show('bonussalary.economy');
  }

  function saveextra() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $bspdate = $this->db->safe($_POST['bspdate']);
    $bspbasevalue = $this->db->safe($_POST['bspbasevalue']);
    $bsppurseextra = $this->db->safe($_POST['bsppurseextra']);

    $sql = "SELECT COUNT(*) FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $isexists = $this->db->scalar($sql);
    if($isexists) {
      $sql = "SELECT id FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
      $bsperiod_id = $this->db->scalar($sql);

      $sql = "UPDATE bsperiods SET bspbasevalue = '$bspbasevalue', bsppurseextra = '$bsppurseextra' WHERE id = '$bsperiod_id'";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO bsperiods(bspdate, bspbasevalue, bsppurseextra, school_id) VALUES ('$bspdate', '$bspbasevalue', '$bsppurseextra', '$school_id')";
      $this->db->query($sql);
      $bsperiod_id = $this->db->last_id();
    }

    $sql = "DELETE FROM bspayextra WHERE bsperiod_id = '$bsperiod_id'";
    $this->db->query($sql);

    foreach($_POST['bsvalue'] as $bsemployee_id => $bsvalue) {
      $bsemployee_id = $this->db->safe($bsemployee_id);
      $bsvalue = $this->db->safe($bsvalue);
      $bsreason = isset($_POST['bsreason'][$bsemployee_id]) ? $this->db->safe($_POST['bsreason'][$bsemployee_id]) : '';
      $bspepercent = isset($_POST['bspepercent'][$bsemployee_id]) ? $this->db->safe($_POST['bspepercent'][$bsemployee_id]) : 0;
      if($bsvalue > 0) {

        $sql = "INSERT INTO bspayextra(bsemployee_id,bsperiod_id,bspesum, bsreason, bspepercent) VALUES
                ('$bsemployee_id', '$bsperiod_id', '$bsvalue', '$bsreason', '$bspepercent')";
        $this->db->query($sql);
      }
    }

    $this->view->message('updated');
    print '<form method="post" action="/bonussalary/extra"><input type="hidden" name="bspdate" value="'.$_POST['bspdate'].'"></form><script>document.forms[0].submit();</script>';
  }

  function extra() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $sql = "SELECT * FROM bsagreement WHERE school_id = '$school_id' ORDER BY bsanumber";
    $data = $this->db->query($sql);
    $this->view->assign('_bsadata', $data);

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('_bsgdata', $data);

    $sql = "SELECT * FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id') ORDER BY bsepriority";
    $data = $this->db->query($sql);
    $bsedata = array();
    foreach($data as $rec) {
      $bsedata[$rec['bsgroup_id']][] = $rec;
    }
    $this->view->assign('_bsedata', $bsedata);

    $bspdate = isset($_POST['bspdate']) ? $this->db->safe($_POST['bspdate']) : mktime(0,0,0,date('n'),1,date('Y'));
    $this->view->assign('bspdate', $bspdate);
    $sql = "SELECT MAX(bspdate) FROM bsperiods WHERE school_id = '$school_id'";
    $maxbspdate = $this->db->scalar($sql);
    $this->view->assign('maxbspdate', $maxbspdate);

    $sql = "SELECT * FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $data = $this->db->query($sql);
    $this->view->assign('_bspdata', $data);

    $bsperiod_id = count($data) > 0 ? $data[0]['id'] : 0;

    $sql = "SELECT * FROM bspayextra WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id IN
            (SELECT id FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id'))";
    $data = $this->db->query($sql);
    $bspaydata = array();
    foreach($data as $rec) {
      $bspaydata[$rec['bsemployee_id']] = $rec;
    }
    $this->view->assign('_bspaydata', $bspaydata);

    $this->view->show('bonussalary.extra');
  }

  function printbonus() {
    $this->allow(87);

    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = {$this->user_id}");

    $sql = "SELECT * FROM bsagreement WHERE school_id = '$school_id' ORDER BY bsanumber";
    $agreements = $this->db->query($sql);

    $sql = "SELECT * FROM bsgroups WHERE school_id = '$school_id' ORDER BY bsgpriority";
    $bsgroups = $this->db->query($sql);

    $sql = "SELECT * FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id') ORDER BY bsepriority";
    $data = $this->db->query($sql);
    $bsedata = array();
    foreach($data as $rec) {
      $bsedata[$rec['bsgroup_id']][] = $rec;
    }

    $bspdate = $this->db->safe($_POST['bspdate']);

    $sql = "SELECT * FROM bsperiods WHERE school_id = '$school_id' AND bspdate = '$bspdate'";
    $periods = $this->db->query($sql);
    $baseval = $periods[0]['bspbasevalue'];

    $bsperiod_id = count($data) > 0 ? $data[0]['id'] : 0;

    $sql = "SELECT * FROM bspaybonus WHERE bsperiod_id = '$bsperiod_id' AND bsemployee_id IN
            (SELECT id FROM bsemployes WHERE bsgroup_id IN (SELECT id FROM bsgroups WHERE school_id = '$school_id'))";
    $data = $this->db->query($sql);
    $bspaydata = array();
    foreach($data as $rec) {
      $bspaydata[$rec['bsemployee_id']][$rec['bsagreement_id']] = $rec['bsvalue'];
    }

    include('printtopng.lib.php');
    $prnt = new CPrintToPng();

    $data = array();
    $colslen = array();
    $data[0] = array();

    $data[0][] = '№ п/п';
    $colslen[] = 0;
    $data[0][] = 'Фамилия И. О.';
    $colslen[] = 0;
    $data[0][] = 'Должность';
    $colslen[] = 0;
    foreach($agreements as $rec) {
      $data[0][] = $rec['bsanumber'];
      $colslen[] = 30;
    }
    $data[0][] = 'Сумма премирования';
    $colslen[] = 0;

    $k = 1;
    foreach($bsgroups as $grprec) {
      foreach($bsedata[$grprec['id']] as $emprec) {
        $data[$k] = array();
        $data[$k][] = $k+1;
        $data[$k][] = CUtilities::initials($emprec['bsename']);
        $data[$k][] = $emprec['bseplace'];
        $sum = 0;
        foreach($agreements as $agrrec) {
          if(isset($bspaydata[$emprec['id']][$agrrec['id']])) {
            $data[$k][] = $bspaydata[$emprec['id']][$agrrec['id']];
            $sum += (float)$bspaydata[$emprec['id']][$agrrec['id']];
          } else {
            $data[$k][] = '';
          }
        }
        $data[$k][] = $sum * $baseval;
        $k++;
      }
    }

    $prnt->drawtable($data, $colslen);

  }
}

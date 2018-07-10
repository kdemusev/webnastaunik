<?php

include ('base.lib.php');

class CTest extends CBase {

  function __construct($db, $view) {
    parent::__construct($db, $view);

    $this->actions = array('showlist', 'show', 'showdetails', 'showanalys',
                           'add', 'choose', 'view', 'complete',
                           'edit', 'change', 'delete',
                           'delresult');
  }

  function showlist() {
    $this->allow(87);
    $user_id = $this->user_id;

    $sql = "SELECT id, tsname, tsdesc, tscode, user_id FROM tests WHERE user_id = '$user_id'
            ORDER BY tstime DESC";
    $data = $this->db->query($sql);
    $this->view->assign('testdata', $data);

    $this->view->show('test.list');
  }

  function show() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $data = $this->db->query("SELECT * FROM tests WHERE id = '$id'");
    $qnum = $data[0]['tsqnum'];
    $this->view->assign('data', $data);

    $total = $this->db->scalar("SELECT COUNT(*) FROM testtasks WHERE test_id = '$id'");
    $total = $qnum > 0 ? $qnum : $total;
    $this->view->assign('total', $total);

    $data = $this->db->query("SELECT tr.id AS trid, usname, scname, usplace,
                              trcount, trpercent
                              FROM testresults AS tr
                              LEFT JOIN users AS u ON tr.user_id = u.id
                              LEFT JOIN schools AS s ON u.school_id = s.id
                              WHERE test_id = '$id'
                              ORDER BY scname, usname, trtime");
    $this->view->assign('results', $data);

    $this->view->show('test.show');
  }

  function showdetails() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);
    $test_id = $this->db->scalar("SELECT test_id FROM testresults WHERE id = '$id'");

    $data = $this->db->query("SELECT * FROM tests WHERE id = '$test_id'");
    $qnum = $data[0]['tsqnum'];

		$test = $this->db->query("SELECT id, tttype, tttask FROM
														  testtasks WHERE test_id = '$test_id'
                              AND id IN (SELECT testtask_id FROM testresultdetails WHERE testresult_id = '$id')
															ORDER BY ttpriority");

    $testtypes = array();
    foreach($test as $rec) {
      $testtypes[$rec['id']] = $rec['tttype'];
    }

		$testvars = array();
		$testaccord = array();
		foreach($test as $rec) {
			$ttid = $rec['id'];

			$vars = $this->db->query("SELECT id, tvvar, tvtrue FROM testvars
																WHERE testtask_id = '$ttid'");

			$testvars[$ttid] = $vars;

			if($rec['tttype']==4) {	// accordance
				$in = '(';
				foreach($svars as $r2) {
					$in .= $r2['id'] . ',';
				}
				$in .= '0)';
				$accord = $this->db->query("SELECT id, taaccord FROM testaccords
																		WHERE testvar_id IN $in");

				$saccord = array();
				if(count($accord) > 0) {
					while(count($accord) > 0) {
						$num = rand(0, count($accord)-1);
						$saccord[] = $accord[$num];
						for($i = $num; $i < (count($accord)-1); $i++) {
							$accord[$i] = $accord[$i+1];
						}
						unset($accord[count($accord)-1]);
					}
				}

				$testaccord[$ttid] = $saccord;
			}
		}

    $total = $this->db->scalar("SELECT COUNT(*) FROM testtasks WHERE test_id = '$test_id'");
    $total = $qnum > 0 ? $qnum : $total;
    $this->view->assign('total', $total);

    $user = $this->db->query("SELECT tr.id AS trid, usname, scname, usplace,
                              trcount, trpercent
                              FROM testresults AS tr
                              LEFT JOIN users AS u ON tr.user_id = u.id
                              LEFT JOIN schools AS s ON u.school_id = s.id
                              WHERE tr.id = '$id'");
    $this->view->assign('user', $user);

    $details = $this->db->query("SELECT * FROM testresultdetails WHERE testresult_id = '$id'");
    $details2 = array();
    foreach($details as $rec) {
      if($testtypes[$rec['testtask_id']]==1) {
        $details2[$rec['testtask_id']] = $rec['testvar_id'];
      } else if ($testtypes[$rec['testtask_id']]==2) {
        $details2[$rec['testtask_id']][] = $rec['testvar_id'];
      }
    }

		$this->view->assign('testaccord', $testaccord);
		$this->view->assign('testvars', $testvars);
		$this->view->assign('test', $test);
    $this->view->assign('data', $data);
    $this->view->assign('details', $details2);

    $this->view->show('test.show.details');
  }

  function showresultstouser($id) {
    $this->allow(1);

    $test_id = $this->db->scalar("SELECT test_id FROM testresults WHERE id = '$id'");

    $data = $this->db->query("SELECT * FROM tests WHERE id = '$test_id'");
    $qnum = $data[0]['tsqnum'];

		$test = $this->db->query("SELECT id, tttype, tttask FROM
														  testtasks WHERE test_id = '$test_id'
                              AND id IN (SELECT testtask_id FROM testresultdetails WHERE testresult_id = '$id')
															ORDER BY ttpriority");


    $testtypes = array();
    foreach($test as $rec) {
      $testtypes[$rec['id']] = $rec['tttype'];
    }

		$testvars = array();
		$testaccord = array();
		foreach($test as $rec) {
			$ttid = $rec['id'];

			$vars = $this->db->query("SELECT id, tvvar, tvtrue FROM testvars
																WHERE testtask_id = '$ttid'");

			$testvars[$ttid] = $vars;

			if($rec['tttype']==4) {	// accordance
				$in = '(';
				foreach($svars as $r2) {
					$in .= $r2['id'] . ',';
				}
				$in .= '0)';
				$accord = $this->db->query("SELECT id, taaccord FROM testaccords
																		WHERE testvar_id IN $in");

				$saccord = array();
				if(count($accord) > 0) {
					while(count($accord) > 0) {
						$num = rand(0, count($accord)-1);
						$saccord[] = $accord[$num];
						for($i = $num; $i < (count($accord)-1); $i++) {
							$accord[$i] = $accord[$i+1];
						}
						unset($accord[count($accord)-1]);
					}
				}

				$testaccord[$ttid] = $saccord;
			}
		}

    $total = $this->db->scalar("SELECT COUNT(*) FROM testtasks WHERE test_id = '$test_id'");
    $total = $qnum > 0 ? $qnum : $total;
    $this->view->assign('total', $total);

    $user = $this->db->query("SELECT tr.id AS trid, usname, scname, usplace,
                              trcount, trpercent
                              FROM testresults AS tr
                              LEFT JOIN users AS u ON tr.user_id = u.id
                              LEFT JOIN schools AS s ON u.school_id = s.id
                              WHERE tr.id = '$id'");
    $this->view->assign('user', $user);

    $details = $this->db->query("SELECT * FROM testresultdetails WHERE testresult_id = '$id'");
    $details2 = array();
    foreach($details as $rec) {
      if($testtypes[$rec['testtask_id']]==1) {
        $details2[$rec['testtask_id']] = $rec['testvar_id'];
      } else if ($testtypes[$rec['testtask_id']]==2) {
        $details2[$rec['testtask_id']][] = $rec['testvar_id'];
      }
    }

		$this->view->assign('testaccord', $testaccord);
		$this->view->assign('testvars', $testvars);
		$this->view->assign('test', $test);
    $this->view->assign('data', $data);
    $this->view->assign('details', $details2);

    $this->view->show('test.show.details.touser');
  }

  function showanalys() {
    $this->allow(87);
    $user_id = $this->user_id;
    $test_id = $this->db->safe($_GET['id']);

    $data = $this->db->query("SELECT * FROM tests WHERE id = '$test_id'");

		$test = $this->db->query("SELECT id, tttype, tttask FROM
														  testtasks WHERE test_id = '$test_id'
															ORDER BY ttpriority");

    $testvars = array();
  	$testaccord = array();
  	foreach($test as $rec) {
  		$ttid = $rec['id'];

  		$vars = $this->db->query("SELECT id, tvvar, tvtrue FROM testvars
  															WHERE testtask_id = '$ttid' AND tvtrue = 1");

  		$testvars[$ttid] = $vars;

  		if($rec['tttype']==4) {	// accordance
  			$in = '(';
  			foreach($svars as $r2) {
  				$in .= $r2['id'] . ',';
  			}
  			$in .= '0)';
  			$accord = $this->db->query("SELECT id, taaccord FROM testaccords
  																	WHERE testvar_id IN $in");

  			$saccord = array();
  			if(count($accord) > 0) {
  				while(count($accord) > 0) {
  					$num = rand(0, count($accord)-1);
  					$saccord[] = $accord[$num];
  					for($i = $num; $i < (count($accord)-1); $i++) {
  						$accord[$i] = $accord[$i+1];
  					}
  					unset($accord[count($accord)-1]);
  				}
  			}

  			$testaccord[$ttid] = $saccord;
  		}
  	}

    $total = $this->db->scalar("SELECT COUNT(*) FROM testresults WHERE test_id = '$test_id'");
    $this->view->assign('total', $total);

    $analys = $this->db->query("SELECT DISTINCT trd.testtask_id AS trdttid, COUNT(tr.user_id) AS cnt
                              FROM testresultdetails AS trd
                              INNER JOIN testresults AS tr ON tr.id = trd.testresult_id
                              INNER JOIN testvars AS tv ON testvar_id = tv.id
                              WHERE tr.test_id = '$test_id' AND tvtrue = 1
                              GROUP BY trd.testtask_id");

    $analys2 = array();
    foreach($analys as $rec) {
      $analys2[$rec['trdttid']]['rightcnt'] = $rec['cnt'];
    }

    $analys = $this->db->query("SELECT DISTINCT trd.testtask_id AS trdttid, COUNT(tr.user_id) AS cnt
                              FROM testresultdetails AS trd
                              INNER JOIN testresults AS tr ON tr.id = trd.testresult_id
                              INNER JOIN testvars AS tv ON testvar_id = tv.id
                              WHERE tr.test_id = '$test_id'
                              GROUP BY trd.testtask_id");

    foreach($analys as $rec) {
      $analys2[$rec['trdttid']]['totalcnt'] = $rec['cnt'];
    }

    $this->view->assign('analys', $analys2);

    $percanalys = $this->db->query("SELECT trpercent FROM testresults WHERE test_id = '$test_id'");
    $percanalys2 = array();
    for($i = 0; $i < 10; $i++) { $percanalys2[$i] = 0; }

    foreach($percanalys as $rec) {
      if($rec['trpercent'] >= 0 && $rec['trpercent'] <= 10) { $percanalys2[0]++; }
      for($i = 1; $i < 10; $i++) {
        if($rec['trpercent'] >= $i*10+1 && $rec['trpercent'] <= ($i+1)*10) { $percanalys2[$i]++; }
      }
    }
    $this->view->assign('percanalys', $percanalys2);

  	$this->view->assign('testaccord', $testaccord);
  	$this->view->assign('testvars', $testvars);
  	$this->view->assign('test', $test);
    $this->view->assign('data', $data);

    $this->view->show('test.show.analys');
  }

  function delresult() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $test_id = $this->db->scalar("SELECT test_id FROM testresults WHERE id = '$id'");
    $cnt = $this->db->scalar("SELECT COUNT(*) FROM tests WHERE id = $test_id AND user_id = '$user_id'");
    if($cnt == 0) {
      $this->page404();
      return;
    }

    $this->db->query("DELETE FROM testresults WHERE id = '$id'");
    $this->db->query("DELETE FROM testresultsdetails WHERE id NOT IN (SELECT id FROM testresults)");

    $this->view->message('deleted');
    $this->view->go("/test/show/$test_id");
  }


  function add() {
    $this->allow(87);
    $user_id = $this->user_id;

    if(isset($_POST['save'])) {
      $tsname = $this->db->safe($_POST['tsname']);
      $tsdesc = $this->db->safe($_POST['tsdesc']);
      $tscode = $this->db->safe($_POST['tscode']);
      $tsqnum = $this->db->safe($_POST['tsqnum']);
      $tstime = time();

      $this->db->query("INSERT INTO tests (tsname, tsdesc, tscode, user_id, tstime, tsqnum)
                        VALUES ('$tsname', '$tsdesc', '$tscode', '$user_id', '$tstime', '$tsqnum')");
      $test_id = $this->db->last_id();

      foreach($_POST['task'] as $key => $rec) {
        if(trim($rec)=='') continue;
        $tttask = $this->db->safe($rec);
        $tttype = $this->db->safe($_POST['type'][$key]);

        $this->db->query("INSERT INTO testtasks (tttask, tttype, test_id,
                          ttpriority) VALUES ('$tttask', '$tttype', '$test_id',
                          '$key')");
        $testtask_id = $this->db->last_id();

        foreach($_POST['vars'.($key+1)] as $k2 => $vars) {
          if(trim($vars) == "") continue;
          $var = $this->db->safe($vars);
          $tvtrue = isset($_POST['trues'.($key+1)][$k2]) ? 1 : 0;

          if($tttype != 4) {	// Accordance
            $this->db->query("INSERT INTO testvars (tvvar, testtask_id, tvpriority,
                              tvtrue)
                              VALUES ('$var', '$testtask_id', '$k2', '$tvtrue')");
          } else {
            $parts = explode("|", $var);
            $tvvar = trim($parts[0]);
            $taaccord = trim($parts[1]);

            $this->db->query("INSERT INTO testvars (tvvar, tasktest_id, tvpriority)
                              VALUES ('$tvvar', '$tasktest_id', '$k2')");
            $taskvar_id = $this->db->last_id();
            $this->db->query("INSERT INTO testaccords (testvar_id, taaccord)
                              VALUES ('$taskvar_id', '$taaccord')");
          }
        }
      }

      $this->view->message('testadded');
      header("Location: /test/showlist");
      return;
    }

    $this->view->show('test.add');
  }

  function delete() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $cnt = $this->db->scalar("SELECT COUNT(*) FROM tests WHERE id = '$id' AND user_id = '$user_id'");
    if($cnt == 0) {
      $this->page404();
      return;
    }

    $this->db->query("DELETE FROM tests WHERE id = '$id'");
    $this->db->query("DELETE FROM testtasks WHERE test_id NOT IN (SELECT id FROM tests)");
    $this->db->query("DELETE FROM testvars WHERE testtask_id NOT IN (SELECT id FROM testtasks)");
    $this->db->query("DELETE FROM testaccords WHERE testvar_id NOT IN (SELECT id FROM testvars)");
    $this->db->query("DELETE FROM testresults WHERE test_id NOT IN (SELECT id FROM tests)");
    $this->db->query("DELETE FROM testresultdetails WHERE testresult_id NOT IN (SELECT id FROM testresults)");

    $this->view->message('testdeleted');
    $this->view->go('/test/showlist');
  }

  function edit() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $cnt = $this->db->scalar("SELECT COUNT(*) FROM tests WHERE id = '$id' AND user_id = '$user_id'");
    if($cnt == 0) {
      $this->page404();
      return;
    }

    $data = $this->db->query("SELECT * FROM tests WHERE id = '$id'");
    $this->view->assign('data', $data);

    $this->q('tests', "SELECT id, tttype, tttask FROM testtasks
              WHERE test_id = '$id' ORDER BY ttpriority");
    $data = $this->db->query("SELECT testtask_id, tvvar, tvpriority, tvtrue,
                   taaccord
                   FROM testvars AS ttv LEFT JOIN testaccords AS tta
                   ON tta.testvar_id = ttv.id WHERE testtask_id IN
                   (SELECT id FROM testtasks WHERE test_id = '$id')
                   ORDER BY tvpriority");
    $newdat = array();
    if(count($data) > 0) {
      foreach($data as $rec) {
        $newdat[$rec['testtask_id']][] = $rec;
      }
    }
    $this->view->assign("taskvars", $newdat);

    $this->view->show('test.edit');
  }

  function change() {
    $this->allow(87);
    $user_id = $this->user_id;
    $id = $this->db->safe($_GET['id']);

    $cnt = $this->db->scalar("SELECT COUNT(*) FROM tests WHERE id = '$id' AND user_id = '$user_id'");
    if($cnt == 0) {
      $this->page404();
      return;
    }

    $tsname = $this->db->safe($_POST['tsname']);
    $tsdesc = $this->db->safe($_POST['tsdesc']);
    $tscode = $this->db->safe($_POST['tscode']);
    $tsqnum = $this->db->safe($_POST['tsqnum']);

    $this->db->query("UPDATE tests SET tsname = '$tsname', tsdesc = '$tsdesc',
                      tscode = '$tscode', tsqnum = '$tsqnum' WHERE id = '$id'");

    $this->db->query("DELETE FROM testaccords WHERE testvar_id IN
                      (SELECT id FROM testvars WHERE testtask_id IN
                      (SELECT id FROM testtasks WHERE test_id = '$id'))");
    $this->db->query("DELETE FROM testvars WHERE testtask_id IN
                      (SELECT id FROM testtasks WHERE test_id = '$id')");
    $this->db->query("DELETE FROM testtasks WHERE test_id = '$id'");
    $this->db->query("DELETE FROM testresultdetails WHERE testresult_id IN
                      (SELECT id FROM testresults WHERE id = '$id')");

    foreach($_POST['task'] as $key => $rec) {
  		if(trim($rec)=='') continue;
  		$tttask = $this->db->safe($rec);
  		$tttype = $this->db->safe($_POST['type'][$key]);

  		$this->db->query("INSERT INTO testtasks (tttask, tttype, test_id,
  									 ttpriority) VALUES ('$tttask', '$tttype', '$id',
  									 '$key')");
  		$testtask_id = $this->db->last_id();

      foreach($_POST['vars'.($key+1)] as $k2 => $vars) {
        if(trim($vars) == "") continue;
        $var = $this->db->safe($vars);
        $tvtrue = isset($_POST['trues'.($key+1)][$k2]) ? 1 : 0;

        if($tttype != 4) {	// Accordance
          $this->db->query("INSERT INTO testvars (tvvar, testtask_id, tvpriority,
                            tvtrue)
                            VALUES ('$var', '$testtask_id', '$k2', '$tvtrue')");
        } else {
          $parts = explode("|", $var);
          $tvvar = trim($parts[0]);
          $taaccord = trim($parts[1]);

          $this->db->query("INSERT INTO testvars (tvvar, tasktest_id, tvpriority)
                            VALUES ('$tvvar', '$tasktest_id', '$k2')");
          $taskvar_id = $this->db->last_id();
          $this->db->query("INSERT INTO testaccords (testvar_id, taaccord)
                            VALUES ('$taskvar_id', '$taaccord')");
        }
      }
  	}

    $this->view->message('testchanged');
    $this->view->go('/test/showlist');
  }

  function choose() {
    $this->allow(1);
    $this->view->show('test.choose');
  }

  function view() {
    $this->allow(1);
    $tscode = trim($this->db->safe($_POST['tscode']));

    $cnt = $this->db->scalar("SELECT COUNT(*) FROM tests WHERE tscode = '$tscode'");
    if($cnt < 1) {
      $this->view->message('wrongcode');
      header('Location: /test/choose');
      return;
    }

    $data = $this->db->query("SELECT * FROM tests WHERE tscode = '$tscode'");
    $id = $data[0]['id'];
    $qnum = $data[0]['tsqnum'];
    $tsover = $data[0]['tsover'];
    $limitadd = $qnum > 0 ? "LIMIT $qnum" : "";

    if($tsover == 1) {
      $user_id = $this->user_id;
      $resid = $this->db->scalar("SELECT id FROM testresults WHERE test_id = '$id' AND user_id = '$user_id'");
      $this->showresultstouser($resid);
      return;
    }

    $cnt = $this->db->scalar("SELECT COUNT(*) FROM testresults WHERE test_id = '$id'
                              AND user_id = '{$this->user_id}'");
    if($cnt > 0) {
      $this->view->message('nomore');
      header('Location: /test/choose');
      return;
    }

		srand((double)microtime()*1000000);

		$test = $this->db->query("SELECT id, tttype, tttask FROM
														  testtasks WHERE test_id = '$id'
															ORDER BY RAND() $limitadd");//ttpriority");

		$testvars = array();
		$testaccord = array();
		foreach($test as $rec) {
			$id = $rec['id'];

			$vars = $this->db->query("SELECT id, tvvar FROM testvars
																WHERE testtask_id = '$id'");

			$svars = array();
			if(count($vars) > 0) {
				while(count($vars) > 0) {
					$num = rand(0, count($vars)-1);
					$svars[] = $vars[$num];
					for($i = $num; $i < (count($vars)-1); $i++) {
						$vars[$i] = $vars[$i+1];
					}
					unset($vars[count($vars)-1]);
				}
			}

			$testvars[$id] = $svars;

			if($rec['tttype']==4) {	// accordance
				$in = '(';
				foreach($svars as $r2) {
					$in .= $r2['id'] . ',';
				}
				$in .= '0)';
				$accord = $this->db->query("SELECT id, taaccord FROM testaccords
																		WHERE testvar_id IN $in");

				$saccord = array();
				if(count($accord) > 0) {
					while(count($accord) > 0) {
						$num = rand(0, count($accord)-1);
						$saccord[] = $accord[$num];
						for($i = $num; $i < (count($accord)-1); $i++) {
							$accord[$i] = $accord[$i+1];
						}
						unset($accord[count($accord)-1]);
					}
				}

				$testaccord[$id] = $saccord;
			}
		}

		$this->view->assign('testaccord', $testaccord);
		$this->view->assign('testvars', $testvars);
		$this->view->assign('test', $test);
    $this->view->assign('data', $data);

    $this->view->show('test.view');

  }

  function complete() {
    $this->allow(1);
    $id = $this->db->safe($_GET['id']);

    $user_id = $this->user_id;
    $trcount = 0;

    foreach($_POST['testtask_id'] as $testtask_id) {
      if(!array_key_exists($testtask_id, $_POST['results'])) {
        $_POST['results'][$testtask_id] = 0;
      }
    }

    $data = $this->db->query("SELECT v.id AS vid, testtask_id, tvtrue, tttype FROM testvars AS v
                              INNER JOIN testtasks AS t ON t.id = v.testtask_id WHERE
                              test_id = '$id'");
    $check = array();
    $checktypes = array();
    $checkmult = array();
    foreach($data as $rec) {
      $check[$rec['vid']] = $rec;
      $checktypes[$rec['testtask_id']] = $rec['tttype'];
      if($rec['tttype']==2) {
        $checkmult[$rec['testtask_id']][$rec['vid']] = $rec['tvtrue'];
      }
    }

    foreach($_POST['results'] as $testtask_id => $testvar_id) {
      $testtask_id = $this->db->safe($testtask_id);

      if($checktypes[$testtask_id]==1) {
        $testvar_id = $this->db->safe($testvar_id);
        if(isset($check[$testvar_id]['tvtrue']) && $check[$testvar_id]['tvtrue'] == 1) {
          $trcount++;
        }
      } else if($checktypes[$testtask_id]==2) {
        $true = 1;
        if($testvar_id===0) { $testvar_id = array('0'); }
        foreach($testvar_id as $vars) {
          if(!isset($checkmult[$testtask_id][$vars]) || $checkmult[$testtask_id][$vars] != 1) {
            $true = 0;
            break;
          }
        }

        $cnttrue = 0;
        foreach($checkmult[$testtask_id] as $chk) {
          if($chk == 1) { $cnttrue++; }
        }
        if($true && $cnttrue == count($testvar_id)) {
          $trcount++;
        }
      }
    }

    $qnum = $this->db->scalar("SELECT tsqnum FROM tests WHERE id = '$id'");
    $total = $this->db->scalar("SELECT COUNT(*) FROM testtasks WHERE test_id = '$id'");
    $total = $qnum > 0 ? $qnum : $total;
    $trpercent = round($trcount / $total * 100);
    $trtime = time();

    $this->db->query("INSERT INTO testresults (user_id, test_id, trcount, trpercent, trtime)
                      VALUES ('$user_id', '$id', '$trcount', '$trpercent', '$trtime')");
    $testresult_id = $this->db->last_id();

    foreach($_POST['results'] as $testtask_id => $testvar_id) {
      $testtask_id = $this->db->safe($testtask_id);

      if($checktypes[$testtask_id]==1) {
        $testvar_id = $this->db->safe($testvar_id);
        $trdright = isset($check[$testvar_id]['tvtrue']) ? $check[$testvar_id]['tvtrue'] : 0;
        $this->db->query("INSERT INTO testresultdetails (testresult_id, testtask_id, testvar_id, trdright)
                          VALUES ('$testresult_id', '$testtask_id', '$testvar_id', '$trdright')");
      } else if($checktypes[$testtask_id]==2) {
        if($testvar_id===0) { $testvar_id = array('0'); }
        foreach($testvar_id as $vars) {
          $vars = $this->db->safe($vars);
          $trdright = isset($checkmult[$testtask_id][$vars]) ? $checkmult[$testtask_id][$vars] : 0;
          $this->db->query("INSERT INTO testresultdetails (testresult_id, testtask_id, testvar_id, trdright)
                            VALUES ('$testresult_id', '$testtask_id', '$vars', '$trdright')");
        }
      }
    }

    $this->view->assign('total', $total);
    $this->view->assign('trcount', $trcount);
    $this->view->assign('trpercent', $trpercent);
    $this->view->show('test.complete');
  }
}


?>

<?php

include ('base.lib.php');

class CAnalysis extends CBase {

  function __construct($db, $view) {
      parent::__construct($db, $view);

      $this->actions = array('edit', 'subjects', 'teachers', 'form',
                             'rating', 'analysissubjects', 'analysisteachers',
                             'analysisform', 'settings', 'setgroups',
                             'setsubjects');
  }

  function edit() {
    $this->allow(1);

    $teacher_id = $this->user_id;
		$school_id = $this->db->scalar("SELECT school_id FROM users WHERE
																		id = '$teacher_id'");
		$_SESSION['mlschool_id'] = $school_id;

		if(isset($_POST['setmlform_id'])) {
			$_SESSION['mlform_id'] = $_POST['setmlform_id'];
		}
		if(isset($_POST['setmlsubject_id'])) {
			$_SESSION['mlsubject_id'] = $_POST['setmlsubject_id'];
		}
		if(isset($_POST['setmlgroup_id'])) {
			$_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];
		}
		if(isset($_POST['setmlteacher_id'])) {
			$_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];
		} else if(!isset($_SESSION['mlteacher_id'])) {
			$_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
  	}
		$teacher_id = $_SESSION['mlteacher_id'];

		// get schools
    $sql = "SELECT school_id FROM users WHERE id = '{$this->user_id}'";
    $school_id = $this->db->scalar($sql);

		// get forms
		$data = $this->db->query("SELECT * FROM forms WHERE school_id = $school_id
														  ORDER BY fmname+0, fmname");
		$this->view->assign('forms', $data);
		$form_id = isset($_SESSION['mlform_id']) ? $this->db->safe($_SESSION['mlform_id'])
																						 : $data[0]['id'];
		$_SESSION['mlform_id'] = $form_id;

		// get subjects
		$data = $this->db->query("SELECT * FROM mlsubjects
														  ORDER BY mspriority");
		$this->view->assign('subjects', $data);
		$subject_id = isset($_SESSION['mlsubject_id']) ?
									$this->db->safe($_SESSION['mlsubject_id']) :
								  $data[0]['id'];
		$_SESSION['mlsubject_id'] = $subject_id;

		// get groups
		$data = $this->db->query("SELECT * FROM mlgroups
														  ORDER BY mgpriority");
		$this->view->assign('groups', $data);
		$mlgroup_id = isset($_SESSION['mlgroup_id']) ?
									$this->db->safe($_SESSION['mlgroup_id']) :
								  $data[0]['id'];
		$_SESSION['mlgroup_id'] = $mlgroup_id;

		$data = $this->db->query("SELECT id, tcname FROM teachers WHERE "
						. "school_id = '$school_id' ORDER BY tcname");
		$this->view->assign('teachers', $data);

		if(isset($_POST['save'])) {
			$this->db->query("DELETE FROM mlist WHERE pupil_id IN (SELECT id FROM "
							. "pupils WHERE form_id = '$form_id') AND mlinfo_id IN "
							. "(SELECT id FROM mlinfo WHERE mlgroup_id = '$mlgroup_id' AND "
							. "mlsubject_id = '$subject_id' AND teacher_id = '$teacher_id')");
			$this->db->query("DELETE FROM mlinfo WHERE id NOT IN (SELECT mlinfo_id FROM mlist)");
			$teacher_id = $this->db->safe($_SESSION['mlteacher_id']);
			$mlhours = $this->db->safe($_POST['mlhours']);
			$this->db->query("INSERT INTO mlinfo (mlgroup_id, mlsubject_id, teacher_id, mlhours) "
							. "VALUES ('$mlgroup_id', '$subject_id', '$teacher_id', '$mlhours')");
			$mlinfo_id = $this->db->last_id();
			foreach($_POST['mlmark'] as $id => $mark) {
				$id = $this->db->safe($id);
				$mark = $this->db->safe($mark);

				$this->db->query("INSERT INTO mlist (pupil_id, mlinfo_id, mlmark) "
								. "VALUES ('$id', '$mlinfo_id', '$mark')");
			}

      $this->view->message('edited');
		}

		$hours = $this->db->scalar("SELECT mlhours FROM mlinfo AS mi, mlist AS ml, pupils AS pp "
						. "WHERE ml.mlinfo_id = mi.id AND ml.pupil_id = pp.id AND pp.form_id = '$form_id' "
						. "AND mlgroup_id = '$mlgroup_id' "
						. "AND mlsubject_id = '$subject_id' "
						. "AND teacher_id = '$teacher_id'");
		$this->view->assign("hours", $hours);

		$data = $this->db->query("SELECT pp.id AS ppid, ppname, mlmark, pppriority "
						. "FROM pupils AS pp LEFT JOIN (SELECT pupil_id, mlmark FROM mlist, mlinfo AS mi "
						. "WHERE mlinfo_id = mi.id AND mlgroup_id = '$mlgroup_id' AND mlsubject_id = '$subject_id' "
						. "AND teacher_id = '$teacher_id') "
						. " AS ml ON ml.pupil_id = pp.id "
						. "WHERE pp.form_id = '$form_id' "
						. "ORDER BY pppriority");
		$this->view->assign('data', $data);

    $this->view->show('analysis.edit');
  }

  function teachers() {
    $this->allow(1);

    $this->subjects(1);
  }

  function subjects($byteachers = 0) {
    $this->allow(1);

    // clear empty mark forms
    $sql = "DELETE FROM mlinfo WHERE id NOT IN (SELECT mlinfo_id FROM mlist WHERE mlmark > 0) OR
            mlsubject_id NOT IN (SELECT id FROM mlsubjects) OR teacher_id NOT IN (SELECT id FROM teachers)";
    $this->db->query($sql);

    $teacher_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$teacher_id'");
    $_SESSION['mlschool_id'] = $school_id;

    if(isset($_POST['setmlform_id'])) {
			$_SESSION['mlform_id'] = $_POST['setmlform_id'];
		}
		if(isset($_POST['setmlsubjects_id'])) {
			$_SESSION['mlsubjects_id'] = $_POST['setmlsubjects_id'];
		} else if(!isset($_SESSION['mlsubjects_id'])) {
      $_SESSION['mlsubjects_id'] = array();
    }
		if(isset($_POST['setmlgroup_id'])) {
			$_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];
		}
		if(isset($_POST['setmlteacher_id'])) {
			$_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];
		} else if(!isset($_SESSION['mlteacher_id'])) {
			$_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
  	}
    $teacher_id = $_SESSION['mlteacher_id'];

    $sql = "SELECT * FROM mlsubjects ORDER BY mspriority";
    $data = $this->db->query($sql);
    $this->view->assign('subjects', $data);

    $data = $this->db->query("SELECT * FROM mlgroups ORDER BY mgpriority");
    $this->view->assign('groups', $data);

    $mlgroup_id = isset($_SESSION['mlgroup_id']) ? $this->db->safe($_SESSION['mlgroup_id']) : $data[0]['id'];
    $_SESSION['mlgroup_id'] = $mlgroup_id;

    if($byteachers) {
      $data = $this->db->query("SELECT id, tcname FROM teachers WHERE "
            . "school_id = '$school_id' ORDER BY tcname");
      $this->view->assign('teachers', $data);
    }

    if(!$byteachers) {
      $subjects = "";
      foreach($_SESSION['mlsubjects_id'] as $k => $rec) {
        $subjects .= "'$k',";
      }
      $subjects .= '0';

      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND tc.school_id = '$school_id'
              AND mlsubject_id IN ($subjects)
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    } else {
      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND teacher_id = '$teacher_id'
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    }
    $groups = $this->db->query($sql);
    foreach($groups as $k => $rec) {
      $msid = $rec['msid'];
      $tcid = $rec['tcid'];
      $sql = "SELECT mlhours, fmname, SUM(IF(mlmark>0,1,0)) AS cnt,
        SUM(IF(mlmark>6,1,0)) AS qcnt,
        SUM(IF(mlmark=10,1,0)) AS c10,
        SUM(IF(mlmark=9,1,0)) AS c9,
        SUM(IF(mlmark=8,1,0)) AS c8,
        SUM(IF(mlmark=7,1,0)) AS c7,
        SUM(IF(mlmark=6,1,0)) AS c6,
        SUM(IF(mlmark=5,1,0)) AS c5,
        SUM(IF(mlmark=4,1,0)) AS c4,
        SUM(IF(mlmark=3,1,0)) AS c3,
        SUM(IF(mlmark=2,1,0)) AS c2,
        SUM(IF(mlmark=1,1,0)) AS c1
        FROM mlist AS ml, pupils AS pl LEFT JOIN forms AS fm ON pl.form_id = fm.id,
        mlinfo AS mi
        WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
        AND mlsubject_id = '$msid' AND teacher_id = '$tcid'
        GROUP BY fmname
        ORDER BY fmname+0, fmname";

      $data[$msid][$tcid] = $this->db->query($sql);
      $groups[$k]['mlhours'] = 0;
      foreach($data[$msid][$tcid] as $i => $r2) {
        $groups[$k]['mlhours'] += $r2['mlhours'];
        if($r2['cnt'] > 0) {
          $data[$msid][$tcid][$i]['quality'] = round($r2['qcnt']/$r2['cnt']*100);
          $data[$msid][$tcid][$i]['average'] = (10*$r2['c10']+9*$r2['c9']+8*$r2['c8']+7*$r2['c7']+6*$r2['c6']+5*$r2['c5']+4*$r2['c4']+3*$r2['c3']+2*$r2['c2']+1*$r2['c1'])/$r2['cnt'];
        }
      }

      if($rec['cnt'] > 0) {
        $groups[$k]['quality'] = round($rec['qcnt']/$rec['cnt']*100);
        $groups[$k]['average'] = (10*$rec['c10']+9*$rec['c9']+8*$rec['c8']+7*$rec['c7']+6*$rec['c6']+5*$rec['c5']+4*$rec['c4']+3*$rec['c3']+2*$rec['c2']+1*$rec['c1'])/$rec['cnt'];
      }


      $groups[$k]['tcname'] = preg_replace('/(.+?) (..).*? (..).*/', '$1 $2.$3.', $rec['tcname']);
      $groups[$k]['mgname'] = preg_replace('/за ((I+V? ч)|(год)).*/', "$1", $rec['mgname']);
    }

    $this->view->assign('tbgroups', $groups);
    $this->view->assign('data', $data);

    if(!$byteachers) {
      $subjects = "";
      foreach($_SESSION['mlsubjects_id'] as $k => $rec) {
        $subjects .= "'$k',";
      }
      $subjects .= '0';
      $sql = "SELECT mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              COUNT(DISTINCT pupil_id) AS ppcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND tc.school_id = '$school_id'
              AND mlsubject_id IN ($subjects)";
    } else {
      $sql = "SELECT mgname,
              COUNT(DISTINCT pupil_id) AS ppcnt,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND teacher_id = '$teacher_id'
              ";
    }
    $total = $this->db->query($sql);
    if($total[0]['cnt'] > 0) {
      $total[0]['quality'] = round($total[0]['qcnt']/$total[0]['cnt']*100);
      $total[0]['average'] = (10*$total[0]['c10']+9*$total[0]['c9']+8*$total[0]['c8']+7*$total[0]['c7']+6*$total[0]['c6']+5*$total[0]['c5']+4*$total[0]['c4']+3*$total[0]['c3']+2*$total[0]['c2']+1*$total[0]['c1'])/$total[0]['cnt'];
    }
    $total[0]['mgname'] = preg_replace('/за ((I+V? ч)|(год)).*/', "$1", $total[0]['mgname']);
    $total[0]['mlhours'] = 0;
    foreach($groups as $rec) {
      $total[0]['mlhours'] += $rec['mlhours'];
    }
    $this->view->assign('total', $total);

    $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
    $this->view->assign('scname', $scname);
    $year = preg_replace('/за ((I+V? ч)|(год)).*([0-9]{4}\/[0-9]{4}).*/', "$4", $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id'"));
    $this->view->assign('year', $year);
    $this->view->assign('byteachers', $byteachers);
    $this->view->show('analysis.subjects');
  }

  function form() {
    $this->allow(1);

    $user_id = $this->user_id;
		$school_id = $this->db->scalar("SELECT school_id FROM users WHERE
																		id = '$user_id'");
		$_SESSION['mlschool_id'] = $school_id;

		if(isset($_POST['setmlform_id'])) {
      $_SESSION['mlform_id'] = $_POST['setmlform_id'];
    } else if(isset($_SESSION['mlform_id'])) {
      $_SESSION['mlform_id'] = $this->db->scalar("SELECT id FROM forms WHERE school_id = '$school_id' ORDER BY fmname+0, fmname LIMIT 1");
    }
		if(isset($_POST['setmlgroup_id'])) { $_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];	}
		if(isset($_POST['setmlteacher_id'])) { $_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];	}
		else if(!isset($_SESSION['mlteacher_id'])) {
      $_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
    }

		// get forms
		$data = $this->db->query("SELECT * FROM forms WHERE school_id = $school_id
														  ORDER BY fmname+0, fmname");
		$this->view->assign('forms', $data);
		$form_id = (isset($_SESSION['mlform_id']) && $_SESSION['mlform_id'] > 0) ? $this->db->safe($_SESSION['mlform_id']) : $data[0]['id'];
		$_SESSION['mlform_id'] = $form_id;

		// get groups
		$data = $this->db->query("SELECT * FROM mlgroups
														  ORDER BY mgname");
		$this->view->assign('groups', $data);
		$mlgroup_id = (isset($_SESSION['mlgroup_id']) && $_SESSION['mlgroup_id'] > 0) ? $this->db->safe($_SESSION['mlgroup_id']) : $data[0]['id'];
		$_SESSION['mlgroup_id'] = $mlgroup_id;

		$data = $this->db->query("SELECT id, tcname FROM teachers WHERE "
						. "school_id = '$school_id' ORDER BY tcname");
		$this->view->assign('teachers', $data);

		$subjects = $this->db->query("SELECT id, msname FROM mlsubjects ORDER BY mspriority");

		$data = $this->db->query("SELECT id, ppname FROM pupils WHERE form_id = '$form_id' ORDER BY pppriority");
		foreach($data as $i => $rec) {
			$pupil_id = $rec['id'];
			$marks = $this->db->query("SELECT mlmark, mlsubject_id FROM mlist AS ml, mlinfo AS mi "
							. "WHERE mi.id = ml.mlinfo_id AND pupil_id = '$pupil_id' AND mlgroup_id = '$mlgroup_id' "
							. "AND mlmark > 0");

			$data[$i]['average'] = 0;
			foreach($marks as $m) {
				if(!isset($data[$i]['marks'][$m['mlsubject_id']])) {
					$data[$i]['marks'][$m['mlsubject_id']] = 0;
				}
				$data[$i]['marks'][$m['mlsubject_id']] += $m['mlmark'];
				$data[$i]['average'] += $m['mlmark'];
			}
			if(count($marks)) {
				$data[$i]['average'] = $data[$i]['average']/count($marks);
			}
		}
		$this->view->assign('data', $data);

		$subjstr = '';
		foreach($subjects as $rec) {
			$was = 0;
			foreach($data as $r2) {
				if(isset($r2['marks'][$rec['id']])) {
					$was = 1;
				}
			}
			if($was) {
				$subjstr .= $rec['id'].',';
			}
		}
		$subjstr = '('.substr($subjstr, 0, -1).')';
		if($subjstr != '()') {
			$subjects = $this->db->query("SELECT id, msname FROM mlsubjects WHERE id IN $subjstr ORDER BY mspriority");
			$this->view->assign('subjects', $subjects);
		}

		$averages = array();
		foreach($subjects as $rec) {
			$averages[$rec['id']] = 0;
			$q = 0;
			foreach($data as $r2) {
				if(isset($r2['marks'][$rec['id']])) {
					$averages[$rec['id']] += $r2['marks'][$rec['id']];
					$q++;
				}
			}
			if($q>0) {
				$averages[$rec['id']] = $averages[$rec['id']]/$q;
			}
		}
		$averages['total'] = 0;
		foreach($data as $r2) {
			$averages['total'] += $r2['average'];
		}
		if(count($data)) {
			$averages['total'] = $averages['total']/count($data);
		}
		$this->view->assign('averages', $averages);

		$quant = array();
		foreach($subjects as $rec) {
			for($i = 10; $i > 0; $i--) {
				$quant[$i][$rec['id']] = 0;
				foreach($data as $r2) {
					if(isset($r2['marks'][$rec['id']]) && $r2['marks'][$rec['id']]==$i) {
						$quant[$i][$rec['id']]++;
 					}
				}
			}
		}
		foreach($quant as $i => $rec) {
			$quant[$i]['total'] = 0;
			foreach($rec as $k) {
				$quant[$i]['total']+=$k;
			}
		}

		$this->view->assign('quant', $quant);

		$scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
		$this->view->assign('scname', $scname);
		$year = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id'");
		$this->view->assign('year', $year);
		$form = $this->db->scalar("SELECT fmname FROM forms WHERE id = '$form_id'");
		$this->view->assign('form', $form);
		$teacher = $this->db->scalar("SELECT tcname FROM teachers WHERE id = '".$_SESSION['mlteacher_id']."'");
		$this->view->assign('teacher', $teacher);

    $this->view->show('analysis.form');
  }

  function rating() {
    $this->allow(1);

    $teacher_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE
                                    id = '$teacher_id'");
    $_SESSION['mlschool_id'] = $school_id;

    if(isset($_POST['setmlform_id'])) {
      $_SESSION['mlform_id'] = $_POST['setmlform_id'];
    }
    if(isset($_POST['setmlsubject_id'])) {
      $_SESSION['mlsubject_id'] = $_POST['setmlsubject_id'];
    }
    if(isset($_POST['setmlgroup_id'])) {
      $_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];
    }
    if(isset($_POST['setmlteacher_id'])) {
      $_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];
    } else if(!isset($_SESSION['mlteacher_id'])) {
      $_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
    }
    $teacher_id = $_SESSION['mlteacher_id'];

    // get forms
    $data = $this->db->query("SELECT * FROM forms WHERE school_id = $school_id
                              ORDER BY fmname+0, fmname");
    $this->view->assign('forms', $data);
    $form_id = isset($_SESSION['mlform_id']) ? $this->db->safe($_SESSION['mlform_id'])
                                             : $data[0]['id'];
    $_SESSION['mlform_id'] = $form_id;

    // get subjects
    $data = $this->db->query("SELECT * FROM mlsubjects
                              ORDER BY msname");
    $this->view->assign('subjects', $data);
    $subject_id = isset($_SESSION['mlsubject_id']) ?
                  $this->db->safe($_SESSION['mlsubject_id']) :
                  $data[0]['id'];
    $_SESSION['mlsubject_id'] = $subject_id;

    // get groups
    $data = $this->db->query("SELECT * FROM mlgroups
                              ORDER BY mgname");
    $this->view->assign('groups', $data);
    $mlgroup_id = isset($_SESSION['mlgroup_id']) ?
                  $this->db->safe($_SESSION['mlgroup_id']) :
                  $data[0]['id'];
    $_SESSION['mlgroup_id'] = $mlgroup_id;

    $where = '';
    if($subject_id) {
      $where = "AND mlsubject_id = '$subject_id'";
    }
    $where2 = '';
    if($form_id) {
      $where2 = "AND form_id = '$form_id'";
    }

    $data = $this->db->query("SELECT ppname, AVG(mlmark) AS averg, fmname FROM "
            . "pupils AS p LEFT JOIN forms AS f ON f.id = p.form_id, "
            . "(SELECT pupil_id, mlmark FROM mlist AS ml, mlinfo AS mi WHERE "
            . "ml.mlinfo_id = mi.id AND mlgroup_id = '$mlgroup_id' AND mlmark > 0 $where) AS mm "
            . "WHERE p.id = mm.pupil_id AND f.school_id = '$school_id' $where2 "
            . "GROUP BY ppname ORDER BY averg DESC, ppname");
    $avgschool = 0;
    foreach($data as $rec) {
      $avgschool += $rec['averg'];
    }

    if(count($data)) {
      $avgschool = $avgschool / count($data);
    }

    $this->view->assign('data', $data);
    $this->view->assign('avgschool', $avgschool);

    $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
    $this->view->assign('scname', $scname);
    $year = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id'");
    $this->view->assign('year', $year);
    $form = $this->db->scalar("SELECT fmname FROM forms WHERE id = '$form_id'");
    $this->view->assign('fmname', $form);
    $subject = $this->db->scalar("SELECT msname FROM mlsubjects WHERE id = '$subject_id'");
    $this->view->assign('msname', $subject);

    $this->view->show('analysis.rating');
  }

  function analysissubjects($byteachers = 0) {
    $this->allow(1);

    // clear empty mark forms
    $sql = "DELETE FROM mlinfo WHERE id NOT IN (SELECT mlinfo_id FROM mlist WHERE mlmark > 0) OR
            mlsubject_id NOT IN (SELECT id FROM mlsubjects) OR teacher_id NOT IN (SELECT id FROM teachers)";
    $this->db->query($sql);

    $teacher_id = $this->user_id;
    $school_id = $this->db->scalar("SELECT school_id FROM users WHERE id = '$teacher_id'");
    $_SESSION['mlschool_id'] = $school_id;

    if(isset($_POST['setmlsubjects_id'])) {
      $_SESSION['mlsubjects_id'] = $_POST['setmlsubjects_id'];
    }
    if(isset($_POST['setmlgroup_id'])) {
      $_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];
    } else if(!isset($_SESSION['mlgroup_id'])) {
      $_SESSION['mlgroup_id'] = $this->db->scalar("SELECT id FROM mlgroups ORDER BY mgpriority LIMIT 1");
    }
    if(isset($_POST['setlastmlgroup_id'])) {
      $_SESSION['lastmlgroup_id'] = $_POST['setlastmlgroup_id'];
    } else if(!isset($_SESSION['lastmlgroup_id'])) {
      $_SESSION['lastmlgroup_id'] = $this->db->scalar("SELECT id FROM mlgroups ORDER BY mgpriority LIMIT 1,1");
    }
    if(isset($_POST['setmlteacher_id'])) { $_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];	}
    else if(!isset($_SESSION['mlteacher_id'])) {
      $_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
    }
    $teacher_id = $_SESSION['mlteacher_id'];

    $sql = "SELECT * FROM mlsubjects ORDER BY mspriority";
    $data = $this->db->query($sql);
    $this->view->assign('subjects', $data);

    $data = $this->db->query("SELECT * FROM mlgroups ORDER BY mgpriority");
    $this->view->assign('groups', $data);
    $mlgroup_id = isset($_SESSION['mlgroup_id']) ? $this->db->safe($_SESSION['mlgroup_id']) : $data[0]['id'];
    $_SESSION['mlgroup_id'] = $mlgroup_id;
    $mlgroup_id2 = isset($_SESSION['lastmlgroup_id']) ? $this->db->safe($_SESSION['lastmlgroup_id']) :	$data[0]['id'];

    if($byteachers) {
      $data = $this->db->query("SELECT id, tcname FROM teachers WHERE "
            . "school_id = '$school_id' ORDER BY tcname");
      $this->view->assign('teachers', $data);
    }

    if(!$byteachers) {
      $subjects = "";
      foreach($_SESSION['mlsubjects_id'] as $k => $rec) {
        $subjects .= "'$k',";
      }
      $subjects .= '0';

      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND mlsubject_id IN ($subjects)
              AND tc.school_id = '$school_id'
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    } else {
      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
              AND teacher_id = '$teacher_id'
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    }
    $groups = $this->db->query($sql);
    foreach($groups as $k => $rec) {
      $msid = $rec['msid'];
      $tcid = $rec['tcid'];
      $sql = "SELECT mlhours, fmname, form_id, SUM(IF(mlmark>0,1,0)) AS cnt,
        SUM(IF(mlmark>6,1,0)) AS qcnt,
        SUM(IF(mlmark=10,1,0)) AS c10,
        SUM(IF(mlmark=9,1,0)) AS c9,
        SUM(IF(mlmark=8,1,0)) AS c8,
        SUM(IF(mlmark=7,1,0)) AS c7,
        SUM(IF(mlmark=6,1,0)) AS c6,
        SUM(IF(mlmark=5,1,0)) AS c5,
        SUM(IF(mlmark=4,1,0)) AS c4,
        SUM(IF(mlmark=3,1,0)) AS c3,
        SUM(IF(mlmark=2,1,0)) AS c2,
        SUM(IF(mlmark=1,1,0)) AS c1
        FROM mlist AS ml, pupils AS pl LEFT JOIN forms AS fm ON pl.form_id = fm.id,
        mlinfo AS mi
        WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id'
        AND mlsubject_id = '$msid' AND teacher_id = '$tcid'
        GROUP BY fmname
        ORDER BY fmname+0, fmname";

      $data[$msid][$tcid] = $this->db->query($sql);
      foreach($data[$msid][$tcid] as $i => $r2) {
        if($r2['cnt'] > 0) {
          $data[$msid][$tcid][$i]['quality'] = round($r2['qcnt']/$r2['cnt']*100);
          $data[$msid][$tcid][$i]['average'] = (10*$r2['c10']+9*$r2['c9']+8*$r2['c8']+7*$r2['c7']+6*$r2['c6']+5*$r2['c5']+4*$r2['c4']+3*$r2['c3']+2*$r2['c2']+1*$r2['c1'])/$r2['cnt'];
        }
      }

      if($rec['cnt'] > 0) {
        $groups[$k]['quality'] = round($rec['qcnt']/$rec['cnt']*100);
        $groups[$k]['average'] = (10*$rec['c10']+9*$rec['c9']+8*$rec['c8']+7*$rec['c7']+6*$rec['c6']+5*$rec['c5']+4*$rec['c4']+3*$rec['c3']+2*$rec['c2']+1*$rec['c1'])/$rec['cnt'];
      }

      $groups[$k]['tcname'] = preg_replace('/(.+?) (..).*? (..).*/', '$1 $2.$3.', $rec['tcname']);
      $groups[$k]['mgname'] = preg_replace('/за ((I+V? ч)|(год)).*/', "$1", $rec['mgname']);
    }

    $this->view->assign('tbgroups', $groups);
    $this->view->assign('data', $data);

    if(!$byteachers) {
      $subjects = "";
      foreach($_SESSION['mlsubjects_id'] as $k => $rec) {
        $subjects .= "'$k',";
      }
      $subjects .= '0';

      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id2'
              AND mlsubject_id IN ($subjects)
              AND tc.school_id = '$school_id'
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    } else {
      $sql = "SELECT mlsubject_id AS msid, teacher_id AS tcid, msname, tcname, form_id, mgname,
              COUNT(DISTINCT form_id) AS fmcnt,
              SUM(IF(mlmark>0,1,0)) AS cnt,
              SUM(IF(mlmark>6,1,0)) AS qcnt,
              SUM(IF(mlmark=10,1,0)) AS c10,
              SUM(IF(mlmark=9,1,0)) AS c9,
              SUM(IF(mlmark=8,1,0)) AS c8,
              SUM(IF(mlmark=7,1,0)) AS c7,
              SUM(IF(mlmark=6,1,0)) AS c6,
              SUM(IF(mlmark=5,1,0)) AS c5,
              SUM(IF(mlmark=4,1,0)) AS c4,
              SUM(IF(mlmark=3,1,0)) AS c3,
              SUM(IF(mlmark=2,1,0)) AS c2,
              SUM(IF(mlmark=1,1,0)) AS c1
              FROM mlist AS ml, pupils AS pl,
              mlinfo AS mi LEFT JOIN mlsubjects AS ms ON mi.mlsubject_id = ms.id
              LEFT JOIN teachers AS tc ON mi.teacher_id = tc.id
              LEFT JOIN mlgroups AS mg ON mi.mlgroup_id = mg.id
              WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id2'
              AND teacher_id = '$teacher_id'
              GROUP BY tcname, msname
              ORDER BY msname, tcname";
    }
    $groups2 = $this->db->query($sql);
    foreach($groups2 as $k => $rec) {
      $msid = $rec['msid'];
      $tcid = $rec['tcid'];
      $sql = "SELECT mlhours, fmname, form_id, SUM(IF(mlmark>0,1,0)) AS cnt,
        SUM(IF(mlmark>6,1,0)) AS qcnt,
        SUM(IF(mlmark=10,1,0)) AS c10,
        SUM(IF(mlmark=9,1,0)) AS c9,
        SUM(IF(mlmark=8,1,0)) AS c8,
        SUM(IF(mlmark=7,1,0)) AS c7,
        SUM(IF(mlmark=6,1,0)) AS c6,
        SUM(IF(mlmark=5,1,0)) AS c5,
        SUM(IF(mlmark=4,1,0)) AS c4,
        SUM(IF(mlmark=3,1,0)) AS c3,
        SUM(IF(mlmark=2,1,0)) AS c2,
        SUM(IF(mlmark=1,1,0)) AS c1
        FROM mlist AS ml, pupils AS pl LEFT JOIN forms AS fm ON pl.form_id = fm.id,
        mlinfo AS mi
        WHERE ml.pupil_id = pl.id AND ml.mlinfo_id = mi.id AND mi.mlgroup_id = '$mlgroup_id2'
        AND mlsubject_id = '$msid' AND teacher_id = '$tcid'
        GROUP BY fmname
        ORDER BY fmname+0, fmname";

      $data2[$msid][$tcid] = $this->db->query($sql);
      foreach($data2[$msid][$tcid] as $i => $r2) {
        if($r2['cnt'] > 0) {
          $data2[$msid][$tcid][$i]['quality'] = round($r2['qcnt']/$r2['cnt']*100);
          $data2[$msid][$tcid][$i]['average'] = (10*$r2['c10']+9*$r2['c9']+8*$r2['c8']+7*$r2['c7']+6*$r2['c6']+5*$r2['c5']+4*$r2['c4']+3*$r2['c3']+2*$r2['c2']+1*$r2['c1'])/$r2['cnt'];
        }
      }

      if($rec['cnt'] > 0) {
        $groups2[$k]['quality'] = round($rec['qcnt']/$rec['cnt']*100);
        $groups2[$k]['average'] = (10*$rec['c10']+9*$rec['c9']+8*$rec['c8']+7*$rec['c7']+6*$rec['c6']+5*$rec['c5']+4*$rec['c4']+3*$rec['c3']+2*$rec['c2']+1*$rec['c1'])/$rec['cnt'];
      }

      $groups2[$k]['tcname'] = preg_replace('/(.+?) (..).*? (..).*/', '$1 $2.$3.', $rec['tcname']);
      $groups2[$k]['mgname'] = preg_replace('/за ((I+V? ч)|(год)).*/', "$1", $rec['mgname']);
    }
    $data_last = array();
    foreach($groups as $m) {
      $msid = $m['msid'];
      $tcid = $m['tcid'];
      foreach($data[$msid][$tcid] as $k => $rec) {
        $form_id = $rec['form_id'];
        if(isset($data2[$msid][$tcid])) {
          foreach($data2[$msid][$tcid] as $r2) {
            if($r2['form_id'] == $form_id) {
              $data_last[$msid][$tcid][$k] = $r2;
            }
          }
        }
      }
    }

        $groups_last = array();
    foreach($groups as $m) {
      foreach($groups as $k => $rec) {
        $msid = $rec['msid'];
                $tcid = $rec['tcid'];
                foreach($groups2 as $r2) {
                    if($r2['msid'] == $msid && $r2['tcid'] == $tcid) {
                        $groups_last[$k] = $r2;
                    }
                }
      }
    }

    $this->view->assign('tbgroups2', $groups_last);
    $this->view->assign('data2', $data_last);

    $scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
    $this->view->assign('scname', $scname);
    $year = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id'");
    $this->view->assign('year', $year);
    $year2 = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id2'");
    $this->view->assign('year2', $year2);
    $this->view->assign('byteachers', $byteachers);
    $this->view->show('analisys.analyssubjects');
  }

  function analysisteachers() {
    $this->allow(1);

    $this->analysissubjects(1);
  }

  function analysisform() {
    $this->allow(1);

    $teacher_id = $this->user_id;
		$school_id = $this->db->scalar("SELECT school_id FROM users WHERE
																		id = '$teacher_id'");
		$_SESSION['mlschool_id'] = $school_id;

		if(isset($_POST['setmlform_id'])) {
			$_SESSION['mlform_id'] = $_POST['setmlform_id'];
		}
    if(isset($_POST['setmlsubject_id'])) {
      $_SESSION['mlsubject_id'] = $_POST['setmlsubject_id'];
    }
    if(isset($_POST['setmlgroup_id'])) {
      $_SESSION['mlgroup_id'] = $_POST['setmlgroup_id'];
    } else if(!isset($_SESSION['mlgroup_id'])) {
      $_SESSION['mlgroup_id'] = $this->db->scalar("SELECT id FROM mlgroups ORDER BY mgpriority LIMIT 1");
    }
    if(isset($_POST['setlastmlgroup_id'])) {
      $_SESSION['lastmlgroup_id'] = $_POST['setlastmlgroup_id'];
    } else if(!isset($_SESSION['lastmlgroup_id'])) {
      $_SESSION['lastmlgroup_id'] = $this->db->scalar("SELECT id FROM mlgroups ORDER BY mgpriority LIMIT 1,1");
    }
    if(isset($_POST['setmlteacher_id'])) { $_SESSION['mlteacher_id'] = $_POST['setmlteacher_id'];	}
    else if(!isset($_SESSION['mlteacher_id'])) {
      $_SESSION['mlteacher_id'] = $this->db->scalar("SELECT id FROM teachers
                      WHERE school_id = '$school_id' ORDER BY tcname LIMIT 1");
    }
		$teacher_id = $_SESSION['mlteacher_id'];

		// get forms
		$data = $this->db->query("SELECT * FROM forms WHERE school_id = $school_id
														  ORDER BY fmname+0, fmname");
		$this->view->assign('forms', $data);
		$form_id = isset($_SESSION['mlform_id']) ? $this->db->safe($_SESSION['mlform_id'])
																						 : $data[0]['id'];
		$_SESSION['mlform_id'] = $form_id;

		// get subjects
		$data = $this->db->query("SELECT * FROM mlsubjects
														  ORDER BY msname");
		$this->view->assign('subjects', $data);
		$subject_id = isset($_SESSION['mlsubject_id']) ?
									$this->db->safe($_SESSION['mlsubject_id']) :
								  $data[0]['id'];
		$_SESSION['mlsubject_id'] = $subject_id;

		// get groups
		$data = $this->db->query("SELECT * FROM mlgroups
														  ORDER BY mgname");
		$this->view->assign('groups', $data);
		$mlgroup_id = isset($_SESSION['mlgroup_id']) ?
									$this->db->safe($_SESSION['mlgroup_id']) :
								  $data[0]['id'];
		$mlgroup_id2 = isset($_SESSION['lastmlgroup_id']) ?
									$this->db->safe($_SESSION['lastmlgroup_id']) :
									$data[0]['id'];
		$_SESSION['mlgroup_id'] = $mlgroup_id;
		$_SESSION['lastmlgroup_id'] = $mlgroup_id2;

		$where = '';
		if($subject_id) {
			$where = "AND mlsubject_id = '$subject_id'";
		}
		$where2 = '';
		if($form_id) {
			$where2 = "AND form_id = '$form_id'";
		}

		$data = $this->db->query("SELECT p.id AS id, ppname, AVG(mlmark) AS averg, fmname FROM "
						. "pupils AS p LEFT JOIN forms AS f ON f.id = p.form_id, "
						. "(SELECT pupil_id, mlmark FROM mlist AS ml, mlinfo AS mi WHERE "
						. "ml.mlinfo_id = mi.id AND mlgroup_id = '$mlgroup_id' AND mlmark > 0 $where) AS mm "
						. "WHERE p.id = mm.pupil_id AND f.school_id = '$school_id' $where2 "
						. "GROUP BY p.id ORDER BY fmname+0, fmname, pppriority");

		if($subject_id) {
			$subjects = $this->db->query("SELECT id, msname FROM mlsubjects WHERE id = '$subject_id'");
		} else {
			$subjects = $this->db->query("SELECT id, msname FROM mlsubjects");
		}

		foreach($data as $i => $rec) {
			$pupil_id = $rec['id'];
			$marks = $this->db->query("SELECT mlmark, mlsubject_id FROM mlist AS ml, mlinfo AS mi "
							. "WHERE mi.id = ml.mlinfo_id AND pupil_id = '$pupil_id' AND mlgroup_id = '$mlgroup_id' "
							. "$where AND mlmark > 0");

			$data[$i]['average'] = 0;
			foreach($marks as $m) {
				if(!isset($data[$i]['marks'][$m['mlsubject_id']])) {
					$data[$i]['marks'][$m['mlsubject_id']] = 0;
				}
				$data[$i]['marks'][$m['mlsubject_id']] += $m['mlmark'];
				$data[$i]['average'] += $m['mlmark'];
			}
			$data[$i]['average'] = round($data[$i]['average']/count($marks)*10)/10;
		}

		foreach($data as $i => $rec) {
			$pupil_id = $rec['id'];
			$marks = $this->db->query("SELECT mlmark, mlsubject_id FROM mlist AS ml, mlinfo AS mi "
							. "WHERE mi.id = ml.mlinfo_id AND pupil_id = '$pupil_id' AND mlgroup_id = '$mlgroup_id2' "
							. "$where AND mlmark > 0");

			$data[$i]['lastaverage'] = 0;
			foreach($marks as $m) {
				if(!isset($data[$i]['lastmarks'][$m['mlsubject_id']])) {
					$data[$i]['lastmarks'][$m['mlsubject_id']] = 0;
				}
				$data[$i]['lastmarks'][$m['mlsubject_id']] += $m['mlmark'];
				$data[$i]['lastaverage'] += $m['mlmark'];
			}
			if(count($marks) > 0) {
				$data[$i]['lastaverage'] = round($data[$i]['lastaverage']/count($marks)*10)/10;
			}
		}

		$this->view->assign('data', $data);

		$subjstr = '';
		foreach($subjects as $rec) {
			$was = 0;
			foreach($data as $r2) {
				if(isset($r2['marks'][$rec['id']])) {
					$was = 1;
				}
			}
			if($was) {
				$subjstr .= $rec['id'].',';
			}
		}
		$subjstr = '('.substr($subjstr, 0, -1).')';
		if($subjstr!='()') {
			$subjects = $this->db->query("SELECT id, msname FROM mlsubjects WHERE id IN $subjstr");
			$this->view->assign('subjectslist', $subjects);
		}

		$scname = $this->db->scalar("SELECT scname FROM schools WHERE id = '$school_id'");
		$this->view->assign('scname', $scname);
		$year = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id'");
		$this->view->assign('year', $year);
		$year2 = $this->db->scalar("SELECT mgname FROM mlgroups WHERE id = '$mlgroup_id2'");
		$this->view->assign('year2', $year2);
		$form = $this->db->scalar("SELECT fmname FROM forms WHERE id = '$form_id'");
		$this->view->assign('fmname', $form);
		$subject = $this->db->scalar("SELECT msname FROM mlsubjects WHERE id = '$subject_id'");
		$this->view->assign('msname', $subject);

		$this->view->show('analysis.analyspupils');
  }

  function settings() {
    $this->allow(99);

    $this->setgroups();
  }

  function setgroups() {
    $this->allow(99);

    if(isset($_POST['save'])) {
      foreach($_POST['mgname'] AS $k => $rec) {
        $id = $this->db->safe($k);
        $mgname = $this->db->safe($rec);
        $mgtype = $this->db->safe($_POST['mgtype'][$k]);
        $mgpriority = $this->db->safe($_POST['mgpriority'][$k]);

        if(trim($mgname)=='') {
          $sql = "DELETE FROM mlgroups WHERE id = '$id'";
          $this->db->query($sql);
        }

        $sql = "UPDATE mlgroups SET mgname = '$mgname', mgtype='$mgtype',
                mgpriority = '$mgpriority' WHERE id = '$id'";
        $this->db->query($sql);
      }

      foreach($_POST['newmgname'] AS $k => $rec) {
        if(trim($rec)=='') { continue; }
        $mgname = $this->db->safe($rec);
        $mgtype = $this->db->safe($_POST['newmgtype'][$k]);
        $mgpriority = $this->db->safe($_POST['newmgpriority'][$k]);

        $sql = "INSERT INTO mlgroups (mgname, mgtype, mgpriority)
                VALUES ('$mgname', '$mgtype', '$mgpriority')";
        $this->db->query($sql);
      }

      $this->view->message('saved');
    }

    $sql = "SELECT * FROM mlgroups ORDER BY mgpriority";
    $data = $this->db->query($sql);
    $this->view->assign('mlgroups', $data);
    $this->view->show('analysis.setgroups');
  }

  function setsubjects() {
    $this->allow(99);

    if(isset($_POST['save'])) {
      foreach($_POST['msname'] AS $k => $rec) {
        $id = $this->db->safe($k);
        $msname = $this->db->safe($rec);
        $tcht_id = $this->db->safe($_POST['tcht_id'][$k]);
        $mspriority = $this->db->safe($_POST['mspriority'][$k]);

        if(trim($msname)=='') {
          $sql = "DELETE FROM mlsubjects WHERE id = '$id'";
          $this->db->query($sql);
        }

        $sql = "UPDATE mlsubjects SET msname = '$msname', tcht_id='$tcht_id',
                mspriority = '$mspriority' WHERE id = '$id'";
        $this->db->query($sql);
      }

      foreach($_POST['newmsname'] AS $k => $rec) {
        if(trim($rec)=='') { continue; }
        $msname = $this->db->safe($rec);
        $tcht_id = $this->db->safe($_POST['newtcht_id'][$k]);
        $mspriority = $this->db->safe($_POST['newmspriority'][$k]);

        $sql = "INSERT INTO mlsubjects (msname, tcht_id, mspriority)
                VALUES ('$msname', '$tcht_id', '$mspriority')";
        $this->db->query($sql);
      }

      $this->view->message('saved');
    }

    $sql = "SELECT * FROM specializations";
    $data = $this->db->query($sql);
    $this->view->assign('specializations', $data);

    $sql = "SELECT * FROM mlsubjects ORDER BY mspriority";
    $data = $this->db->query($sql);
    $this->view->assign('mlsubjects', $data);

    $this->view->show('analysis.setsubjects');
  }

}

?>

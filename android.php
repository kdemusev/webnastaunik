<?php

include("core/data.lib.php");

$db = new CData();

if(!isset($_GET['action'])) {
  die();
}

if($_GET['action'] == 'login') {
  header('Content-type: text/xml');
  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  print '<answer>'."\n";
  
  $uslogin = $db->safe($_POST['uslogin']);
  $sql = "SELECT id, uspassword FROM users WHERE uslogin = '$uslogin'";
  $data = $db->query($sql);
  
  if(count($data) > 0 &&
     md5($data[0]['uspassword']) == $_POST['uspassword']) {
    print '<result>ok</result>'."\n";
    print '<user_id>'.$data[0]['id'].'</user_id>'."\n";
  } else {
    print '<result>wrong</result>'."\n";
  }
  
  print '</answer>';
}
if($_GET['action'] == 'getnotifications') {
  header('Content-type: text/xml');
  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  print '<answer>'."\n";
  
  $user_id = $db->safe($_POST['user_id']);
  $sql = "SELECT nttopic FROM notifications WHERE user_id = '$user_id' ORDER BY nttime DESC LIMIT 1";
  $data = $db->query($sql);

  if(count($data) > 0) {
    print '<result>'.$data[0]['nttopic'].'</result>'."\n";
  }
  
  print '</answer>';
}
if($_GET['action'] == 'getmessages') {
  header('Content-type: text/xml');
  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  print '<answer>'."\n";
  
  $user_id = $db->safe($_POST['user_id']);
  
  $sql = "SELECT mstopic FROM messages WHERE user_id = '$user_id' AND msreaded = 0 ORDER BY mstime DESC LIMIT 1";
  $data = $db->query($sql);
  if(count($data) > 0) {
    print '<result>'.$data[0]['mstopic'].'</result>'."\n";
  }

  print '</answer>';
}

?>
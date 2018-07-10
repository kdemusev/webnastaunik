<?php

include('templates.lib.php');

class CView {
    private $folder;
    private $data;

    function __construct() {
        $this->folder = 'view';
    }

    function entity($val) {
      if(is_array($val)) {
        foreach($val as $k => $rec) {
          $val[$k] = $this->entity($rec);
        }
        return $val;
      } else {
        return htmlentities($val, ENT_QUOTES, "UTF-8");
      }
    }


    function assign($var, $val) {
      $this->data[$var] = $val;
    }

    function assign_safe($var, $val) {
      $this->data[$var] = $this->entity($val);
    }

    function show($__page) {
        if(is_array($this->data)) {
            foreach($this->data as $k => $v) {
                $$k = $v;
            }
        }

        include($this->folder."/index.php");
        unset($_SESSION['message']);
    }

    function go($link) {
        header('Location: '.$link);
    }

    function page404() {
      $this->show('page404');
      die();
    }

    function message($mes) {
        $_SESSION['message'] = $mes;
    }
}

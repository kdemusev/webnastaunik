<?php

class CTeacher {
    private $db;
    private $view;
    private $actions;

    function __construct($db, $view) {
        $this->db = $db;
        $this->view = $view;
        $this->actions = array('getregions', 'getschools', 'getteachers',
                               'register',
                               'login',
                               'logout');
    }

    function run() {
        if(!isset($_GET['action']) || !in_array($_GET['action'],$this->actions)) {
            $this->view->page404();
            return;
        }
        $this->$_GET['action']();
    }



}

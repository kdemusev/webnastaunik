<?php

include("data.lib.php");
include("view.lib.php");

class CController {
    private $db;
    private $view;

    function __construct() {
        $this->db = new CData();
        $this->view = new CView();

        date_default_timezone_set('Europe/Minsk');
        session_start();
    }

    function run() {
        $sections = array('users', 'teacher', 'ktp', 'timetable', 'school', 'journal',
                          'tasks', 'appeals', 'forum', 'webinar', 'content', 'analysis',
                          'messages', 'notifications', 'lesson', 'autosave', 'methodblog',
                          'quiz', 'bonussalary', 'dbolymp', 'dbconcurs', 'test');
        if(!isset($_GET['section']) || !in_array($_GET['section'], $sections)) {
            $_GET['section'] = 'users';
        }
        $section = $_GET['section'];
        include("$section.lib.php");
        $section = "C".$section;
        $pass = new $section($this->db, $this->view);
        $pass->run();
    }
}

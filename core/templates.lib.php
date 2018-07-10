<?php

class CTemplates {
    private static $folder = "view/templates";

    /**
     * Show message about something
     *
     * @param string $msg_textid text identity given by PHP script
     * @param string $msgtext text to display
     */
    static function showMessage($msg_textid, $msg_text) {
        include(self::$folder.'/message.php');
    }

    /**
     * Show alert message about something
     *
     * @param string $msgtext text to display
     */
    static function showAlert($msg_text) {
        include(self::$folder.'/alert.php');
    }


    /**
     * Display choose bar
     *
     * @param string $_name the name of bar
     * @param string $_var variable name to process by PHP script
     * @param array $_array data array in form of {id:, fieldname:}
     * @param integer $_curvar current selected id
     * @param string $_fieldname fieldname in array of data (like ppname)
     */
    static function chooseBar($_name, $_var, $_array, $_curvar, $_fieldname, $_hidden = array()) {
        include(self::$folder.'/choosebar.php');
    }

    /**
     * Display form as the list
     *
     * @param array $_columns column names for table
     * @param array $_hidden hidden input fields
     */
    static function formList($_columns, $_hidden, $_formlistid = "listformtable",
                             $_showbutton = 1, $withoutform = false) {
        include(self::$folder.'/formlist.php');
    }

    /**
     * Add sc2editor to page
     *
     * @param string $_action action for form submitting
     * @param array $_hidden hidden input fields
     */
    static function sc2editor($_action, $_hidden, $_buttonlabel, $_checkscript = null) {
        include(self::$folder.'/sc2editor.php');
    }
}

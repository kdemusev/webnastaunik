<?php
class CFiles {

  static function download($file, $filename) {
    if(is_file("$file")) {
      header ("Content-type: application/octet-stream");
      header ("Accept-Ranges: bytes");
      header ("Content-Disposition: attachment; filename=\"$filename\"");

      readfile("$file");

      return true;
    }

    return false;
  }

}
 ?>

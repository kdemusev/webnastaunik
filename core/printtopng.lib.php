<?php

class CPrintToPng {
  var $padding = 0;
  var $font = null;
  var $lineheight = 0;
  var $midline = 0;
  var $fontsize = 12;

  function __construct() {
    $this->padding = 3;

    $this->font = 'style/times.ttf';

    $bbox = imagettfbbox($this->fontsize, 0, $this->font, 'ЙQ');

    $textheight = $bbox[3]-$bbox[7];
    $this->lineheight = $textheight + $this->padding*2;

    $this->midline = $textheight-$textheight/7 + $this->padding;



  }

  function deleteold() {
    $handle = opendir('tmpimgs');

    while (false !== ($file = readdir($handle))) {
      $fname = explode('_', $file);
      if(count($fname) == 3) {
        if($fname[1] < time()-60) {
          unlink('tmpimgs/'.$file);
        }
      }
    }

    closedir($handle);
  }

  function drawtable($data, $colslen) {
    $this->deleteold();
    // find every col length
    $collen = array();

    for($k = 0; $k < count($data[0]); $k++) {  // for every col in first row
      $collen[$k] = 0;
      foreach($data as $row) {
        $bbox = imagettfbbox($this->fontsize, 0, $this->font, $row[$k]);
        $agrwidth = $bbox[4]-$bbox[6] + $this->padding*2;

        if($collen[$k] < $agrwidth) {
          $collen[$k] = $agrwidth;
        }
      }
      if($collen[$k] < $colslen[$k]) {
        $collen[$k] = $colslen[$k];
      }
    }

    // count the width of the table
    $tblwidth = 0; // first vertical line;
    for($k = 0; $k < count($collen); $k++) {
      $tblwidth += $collen[$k] + 1; // 1 for vertical line
    }

    $im = imagecreatetruecolor($tblwidth+20, $this->lineheight*count($data)+1);
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);

    imagefilledrectangle($im, 0, 0, $tblwidth+20-1, $this->lineheight*count($data)+1, $white);

    $y = 0;
    $x = 0;
    imageline($im, 0, $y, $tblwidth, $y, $black);

    for($i = 0; $i < count($data); $i++) {
      imageline($im, 0, $y, 0, $y+$this->lineheight, $black);
      $x = 0;
      for($k = 0; $k < count($collen); $k++) {
        $x += $this->padding;
        imagettftext($im, $this->fontsize, 0, $x, $y+$this->midline, $black, $this->font, $data[$i][$k]);
        $x += $collen[$k] - $this->padding + 1;
        imageline($im, $x, $y, $x, $y+$this->lineheight, $black);
      }
      $y += $this->lineheight;
      imageline($im, 0, $y, $tblwidth, $y, $black);
    }

    $uniq = uniqid().'_'.time();
    $fn = 0;

    $x = 0;
    $y = 0;
    $w = $tblwidth+20;
    $h = $this->lineheight*count($data)+1;
    $xstep = 640;
    $ystep = 886;
    while($y <= $h) {
      $x = 0;
      while($x <= $w) {
        $im2 = imagecreatetruecolor($xstep, $ystep);
        $white = imagecolorallocate($im2, 255, 255, 255);
        imagefilledrectangle($im2, 0, 0, $xstep, $ystep, $white);
        $srcw = $x+$xstep < $w ? $xstep : $w - $x;
        $srcy = $y+$ystep < $h ? $ystep : $h - $y;
        imagecopy($im2, $im, 0, 0, $x, $y, $srcw, $srcy);
        imagepng($im2, 'tmpimgs/'.$uniq.'_'.$fn.'.png');
        print '<img src="/tmpimgs/'.$uniq.'_'.$fn.'.png"><br>';
        $fn++;
        imagedestroy($im2);

        $x += $xstep;
      }
      $y += $ystep;
    }


    imagedestroy($im);

  }



}

?>

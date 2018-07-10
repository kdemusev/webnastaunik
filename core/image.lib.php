<?php

class CImage {
  public $type;
  public $src;
  public $dst;

  function __construct($filename) {
    $this->setMemoryForImage($filename);

    $this->type = exif_imagetype($filename);

    switch($this->type) {
      case '2':
        $this->src = imagecreatefromjpeg($filename);
        break;
      case '1':
        $this->src = imagecreatefromgif($filename);
        break;
      case '3':
        $this->src = imagecreatefrompng($filename);
        break;
      case '15':
        $this->src = imagecreatefromwbmp($filename);
        break;
      default:
        $this->src = imagecreatetruecolor(1, 1);
        break;
    }
  }

  private function setMemoryForImage($filename)
  {
  	$imageInfo = getimagesize($filename);
  	$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);

  	$memoryLimit = (int) ini_get('memory_limit')*1048576;

  	if ((memory_get_usage() + $memoryNeeded) > $memoryLimit)
  	{
  		ini_set('memory_limit', ceil((memory_get_usage() + $memoryNeeded + $memoryLimit)/1048576).'M');
  		return (true);
  	}
  	else return(false);
  }

  function scale_and_crop($w, $h) {
    $dw = 0;
    $dh = 0;

    $sx = imagesx($this->src);
    $sy = imagesy($this->src);

    $dw = (int)$w;
    $dh = (int)($sy*($dw/$sx));
    $crop = 1;
    if($dh < $h) {
      $dh = (int)$h;
      $dw = (int)($sx*($dh/$sy));
      $crop = 2;
    }

    $imgtmp = imagecreatetruecolor($dw,$dh);
    imagecopyresampled($imgtmp, $this->src, 0,0,0,0,$dw,$dh,$sx,$sy);
    $this->dst = imagecreatetruecolor($w, $h);
    if($crop == 1) {
      $y = ($dh-$h)/2;
      imagecopy($this->dst, $imgtmp, 0,0,0,$y,$w,$h);
    } else {
      $x = ($dw-$w)/2;
      imagecopy($this->dst, $imgtmp, 0,0,$x,0,$w,$h);
    }
  }

  function scale_width($w) {
    $dw = 0;
    $dh = 0;

    $sx = imagesx($this->src);
    $sy = imagesy($this->src);

    if($w >= $sx) {
      $this->dst = $this->src;
      return;
    }

    $dw = (int)$w;
    $dh = (int)($sy*($dw/$sx));

    $this->dst = imagecreatetruecolor($dw,$dh);
    imagecopyresampled($this->dst, $this->src, 0,0,0,0,$dw,$dh,$sx,$sy);
  }

  function scale_height($h) {
    $dw = 0;
    $dh = 0;

    $sx = imagesx($this->src);
    $sy = imagesy($this->src);

    if($h >= $sy) {
      $this->dst = $this->src;
      return;
    }

    $dh = (int)$h;
    $dw = (int)($sx*($dh/$sy));

    $this->dst = imagecreatetruecolor($dw,$dh);
    imagecopyresampled($this->$st, $this->src, 0,0,0,0,$dw,$dh,$sx,$sy);
  }

  function scale($w, $h) {
    $dw = 0;
    $dh = 0;

    $sx = imagesx($this->src);
    $sy = imagesy($this->src);

    $dw = (int)$w;
    $dh = (int)($sy*($dw/$sx));
    if($dh < $h) {
      $dh = (int)$h;
      $dw = (int)($sx*($dh/$sy));
    }

    $this->dst = imagecreatetruecolor($dw,$dh);
    imagecopyresampled($this->dst, $this->src, 0,0,0,0,$dw,$dh,$sx,$sy);
  }

  function save_as($filename) {
    switch($this->type) {
      case '2':
        return imagejpeg($this->dst, $filename);
        break;
      case '1':
        return imagegif($this->dst, $filename);
        break;
      case '3':
        return imagepng($this->dst, $filename);
        break;
      case '15':
        return imagewbmp($this->dst, $filename);
        break;
      default:
        return imagejpeg($this->dst, $filename);
        break;
      }
  }

  function show() {
    switch($this->type) {
      case '2':
        header ("Content-type: image/jpeg");
        imagejpeg($this->dst);
        break;
      case '1':
        header ("Content-type: image/gif");
        imagegif($this->dst);
        break;
      case '3':
        header ("Content-type: image/png");
        imagepng($this->dst);
        break;
      case '15':
        header ("Content-type: image/wbmp");
        imagewbmp($this->dst);
        break;
      default:
        header ("Content-type: image/jpeg");
        imagejpeg($this->dst);
        break;
      }
  }


}

 ?>

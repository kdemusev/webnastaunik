<?php
class CUtilities {

  static function dateToTimestamp($date) {
    $dateparts = explode('.',$date);
    if(count($dateparts) < 3) {
      return 0;
    }

    return mktime(0,0,0,$dateparts[1],$dateparts[0],$dateparts[2]);
  }

  static function initials($text) {
    return preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.$3.', $text);
  }

  static function truncate($text, $length) {
    $text = strip_tags($text)." ";
    if(mb_strlen($text) <= $length) {
      return $text;
    }
    $text = mb_substr($text, 0, $length);
    $text = mb_substr($text, 0, mb_strrpos($text,' '));
    $text = $text."...";
    return $text;
  }

  static function date_like_gmail($date) {
    $compare = mktime(0,0,0,date('n',$date),date('j',$date),date('Y',$date));
    $today = mktime(0,0,0);

    if($compare == $today) {
      return date('G:i', $date);
    } else if(date('Y',$date) != date('Y')) {
      return date('j.m.Y', $date);
    } else {
      return CUtilities::date_day_month($date);
    }
  }

  static function date_day_month($date) {
    $day = date('j', $date);
    $mnum = date('n', $date);

    $str = $day.' '.($mnum==1?'января':($mnum==2?'февраля':($mnum==3?'марта':
          ($mnum==4?'апреля':($mnum==5?'мая':($mnum==6?'июня':($mnum==7?'июля':
          ($mnum==8?'августа':($mnum==9?'сентября':($mnum==10?'октября':
          ($mnum==11?'ноября':'декабря')))))))))));

    return $str;
  }

  static function text_month($date) {
    $mnum = date('n', $date);

    $str = ($mnum==1?'январь':($mnum==2?'февраль':($mnum==3?'март':
          ($mnum==4?'апрель':($mnum==5?'май':($mnum==6?'июнь':($mnum==7?'июль':
          ($mnum==8?'август':($mnum==9?'сентябрь':($mnum==10?'октябрь':
          ($mnum==11?'ноябрь':'декабрь')))))))))));

    return $str;
  }
}
 ?>

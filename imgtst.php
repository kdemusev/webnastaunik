<?php
include('core/image.lib.php');

$folder = $_GET['f'];
$file = $_GET['id'];
$w = isset($_GET['w']) ? $_GET['w'] : 0;
$h = isset($_GET['h']) ? $_GET['h'] : 0;
$action = $_GET['a'];

$img = new CImage("$folder/$file");

if($action = crop)


$img->scale_and_crop(150, 110);
$img->save_as('test_sm.jpg');

?>

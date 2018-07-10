<?php
error_reporting(E_ERROR | E_WARNING);
header('Content-Type: text/html; charset=utf-8');
include("core/data.lib.php");

error_reporting(E_ALL);
ini_set('display_errors', 'true');
set_time_limit(0);
ini_set('max_execution_time',0);

$main = new CData();

if(isset($_GET['sqldirect']))
{
	print 'Выполнение прямого запроса<br>';
	processData($main->unsafe($_POST['sqltext']));
	print 'Работа завершена<br><br>';
}

else if(isset($_GET['sqlfile']))
{
	foreach($_POST['sqlfiles'] as $rec)
	{
		print 'Обработка файла '.$rec.'<br><hr>';
		$file = fopen("setup/$rec", "r");
		fseek($file, 0, SEEK_SET);
		$contents = fread ($file, filesize("setup/$rec"));
		fclose($file);

		processData($contents);

		print 'Работа завершена<br><br>';
	}
}
else
{
	print '<div style="color: green; font-family: tahoma; font-size: 12px;"><form action="/setup.php?sqlfile" method="post">';
	$handle = opendir('setup');
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			print '<input type="checkbox" name="sqlfiles[]" value="'.$file.'">'.$file.'<br>';
		}
	}
	closedir($handle);
	print '<input type="submit" value="Выполнить">';
	print '</form></div>';

	print '<h2>Прямой запрос</h2>';
	print '<div style="color: green; font-family: tahoma; font-size: 12px;"><form action="/setup.php?sqldirect" method="post">';
	print '<textarea rows="5" cols="80" name="sqltext" id="directquery"></textarea><br>';
	print '<input type="submit" value="Выполнить">';
	print '</form></div>';
	print '<script type="text/javascript">';
	print 'document.getElementById("directquery").focus();';
	print '</script>';

}


function processData($contents)
{
	global $main;

	while(1)
	{
		$comstart = strpos($contents, "/*");
		if($comstart===FALSE)
		{
			break;
		}
		$contents2 = substr($contents, 0, $comstart);
		$comend = strpos($contents, "*/");
		$contents2 .= substr($contents, $comend+2);
		$contents = $contents2;
	}

	$contents = str_replace(";\n", ";\r\n", $contents);
	$queries = explode(";\r\n", $contents);

	foreach($queries as $rec)
	{
		$rec= trim($rec);
		if($rec=='')
			continue;

		print '<div style="color: green; font-family: tahoma; font-size: 12px;">Выполнение запроса</div>';
		print '<div style="color: black; font-family: Courier New; font-size: 12px;">'.nl2br($rec).'</div>';
		print '<div style="color: red; font-family: tahoma; font-size: 12px;">';
		$data = $main->query($rec);
		print '</div>';
		$isselect = stripos($rec, "SELECT");
		if($isselect==0 && $isselect!==false)
		{
			print '<div>';
			print '<table border=1 style="border-collapse: collapse; border: 1px solid black;">';
				print '<tr>';
				foreach($data[0] as $i=>$rec)
				{
					print '<th>'.htmlspecialchars($i).'</th>';
				}
				print '</tr>';
			foreach($data as $rec)
			{
				print '<tr>';
					foreach($rec as $rec2)
					{
						print '<td>'.htmlspecialchars($rec2).'</td>';
					}
				print '</tr>';
			}
			print '</table>';
			print '</div>';
		}
		print '<div style="color: blue; font-family: tahoma; font-size: 12px;">Запрос выполнен</div>';
		print '<hr>';
	}

}

?>

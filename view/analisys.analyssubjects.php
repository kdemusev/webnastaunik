<div class="mainframe">
	<div class="subheader">Анализ состояния преподавания по <?php if($byteachers) { ?>учителям<?php } else { ?>предметам<?php } ?></div>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/analysis/analysissubjects';" value="по предметам"
         <?php if(!$byteachers) { ?>class="checked"<?php } ?> />
  <input type="button" name="newtask" onclick="window.location='/analysis/analysisteachers';" value="по учителям"
         <?php if($byteachers) { ?>class="checked"<?php } ?> />
  <hr />
  </form>

  <form class="transpform" name="formform" action="/analysis/analysis<?php if($byteachers) { ?>teachers<?php } else { ?>subjects<?php } ?>" method="post">
    <label>Период:</label>
		<select name="setmlgroup_id" onchange="document.formform.submit();">
		<?php foreach($groups as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlgroup_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['mgname']?></option>
		<?php } ?>
		</select>
    <label>Сравнительный период:</label>
		<select name="setlastmlgroup_id" onchange="document.formform.submit();">
		<?php foreach($groups as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['lastmlgroup_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['mgname']?></option>
		<?php } ?>
		</select>
    <?php if(!$byteachers) { ?>
    <label>Предметы:</label>
    <div class="checkboxes">
      <input type="checkbox" onchange="setAll(this);" /> Все предметы<br />
		<?php foreach($subjects as $rec) { ?>
			<input type="checkbox" value='1' name='setmlsubjects_id[<?=$rec['id']?>]' <?php if(isset($_SESSION['mlsubjects_id'][$rec['id']])) { ?>checked<?php } ?> /> <?=$rec['msname']?><br />
		<?php } ?>
    </div>
    <input type="submit" value="Выбрать" /><br /><br />
    <?php } else { ?>
    <label>Учитель:</label>
		<select name="setmlteacher_id" onchange="document.formform.submit();">
		<?php foreach($teachers as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlteacher_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['tcname']?></option>
		<?php } ?>
		</select>
    <?php } ?>
  </form>

<div class="transpform">
	<button onclick="onPrint();">Распечатать</button>
</div>
<br />
<div id="toprint">
	<div class="printable">
		<center>Государственное учреждение образования "<?=$scname?>"</center>
		<h1 align="center">Анализ качества преподавания предметов <?=$year?> учебного года<br> по сравнению с  периодом <?=$year2?> учебного года</h1>
	</div>

	<table class="smptable overable">
  	<thead>
  		<tr>
  			<th rowspan="2" valign="center" align="center">№ п/п</th>
  			<th rowspan="2" valign="center" align="center">Название предмета</th>
  			<th rowspan="2" valign="center" align="center">Учитель</th>
  			<th rowspan="2">
  				<div class="vertwrapper">
  					<div class="vert">
  						Класс
  					</div>
  				</div>
  			</th>
  			<th colspan="3" align="center">Качество преподавания</th>
  			<th colspan="3" align="center">Средний балл</th>
  		</tr>
  		<tr>
  			<th align="center">
  				<?=$tbgroups2[0]['mgname']?>
  			</th>
  			<th align="center">
  				<?=$tbgroups[0]['mgname']?>
  			</th>
  			<th align="center">
  				Динамика
  			</th>
  			<th align="center">
  				<?=$tbgroups2[0]['mgname']?>
  			</th>
  			<th align="center">
  				<?=$tbgroups[0]['mgname']?>
  			</th>
  			<th align="center">
  				Динамика
  			</th>
  		</tr>
  	</thead>
  		<?php $vedindex = 1; foreach($tbgroups as $vedk => $ved) { ?>
  		<tr>
  			<td align="right">
  				<?=$vedindex++?>
  			</td>
  			<td nowrap>
  				<?=$ved['msname']?>
  			</td>
  			<td nowrap>
  				<?=$ved['tcname']?>
  			</td>
  			<?php $lastdat = count($data[$ved['msid']][$ved['tcid']]); foreach($data[$ved['msid']][$ved['tcid']] as $k => $rec) { ?>
  			<td nowrap align="center">
  				<?=$rec['fmname']?>
  			</td>
  			<td align="center">
  				<?=$data2[$ved['msid']][$ved['tcid']][$k]['quality']?>%
  			</td>
  			<td align="center">
  				<?=$rec['quality']?>%
  			</td>
  			<td align="center">
  				<?php if($rec['quality']-$data2[$ved['msid']][$ved['tcid']][$k]['quality'] != 0) { ?>
  					<?=$rec['quality']-$data2[$ved['msid']][$ved['tcid']][$k]['quality']?>%
  				<?php } ?>
  				<?php if($rec['quality'] > $data2[$ved['msid']][$ved['tcid']][$k]['quality']) { ?>
  					<b>&uarr;</b>
  				<?php } else if($rec['quality'] < $data2[$ved['msid']][$ved['tcid']][$k]['quality']) { ?>
  					<b>&darr;</b>
  				<?php } ?>
  			</td>
  			<td align="center">
          <?=number_format(round($data2[$ved['msid']][$ved['tcid']][$k]['average'], 2),2)?>
  			</td>
  			<td align="center">
          <?=number_format(round($rec['average'],2),2)?>
  			</td>
  			<td align="center">
          <?php if($rec['average']-$data2[$ved['msid']][$ved['tcid']][$k]['average'] != 0) { ?>
  					<?=number_format(round($rec['average']-$data2[$ved['msid']][$ved['tcid']][$k]['average'],2),2)?>
  				<?php } ?>
  				<?php if($rec['average'] > $data2[$ved['msid']][$ved['tcid']][$k]['average']) { ?>
  					<b>&uarr;</b>
  				<?php } else if($rec['average'] < $data2[$ved['msid']][$ved['tcid']][$k]['average']) { ?>
  					<b>&darr;</b>
  				<?php } ?>
  			</td>
  		</tr>
  		<tr>
  			<?php if($lastdat == 1) { ?>
  				<td colspan="3" align="right"><b>Итого:</b></td>
  			<?php } else { $lastdat--; ?>
  				<td></td>
  				<td></td>
  				<td></td>
  			<?php } ?>
  		<?php } ?>
  				<td align="center"><b><?=$ved['fmcnt']?></b></td>
  				<td align="center"><b><?=$tbgroups2[$vedk]['quality']?>%</b></td>
  				<td align="center"><b><?=$ved['quality']?>%</b></td>
  				<td align="center">
  					<?php if($ved['quality']-$tbgroups2[$vedk]['quality'] != 0) { ?>
  						<b><?=$ved['quality']-$tbgroups2[$vedk]['quality']?>%</b>
  					<?php } ?>
  					<?php if($ved['quality'] > $tbgroups2[$vedk]['quality'] != 0) { ?>
  						<b>&uarr;</b>
  					<?php } else if($ved['quality'] < $tbgroups2[$vedk]['quality'] != 0) { ?>
  						<b>&darr;</b>
  					<?php } ?>
  				</td>
  				<td align="center"><b><?=number_format(round($tbgroups2[$vedk]['average'],2),2)?></b></td>
  				<td align="center"><b><?=number_format(round($ved['average'],2),2)?></b></td>
  				<td align="center">
            <?php if($ved['average']-$tbgroups2[$vedk]['average'] != 0) { ?>
  						<b><?=number_format(round($ved['average']-$tbgroups2[$vedk]['average'],2),2)?></b>
  					<?php } ?>
  					<?php if($ved['average'] > $tbgroups2[$vedk]['average'] != 0) { ?>
  						<b>&uarr;</b>
  					<?php } else if($ved['average'] < $tbgroups2[$vedk]['average'] != 0) { ?>
  						<b>&darr;</b>
  					<?php } ?>
  				</td>
  			</tr>
  		<?php } ?>
  </table>
</div>
</div>

<script>
	function onPrint() {
		var objbody = document.body.innerHTML;
		var els = document.getElementsByClassName('printable');

		var i = 0;
		var l = els.length;
		for(i = 0; i < l; i++) {
			els[i].style.display = 'block';
		}

		var objtoprint = document.getElementById('toprint').innerHTML;

		document.body.innerHTML = objtoprint;

		window.print();

		document.body.innerHTML = objbody;
	}

  function setAll(obj) {
    var pnt = obj.parentNode;
    var l = pnt.children.length;
    var i = 0;
    var checked = obj.checked ? 1 : 0;
    for(i = 1; i < l; i++) {
      if(checked) {
        pnt.children[i].checked = true;
      } else {
        pnt.children[i].checked = false;
      }
    }
  }
</script>

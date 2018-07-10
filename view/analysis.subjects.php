<div class="mainframe">
	<div class="subheader">Сводная ведомость состояния преподавания по <?php if($byteachers) { ?>учителям<?php } else { ?>предметам<?php } ?></div>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/analysis/subjects';" value="по предметам"
         <?php if(!$byteachers) { ?>class="checked"<?php } ?> />
  <input type="button" name="newtask" onclick="window.location='/analysis/teachers';" value="по учителям"
         <?php if($byteachers) { ?>class="checked"<?php } ?> />
  <hr />
  </form>

  <form class="transpform" name="formform" action="/analysis/<?php if($byteachers) { ?>teachers<?php } else { ?>subjects<?php } ?>" method="post">
    <label>Период:</label>
		<select name="setmlgroup_id" onchange="document.formform.submit();">
		<?php foreach($groups as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlgroup_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['mgname']?></option>
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
    <input type="submit" value="Выбрать" />
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
	<button onclick="onPrint()">Распечатать</button>
</div>
<br />

	<div id="toprint">
		<div class="printable">
			<center>Государственное учреждение образования "<?=$scname?>"</center>
			<h1 align="center">Сводная ведомость<br>состояния преподавания предметов в <?=$year?> учебном году</h1>
		</div>

		<table class="smptable overable">
			<thead>
				<tr>
					<th rowspan="2" valign="center" align="center">№<br>п/п</th>
					<th rowspan="2" valign="center" align="center">Название предмета</th>
					<th rowspan="2" valign="center" align="center">Учитель</th>
					<th rowspan="2">
						<div class="vertwrapper">
							<div class="vert">
								Нагрузка
							</div>
						</div>
					</th>
					<th rowspan="2">
						<div class="vertwrapper">
							<div class="vert">
								Период
							</div>
						</div>
					</th>
					<th rowspan="2">
						<div class="vertwrapper">
							<div class="vert">
								Класс
							</div>
						</div>
					</th>
					<th rowspan="2">
						<div class="vertwrapper">
							<div class="vert2lines">
								Количество учащихся
							</div>
						</div>
					</th>
					<th colspan="10" align="center">Количество оценок</th>
					<th rowspan="2" align="center">Качество преподавания</th>
					<th rowspan="2" align="center">Средний балл</th>
				</tr>
				<tr>
					<th align="center">10</th>
					<th align="center">9</th>
					<th align="center">8</th>
					<th align="center">7</th>
					<th align="center">6</th>
					<th align="center">5</th>
					<th align="center">4</th>
					<th align="center">3</th>
					<th align="center">2</th>
					<th align="center">1</th>
				</tr>
			</thead>
			<?php $grindex = 1; foreach($tbgroups as $ved) { ?>
				<tr>
					<td align="right">
						<?=$grindex++?>
					</td>
					<td nowrap>
						<?=$ved['msname']?>
					</td>
					<td nowrap>
						<?=$ved['tcname']?>
					</td>
					<?php $sblast = count($data[$ved['msid']][$ved['tcid']]);
          foreach($data[$ved['msid']][$ved['tcid']] as $rec) { ?>
					<td align="center">
						<?=$rec['mlhours']?>
					</td>
					<td align="center">
						<?=$ved['mgname']?>
					</td>
					<td nowrap align="center">
						<?=$rec['fmname']?>
					</td>
					<td align="center">
						<?=$rec['cnt']?>
					</td>
					<td align="center">
						<?=$rec['c10']?>
					</td>
					<td align="center">
						<?=$rec['c9']?>
					</td>
					<td align="center">
						<?=$rec['c8']?>
					</td>
					<td align="center">
						<?=$rec['c7']?>
					</td>
					<td align="center">
						<?=$rec['c6']?>
					</td>
					<td align="center">
						<?=$rec['c5']?>
					</td>
					<td align="center">
						<?=$rec['c4']?>
					</td>
					<td align="center">
						<?=$rec['c3']?>
					</td>
					<td align="center">
						<?=$rec['c2']?>
					</td>
					<td align="center">
						<?=$rec['c1']?>
					</td>
					<td align="center">
						<?=$rec['quality']?>%
					</td>
					<td align="center">
						<?=number_format(round($rec['average'],2),2)?>
					</td>
				</tr>
				<tr>
					<?php if($sblast==1) { ?>
						<td colspan="3" align="right" style="border-bottom: 2px solid;"><b>Итого:</b></td>
					<?php } else { $sblast--; ?>
						<td></td>
						<td></td>
						<td></td>
					<?php } ?>
				<?php } ?>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['mlhours']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['mgname']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['fmcnt']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['cnt']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c10']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c9']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c8']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c7']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c6']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c5']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c4']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c3']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c2']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['c1']?></b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=$ved['quality']?>%</b></td>
						<td align="center" style="border-bottom: 2px solid;"><b><?=number_format(round($ved['average'],2),2)?></b></td>
					</tr>
				<?php } ?>
        <?php if(count($tbgroups)) { ?>
				<tr>
					<td colspan="3" align="right"><b>Итого по перечисленным предметам:</b></td>
					<td align="center"><b><?=$total[0]['mlhours']?></b></td>
					<td align="center"><b><?=$total[0]['mgname']?></b></td>
					<td align="center"><b><?=$total[0]['fmcnt']?></b></td>
					<td align="center"><b><?=$total[0]['ppcnt']?></b></td>
					<td align="center"><b><?=$total[0]['c10']?></b></td>
					<td align="center"><b><?=$total[0]['c9']?></b></td>
					<td align="center"><b><?=$total[0]['c8']?></b></td>
					<td align="center"><b><?=$total[0]['c7']?></b></td>
					<td align="center"><b><?=$total[0]['c6']?></b></td>
					<td align="center"><b><?=$total[0]['c5']?></b></td>
					<td align="center"><b><?=$total[0]['c4']?></b></td>
					<td align="center"><b><?=$total[0]['c3']?></b></td>
					<td align="center"><b><?=$total[0]['c2']?></b></td>
					<td align="center"><b><?=$total[0]['c1']?></b></td>
					<td align="center"><b><?=$total[0]['quality']?>%</b></td>
					<td align="center"><b><?=number_format(round($total[0]['average'],2),2)?></b></td>
				</tr>
        <?php } else { ?>
        <tr>
          <td colspan="19" align="center">Итоговая ведомость оценок за данный период по выбранному критерию не заполнена</td>
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

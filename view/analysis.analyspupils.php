<div class="mainframe">
	<div class="subheader">Сравнительный анализ успеваемости учащихся</div>

  <form class="transpform" name="formform" action="/analysis/analysisform" method="post">
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
		<label>Предмет:</label>
		<select name="setmlsubject_id" onchange="document.formform.submit();">
			<option value="0" <?php if($_SESSION['mlsubject_id']==0) {?>selected<?php } ?> >Все предметы</option>
		<?php foreach($subjects as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlsubject_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['msname']?></option>
		<?php } ?>
		</select>
		<label>Класс:</label>
		<select name="setmlform_id" onchange="document.formform.submit();">
			<option value="0" <?php if($_SESSION['mlform_id']==0) {?>selected<?php } ?> >Все классы</option>
		<?php foreach($forms as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlform_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['fmname']?></option>
		<?php } ?>
		</select>
  </form>

<div class="transpform">
<button onclick="onPrint();">Распечатать</button>
</div>
<br />

<div id="toprint">
	<div class="printable">
		<center>Государственное учреждение образования "<?=$scname?>"</center>
		<h1 align="center">Анализ успеваемости учащихся <?php if($_SESSION['mlform_id']) { ?><?=$fmname?> класса<?php } ?>
			<?php if($_SESSION['mlsubject_id']) { ?>по предмету &quot;<?=$msname?>&quot;<?php } ?>
			<?=$year?> учебного года<br> по сравнению с результатами за <?=$year2?> учебного года</h1>
	</div>

	<?php $showmore = 0; ?>
	<table class="smptable overable">
		<thead>
			<tr>
				<th align="center" nowrap>№ п/п</th>
				<th width="100%">Фамилия, имя ученика</th>
				<th nowrap align="center" style="border-right: 2px solid;">Класс</th>
				<?php $hdsubj = 0; foreach($subjectslist as $rec) { ?>
					<?php if($hdsubj++ < 7) { ?>
					<th colspan="3"  style="border-right: 2px solid;">
						<div class="vertwrapper">
							<div class="vert">
								<?=$rec['msname']?>
							</div>
						</div>
					</th>
					<?php } else {
						$showmore = 1;
					} } ?>
				<?php if($showmore == 0) { ?>
					<?php if($_SESSION['mlsubject_id'] == 0) { ?>
				<th colspan="3">
					<div class="vertwrapper">
						<div class="vert2lines">
								Средний балл
						</div>
					</div>
				</th>
				<?php } } ?>
			</tr>
		</thead>
		<?php $upcnt = 0;
		$downcnt = 0;
		$vedsindex = 1;
		foreach($data as $rec) { ?>
			<tr>
				<td align="right">
					<?=$vedsindex++?>
				</td>
				<td nowrap>
					<?=$rec['ppname']?>
				</td>
				<td align="center"  style="border-right: 2px solid;">
					<?=$rec['fmname']?>
				</td>
				<?php $subjindex = 0; foreach($subjectslist as $r2) { ?>
					<?php if($subjindex++ < 7) { ?>
					<td align="center" ><?=@$rec['lastmarks'][$r2['id']]?></td>
					<td align="center" ><?=@$rec['marks'][$r2['id']]?></td>
					<td align="center" nowrap  style="border-right: 2px solid;">
					<?php if(isset($rec['lastmarks'][$r2['id']])) { ?>
					<?php if($rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']] > 0) { ?>
						<?=$rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']]?>
						<?php if($_SESSION['mlsubject_id'] > 0) { $upcnt++; } ?>
						<b>&uarr;</b>
					<?php } else if($rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']] < 0) { ?>
							<?=$rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']]?>
							<?php if($_SESSION['mlsubject_id'] > 0) { $downcnt++; } ?>
						<b>&darr;</b>
					<?php } ?>
					</td>
					<?php } ?>
					<?php } ?>
				<?php } ?>
				<?php if($showmore == 0) { ?>
				<?php if($_SESSION['mlsubject_id'] == 0) { ?>
					<td align="center"><?=round($rec['lastaverage'],2)?></td>
					<td align="center"><?=round($rec['average'],2)?></td>
					<td align="center" nowrap>
					<?php if($rec['average']-$rec['lastaverage'] > 0) { ?>
						<?=round($rec['average']-$rec['lastaverage'],2)?>
						<?php $upcnt++; ?>
					<b>&uarr;</b>
				<?php } else if($rec['average']-$rec['lastaverage'] < 0) { ?>
					<?=round($rec['average']-$rec['lastaverage'],2)?>
					<?php $downcnt++; ?>
						<b>&darr;</b>
					<?php } ?>
					</td>
				<?php } } ?>
			</tr>
		<?php } ?>
		<?php if($showmore == 0) { ?>
			<tr>
				<td colspan="<?php if($_SESSION['mlsubject_id'] == 0) { print count($subjectslist)*3+2+3; } else { print count($subjectslist)*3+2; }?>" align="right"><b>Всего учеников повысили свой уровень знаний:</b></td>
				<td align="center"><b><?=$upcnt?></b></td>
			</tr>
			<tr>
				<td colspan="<?php if($_SESSION['mlsubject_id'] == 0) { print count($subjectslist)*3+2+3; } else { print count($subjectslist)*3+2; }?>" align="right"><b>Всего учеников понизили свой уровень знаний:</b></td>
				<td align="center"><b><?=$downcnt?></b></td>
			</tr>
		<?php } ?>
	</table>
	<?php if($showmore) { ?>
		<table class="smptable overable">
			<thead>
				<tr>
					<th align="center" nowrap>№ п/п</th>
					<th width="100%">Фамилия, имя ученика</th>
					<?php $hdsubj2 = 0; foreach($subjectslist as $rec) { ?>
						<?php if($hdsubj2++ >= 7) { ?>
						<th colspan="3"  style="border-right: 2px solid;">
							<div class="vertwrapper">
								<div class="vert">
									<?=$rec['msname']?>
								</div>
							</div>
						</th>
						<?php } } ?>
						<?php if($_SESSION['mlsubject_id'] == 0) { ?>
					<th colspan="3">
						<div class="vertwrapper">
							<div class="vert2lines">
									Средний балл
							</div>
						</div>
					</th>
					<?php } ?>
				</tr>
			</thead>
			<?php
			$vedsindex = 1;
			foreach($data as $rec) { ?>
				<tr>
					<td align="right">
						<?=$vedsindex++?>
					</td>
					<td nowrap>
						<?=$rec['ppname']?>
					</td>
					<?php $subjindex2 = 0; foreach($subjectslist as $r2) { ?>
						<?php if($subjindex2++ >= 7) { ?>
						<td align="center" ><?=$rec['lastmarks'][$r2['id']]?></td>
						<td align="center" ><?=$rec['marks'][$r2['id']]?></td>
						<td align="center" nowrap  style="border-right: 2px solid;">
						<?php if($rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']] > 0) { ?>
							<?=$rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']]?>
							<?php if($_SESSION['mlsubject_id'] > 0) { $upcnt++; } ?>
							<b>&uarr;</b>
						<?php } else if($rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']] < 0) { ?>
								<?=$rec['marks'][$r2['id']]-$rec['lastmarks'][$r2['id']]?>
								<?php if($_SESSION['mlsubject_id'] > 0) { $downcnt++; } ?>
							<b>&darr;</b>
						<?php } ?>
						</td>
						<?php } ?>
					<?php } ?>
					<?php if($_SESSION['mlsubject_id'] == 0) { ?>
						<td align="center"><?=round($rec['lastaverage'],2)?></td>
						<td align="center"><?=round($rec['average'],2)?></td>
						<td align="center" nowrap>
						<?php if($rec['average']-$rec['lastaverage'] > 0) { ?>
							<?=round($rec['average']-$rec['lastaverage'],2)?>
							<?php $upcnt++; ?>
						<b>&uarr;</b>
					<?php } else if($rec['average']-$rec['lastaverage'] < 0) { ?>
						<?=round($rec['average']-$rec['lastaverage'],2)?>
						<?php $downcnt++; ?>
							<b>&darr;</b>
						<?php } ?>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
				<tr>
					<td colspan="<?php if($_SESSION['mlsubject_id'] == 0) { print count($subjectslist)*3+2+3; } else { print count($subjectslist)*3+2; }?>" align="right"><b>Всего учеников повысили свой уровень знаний:</b></td>
					<td align="center"><b><?=$upcnt?></b></td>
				</tr>
				<tr>
					<td colspan="<?php if($_SESSION['mlsubject_id'] == 0) { print count($subjectslist)*3+2+3; } else { print count($subjectslist)*3+2; }?>" align="right"><b>Всего учеников понизили свой уровень знаний:</b></td>
					<td align="center"><b><?=$downcnt?></b></td>
				</tr>
		</table>
	<?php } ?>
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
</script>

<div class="mainframe">
	<div class="subheader">Рейтинг учащихся</div>

  <form class="transpform" name="formform" action="/analysis/rating" method="post">
    <label>Класс:</label>
		<select name="setmlform_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['mlform_id']==0) {?>selected<?php } ?> >Все классы</option>
		<?php foreach($forms as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlform_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['fmname']?></option>
		<?php } ?>
		</select>
    <label>Период:</label>
		<select name="setmlgroup_id" onchange="document.formform.submit();">
		<?php foreach($groups as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlgroup_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['mgname']?></option>
		<?php } ?>
		</select>
    <label>Предмет:</label>
    <select name="setmlsubject_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['mlsubject_id']==0) {?>selected<?php } ?> >Все предметы</option>
    <?php foreach($subjects as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['mlsubject_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['msname']?></option>
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
		<h1 align="center">Рейтинг учеников <?php if($_SESSION['mlform_id']) { ?><?=$fmname?> класса<?php } ?>
			<?php if($_SESSION['mlsubject_id']) { ?>по предмету &quot;<?=$msname?>&quot;<?php } ?>
			<?=$year?> учебного года</h1>
	</div>

  <table class="smptable overable">
		<thead>
			<tr>
				<th align="center" nowrap>№ п/п</th>
				<th width="100%">Фамилия, имя ученика</th>
				<th align="center">Класс</th>
				<th align="center" nowrap>Средний балл</th>
			</tr>
		</thead>
		<?php $vedsindex = 1; foreach($data as $rec) { ?>
			<tr>
				<td align="right">
					<?=$vedsindex++?>
				</td>
				<td>
					<?=$rec['ppname']?>
				</td>
				<td align="center">
					<?=$rec['fmname']?>
				</td>
				<td align="center">
					<?=round($rec['averg'],2)?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td align="right" colspan="3">
				<b>Средний балл по всем ученикам:</b>
			</td>
			<td align="center">
				<b><?=round($avgschool,2)?></b>
			</td>
		</tr>
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
</script>

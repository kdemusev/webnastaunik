<div class="mainframe">
	<div class="subheader">Сводная ведомость учета успеваемости и поведения учащихся</div>

  <form class="transpform" name="formform" action="/analysis/form" method="post">
    <label>Класс:</label>
		<select name="setmlform_id" onchange="document.formform.submit();">
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
    <label>Классный руководитель:</label>
		<select name="setmlteacher_id" onchange="document.formform.submit();">
		<?php foreach($teachers as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlteacher_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['tcname']?></option>
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
		<h1 align="center">Сводная ведомость учета успеваемости и поведения учащихся <?=$form?> класса <?=$year?> учебного года<br>
		Классный руководитель: <?=$teacher?></h1>
	</div>

<?php if(isset($subjects)) { ?>
  <table class="smptable overable">
    <thead>
      <tr>
        <th>№ п/п</th>
        <th width="100%">Фамилия, имя учащегося</th>
        <?php foreach($subjects as $rec) { ?>
          <th>
            <div class="vertwrapper">
              <div class="vert">
                <?=$rec['msname']?>
              </div>
            </div>
          </th>
        <?php } ?>
        <th>
          <div class="vertwrapper">
              <div class="vert2lines">
                Средний балл
            </div>
          </div>
        </th>
        <th>Поведение</th>
      </tr>
    </thead>
  <?php $fepupilsindex = 1; foreach($data as $rec) { ?>
      <tr>
        <td align="center">
          <?=$fepupilsindex++?>
        </td>
        <td nowrap>
          <?=$rec['ppname']?>
        </td>
        <?php foreach($subjects as $r2) { ?>
          <td align="center" style="width: 40px;"><?=$rec['marks'][$r2['id']]?></td>
        <?php } ?>
        <td align="center"><b><?=round($rec['average'], 1)?></b></td>
        <td></td>
      </tr>
  <?php } ?>
  <tr>
    <td colspan="2" align="right" nowrap><b>Средний балл по предмету:</b></td>
    <?php foreach($subjects as $r2) { ?>
      <td align="center"><b><?=round($averages[$r2['id']],1)?></b></td>
    <?php } ?>
    <td align="center"><b><?=round($averages['total'],1)?></b></td>
    <td></td>
  </tr>
  </tbody>

  <thead>
        <tr>
          <th colspan="2" align="right">Отметка</th>
          <th colspan="<?=count($subjects)?>" align="center">Количество отметок</th>
          <th>Всего</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($quant as $k => $rec) { ?>
        <tr>
          <td colspan="2" align="right"><b><?=$k?></b></td>
          <?php foreach($subjects as $r2) { ?>
            <td align="center"><?=$rec[$r2['id']]?></td>
          <?php } ?>
          <td align="center"><b><?=$rec['total']?></b></td>
        </tr>
        <?php } ?>
      </tbody>
  	</table>
  <?php } else { ?>
  Для выбранного класса не определен ни один предмет
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

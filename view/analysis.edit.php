<div class="mainframe">
	<div class="subheader">Заполнение ведомости итоговых оценок</div>

	<?php CTemplates::showMessage('edited', 'Изменения сохранены'); ?>

  <form class="transpform" name="formform" action="/analysis/edit" method="post">
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
    <label>Предмет:</label>
		<select name="setmlsubject_id" onchange="document.formform.submit();">
		<?php foreach($subjects as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlsubject_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['msname']?></option>
		<?php } ?>
		</select>
    <label>Учитель:</label>
		<select name="setmlteacher_id" onchange="document.formform.submit();">
		<?php foreach($teachers as $rec) { ?>
			<option value="<?=$rec['id']?>" <?php if($_SESSION['mlteacher_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['tcname']?></option>
		<?php } ?>
		</select>
  </form>

  <form class="transpform" action="/analysis/edit" method="post"
        id="marksform">
    <label>Нагрузка (количество часов изучения предмета в неделю):</label>
		<input type="text" name="mlhours" id="mlhours" value="<?=isset($hours) ? $hours : ''?>" />

    <hr />

		<table class="invtable overable">
			<thead>
				<tr>
					<th>№</th>
					<th width="200">Фамилия, имя</th>
					<th>Отметка</th>
				</tr>
			</thead>
		  <?php foreach($data AS $rec) { ?>
				<tr>
					<td align="center">
						<?=$rec['pppriority']?>
					</td>
					<td>
						<?=$rec['ppname']?>
					</td>
					<td align="center">
						<input type="text" name="mlmark[<?=$rec['ppid']?>]" style="width: 20px; text-align: center;"
									 size="2" maxlength="2" value="<?=($rec['mlmark']>0) ? $rec['mlmark'] : ''?>"
									 onfocus="onFocus(this);" onblur="onBlur(this);"
									 autocomplete="off" />
					</td>
				</tr>
  		<?php } ?>
		</table>

		<input type="hidden" name="save" value="1" />
		<input type="button" value="Сохранить" onclick="onSave();" class="button" />
  </form>
</div>

<script>

  function onSave() {
    if(document.getElementById('mlhours').value.trim() == '' ||
       document.getElementById('mlhours').value.trim() == '0' ||
       isNaN(document.getElementById('mlhours').value)) {
      var popup = new sc2Popup();
      seterror('mlhours');
      popup.showMessage('Ошибка при добавлении оценок',
                        'Не указана недельная нагрузка в часах', 'Закрыть',
                        null, null);
      return;
    }

    document.getElementById('marksform').submit();
  }

	function onFocus(obj) {
		obj.parentNode.parentNode.style.backgroundColor = '#c9c9c9';
	}

	function onBlur(obj) {
		obj.parentNode.parentNode.style.backgroundColor = 'transparent';
	}

</script>

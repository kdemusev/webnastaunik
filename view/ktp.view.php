<div class="mainframe">

    <div class="subheader">Календарно-тематическое планирование</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php if(count($_days) > 0) { CTemplates::showMessage('auto', 'КТП составлено автоматически'); } ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

    <div class="transpform">
    <button onclick="onPrint();">Распечатать</button>
    </div>
    <br />

    <div id="toprint">
    	<div class="printable">
    		<center>Государственное учреждение образования "<?=$scname?>"</center>
    		<h1 align="center">Календарно-тематическое планирование по учебному предмету "<?=$sbname?>"<br>
        в <?=$fmname?> классе
    		</h1>
    	</div>

    <?php if(count($_days) > 0) { ?>
        <div class="largetables">
        <table width="100%" id="ktptable">
            <tr>
                <th>№ занятия:</th>
                <th nowrap>Дата</th>
                <th width="100%">Тема урока</th>
            </tr>
            <?php
            $tbln=1;
            foreach($_ktp as $rec) { ?>
            <tr>
              <td align="center"><?=$tbln++?> (<?=date('w', $rec['ktdate'])==1?'пн':(date('w', $rec['ktdate'])==2?'вт':(date('w', $rec['ktdate'])==3?'ср':(date('w', $rec['ktdate'])==4?'чт':(date('w', $rec['ktdate'])==5?'пт':'сб'))))?>)</td>
              <td><?=date('d.m.Y', $rec['ktdate'])?></td>
              <td
              <?php if($rec['ktcolor']==0) { ?>
              <?php } else if($rec['ktcolor']==1) { ?>
                                       style="color: red; font-weight: bold;"
              <?php } else if($rec['ktcolor']==2) { ?>
                                       style="color: green; font-style: italic;"
              <?php } else if($rec['ktcolor']==3) { ?>
                                       style="color: blue; font-weight: bold;"
              <?php } ?>
              ><?=$rec['kttopic']?></td>
            </tr>
            <?php } ?>
        </table>
        </div>
      <?php } else { ?>
      <div class="emptylist">Для выбранного класса и предмета не составлено расписание</div>
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

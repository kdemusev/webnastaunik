<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Список учащихся учреждения образования</div>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

    <div class="transpform">
    <button onclick="onPrint();">Распечатать</button>
    </div>


    <div id="toprint">
    	<div class="printable">
    		<center>Государственное учреждение образования "<?=$scname?>"</center>
    		<h1 align="center">Список учащихся <?php if($form_id > 0) { ?><?=$form?> класса<?php } ?></h1>
    	</div>

    <?php if(isset($pupils) && count($pupils) > 0) { ?>
      <table class="smptable overable">
        <thead>
          <tr>
            <th>№ п/п</th>
            <?php if($form_id == 0) { ?><th>Класс</th><?php } ?>
            <th>Фамилия, имя, отчество</th>
            <th>Пол</th>
            <th>Дата рождения</th>
            <th>Родители</th>
            <th>Домашний адрес, телефоны</th>
            <th>Группа здоровья, медицинская группа</th>
            <th>Примечания</th>
          </tr>
        </thead>
        <?php $i = 1; foreach($pupils as $rec) { ?>
          <tr>
            <td align="center"><?=$i++?></td>
            <?php if($form_id == 0) { ?><td align="center"><?=$rec['fmname']?></td><?php } ?>
            <td><?=$rec['ppname']?> <?=$rec['ppsurname']?></td>
            <td align="center"><?=$rec['ppsex']==0?'мужской':'женский'?></td>
            <td align="center"><?=date('d.m.Y', $rec['ppbirth'])?></td>
            <td><?=$rec['ppmother']?>, <?=$rec['ppmotherplace']?>, <?=$rec['ppfather']?>, <?=$rec['ppfatherplace']?></td>
            <td><?=$rec['ppaddress']?>, тел. учащегося: <?=$rec['ppphone']?>, тел. родителей: <?=$rec['ppmotherphone']?>, <?=$rec['ppfatherphone']?>, тел. дом.: <?=$rec['pphomephone']?></td>
            <td><?=$rec['pphealth']==1?'I':($rec['pphealth']==2?'II':($rec['pphealth']==3?'III':($rec['pphealth']==4?'IV':'V')))?>, <?=$rec['ppphyz']==1?'основная':($rec['ppphyz']==2?'подготовительная':'специальная медицинская')?></td>
            <td align="center"><?=$rec['ppnotes']?></td>
          </tr>
        <?php } ?>
      </table>
    <?php } else { ?>
      <div class="emptylist">В выбранный класс не добавлен ни один учащийся</div>
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

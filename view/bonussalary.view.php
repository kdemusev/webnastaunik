<div class="mainframe">

    <div class="subheader">Информация о премировании работников учреждения образования</div>

    <form class="transpform" name="formform" action="/bonussalary/view" method="post">
      <label>Период:</label>
  		<select name="bspdate">
        <?php $cycledate = mktime(0,0,0,1,1,2016); while($cycledate < $maxbspdate || $cycledate < strtotime('+1 month', mktime(0,0,0,date('n'),1,date('Y')))) { ?>
        <option value="<?=$cycledate?>" <?php if($cycledate == $bspdate) {?>selected<?php } ?>>за <?=CUtilities::text_month($cycledate)?> <?=date('Y', $cycledate)?></option>
        <?php $cycledate = strtotime('+1 month', $cycledate); } ?>
  		</select>
      <input type="submit" name="chooseperiod" value="Выбрать" />
    </form>

    <?php if(count($_edata) == 0) { ?>
      <div class="emptylist">Вам пока еще не присвоен статус работника учреждения образования. Обратитесь к администрации</div>
    <?php } else { ?>

      <?php foreach($_edata as $rece) { ?>
        <p>Работник <b><?=$rece['bsename']?></b> в должности <b><?=$rece['bseplace']?></b></p>
        <p>Премирование из основного фонда:<br />
        <?php if(count($_bsbdata[$rece['id']])>0) { ?>
          &nbsp;&nbsp;Согласно пунктам коллективного договора<br />
          <?php foreach($_bsbdata[$rece['id']] as $recb) { ?>
            &nbsp;&nbsp;&nbsp;&nbsp;<?=$recb['bsanumber']?> (<?=$recb['bsaname']?> <?php if($recb['bsafrom']>0) { ?>от <?=$recb['bsafrom']?> <?php } ?>до <?=$recb['bsato']?> б.в.) - <b><?=$recb['bsvalue']?></b> б.в.<br />
          <?php } ?>
            &nbsp;&nbsp;Итого с учетом округления: <b><?=$_bspaybonus[$rece['id']]?></b> бел. руб.
        <?php } else { ?>
            &nbsp;&nbsp;<b>Нет</b>
        <?php } ?>
        </p>

        <p>Премирование из фонда экономии:<br />
        <?php if(count($_bsedata[$rece['id']])>0) { ?>
          &nbsp;&nbsp;Согласно пунктам коллективного договора<br />
          <?php foreach($_bsedata[$rece['id']] as $recb) { ?>
            &nbsp;&nbsp;&nbsp;&nbsp;<?=$recb['bsanumber']?> (<?=$recb['bsaname']?> <?php if($recb['bsafrom']>0) { ?>от <?=$recb['bsafrom']?> <?php } ?>до <?=$recb['bsato']?> б.в.) - <b><?=$recb['bsvalue']?></b> б.в.<br />
          <?php } ?>
            &nbsp;&nbsp;Итого с учетом округления: <b><?=$_bspayeconomy[$rece['id']]?></b> бел. руб.
        <?php } else { ?>
            &nbsp;&nbsp;<b>Нет</b>
        <?php } ?>
        </p>

        <p>Надбавка за текущий месяц:<br />
        <?php if(count($_bsxdata[$rece['id']]) > 0) { ?>
          &nbsp;&nbsp;Процент надбавки: <b><?=$_bsxdata[$rece['id']][0]['bspepercent']?>%</b><br />
          &nbsp;&nbsp;Сумма надбавки с учетом округления: <b><?=$_bsxdata[$rece['id']][0]['bspesum']?></b> бел. руб.<br />
          &nbsp;&nbsp;Основание: <?=$_bsxdata[$rece['id']][0]['bsreason']?>
        <?php } else { ?>
            &nbsp;&nbsp;<b>Нет</b>
        <?php } ?>
        </p>

      <?php } ?>

    <?php } ?>

</div>

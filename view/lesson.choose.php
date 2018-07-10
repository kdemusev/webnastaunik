<div class="mainframe">

    <div class="subheader"><?=$forplan?'Выбор урока для планирования':'Выбор текущего урока'?></div>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

    <?php if(count($_ktp) > 0) { ?>
    <div class="largelist overable">
      <?php $ktnum = 1; foreach($_ktp as $rec) { ?>
      <div class="tr">
        <div class="col1" onclick="window.location='/lesson/confirm<?=$forplan?'plan':''?>/<?=$rec['id']?>';"><b><?=$ktnum++?></b></div>
        <div class="col3" style="left: 40px; text-align: left;" onclick="window.location='/lesson/confirm<?=$forplan?'plan':''?>/<?=$rec['id']?>';"><b><?=date('d.m.Y', $rec['ktdate'])?></b></div>
        <div class="col2" style="right: 0px; left: 140px;" onclick="window.location='/lesson/confirm<?=$forplan?'plan':''?>/<?=$rec['id']?>';">
          <b
          <?php if($rec['ktcolor']==0) { ?>
          <?php } else if($rec['ktcolor']==1) { ?>
                                   style="color: red; font-weight: bold;"
          <?php } else if($rec['ktcolor']==2) { ?>
                                   style="color: green; font-style: italic;"
          <?php } else if($rec['ktcolor']==3) { ?>
                                   style="color: blue; font-weight: bold;"
          <?php } ?>
          ><?=$rec['kttopic']?></b>
        </div>
      </div>
      <?php } ?>
    </div>
    <?php } else { ?>
    <div class="emptylist">
      Не составлено календарно-тематическое планирование для выбранного класса и предмета
    </div>
    <?php } ?>


</div>

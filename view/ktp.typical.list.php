<div class="mainframe">

    <div class="subheader">Типовые календарно-тематические планирования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php CTemplates::showMessage('deleted', 'Типовое КТП удалено'); ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

    <div class="topics">
      <?php if(count($data)) { foreach($data as $rec) { ?>
      <div class="topic">
        <div class="desc">
          <a href="/ktp/viewtypical/<?=$rec['kttiid']?>"><?=$rec['kttiform']?> класс <?=$rec['kttiname']?></a>
          <small>
            <?=$rec['tcname']?>, <?=$rec['scname']?> (<?=$rec['dtname']?>, <?=$rec['rgname']?>)
          </small>
        </div>
      </div>
      <?php } ?>
      <?php } else { ?>
        <div class="emptylist">В выбранном классе не найдено сохраненного календарно-тематического планирования</div>
      <?php } ?>
    </div>

</div>

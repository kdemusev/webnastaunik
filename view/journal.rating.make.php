<div class="mainframe">

    <div class="subheader">Создание рейтинговой системы</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

    <form method="post" action="/journal/makerating" name="copyfromform"
          class="transpform" >
      <input type="hidden" name="form_id" value="<?=$form_id?>" />
      <input type="hidden" name="subject_id" value="<?=$subject_id?>" />
      <label>Скопировать рейтинг</label>
      <select name="copyfrom_id" onchange="this.parentNode.submit();">
        <option value="0" selected disabled></option>
        <?php foreach($copyfrom as $rec) { ?>
        <option value="<?=$rec['subject_id']?>"><?=$rec['fmname']?> класс, <?=$rec['sbname']?></option>
        <?php } ?>
      </select>
    </form><br />

    <?php CTemplates::formList(array('','','Критерий', 'Отметка', ''),
                               array("subject_id" => $subject_id)); ?>


</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', '<?=isset($copied)?'new':''?>rcpriority');
  tl.addField('text', '<?=isset($copied)?'new':''?>rcname', '250px', 1);
  tl.addField('text', '<?=isset($copied)?'new':''?>rcrating', '50px');
  tl.addField('buttons');

  <?php foreach($rc as $rec) { ?>
    tl.addRecord({id: '<?=$rec['id']?>', <?=isset($copied)?'new':''?>rcname: '<?=$rec['rcname']?>', <?=isset($copied)?'new':''?>rcrating: '<?=$rec['rcrating']?>'});
  <?php } ?>

  tl.addEmpty();
</script>

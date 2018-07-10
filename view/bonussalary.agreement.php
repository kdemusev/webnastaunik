<div class="mainframe">

    <div class="subheader">Положение о премировании</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

<p>Количество базовых величин в соответствии с пунктами коллективного договора для премирования из основного фонда и фонда экономии.<br>
Обратите внимание, что поля "От" и "До" должны быть обязательно заполнены. При необходимости ввода фиксированного значения базовой величины
(например 2 базовых величины), необходимо в полях "От" и "До" ввести одно и то же значение.</p>

    <?php CTemplates::formList(array('','Пункт договора', 'Расшифровка', 'От', 'До', ''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('text', 'bsanumber', '70px', 1);
  tl.addField('text', 'bsaname', '800px');
  tl.addField('text', 'bsafrom', '25px');
  tl.addField('text', 'bsato', '25px');
  tl.addField('buttons');

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', bsanumber:new String('<?=$rec['bsanumber']?>'),
                  bsaname:new String('<?=$rec['bsaname']?>'),
                  bsafrom: new String('<?=$rec['bsafrom']?>'),
                  bsato: new String('<?=$rec['bsato']?>')});
  <?php } ?>

  tl.addEmpty();
</script>

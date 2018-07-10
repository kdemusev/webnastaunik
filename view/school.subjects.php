<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Предметы</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

    <form action="/school/subjects" method="post" class="transpform">
    <h2>Основные предметы</h2>
    <?php CTemplates::formList(array('', '', 'Название','Балл<br>трудности','Количество<br>часов', 'Учитель'),
                               array(), 'listformsubjects', 0, true); ?>

    <h2>Факультативные предметы</h2>
    <?php CTemplates::formList(array('', '', 'Название', 'Количество<br>  часов', 'Учитель'),
                               array(), 'listformelectives', 0, true); ?>
    <input type="hidden" name="form_id" value="<?=$form_id?>" />
    <input type="submit" name="save" value="Сохранить" />
    </form>

</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformsubjects');
  tl.addField('number');
  tl.addField('order', 'sbpriority');
  tl.addField('text', 'sbname', '250px', 1);
  tl.addField('text', 'sbrating', '50px');
  tl.addField('text', 'sbhours', '60px');
  tl.addField('select', 'teacher_id', '250px');
  tl.addField('buttons');

  var mgtypes = [];
  <?php foreach($_teachers as $rec) { ?>
    mgtypes.push({value:'<?=$rec['tcname']?>', id:'<?=$rec['id']?>'});
  <?php } ?>

  <?php foreach($_data as $rec) { if($rec['sbiselective']!='1') { ?>
    tl.addRecord({id: '<?=$rec['id']?>', sbname:'<?=$rec['sbname']?>',
                  sbrating: '<?=$rec['sbrating']?>', sbhours:'<?=$rec['sbhours']?>',
                  teacher_id: '<?=$rec['teacher_id']?>', list: mgtypes });
  <?php } } ?>

  tl.addEmpty({list: mgtypes});


  var tle = new sc2TableList('listformelectives');
  tle.addField('number');
  tle.addField('order', 'esbpriority');
  tle.addField('text', 'esbname', '250px', 1);
  tle.addField('text', 'esbhours', '60px');
  tle.addField('select', 'eteacher_id', '250px');
  tle.addField('buttons');

  <?php foreach($_data as $rec) { if($rec['sbiselective']=='1') {?>
    tle.addRecord({id: '<?=$rec['id']?>', esbname:'<?=$rec['sbname']?>',
                  esbrating: '<?=$rec['sbrating']?>', esbhours:'<?=$rec['sbhours']?>',
                  eteacher_id: '<?=$rec['teacher_id']?>', list: mgtypes });
  <?php } } ?>

  tle.addEmpty({list: mgtypes});
</script>

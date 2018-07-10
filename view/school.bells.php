<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Расписание звонков &quot;<?=$bellsgroup['bgname']?>&quot;</div>

    <form class="transpform">
    <input type="button" name="newtask" onclick="window.location='/school/bellsgroups';" value="Все расписания звонков" />
    <input type="button" name="newtask" onclick="window.location='/school/assignbells/<?=$bellsgroup['id']?>';" value="Применить к..." />
    </form>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php CTemplates::showMessage('assigned', 'Расписание применено к выбранным дням и классам. При внесении изменений в расписание, необходимо применить его заново'); ?>

    <?php CTemplates::formList(array('','','','','','','','','','',''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', 'blnumber');
  tl.addField('label', 'c');
  tl.addField('text', 'shour', '20px', 1);
  tl.addField('label', ':');
  tl.addField('text', 'smin', '20px');
  tl.addField('label', 'по');
  tl.addField('text', 'ehour', '20px');
  tl.addField('label', ':');
  tl.addField('text', 'emin', '20px');
  tl.addField('buttons');

  <?php foreach($bells as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', blnumber: '<?=$rec['blnumber']?>',
                  shour: '<?=$rec['shour']?>', smin: '<?=$rec['smin']?>',
                  ehour: '<?=$rec['ehour']?>', emin: '<?=$rec['emin']?>'});
  <?php } ?>

  tl.addEmpty();
</script>

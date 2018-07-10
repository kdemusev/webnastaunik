<div class="mainframe">

    <div class="subheader">Группы работников учреждения образования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::formList(array('','','Название группы', ''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', 'bsgpriority');
  tl.addField('text', 'bsgname', '250px', 1);
  tl.addField('buttons');

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', bsgname:'<?=$rec['bsgname']?>'});
  <?php } ?>

  tl.addEmpty();
</script>

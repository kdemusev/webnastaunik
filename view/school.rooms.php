<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Кабинеты для проведения занятий</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::formList(array('Номер кабинета','Название кабинета', ''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('text', 'rmnumber', '75px', 1);
  tl.addField('text', 'rmname', '250px');
  tl.addField('delbutton');

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', rmnumber: '<?=$rec['rmnumber']?>',
                  rmname: '<?=$rec['rmname']?>'});
  <?php } ?>

  tl.addEmpty();
</script>

<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Расписание звонков</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::formList(array('Название расписания', ''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('text', 'bgname', '250px', 1);
  tl.addField('deleditbuttons');

  <?php foreach($bellsgroups as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', bgname: '<?=$rec['bgname']?>', funcEdit: function() { window.location='/school/bells/<?=$rec['id']?>';}});
  <?php } ?>

  tl.addEmpty();
</script>

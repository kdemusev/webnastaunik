<div class="mainframe">
  <div class="subheader">Список предметов для БД олимпиад</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <?php CTemplates::formList(array('', 'Название'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'osname', '350px', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({osname: '<?=$rec['osname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

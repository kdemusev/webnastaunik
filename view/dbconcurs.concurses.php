<div class="mainframe">
  <div class="subheader">Список конкурсов для БД конкурсов</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <?php CTemplates::formList(array('', 'Название'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'ctname', '100%', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({ctname: '<?=$rec['ctname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

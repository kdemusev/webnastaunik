<div class="mainframe">
  <div class="subheader">Список классов для БД олимпиад и конкурсов</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <?php CTemplates::formList(array('', 'Класс'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'ofname', '350px', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({ofname: '<?=$rec['ofname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

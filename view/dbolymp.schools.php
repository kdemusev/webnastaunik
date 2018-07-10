<div class="mainframe">
  <div class="subheader">Учреждения образования для БД олимпиад и конкурсов</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <form class="transpform">
    <label><?=$districtdata['dtname']?>:</label>
  </form>

  <?php CTemplates::formList(array('', 'Название учреждения'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'oscname', '350px', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({oscname: '<?=$rec['oscname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

<div class="mainframe">
  <div class="subheader">Список секций конкурсов для БД конкурсов</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <form class="transpform" name="formform"
        action="/dbconcurs/sectionsdb" method="post">

    <label>Конкурс:</label>
    <select name="setconcurs_type_id" onchange="document.formform.submit();">
    <?php foreach($concurses as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['concurs_type_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['ctname']?></option>
    <?php } ?>
    </select>

  </form>

  <?php CTemplates::formList(array('', 'Название'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'csname', '100%', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({csname: '<?=$rec['csname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

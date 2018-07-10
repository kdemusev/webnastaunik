<div class="mainframe">
  <div class="subheader">Список учащихся для БД олимпиад и конкурсов</div>

  <?php CTemplates::showMessage('changed', 'Изменения сохранены'); ?>

  <form class="transpform" name="formform"
        action="/dbolymp/pupilsdb" method="post">

    <label>Учреждения образования:</label>
    <select name="setolymp_school_id" onchange="document.formform.submit();">
    <?php foreach($schools as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_school_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['oscname']?></option>
    <?php } ?>
    </select>

  </form>

  <?php CTemplates::formList(array('', 'Фамилия, имя, отчество'),
                             array(),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'opname', '350px', 1);

  <?php foreach($data as $rec) { ?>
    tlus.addRecord({opname: '<?=$rec['opname']?>', id: '<?=$rec['id']?>'});
  <?php } ?>

</script>

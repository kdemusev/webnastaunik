<div class="mainframe">
  <div class="subheader">Настройки анализа качества образования</div>

  <?php CTemplates::showMessage('saved', 'Изменения сохранены'); ?>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/analysis/setgroups';" value="Четверти" />
  <input type="button" name="newtask" onclick="window.location='/analysis/setsubjects/';" value="Предметы" class="checked" />
  </form>

<h2>Настройка предметов</h2>

<?php CTemplates::formList(array('','','','',''),
                           array(),
                           "listformtablesubjects", true, false); ?>

</div>
<script src="/js/sc2tablelist.js"></script>
<script>
var tl = new sc2TableList('listformtablesubjects');
tl.addField('number');
tl.addField('order', 'mspriority');
tl.addField('text', 'msname', '200px', 1);
tl.addField('select', 'tcht_id', '200px');
tl.addField('buttons');

var tcht_ids = [];
<?php foreach($specializations as $rec) { ?>
tcht_ids.push({value:'<?=$rec['spname']?>', id:'<?=$rec['id']?>'});
<?php } ?>

<?php if(isset($mlsubjects)) { foreach($mlsubjects as $rec) { ?>
tl.addRecord({id: '<?=$rec['id']?>',
               msname: new String('<?=$rec['msname']?>'),
               tcht_id: new String('<?=$rec['tcht_id']?>'),
               list: tcht_ids});
<?php } } ?>
tl.addEmpty({list: tcht_ids});

</script>

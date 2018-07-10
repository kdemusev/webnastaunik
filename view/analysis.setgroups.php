<div class="mainframe">
  <div class="subheader">Настройки анализа качества образования</div>

  <?php CTemplates::showMessage('saved', 'Изменения сохранены'); ?>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/analysis/setgroups';" value="Четверти" class="checked"/>
  <input type="button" name="newtask" onclick="window.location='/analysis/setsubjects/';" value="Предметы" />
  </form>

<h2>Настройка четвертей</h2>

<?php CTemplates::formList(array('','','','',''),
                           array(),
                           "listformtablegroups", true, false); ?>

</div>
<script src="/js/sc2tablelist.js"></script>
<script>
var tl = new sc2TableList('listformtablegroups');
tl.addField('number');
tl.addField('order', 'mgpriority');
tl.addField('text', 'mgname', '200px', 1);
tl.addField('select', 'mgtype', '200px');
tl.addField('buttons');

var mgtypes = [];
mgtypes.push({value:'четвертной период', id:'0'});
mgtypes.push({value:'годовой период', id:'1'});

<?php if(isset($mlgroups)) { foreach($mlgroups as $rec) { ?>
tl.addRecord({id: '<?=$rec['id']?>',
               mgname: new String('<?=$rec['mgname']?>'),
               mgtype: new String('<?=$rec['mgtype']?>'),
               list: mgtypes});
<?php } } ?>
tl.addEmpty({list: mgtypes});

</script>

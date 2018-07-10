<div class="mainframe">

    <div class="subheader">Работники учреждения образования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::chooseBar('Группы работников', 'bsgroup_id', $_bsgroups, $bsgroup_id, 'bsgname'); ?>

    <?php CTemplates::formList(array('','','Фамилия, имя, отчество', 'Должность', 'Привязать пользователя', ''),
                               array('bsgroup_id' => $bsgroup_id)); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', 'bsepriority');
  tl.addField('text', 'bsename', '250px', 1);
  tl.addField('text', 'bseplace', '125px');
  tl.addField('select', 'bseuser_id', '250px');
  tl.addField('buttons');

  var mgtypes = [];
  mgtypes.push({value:'',id:'0'});
  <?php foreach($_users as $rec) { ?>
    mgtypes.push({value:'<?=$rec['usname']?>', id:'<?=$rec['id']?>'});
  <?php } ?>

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>',
                  bsename:'<?=$rec['bsename']?>',
                  bseplace: '<?=$rec['bseplace']?>',
                  bseuser_id: '<?=$rec['user_id']?>', list: mgtypes });
  <?php } ?>

  tl.addEmpty({list: mgtypes});
</script>

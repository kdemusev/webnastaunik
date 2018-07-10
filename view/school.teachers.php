<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Педагоги учреждения образования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::formList(array('','','Фамилия, имя, отчество', 'Пользователь',''),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', 'tcpriority');
  tl.addField('text', 'tcname', '250px', 1);
  tl.addField('select', 'user_id', '250px');
  tl.addField('buttons');

  var mgtypes = [];
  <?php foreach($_users as $rec) { ?>
    mgtypes.push({value:'<?=$rec['usname']?>', id:'<?=$rec['id']?>'});
  <?php } ?>

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['tc_id']?>', tcname:'<?=$rec['tcname']?>',
                  user_id: '<?=$rec['user_id']?>', list: mgtypes });
  <?php } ?>

  tl.addEmpty({list: mgtypes});
</script>

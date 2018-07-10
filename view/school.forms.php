<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Классы учреждения образования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::formList(array('Название класса'),
                               array()); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformtable');
  tl.addField('text', 'fmname', '75px', 1);
  tl.addField('delbutton');

  <?php foreach($_data as $rec) {?>
    tl.addRecord({id: '<?=$rec['id']?>', fmname: '<?=$rec['fmname']?>'});
  <?php } ?>

  tl.addEmpty();

</script>

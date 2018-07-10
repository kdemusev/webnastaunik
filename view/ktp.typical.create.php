<div class="mainframe">

    <div class="subheader">Создание типового календарно-тематического планирования</div>

    <?php CTemplates::showAlert('Просмотрите планирование, при необходимости внесите изменения и сохраните. После сохранения изменений данное типовое планирование будет доступно всей педагогической общественности'); ?>

    <form action="/ktp/createtypical" method="post" class="transpform">
      <label>Предмет: </label>
      <input type="text" name="kttiname" value="<?=$sbname?>" />

      <label>Класс: </label>
      <select name="kttiform">
        <?php for($i = 1; $i <= 11; $i++) { ?>
        <option value="<?=$i?>" <?php if($fmname==$i) { ?>selected<?php } ?>><?=$i?></option>
        <?php } ?>
      </select>

      <label>Описание: </label>
      <textarea name="kttidesc"></textarea>

      <?php CTemplates::formList(array('<center>№<br>п/п</center>', 'Тема', 'Тип урока', ''),
                                 array(), 'listformktp', 0, true); ?>

      <input type="submit" name="save" value="Сохранить" />
    </form>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformktp');
  tl.addField('number');
  tl.addField('text', 'kttopic', '550px', 1);
  tl.addField('select', 'ktcolor', '200px');
  tl.addField('buttons');

  var lstypes = [];
  lstypes.push({value:'Обычный урок', id:'0'});
  lstypes.push({value:'Контрольная работа', id:'1'});
  lstypes.push({value:'Практическая работа', id:'2'});
  lstypes.push({value:'Обобщение материала', id:'3'});
  lstypes.push({value:'Самостоятельная работа', id:'4'});
  lstypes.push({value:'Закрепление материала', id:'5'});

  <?php foreach($_ktp as $rec) { ?>
    tl.addRecord({id: '<?=$rec['id']?>', kttopic:'<?=$rec['kttopic']?>',
                  ktcolor: '<?=$rec['ktcolor']?>', list: lstypes});
  <?php } ?>

  tl.addEmpty({list: lstypes});

</script>

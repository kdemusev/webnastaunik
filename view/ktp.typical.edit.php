<div class="mainframe">

    <div class="subheader">Создание типового календарно-тематического планирования</div>

    <form action="/ktp/edittypical/<?=$info[0]['id']?>" method="post" class="transpform">
      <label>Предмет: </label>
      <input type="text" name="kttiname" value="<?=$info[0]['kttiname']?>" />

      <label>Класс: </label>
      <input type="text" name="kttiform" value="<?=$info[0]['kttiform']?>" />

      <label>Описание: </label>
      <textarea name="kttidesc"><?=$info[0]['kttidesc']?></textarea>

      <?php CTemplates::formList(array('<center>№<br>п/п</center>', '', 'Тема', 'Тип урока', ''),
                                 array(), 'listformktp', 0, true); ?>

      <input type="submit" name="save" value="Сохранить" />
      <input type="button" onclick="back();" value="Вернуться без сохранения" />
    </form>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tl = new sc2TableList('listformktp');
  tl.addField('number');
  tl.addField('order', 'kttnumber');
  tl.addField('text', 'ktttopic', '550px', 1);
  tl.addField('select', 'kttcolor', '200px');
  tl.addField('buttons');

  var lstypes = [];
  lstypes.push({value:'Обычный урок', id:'0'});
  lstypes.push({value:'Контрольная работа', id:'1'});
  lstypes.push({value:'Практическая работа', id:'2'});
  lstypes.push({value:'Обобщение материала', id:'3'});
  lstypes.push({value:'Самостоятельная работа', id:'4'});
  lstypes.push({value:'Закрепление материала', id:'5'});

  <?php foreach($_ktp as $rec) { ?>
    tl.addRecord({id: '<?=$rec['id']?>', ktttopic:'<?=$rec['ktttopic']?>',
                  kttcolor: '<?=$rec['kttcolor']?>', list: lstypes});
  <?php } ?>

  tl.addEmpty({list: lstypes});


  function back() {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение выхода', 'Вы действительно хотите выйти без сохранения новых данных?',
                      'Нет', 'Да', function() { onBack(); });
  }

  function onBack() {
    window.location='/ktp/viewtypical/<?=$info[0]['id']?>';
  }

</script>

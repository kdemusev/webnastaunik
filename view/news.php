<div class="mainframe">
  <div class="subheader">Новости</div>

  <?php CTemplates::showMessage('added', 'Новость добавлена'); ?>
  <?php CTemplates::showMessage('deleted', 'Новость удалена'); ?>

<?php if($user_rights >= 88) { ?>
  <div class="transpform">
    <label>Добавить новость</label>
    <input type="text" id="nsnameinput" name="tsname" placeholder="Введите заголовок новостей" />
    <input type="checkbox" id="cbShowMore" class="showmorecheckbox"><label for="cbShowMore" class="showmore">+</label>
    <div class="showmorediv">
      <label>Уровень новостей</label>
      <input type="radio" id="rType1" class="customRadioButton" name='nl' value="0" checked="checked" />
      <label for="rType1" class="button">Районная</label>
      <?php if($user_rights >= 89) { ?>
      <input type="radio" id="rType2"  class="customRadioButton" name='nl' value="1" />
      <label for="rType2" class="button">Областная</label>
      <?php if($user_rights >= 99) { ?>
      <input type="radio" id="rType3"  class="customRadioButton" name='nl' value="2" />
      <label for="rType3" class="button">Портала</label>
      <?php } ?>
      <?php } ?>

      <hr />

      <label>Заглавное изображение:</label>
      <input type="file" name="mainimage" id="mainimage"/>
      <hr />

      <label>Текст новостей</label>
      <div class="answerbox">
      <div class="forinput" id="forinput">
      <?php CTemplates::sc2editor('/content/addnews',
                                  array("nstype" => '',
                                  "nsname" => ''),
                                  'Сохранить'); ?>
      </div>
    </div>
    </div>
  </div>
<?php } ?>

<div class="newsbox">
  <?php foreach($news as $rec) { ?>
  <div class="newstile" onclick="window.location='/content/shownews/<?=$rec['id']?>';">
    <img src="/newsfiles/sm/<?=$rec['id']?>" onerror="this.style.visibility='hidden';">
    <h5><?=$rec['nsname']?></h5>
    <!--<p><?=$rec['nstext']?></p>-->
    <div class="dimmer"></div>
    <small>
   <div style="float: right;"><?=CUtilities::date_like_gmail($rec['nstime'])?></div>
   <?php if($rec['nstype']==0) { ?>
    <span class="marker" style="background-color: rgb(67, 108, 33);"></span> районная новость
   <?php } else if($rec['nstype']==1) { ?>
    <span class="marker" style="background-color: rgb(228, 120, 86);"></span> областная новость
   <?php } else { ?>
    <span class="marker" style="background-color: grey;"></span> новость портала
   <?php } ?>
    </small>
    <div class="divider"></div>
  </div>

      <!--<img src="/style/icons/admin.delete.png" class="adminbutton" title="удалить"
            onclick="deleteNews('<?=$rec['id']?>');"
            style="float: right;"/>
      <img src="/style/icons/admin.edit.png" class="adminbutton" title="изменить"
            onclick="window.location='/content/editnews/<?=$rec['id']?>';"
            style="float: right;"/>  -->
  <?php } ?>
  <br style="clear: both;" />
  <div class="hider"></div>
</div>

</div>

<?php if($user_rights >= 99) { ?>
<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2editor.js"></script>
<script>
  function onDeleteNews(news_id) {
    window.location = '/content/delnews/'+news_id;
  }

  function deleteNews(news_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление новости', 'Вы действительно хотите удалить новость?',
                      'Нет', 'Да', function() { onDeleteNews(news_id); });
  }

  function onSave() {
    if(document.getElementById('nsnameinput').value.trim() == '') {
      seterror('nsnameinput');
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при создании новости', 'Не указан заголовок новости', 'Закрыть', null, null);
      return false;
    }

    document.getElementsByName('nsname')[0].value = document.getElementById('nsnameinput').value
    document.getElementsByName('nstype')[0].value = document.getElementById('rType1').checked ?
                                                    0 : document.getElementById('rType2').checked ?
                                                    1 : 2;
    document.getElementById('filesbox').appendChild(document.getElementById('mainimage'));
    return true;
  }


  document.getElementById('forinput').appendChild(g_sc2Editor);
  var sc2ed = new sc2Editor('sc2editor');
  sc2ed.initEditor(null, onSave);
  var tlfl = new sc2TableList('listformtablefiles');
  tlfl.addField('file', 'postFile', '300px', 1);
  tlfl.addField('delbutton');

  tlfl.addEmpty({value: 'Выберите файл'});

</script>
<?php } ?>

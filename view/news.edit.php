<div class="mainframe">
  <div class="subheader">Изменение новости</div>

  <div class="transpform">
    <label>Заголовок новостей</label>
    <input type="text" id="nsnameinput" name="tsname" value="<?=$news['nsname']?>" />
    <label>Уровень новостей</label>
    <input type="radio" id="rType1" class="customRadioButton" name='nl' value="0"
           <?php if($news['nstype']==0) { print 'checked="checked"'; } ?> />
    <label for="rType1" class="button">Районная</label>
    <?php if($user_rights >= 89) { ?>
    <input type="radio" id="rType2"  class="customRadioButton" name='nl' value="1"
           <?php if($news['nstype']==1) { print 'checked="checked"'; } ?> />
    <label for="rType2" class="button">Областная</label>
    <?php if($user_rights >= 99) { ?>
    <input type="radio" id="rType3"  class="customRadioButton" name='nl' value="2"
           <?php if($news['nstype']==2) { print 'checked="checked"'; } ?> />
    <label for="rType3" class="button">Портала</label>
    <?php } ?>
    <?php } ?>
    <hr />

    <label>Заменить заглавное изображение:</label>
    <img src="/newsfiles/sm/<?=$news['id']?>" onerror="this.style.display='none';" />
    <input type="file" name="mainimage" id="mainimage"/>
    <hr />

    <label>Текст новостей</label>
    <div class="answerbox">
      <div class="forinput" id="forinput">
      <?php CTemplates::sc2editor('/content/savenews',
                                  array("news_id" => $news['id'],
                                  "nstype" => '',
                                  "nsname" => ''),
                                  'Сохранить'); ?>
      </div>
    </div>
  </div>
</div>

<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2editor.js"></script>
<script>
  function onSave() {
    if(document.getElementById('nsnameinput').value.trim() == '') {
      seterror('nsnameinput');
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при изменении новости', 'Не указан заголовок новости', 'Закрыть', null, null);
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
  document.getElementById('sc2editorRich').innerHTML = '<?=str_replace('\'', '\\\'', str_replace('\\', '\\\\', $news['nstext']))?>';
  var tlfl = new sc2TableList('listformtablefiles');
  tlfl.addField('file', 'postFile', '300px', 1);
  tlfl.addField('delbutton');

  tlfl.addEmpty({value: 'Выберите файл'});

</script>

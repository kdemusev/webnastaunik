<div class="mainframe">
  <div class="subheader">Изменить сообщение в методическом диалоге &quot;<?=$mbdata['mbname']?>&quot;</div>

  <div class="answerbox">
    <div class="author">

      <?php if($data['mbdanonym']==0) { ?>
      <b id="authorName"><?=$data['usname']?></b><br>
      <?php } else { ?>
      <b id="authorName">Анонимный режим</b><br>
      <?php } ?>
<!--      <a id="authorNameToggle" href="#" onclick="toggleAuthor(event);">
        <?php if($data['mbdanonym']==0) { ?>
        Перейти в анонимный режим
        <?php } else { ?>
        Выйти из анонимного режима
        <?php } ?>
      </a>
    -->
      <span style="display: none;" id="authorType"><?=$data['mbdanonym']?></span>

    </div>
    <div class="forinput">
      <textarea id="methodblogDialogTextInput"><?=$data['mbdtext']?></textarea>
    </div>
  </div>


</div>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor('/methodblog/changemessage',
                            array("methodblog_id" => $mbdata['id'],
                            "id" => $data['mbd_id'],
                            "isanonym" => '0'),
                            'Изменить'); ?>

<script src="/js/sc2editor.js"></script>
<script>
  function onSave() {
    if(document.getElementById('sc2editorRich').innerHTML.trim().replace(/<\/?[^>]+>/gi, '') == '') {
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при изменении сообщения', 'Отсутствует текст сообщения', 'Закрыть', null, null);
      return false;
    }

    document.getElementsByName('isanonym')[0].value = document.getElementById('authorType').innerHTML;
    return true;
  }

  function back() {
    window.location = '/methodblog/dialog/<?=$mbdata['id']?>';
  }

  function replaceEditor() {
    var obj = document.getElementById('methodblogDialogTextInput');
    var data = obj.value;
    obj.parentNode.replaceChild(g_sc2Editor, obj);
    var sc2ed = new sc2Editor('sc2editor');
    sc2ed.initEditor(null, onSave);
    sc2editorRich.innerHTML = data;

    var tlfl = new sc2TableList('listformtablefiles');
    tlfl.addField('file', 'postFile', '300px', 1);
    tlfl.addField('delbutton');

    tlfl.addEmpty({value: 'Выберите файл'});
  }

  replaceEditor();

  function toggleAuthor(e) {
    var isanonym = document.getElementById('authorType');
    if(isanonym.innerHTML == 0) {
      document.getElementById('authorName').innerHTML = "Анонимный режим";
      document.getElementById('authorNameToggle').innerHTML = "Выйти из анонимного режима";
      isanonym.innerHTML = 1;
    } else {
      document.getElementById('authorName').innerHTML = "<?=$data['usname']?>";
      document.getElementById('authorNameToggle').innerHTML = "Перейти в анонимный режим";
      isanonym.innerHTML = 0;
    }
    e.preventDefault();
  }


</script>

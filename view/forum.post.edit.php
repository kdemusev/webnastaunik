<div class="mainframe">
  <div class="subheader">Изменение сообщения</div>

  <div class="answerbox">
    <div class="author">
      <b id="authorName"><?=$fmpost['usname']?></b><br>
      <a id="authorNameToggle" href="#" onclick="toggleAuthor(event);">Перейти в анонимный режим</a>
      <span style="display: none;" id="authorType">0</span>
    </div>
    <div class="forinput" id="forinput">
      <?php CTemplates::sc2editor('/forum/savepost',
                                  array("fmpost_id" => $fmpost['fmpost_id'],
                                  "isanonym" => $fmpost['fmptanonym']),
                                  'Сохранить изменения'); ?>
    </div>
  </div>

</div>
<script src="/js/sc2tablelist.js"></script>
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

  document.getElementById('forinput').appendChild(g_sc2Editor);
  var sc2ed = new sc2Editor('sc2editor');
  sc2ed.initEditor(null, onSave);
  var tlfl = new sc2TableList('listformtablefiles');
  tlfl.addField('file', 'postFile', '300px', 1);
  tlfl.addField('delbutton');

  <?php foreach($fmfiles as $rec) { ?>
  tlfl.addRecord({postFile: '<?=$rec['fmflsource']?>', id: '<?=$rec['id']?>'})
  <?php } ?>

  <?php if(count($fmfiles) > 0) { ?>
  document.getElementById('sc2editor_filepanel').style.display = 'block';
  <?php } ?>

  tlfl.addEmpty({value: 'Выберите файл'});


  document.getElementById('sc2editorRich').innerHTML = '<?=str_replace("\r", "", str_replace("\n", " ", s_q($fmpost['fmtext'])))?>';
  document.getElementById('sc2editorRich').focus();

  function toggleAuthor(e) {
    var isanonym = document.getElementById('authorType');
    if(isanonym.innerHTML == 0) {
      document.getElementById('authorName').innerHTML = "Анонимный режим";
      document.getElementById('authorNameToggle').innerHTML = "Выйти из анонимного режима";
      isanonym.innerHTML = 1;
    } else {
      document.getElementById('authorName').innerHTML = "<?=$fmpost['usname']?>";
      document.getElementById('authorNameToggle').innerHTML = "Перейти в анонимный режим";
      isanonym.innerHTML = 0;
    }
    if(e) {
      e.preventDefault();
    }
  }

  <?php if($fmpost['fmptanonym']==1) { ?>
  toggleAuthor();
  <?php } ?>
</script>

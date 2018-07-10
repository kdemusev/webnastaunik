<div class="mainframe">

  <div class="subheader">Педагогический форум &quot;<?=$fmsection['fmscname']?>&quot;</div>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/forum';" value="Форум" />
  </form>

  <?php CTemplates::showMessage('added', 'Сообщение добавлено'); ?>
  <?php CTemplates::showMessage('topicdeleted', 'Тема удалена'); ?>

  <div class="answerbox">
    <div class="author">
      <b id="authorName"><?=$usinfo[0]['usname']?></b><br>
      <a id="authorNameToggle" href="#" onclick="toggleAuthor(event);">Перейти в анонимный режим</a>
      <span style="display: none;" id="authorType">0</span>
    </div>
    <div class="forinput">
      <input type="text" class="fakeEdit" id="fmtpnameId"
             value="Задать новый вопрос" onfocus="replaceEditor(this);" />
    </div>
  </div>

<?php if(count($fmtopics) > 0) { ?>
<div class="topics">
<?php foreach($fmtopics as $rec) { ?>
<div class="topic">
  <div class="desc">
    <a href="/forum/posts/<?=$rec['fmtopic_id']?>"><?=$rec['fmtpname']?></a>
    <?=$rec['fmtpdesc']?>
    <br>
    <small>
      <?php if($rec['fmtpanonym']==1) { print 'Анонимный вопрос'; } else { print $rec['usname']; } ?>
    </small>
  </div>
  <div class="info">
    <div class="num"><?=$rec['fmtpposts']?></div>
    ответа
  </div>
  <div class="info2">
    <div class="num"><?=$rec['fmtpviews']?></div>
    просмотров
  </div>
  <div class="when">
    <span style="font-size: 12px;">последнее сообщение</span><br><?=CUtilities::date_like_gmail($rec['fmtplast'])?>
  </div>
</div>
<?php } ?>
</div>
<?php } else { ?>
  <div class="emptylist">В данной секции педагогического форума не задано ни одного вопроса</div>
<?php } ?>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor('/forum/newtopic',
                              array("fmsection_id" => $fmsection['id'],
                              "fmtpname" => 'null',
                              "isanonym" => '0'),
                              'Задать вопрос'); ?>

</div>


<script src="/js/sc2editor.js"></script>
<script>
  function onSave() {
    if(document.getElementById('fmtpnameId').value.trim() == '') {
      seterror('fmtpnameId');
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при создании нового вопроса', 'Не указана тема сообщения', 'Закрыть', null, null);
      return false;
    }

    document.getElementsByName('fmtpname')[0].value = document.getElementById('fmtpnameId').value;
    document.getElementsByName('isanonym')[0].value = document.getElementById('authorType').innerHTML;
    return true;
  }

  function replaceEditor(obj) {
    obj.value='';
    obj.style.color = 'black';
    obj.onfocus = function() { };
    obj.parentNode.appendChild(document.createElement('br'));
    obj.parentNode.appendChild(g_sc2Editor);


    var sc2ed = new sc2Editor('sc2editor');
    sc2ed.initEditor(null, onSave);
    //document.getElementById('sc2editorRich').focus();

    var tlfl = new sc2TableList('listformtablefiles');
    tlfl.addField('file', 'postFile', '300px', 1);
    tlfl.addField('delbutton');

    tlfl.addEmpty({value: 'Выберите файл'});
  }

  function toggleAuthor(e) {
    var isanonym = document.getElementById('authorType');
    if(isanonym.innerHTML == 0) {
      document.getElementById('authorName').innerHTML = "Анонимный режим";
      document.getElementById('authorNameToggle').innerHTML = "Выйти из анонимного режима";
      isanonym.innerHTML = 1;
    } else {
      document.getElementById('authorName').innerHTML = "<?=$usinfo[0]['usname']?>";
      document.getElementById('authorNameToggle').innerHTML = "Перейти в анонимный режим";
      isanonym.innerHTML = 0;
    }
    e.preventDefault();
  }
</script>

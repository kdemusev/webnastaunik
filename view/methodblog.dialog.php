<div class="mainframe">
  <div class="subheader">Методический диалог &quot;<?=$mbdata['mbname']?>&quot;</div>

  <?php CTemplates::showMessage('messageadded', 'Cooбщение добавлено'); ?>
  <?php CTemplates::showMessage('messagedeleted', 'Сообщение удалено'); ?>
  <?php CTemplates::showMessage('messagechanged', 'Сообщение изменено'); ?>

<?php if(count($usinfo)) { ?>
<form class="transpform">
  <input type="button" onclick="window.location='/methodblog/show/<?=$mbdata['id']?>';" value="Новости и объявления" />
  <input type="button" onclick="window.location='/methodblog/dialog/<?=$mbdata['id']?>';" value="Методический диалог" class="checked" />
  <?php if($blog_owner || $blog_author) { ?>
    <input type="button" onclick="window.location='/methodblog/addnews/<?=$mbdata['id']?>';" value="Добавить новость" />
  <?php } ?>
  <?php if($blog_owner) { ?>
    <input type="button" onclick="window.location='/methodblog/settings/<?=$mbdata['id']?>';" value="Настройки" />
  <?php } ?>
</form>
<?php } ?>

<div class="answerbox">
  <div class="author">
    <?php if(count($usinfo)) { ?>
      <b id="authorName"><?=$usinfo[0]['usname']?></b><br>
      <a id="authorNameToggle" href="#" onclick="toggleAuthor(event);">Перейти в анонимный режим</a>
      <span style="display: none;" id="authorType">0</span>
      <input type="hidden" name="usfreenametmp" id="freename" value="" />
    <?php } else { ?>
      <input type="text" name="usfreenametmp" id="freename" class="fakeEdit" placeholder="Введите ваше имя или оставьте поле пустым" style="color: black;" value="" />
      <span style="display: none;" id="authorType">0</span>
    <?php } ?>
  </div>
  <div class="forinput">
    <input type="text" class="fakeEdit" value="Задать вопрос либо написать ответ" onfocus="replaceEditor(this);" />
  </div>
</div>

<?php if(count($data) > 0) { ?>
<?php foreach($data as $rec) { ?>
<div class="post">
  <div class="author">

<?php if($blog_owner || $blog_author) { ?>
      <button class="adminbuttonnew" onclick="deleteMBMessage(<?=$rec['mbd_id']?>)">Удалить</button>
      <button class="adminbuttonnew" onclick="window.location='/methodblog/editmessage/<?=$rec['mbd_id']?>';">Изменить</button>
<?php } ?>

      <?php if($rec['mbdanonym']==0) { ?>
        <?php if($rec['user_id']>0) { ?>
      <b><?=$rec['usname']?></b>, <?=$rec['usplace']?> (<?=$rec['scname']?>)<br>
        <?php } else { ?>
          <?php if($rec['mbdusername'] == '') { ?>
              <b>Анонимное сообщение</b><br>
          <?php } else { ?>
            <b><?=$rec['mbdusername']?></b><br>
          <?php } ?>
        <?php } ?>
    <?php } else { ?>
      <b>Анонимное сообщение</b><br>
      <?php } ?>
       <small><?=CUtilities::date_like_gmail($rec['mbdtime'])?></small>

  </div>
  <?=$rec['mbdtext']?>
  <?php if(isset($wbfiles[$rec['mbd_id']])) { ?>
  <h4>Прикрепленные файлы:</h4>
  <?php foreach($wbfiles[$rec['wid']] as $recfile) { ?>
    <a href="/webinar/file/<?=$recfile['id']?>"><?=$recfile['wbflsource']?></a><br>
  <?php } } ?>

</div>
<?php } ?>
<?php } else { ?>
  <div class="emptylist">
    Сообщений в текущем диалоге нет
  </div>
<?php } ?>

</div>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor('/methodblog/addmessage',
                              array("methodblog_id" => $mbdata['id'],
                              "isanonym" => '0',
                              "usfreename" => ''),
                              'Сохранить и добавить'); ?>

<script src="/js/sc2editor.js"></script>
<script>

  function onSave() {
    if(document.getElementById('sc2editorRich').innerHTML.trim().replace(/<\/?[^>]+>/gi, '') == '') {
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при добавлении сообщения', 'Отсутствует текст сообщения', 'Закрыть', null, null);
      return false;
    }

    document.getElementsByName('isanonym')[0].value = document.getElementById('authorType').innerHTML;
    document.getElementsByName('usfreename')[0].value = document.getElementById('freename').value;
    return true;
  }

  function replaceEditor(obj) {
    obj.parentNode.replaceChild(g_sc2Editor, obj);
    var sc2ed = new sc2Editor('sc2editor');
    sc2ed.initEditor(null, onSave);
    document.getElementById('sc2editorRich').focus();
    var tlfl = new sc2TableList('listformtablefiles');
    tlfl.addField('file', 'postFile', '300px', 1);
    tlfl.addField('delbutton');

    tlfl.addEmpty({value: 'Выберите файл'});
  }
</script>


<script>

function deleteMBMessage(mbdid) {
  var popup = new sc2Popup();
  popup.showMessage('Удаление сообщения из методического диалога', 'Вы действительно хотите удалить сообщение из методического диалога?',
                    'Нет', 'Да', function() { window.location = '/methodblog/deletemessage/'+mbdid; });
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

<div class="mainframe">

  <div class="subheader">Тема форума &quot;<?=$fmtopic[0]['fmtpname']?>&quot;</div>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/forum';" value="Форум" />
  <input type="button" name="newtask" onclick="window.location='/forum/topics/<?=$fmsection[0]['id']?>';" value="<?=$fmsection[0]['fmscname']?>" />
  </form>

  <?php CTemplates::showMessage('added', 'Сообщение добавлено'); ?>
  <?php CTemplates::showMessage('postdeleted', 'Сообщение удалено'); ?>
  <?php CTemplates::showMessage('topicedited', 'Тема изменена'); ?>
  <?php CTemplates::showMessage('postedited', 'Сообщение изменено'); ?>

  <div class="post">
    <div class="author">

<?php if($user_rights >= 99 || $fmtopic[0]['user_id'] == $user_id) { ?>
      <img src="/style/icons/admin.edit.png" class="adminbutton" title="изменить"
            onclick="window.location='/forum/edittopic/<?=$fmtopic[0]['fmtopic_id']?>';"
            style="float: right;"/>
      <img src="/style/icons/admin.delete.png" class="adminbutton" title="удалить"
            onclick="deleteTopic();"
            style="float: right;"/>
<?php } ?>

      <b><?php if($fmtopic[0]['fmtpanonym']==1) { print 'Анонимный вопрос</b>'; }
      else { print $fmtopic[0]['usname'].'</b>, '.$fmtopic[0]['usplace'].' ('.$fmtopic[0]['scname'].')'; } ?> <br>

       <small><?=date('d.m.y G:i', $fmtopic[0]['fmtptime'])?></small>
       <?php if(isset($ftfiles) && count($ftfiles) > 0) { ?>
       <h4>Прикрепленные файлы:</h4>
       <?php foreach($ftfiles as $recfile) { ?>
         <a href="/forum/filetopic/<?=$recfile['id']?>"><?=$recfile['fmflsource']?></a><br>
       <?php } } ?>
    </div>
    <?=$fmtopic[0]['fmtpdesc']?>
  </div><br>

  <div class="answerbox">
    <div class="author">
      <b id="authorName"><?=$usinfo[0]['usname']?></b><br>
      <a id="authorNameToggle" href="#" onclick="toggleAuthor(event);">Перейти в анонимный режим</a>
      <span style="display: none;" id="authorType">0</span>
    </div>
    <div class="forinput">
      <input type="text" class="fakeEdit" value="Добавление сообщения" onfocus="replaceEditor(this);" />
    </div>
  </div>


  <?php foreach($fmposts as $rec) { ?>
    <div class="post">
      <div class="author">

<?php if($user_rights >= 99 || $rec['user_id'] == $user_id) { ?>
        <img src="/style/icons/admin.delete.png" class="adminbutton" title="удалить"
              onclick="deletePost('<?=$rec['fmpost_id']?>');"
              style="float: right;"/>
        <img src="/style/icons/admin.edit.png" class="adminbutton" title="изменить"
              onclick="window.location='/forum/editpost/<?=$rec['fmpost_id']?>';"
              style="float: right;"/>
<?php } ?>

        <b><?php if($rec['fmptanonym']==1) { print 'Анонимный ответ</b>'; }
        else { print $rec['usname'].'</b>, '.$rec['usplace'].' ('.$rec['scname'].')'; } ?> <br>
         <small><?=date('d.m.y G:i', $rec['fmpttime'])?></small>
      </div>
      <?=$rec['fmtext']?>
      <?php if(isset($fmfiles[$rec['fmpost_id']])) { ?>
      <h4>Прикрепленные файлы:</h4>
      <?php foreach($fmfiles[$rec['fmpost_id']] as $recfile) { ?>
        <a href="/forum/file/<?=$recfile['id']?>"><?=$recfile['fmflsource']?></a><br>
      <?php } } ?>
    </div>
  <?php } ?>

  <script src="/js/sc2tablelist.js"></script>
    <?php CTemplates::sc2editor('/forum/newpost',
                                array("fmtopic_id" => $fmtopic[0]['fmtopic_id'],
                                "isanonym" => '0'),
                                'Добавить сообщение'); ?>

</div>


  <script src="/js/sc2editor.js"></script>
  <script>
  function onSave() {
    if(document.getElementById('sc2editorRich').innerHTML.trim().replace(/<\/?[^>]+>/gi, '') == '') {
      var popup2 = new sc2Popup();
      popup2.showMessage('Ошибка при добавлении сообщения', 'Отсутствует текст сообщения', 'Закрыть', null, null);
      return false;
    }

    document.getElementsByName('isanonym')[0].value = document.getElementById('authorType').innerHTML;
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

    function onDeleteTopic() {
      window.location = '/forum/deltopic/<?=$fmtopic[0]['fmtopic_id']?>';
    }

    function deleteTopic() {
      var popup = new sc2Popup();
      popup.showMessage('Удаление темы', 'Вы действительно хотите удалить тему полностью?',
                        'Нет', 'Да', function() { onDeleteTopic(); });
    }

    function onDeletePost(fmpost_id) {
      window.location = '/forum/delpost/'+fmpost_id;
    }

    function deletePost(fmpost_id) {
      var popup = new sc2Popup();
      popup.showMessage('Удаление сообщения', 'Вы действительно хотите удалить сообщение?',
                        'Нет', 'Да', function() { onDeletePost(fmpost_id); });
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

<div class="mainframe">
  <div class="subheader"><?=$webinar['wbname']?>

    <?php if($webinar_owner) { ?>
      <button class="adminbuttonnew" onclick="deleteWebinar();">Удалить</button>
      <button class="adminbuttonnew" onclick="window.location='/webinar/edit/<?=$webinar['wid']?>';">Изменить</button>
    <?php } ?>

  </div>

  <?php CTemplates::showMessage('posted', 'Ваше сообщение добавлено'); ?>
  <?php CTemplates::showMessage('edited', 'Информация о семинаре изменена'); ?>
  <?php CTemplates::showMessage('postedited', 'Сообщение изменено'); ?>
  <?php CTemplates::showMessage('postdeleted', 'Сообщение удалено'); ?>

  <div class="frameinfo">
<?php if($webinar['wbstart'] < time() && $webinar['wbend'] > time()-86400) { ?>
    <p>
      Семинар проходит с <b><?=date('d.m.Y', $webinar['wbstart'])?></b>
      по <b><?=date('d.m.Y', $webinar['wbend'])?></b>
    </p>
<?php } else if($webinar['wbstart'] > time()) { ?>
  <p>
    Период проведения семинара <b>с <?=date('d.m.Y', $webinar['wbstart'])?>
    по <?=date('d.m.Y', $webinar['wbend'])?></b>
  </p>
<?php } else { ?>
  <p>
    <b>Семинар завершен</b> (проходил с <?=date('d.m.Y', $webinar['wbstart'])?>
    по <?=date('d.m.Y', $webinar['wbend'])?>)
  </p>
<?php } ?>
    <p>
      Семинар проводит <b><?=$webinar['usname']?></b>, <?=$webinar['usplace']?> (<?=$webinar['scname']?>)
    </p>
    <p>
      <?=nl2br($webinar['wbdesc'])?>
    </p>
<?php if (count($webinarmembers) > 0) { ?>
    <h3>Основные доклады:</h3>
    <ol>
      <?php foreach($webinarmembers as $rec) { ?>
      <?php if($rec['wbmember_id'] > 0) { ?>
      <li><b><?=$rec['usname']?></b>, <?=$rec['usplace']?> (<?=$rec['scname']?>) &quot;<?=$rec['wbmbtopic']?>&quot;</li>
      <?php } else { ?>
      <?php if(strpos($rec['wbmemberinfo'], ',')===FALSE) $rec['wbmemberinfo'].=', '; ?>
      <li><b><?=preg_replace('/(.+?),? (.+)/', '$1',$rec['wbmemberinfo'])?></b>, <?=preg_replace('/(.+?),? (.+)/', '$2', $rec['wbmemberinfo'])?> &quot;<?=$rec['wbmbtopic']?>&quot;</li>
      <?php } } ?>
    </ol>
<?php } ?>
    <h3>Разделы:</h3>
    <p>
      <?php foreach($webinarsections as $rec) { ?>
      <a href="/webinar/showsection/<?=$rec['id']?>"><?=$rec['wbscpriority']?>. <?=$rec['wbscname']?><br>
      <small><?=$rec['wbscdesc']?></small></a><br>
      <?php } ?>
    </p>

    <h3>Вы работаете с разделом семинара №<?=$wbsection[0]['wbscpriority']?> - <?=$wbsection[0]['wbscname']?><br>
    <small><?=$wbsection[0]['wbscdesc']?></small></h3>

<?php if($webinar['wbstart'] < time() && $webinar['wbend'] > time()-86400) { ?>
    <div class="answerbox">
      <div class="author">
        <?php if(isset($usinfo[0]['usname'])) { ?>
        <b><?=$usinfo[0]['usname']?></b>
        <input type="hidden" name="usfreenametmp" id="freename" value="" />
        <?php } else { ?>
          <input type="text" name="usfreenametmp" id="freename" class="fakeEdit" placeholder="Введите ваше имя или оставьте поле пустым" style="color: black;" value="" />
        <?php } ?>
        <br>
        <!--<a href="#">Перевести в анонимный режим</a>-->
      </div>
      <div class="forinput">
        <input type="text" class="fakeEdit" value="Добавление сообщения в раздел" onfocus="replaceEditor(this);" />
      </div>
    </div>
<?php } ?>

    <?php foreach($wbmessages as $rec) { ?>
    <div class="post">
      <div class="author">

        <?php if($webinar_owner) { ?>
          <button class="adminbuttonnew" onclick="deletePost('<?=$rec['wid']?>');">Удалить</button>
          <button class="adminbuttonnew" onclick="window.location='/webinar/editpost/<?=$rec['wid']?>';">Изменить</button>
        <?php } ?>

        <?php if($rec['user_id']==0) { ?>
        <b><?=$rec['wbmsusername']=='' ? 'Анонимное сообщение' : $rec['wbmsusername']?></b>
        <?php } else { ?>
        <b><?=$rec['usname']?></b>, <?=$rec['usplace']?> (<?=$rec['scname']?>)
        <?php } ?>
        <br>
         <small><?=date('d.m.y G:i', $rec['wbmstime'])?></small>
      </div>
      <?=$rec['wbmstext']?>
      <?php if(isset($wbfiles[$rec['wid']])) { ?>
      <h4>Прикрепленные файлы:</h4>
      <?php foreach($wbfiles[$rec['wid']] as $recfile) { ?>
        <a href="/webinar/file/<?=$recfile['id']?>"><?=$recfile['wbflsource']?></a><br>
      <?php } } ?>
    </div>
    <?php } ?>

  </div>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor('/webinar/post',
                              array("wbsection_id" => $wbsection[0]['id'],
                              "usfreename" => ''),
                              'Добавить сообщение'); ?>

</div>
<?php if($webinar['wbstart'] < time() && $webinar['wbend'] > time()-86400) { ?>
<script src="/js/sc2editor.js"></script>
<script>
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

  function onSave() {
    document.getElementsByName('usfreename')[0].value = document.getElementById('freename').value;
    return true;
  }
</script>
<?php } ?>
<script>
  function onDeleteWebinar() {
    window.location = '/webinar/delete/<?=$webinar['wid']?>';
  }

  function deleteWebinar() {
    var popup = new sc2Popup();
    popup.showMessage('Удаление семинара', 'Вы действительно хотите удалить семинар полностью?',
                      'Нет', 'Да', function() { onDeleteWebinar(); });
  }

  function onDeletePost(wbmsid) {
    window.location = '/webinar/deletepost/'+wbmsid;
  }

  function deletePost(wbmsid) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление сообщения', 'Вы действительно хотите удалить сообщение?',
                      'Нет', 'Да', function() { onDeletePost(wbmsid); });
  }
</script>

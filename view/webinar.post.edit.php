<div class="mainframe">
  <div class="subheader">Изменение сообщения</div>

  <div class="answerbox">
    <div class="author">
      <b><?=$wbmessages['usname']?></b><br>
      <!--<a href="#">Перевести в анонимный режим</a>-->
    </div>
    <div class="forinput" id="forinput">
      <?php CTemplates::sc2editor('/webinar/savepost',
                                  array("wbmsid" => $wbmessages['wid']),
                                  'Сохранить изменения'); ?>
    </div>
  </div>

</div>
<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2editor.js"></script>
<script>
  document.getElementById('forinput').appendChild(g_sc2Editor);
  var sc2ed = new sc2Editor('sc2editor');
  sc2ed.initEditor();
  var tlfl = new sc2TableList('listformtablefiles');
  tlfl.addField('file', 'postFile', '300px', 1);
  tlfl.addField('delbutton');

  <?php foreach($wbfiles as $rec) { ?>
  tlfl.addRecord({postFile: '<?=$rec['wbflsource']?>', id: '<?=$rec['id']?>'})
  <?php } ?>

  <?php if(count($wbfiles) > 0) { ?>
  document.getElementById('sc2editor_filepanel').style.display = 'block';
  <?php } ?>

  tlfl.addEmpty({value: 'Выберите файл'});


  document.getElementById('sc2editorRich').innerHTML = '<?=str_replace("\r", "", str_replace("\n", " ", s_q($wbmessages['wbmstext'])))?>';
  document.getElementById('sc2editorRich').focus();
</script>

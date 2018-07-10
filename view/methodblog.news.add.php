<div class="mainframe">
  <div class="subheader">Добавить новость в методический блог &quot;<?=$methodblog['mbname']?>&quot;</div>

  <form class="transpform" action="/methodblog/savenews" method="post"
        name="methodblogForm" enctype="multipart/form-data">

    <input type="hidden" name="methodblog_id" value="<?=$methodblog['id']?>" />
    <input type="hidden" id="mbntext" name="mbntext" value="" />

    <label>Название</label>
    <input type="text" id="methodblogNewsNameInput" name="mbnname" />

    <label>Содержание</label>
    <textarea id="methodblogNewsTextInput"></textarea>
    <br />

    <input type="button" name="newtask" onclick="checkInput();" value="Добавить новость" />
    <input type="button" name="newtask" onclick="back();" value="Отменить добавление" />

  </form>

</div>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor(null, null, null); ?>

<script src="/js/sc2editor.js"></script>
<script>
  function checkInput() {
    var popup = new sc2Popup();
    if(id('methodblogNewsNameInput').value.trim() == '') {
      popup.showMessage('Ошибка при добавлении новости', 'Не указано название новости', 'Закрыть');
      seterror('methodblogNewsNameInput');
      return;
    } else if(id('sc2editorRich').innerHTML.trim() == '') {
      popup.showMessage('Ошибка при добавлении новости', 'Не введено содержание новости', 'Закрыть');
      seterror('methodblogNewsTextInput');
      return;
    }

    id('mbntext').value = id('sc2editorRich').innerHTML;
    document.forms['methodblogForm'].submit();
  }

  function back() {
    window.location = '/methodblog/show/<?=$methodblog['id']?>';
  }

  function replaceEditor() {
    var obj = document.getElementById('methodblogNewsTextInput');
    obj.parentNode.replaceChild(g_sc2Editor, obj);
    var sc2ed = new sc2Editor('sc2editor');
    sc2ed.initEditor();

    var tlfl = new sc2TableList('listformtablefiles');
    tlfl.addField('file', 'postFile', '300px', 1);
    tlfl.addField('delbutton');

    tlfl.addEmpty({value: 'Выберите файл'});
  }

  replaceEditor();
</script>

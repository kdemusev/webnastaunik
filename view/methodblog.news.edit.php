<div class="mainframe">
  <div class="subheader">Изменить новость в методическом блоге &quot;<?=$mbdata['mbname']?>&quot;</div>

  <form class="transpform" action="/methodblog/changenews" method="post"
        name="methodblogForm" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=$data['id']?>" />
    <input type="hidden" id="mbntext" name="mbntext" value="" />

    <label>Название</label>
    <input type="text" id="methodblogNewsNameInput" name="mbnname" value="<?=$data['mbnname']?>" />

    <label>Содержание</label>
    <textarea id="methodblogNewsTextInput"><?=$data['mbntext']?></textarea>
    <br />

    <input type="button" onclick="checkInput();" value="Сохранить изменения" />
    <input type="button" onclick="back();" value="Отменить" />

  </form>

</div>

<script src="/js/sc2tablelist.js"></script>
  <?php CTemplates::sc2editor(null, null, null); ?>

<script src="/js/sc2editor.js"></script>
<script>
  function checkInput() {
    var popup = new sc2Popup();
    if(id('methodblogNewsNameInput').value.trim() == '') {
      popup.showMessage('Ошибка при изменении новости', 'Не указано название новости', 'Закрыть');
      seterror('methodblogNewsNameInput');
      return;
    } else if(id('sc2editorRich').innerHTML.trim() == '') {
      popup.showMessage('Ошибка при изменении новости', 'Не введено содержание новости', 'Закрыть');
      seterror('methodblogNewsTextInput');
      return;
    }

    id('mbntext').value = id('sc2editorRich').innerHTML;
    document.forms['methodblogForm'].submit();
  }

  function back() {
    window.location = '/methodblog/show/<?=$mbdata['id']?>';
  }

  function replaceEditor() {
    var obj = document.getElementById('methodblogNewsTextInput');
    var data = obj.value;
    obj.parentNode.replaceChild(g_sc2Editor, obj);
    var sc2ed = new sc2Editor('sc2editor');
    sc2ed.initEditor();
    sc2editorRich.innerHTML = data;

    var tlfl = new sc2TableList('listformtablefiles');
    tlfl.addField('file', 'postFile', '300px', 1);
    tlfl.addField('delbutton');

    tlfl.addEmpty({value: 'Выберите файл'});
  }

  replaceEditor();

</script>

<div class="mainframe">

  <div class="subheader">Изменить информацию об участии в конкурсе</div>

  <form class="transpform">
    <input type="button" name="newtask" onclick="back();" value="Вернуться без сохранения" />
  </form>

  <form class="transpform" action="/dbconcurs/changeconcurs/<?=$data['ctid']?>" method="post"
        name="addForm">

    <h2>Информация о конкурсе</h2>

    <p>Конкурс <b>&quot;<?=$data['ctname']?>&quot;</b> (секция <b>&quot;<?=$data['csname']?>&quot;</b>) в <b><?=$data['oyname']?></b> учебном году</p>
    <p>Учреждение: <b><?=$data['oscname']?></b></p>

    <label>Секция:</label>
    <div class="sc3textpopup" data-name="section_id" id="section_id" data-id="<?=$data['concurs_section_id']?>"></div>

    <label>Педагог:</label>
    <div class="sc3textpopup" data-name="teacher_id" id="teacher_id" data-id="<?=$data['olymp_teacher_id']?>"></div>

    <label>Название работы:</label>
    <input type="text" name="cnname" value="<?=$data['cnname']?>" autocomplete="off" />

    <h2>Информация о результатах участия</h2>

    <label>Диплом:</label>
    <div class="sc3textpopup" data-name="ctdiploma" id="ctdiploma"
         data-id="<?=$data['ctdiploma']?>"></div>

    <label>Прочие отметки:</label>
    <label><input type="checkbox" name="ctismore" value="1"
                  <?php if($data['ctismore']==1) {?>checked<?php } ?>/> приглашен к участию в следующем этапе</label>

    <br /><br />

    <input type="button" onclick="checkInput();" value="Сохранить" />
    <input type="button" onclick="back()" value="Выйти без сохранения" />

  </form>

</div>

<script src="/js/sc3form.js"></script>

<script>
  var psoptions = [];
  var psvalues = [];

  psoptions = [];
  psvalues = [];
  <?php foreach($teachers as $rec) { ?>
    psoptions.push('<?=$rec['otname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>
  var inputteacher = new sc3FormEditableSelect('teacher_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  <?php foreach($sections as $rec) { ?>
    psoptions.push('<?=$rec['csname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>
  var inputsection = new sc3FormEditableSelect('section_id', psoptions, psvalues);

  psoptions = ['нет', 'I степени', 'II степени', 'III степени'];
  psvalues = ["0","1","2","3"];
  var inputdiploma = new sc3FormEditableSelect('ctdiploma', psoptions, psvalues, false);

  function checksc3Field(elem_id, err_text) {
    var popup = new sc2Popup();
    if(id(elem_id).children[2].textContent.trim() == '') {
        popup.showMessage('Ошибка заполнения', err_text, 'Закрыть');
        var o = id(elem_id).children[2];
        var oldClassName = o.className;
        o.className += ' seterror';
        o.onfocus = function() {
          o.className = oldClassName;
        };
        return false;
      }
      return true;
  }

  function checkInput() {
    if(!checksc3Field('teacher_id', 'Не введены фамилия, имя, отчество педагога')) { return; }

    document.forms['addForm'].submit();
  }

  function back() {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение выхода', 'Вы действительно хотите выйти без сохранения изменений?',
                      'Нет', 'Да', function() { onBack(); });
  }

  function onBack() {
    window.location='/dbconcurs/results';
  }
</script>

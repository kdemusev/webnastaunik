<div class="mainframe">

  <div class="subheader">Изменение сведений об участнике конкурса</div>

  <form class="transpform">
    <input type="button" name="newtask" onclick="back();" value="Вернуться без сохранения" />
  </form>

  <form class="transpform" action="/dbconcurs/changefio/<?=$data['id']?>" method="post"
        name="addForm">

    <label>Класс:</label>
    <div class="sc3textpopup" data-name="form_id" id="form_id" data-id="<?=$data['olymp_form_id']?>"></div>

    <label>Фамилия, имя, отчество:</label>
    <div class="sc3textpopup" data-name="pupil_id" id="pupil_id" data-id="<?=$data['olymp_pupil_id']?>"></div>

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
  <?php foreach($pupils as $rec) { ?>
    psoptions.push('<?=$rec['opname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputpupil = new sc3FormEditableSelect('pupil_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  <?php foreach($forms as $rec) { ?>
    psoptions.push('<?=$rec['ofname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputform = new sc3FormEditableSelect('form_id', psoptions, psvalues);

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
    if(!checksc3Field('form_id', 'Не введен класс участника конкурса')) { return; }
    if(!checksc3Field('pupil_id', 'Не введены фамилия, имя, отчество участника конкурса')) { return; }

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

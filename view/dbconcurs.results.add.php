<div class="mainframe">

  <div class="subheader">Добавить информацию об участии в конкурсе</div>

  <?php CTemplates::showMessage('added', 'Информация добавлена'); ?>

  <form class="transpform">
    <input type="button" name="newtask" onclick="back();" value="Вернуться без сохранения" />
  </form>

  <form class="transpform" action="/dbconcurs/save" method="post"
        name="addForm">

    <h2>Информация о конкурсе</h2>

    <label>Учебный год:</label>
    <div class="sc3textpopup" data-name="year_id" id="year_id"></div>

    <label>Уровень проведения:</label>
    <div class="sc3textpopup" data-name="concurstype" id="concurstype"></div>

    <label>Название конкурса:</label>
    <div class="sc3textpopup" data-name="concurs_id" id="concurs_id"></div>

    <label>Секция:</label>
    <div class="sc3textpopup" data-name="section_id" id="section_id"></div>

    <label>Название работы:</label>
    <input type="text" name="cnname" autocomplete="off" />

    <h2>Информация об участниках</h2>

    <label>Учреждение образования:</label>
    <div class="sc3textpopup" data-name="school_id" id="school_id"></div>

    <label>Педагог:</label>
    <div class="sc3textpopup" data-name="teacher_id" id="teacher_id"></div>

    <label>Класс:</label>
    <div class="sc3textpopup" data-name="form_id" id="form_id"></div>

    <label>Фамилия, имя, отчество:</label>
    <div class="sc3textpopup" data-name="pupil_id" id="pupil_id"></div>

    <p>Добавить дополнительных участников конкурса можно будет после добавления конкурса из таблицы результатов</p>

    <h2>Информация о результатах участия</h2>

    <label>Диплом:</label>
    <div class="sc3textpopup" data-name="ctdiploma" id="ctdiploma"></div>

    <label>Прочие отметки:</label>
    <label><input type="checkbox" name="ctismore" value="1" /> приглашен к участию в следующем этапе</label>

    <br /><br />

    <input type="hidden" name="entermore" id="entermore" value="0" />

    <input type="button" onclick="checkInput('next');" value="Сохранить ввести следующего" />
    <input type="button" onclick="checkInput('exit');" value="Сохранить и выйти" />
    <input type="button" onclick="back()" value="Выйти без сохранения" />

  </form>

</div>

<script src="/js/sc3form.js"></script>

<script>
  var psoptions = [];
  var psvalues = [];
  <?php foreach($olymp_years as $rec) { ?>
    psoptions.push('<?=$rec['oyname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputyear = new sc3FormEditableSelect('year_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  var inputsection = new sc3FormEditableSelect('section_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  var inputpupil = new sc3FormEditableSelect('pupil_id', psoptions, psvalues);
  var inputteacher = new sc3FormEditableSelect('teacher_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  <?php foreach($concurs_types as $rec) { ?>
    psoptions.push('<?=$rec['ctname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>
  var inputconcurs = new sc3FormEditableSelect('concurs_id', psoptions, psvalues, true, function() {
    var popup = new sc2Popup();
    popup.showWaiting('Получение данных...');
    var idtoget = inputconcurs.getIdValue();
    idtoget = idtoget < 0 ? 0 : idtoget;
    SMPAjaxGet('/dbconcurs/getsections/'+idtoget, function(res) {
        var x = res.documentElement.getElementsByTagName('section');
        var i;
        var l = x.length;
        psoptions = [];
        psvalues = [];
        for(i = 0; i < l; i++) {
          psvalues.push(x[i].getAttribute('id'));
          psoptions.push(x[i].firstChild ? x[i].firstChild.nodeValue : '');
        }
        inputsection.change(psoptions, psvalues);
        popup.hideWaiting();
    }, true);
  });

  psoptions = [];
  psvalues = [];
  <?php foreach($olymp_forms as $rec) { ?>
    psoptions.push('<?=$rec['ofname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputform = new sc3FormEditableSelect('form_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  <?php foreach($olymp_schools as $rec) { ?>
    psoptions.push('<?=$rec['oscname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputschool = new sc3FormEditableSelect('school_id', psoptions, psvalues, true, function() {
    var popup = new sc2Popup();
    popup.showWaiting('Получение данных...');
    var idtoget = inputschool.getIdValue();
    idtoget = idtoget < 0 ? 0 : idtoget;
    SMPAjaxGet('/dbolymp/getpupandteach/'+idtoget, function(res) {
        var x = res.documentElement.getElementsByTagName('pupil');
        var i;
        var l = x.length;
        psoptions = [];
        psvalues = [];
        for(i = 0; i < l; i++) {
          psvalues.push(x[i].getAttribute('id'));
          psoptions.push(x[i].firstChild ? x[i].firstChild.nodeValue : '');
        }
        inputpupil.change(psoptions, psvalues);

        x = res.documentElement.getElementsByTagName('teacher');
        l = x.length;
        psoptions = [];
        psvalues = [];
        for(i = 0; i < l; i++) {
            psvalues.push(x[i].getAttribute('id'));
            psoptions.push(x[i].firstChild ? x[i].firstChild.nodeValue : '');
        }
        inputteacher.change(psoptions, psvalues);

        popup.hideWaiting();
    }, true);
  });

  psoptions = ['нет', 'I степени', 'II степени', 'III степени'];
  psvalues = [0,1,2,3];
  var inputdiploma = new sc3FormEditableSelect('ctdiploma', psoptions, psvalues, false);

  psoptions = ['районный', 'региональный', 'областной', 'республиканский'];
  psvalues = [1,2,3,4];
  var inputolymptype = new sc3FormEditableSelect('concurstype', psoptions, psvalues, false);

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

  function checkInput(whatnext) {
    if(!checksc3Field('year_id', 'Не введен учебный год')) { return; }
    if(!checksc3Field('concurstype', 'Не введен уровень проведения')) { return; }
    if(!checksc3Field('concurs_id', 'Не введено название конкурса')) { return; }
    if(!checksc3Field('school_id', 'Не введено учреждение образования участника конкурса')) { return; }
    if(!checksc3Field('teacher_id', 'Не введены фамилия, имя, отчество педагога')) { return; }
    if(!checksc3Field('form_id', 'Не введен класс участника конкурса')) { return; }
    if(!checksc3Field('pupil_id', 'Не введены фамилия, имя, отчество участника конкурса')) { return; }

    if(whatnext=='next') {
      document.getElementById('entermore').value = '1';
    }
    document.forms['addForm'].submit();
  }

  function back() {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение выхода', 'Вы действительно хотите выйти без сохранения новых данных?',
                      'Нет', 'Да', function() { onBack(); });
  }

  function onBack() {
    window.location='/dbconcurs/results';
  }
</script>

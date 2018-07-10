<div class="mainframe">

  <div class="subheader">Добавить информацию об участии в олимпиаде</div>

  <?php CTemplates::showMessage('added', 'Информация добавлена'); ?>

  <form class="transpform">
    <input type="button" name="newtask" onclick="back();" value="Вернуться без сохранения" />
  </form>

  <form class="transpform" action="/dbolymp/save" method="post"
        name="addForm">

    <h2>Информация об учащемся</h2>

    <label>Вид олимпиады:</label>
    <div class="sc3textpopup" data-name="olymptype" id="olymptype"></div>

    <label>Учебный год:</label>
    <div class="sc3textpopup" data-name="year_id" id="year_id"></div>

    <label>Предмет:</label>
    <div class="sc3textpopup" data-name="subject_id" id="subject_id"></div>

    <label>Класс:</label>
    <div class="sc3textpopup" data-name="form_id" id="form_id"></div>

    <label>Учреждение образования:</label>
    <div class="sc3textpopup" data-name="school_id" id="school_id"></div>

    <label>Фамилия, имя, отчество:</label>
    <div class="sc3textpopup" data-name="pupil_id" id="pupil_id"></div>

    <label>Педагог:</label>
    <div class="sc3textpopup" data-name="teacher_id" id="teacher_id"></div>

    <h2>Информация об участии во II этапе</h2>

    <label>Максимальное колличество баллов:</label>
    <input type="text" name="olmaxpoints" autocomplete="off" />

    <label>Колличество набранных баллов:</label>
    <input type="text" name="olpoints" autocomplete="off" />

    <label>Процент выполнения:</label>
    <input type="text" name="olpercent" autocomplete="off" />

    <label>Место в рейтинге:</label>
    <input type="text" name="olrating" autocomplete="off" />

    <label>Диплом:</label>
    <div class="sc3textpopup" data-name="oldiploma" id="oldiploma"></div>

    <label>Прочие отметки:</label>
    <label><input type="checkbox" name="olnopassport" value="1" /> отсутствует документ</label>
    <label><input type="checkbox" name="olabsend" value="1" /> не участвовал</label>
    <label><input type="checkbox" name="olnoinapplication" value="1" /> нет в заявке</label>

    <h2>Информация об участии в III этапе</h2>

    <label>Приглашен к участию: <input type="checkbox" name="olisregion" value="1" onchange="toggleReg(this);" /></label>
    <br /><br />

    <div id="regblock" style="display: none;" >
      <label>Место в рейтинге:</label>
      <input type="text" name="olregrating" autocomplete="off" />

      <label>Диплом:</label>
      <div class="sc3textpopup" data-name="olregdiploma" id="olregdiploma"></div>

      <label>Прочие отметки:</label>
      <label><input type="checkbox" name="olregabsend" value="1" /> не участвовал</label>


      <h2>Информация об участии в заключительном этапе</h2>

      <label>Приглашен к участию: <input type="checkbox" name="olisrepublic" value="1" onchange="toggleRep(this);" /></label>
      <br /><br />

      <div id="repblock" style="display: none;" >
        <label>Место в рейтинге:</label>
        <input type="text" name="olreprating" autocomplete="off" />

        <label>Диплом:</label>
        <div class="sc3textpopup" data-name="olrepdiploma" id="olrepdiploma"></div>

        <label>Прочие отметки:</label>
        <label><input type="checkbox" name="olrepabsend" value="1" /> не участвовал</label>

      </div>
    </div>

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
  <?php foreach($olymp_subjects as $rec) { ?>
    psoptions.push('<?=$rec['osname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputsubject = new sc3FormEditableSelect('subject_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  <?php foreach($olymp_forms as $rec) { ?>
    psoptions.push('<?=$rec['ofname']?>');
    psvalues.push('<?=$rec['id']?>');
  <?php } ?>

  var inputform = new sc3FormEditableSelect('form_id', psoptions, psvalues);

  psoptions = [];
  psvalues = [];
  var inputpupil = new sc3FormEditableSelect('pupil_id', psoptions, psvalues);
  var inputteacher = new sc3FormEditableSelect('teacher_id', psoptions, psvalues);

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
  var inputdiploma = new sc3FormEditableSelect('oldiploma', psoptions, psvalues, false);
  var inputregdiploma = new sc3FormEditableSelect('olregdiploma', psoptions, psvalues, false);
  var inputrepdiploma = new sc3FormEditableSelect('olrepdiploma', psoptions, psvalues, false);

  psoptions = ['республиканская', 'областная', 'районная'];
  psvalues = [1,2,3];
  var inputolymptype = new sc3FormEditableSelect('olymptype', psoptions, psvalues, false);

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
    if(!checksc3Field('olymptype', 'Не введен вид олимпиады')) { return; }
    if(!checksc3Field('subject_id', 'Не введено название предмета')) { return; }
    if(!checksc3Field('school_id', 'Не введено учреждение образования участника конкурса')) { return; }
    if(!checksc3Field('teacher_id', 'Не введены фамилия, имя, отчество педагога')) { return; }
    if(!checksc3Field('form_id', 'Не введен класс участника конкурса')) { return; }
    if(!checksc3Field('pupil_id', 'Не введены фамилия, имя, отчество участника конкурса')) { return; }

    if(whatnext=='next') {
      document.getElementById('entermore').value = '1';
    }
    document.forms['addForm'].submit();
  }

  function toggleReg(obj) {
    if(obj.checked) {
      document.getElementById('regblock').style.display = 'block';
    } else {
      document.getElementById('regblock').style.display = 'none';
    }
  }

  function toggleRep(obj) {
    if(obj.checked) {
      document.getElementById('repblock').style.display = 'block';
    } else {
      document.getElementById('repblock').style.display = 'none';
    }
  }

  function back() {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение выхода', 'Вы действительно хотите выйти без сохранения новых данных?',
                      'Нет', 'Да', function() { onBack(); });
  }

  function onBack() {
    window.location='/dbolymp/results';
  }
</script>

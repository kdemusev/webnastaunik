<div class="mainframe">

  <div class="subheader">Изменить информацию о результате участия в олимпиаде</div>

  <form class="transpform">
    <input type="button" name="newtask" onclick="back();" value="Вернуться без сохранения" />
  </form>

  <form class="transpform" action="/dbolymp/changesubj/<?=$data['olymp_id']?>" method="post"
        name="addForm">

    <h2>Информация об учащемся</h2>

    <p><?=$data['opname']?>, <?=$data['ofname']?>, <?=$data['oscname']?></p>
    <p>Олимпиада по предмету <b>&quot;<?=$data['osname']?>&quot;</b> в <b><?=$data['oyname']?></b></p>

    <label>Педагог:</label>
    <div class="sc3textpopup" data-name="teacher_id" id="teacher_id" data-id="<?=$data['olymp_teacher_id']?>"></div>

    <h2>Информация об участии во II этапе</h2>

    <label>Максимальное колличество баллов:</label>
    <input type="text" name="olmaxpoints" value="<?=$data['olmaxpoints']?>" />

    <label>Колличество набранных баллов:</label>
    <input type="text" name="olpoints" value="<?=$data['olpoints']?>" />

    <label>Процент выполнения:</label>
    <input type="text" name="olpercent" value="<?=$data['olpercent']?>" />

    <label>Место в рейтинге:</label>
    <input type="text" name="olrating" value="<?=$data['olrating']?>" />

    <label>Диплом:</label>
    <div class="sc3textpopup" data-name="oldiploma" id="oldiploma"
         data-id="<?=$data['oldiploma']?>"></div>

    <label>Прочие отметки:</label>
    <label><input type="checkbox" name="olnopassport" value="1"
                  <?php if($data['olnopassport']==1) {?>checked<?php } ?> /> отсутствует документ</label>
    <label><input type="checkbox" name="olabsend" value="1"
                  <?php if($data['olabsend']==1) {?>checked<?php } ?> /> не участвовал</label>
    <label><input type="checkbox" name="olnoinapplication" value="1"
                  <?php if($data['olnoinapplication']==1) {?>checked<?php } ?> /> нет в заявке</label>


    <h2>Информация об участии в III этапе</h2>

    <label>Приглашен к участию:
      <input type="checkbox" name="olisregion" value="1" onchange="toggleReg(this);"
             <?php if($data['olisregion']==1) {?>checked<?php } ?>/></label>
    <br /><br />

    <div id="regblock" style="display: <?php if($data['olisregion']==1) {?>block<?php } else { ?>none<?php } ?>;" >
      <label>Место в рейтинге:</label>
      <input type="text" name="olregrating" value="<?=$data['olregrating']?>" />

      <label>Диплом:</label>
      <div class="sc3textpopup" data-name="olregdiploma" id="olregdiploma"
           data-id="<?=$data['olregdiploma']?>"></div>

      <label>Прочие отметки:</label>
      <label><input type="checkbox" name="olregabsend" value="1"
                    <?php if($data['olregabsend']==1) {?>checked<?php } ?>/> не участвовал</label>


      <h2>Информация об участии в заключительном этапе</h2>

      <label>Приглашен к участию:
        <input type="checkbox" name="olisrepublic" value="1" onchange="toggleRep(this);"
               <?php if($data['olisrepublic']==1) {?>checked<?php } ?>/></label>
      <br /><br />

      <div id="repblock" style="display: <?php if($data['olisrepublic']==1) {?>block<?php } else { ?>none<?php } ?>;" >
        <label>Место в рейтинге:</label>
        <input type="text" name="olreprating" value="<?=$data['olreprating']?>" />

        <label>Диплом:</label>
        <div class="sc3textpopup" data-name="olrepdiploma" id="olrepdiploma"
             data-id="<?=$data['olrepdiploma']?>"></div>

        <label>Прочие отметки:</label>
        <label><input type="checkbox" name="olrepabsend" value="1"
                      <?php if($data['olrepabsend']==1) {?>checked<?php } ?>/> не участвовал</label>

      </div>
    </div>

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

  psoptions = ['нет', 'I степени', 'II степени', 'III степени'];
  psvalues = ["0","1","2","3"];
  var inputdiploma = new sc3FormEditableSelect('oldiploma', psoptions, psvalues, false);
  var inputregdiploma = new sc3FormEditableSelect('olregdiploma', psoptions, psvalues, false);
  var inputrepdiploma = new sc3FormEditableSelect('olrepdiploma', psoptions, psvalues, false);

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
    popup.showMessage('Подтверждение выхода', 'Вы действительно хотите выйти без сохранения изменений?',
                      'Нет', 'Да', function() { onBack(); });
  }

  function onBack() {
    window.location='/dbolymp/results';
  }
</script>

<div class="mainframe">
  <div class="subheader">
    <?php if(!$_edit) { ?>
      Открыть дистанционный семинар
    <?php } else { ?>
      Изменить дистанционный семинар
    <?php } ?>
  </div>

  <form class="transpform" action="/webinar/<?=$_edit ? 'save' : 'add'?>" method="post"
        name="webinarForm">
    <input type="hidden" name="webinar_id" value="<?=$_edit ? $webinar['id'] : ''?>" />

    <label>Название</label>
    <input type="text" id="webinarNameInput" name="wbname"
           value="<?=$_edit ? htmlentities($webinar['wbname']) : ''?>" />

    <label>Уровень проведения</label>
    <input type="radio" id="rType1" class="customRadioButton" name="wbtype"
           value="0"
           <?=$_edit ? $webinar['wbtype']==0 ? 'checked="checked"' : '' : 'checked="checked"'?> />
    <label for="rType1" class="button">Районный семинар</label>
    <input type="radio" id="rType2"  class="customRadioButton" name="wbtype"
           value="1"
           <?=$_edit ? $webinar['wbtype']==1 ? 'checked="checked"' : '' : ''?> />
    <label for="rType2" class="button">Областной семинар</label>
    <br /><br />

    <label>Специализации:</label>
    <div class="checkboxes">
      <label><input type="checkbox" style="margin: 0;" name="specialization_id[]"
                    value="0"/> Все заинтересованные</label>
    <?php foreach($_specializations as $rec) { ?>
        <label><input type="checkbox" style="margin: 0;" name="specialization_id[]"
                      value="<?=$rec['id']?>"
                      <?=isset($webinarspecs[$rec['id']]) ? 'checked="checked"' : ''?>/>
                      <?=$rec['spname']?></label>
    <?php } ?>
    </div>

    <label>Дата начала</label>
    <input type="hidden" name="wbstart" id="wbstart"
           value="<?=$_edit ? $webinar['wbstart'] : '0'?>" />
    <input type="button" id="wbstartlabel" onclick="selectStart();"
           value="<?=$_edit ? date('d.m.Y',$webinar['wbstart']) : 'Выбрать дату'?>" />

    <label>Дата окончания</label>
    <input type="hidden" name="wbend" id="wbend"
           value="<?=$_edit ? $webinar['wbend'] : '0'?>" />
    <input type="button" id="wbendlabel" onclick="selectEnd();"
           value="<?=$_edit ? date('d.m.Y',$webinar['wbend']) : 'Выбрать дату'?>" />

    <p> Следующие две даты указываются при необходимости проведения семинаров с участием всех заинтересованных лиц, в том числе незарегистрированных на портале &quot;Ежедневник учителя&quot;</p>

    <label>Дата открытия свободного доступа</label>
    <input type="hidden" name="wbfreestart" id="wbfreestart"
           value="<?=$_edit ? $webinar['wbfreestart'] : '0'?>" />
    <input type="button" id="wbfreestartlabel" onclick="selectFreeStart();"
           value="<?=$_edit ? ($webinar['wbfreestart']>0 ? date('d.m.Y',$webinar['wbfreestart']) : 'Выбрать дату') : 'Выбрать дату'?>" />

    <label>Дата окончания свободного доступа</label>
    <input type="hidden" name="wbfreeend" id="wbfreeend"
           value="<?=$_edit ? $webinar['wbfreeend'] : '0'?>" />
    <input type="button" id="wbfreeendlabel" onclick="selectFreeEnd();"
           value="<?=$_edit ? ($webinar['wbfreeend']>0 ? date('d.m.Y',$webinar['wbfreeend']) : 'Выбрать дату') : 'Выбрать дату'?>" />
<br/><br/>
    <label>Описание семинара</label>
    <textarea name="wbdesc"><?=$_edit ? $webinar['wbdesc'] : ''?></textarea>

    <label>Разделы семинара</label>
    <?php CTemplates::formList(array('','','','','',''),
                               array(),
                               "listformtablewbsections", false, true); ?>
    <br /><br />

    <label>Основные доклады</label>
    <?php CTemplates::formList(array('','','','','','','','',''),
                               array(),
                               "listformtablewbmembers", false, true); ?>
    <br /><br />

    <?php if(!$_edit) { ?>
    <input type="button" name="newtask" onclick="checkInput();" value="Открыть семинар" />
    <input type="button" name="newtask" onclick="back();" value="Отменить открытие" />
    <?php } else { ?>
      <input type="button" name="newtask" onclick="checkInput();" value="Сохранить изменения" />
      <input type="button" name="newtask" onclick="back();" value="Отменить" />
    <?php } ?>
  </form>

  <form class="transpform" id="selectteacher">
    <div id="teacherChoose">
      <label>Область:</label>
      <select onchange="regionSelected(this.options[this.selectedIndex])">
          <option value="0" selected style="display: none;"></option>
          <?php foreach($_regions as $rec) { ?>
              <option value="<?=$rec['id'];?>"><?=$rec['rgname'];?></option>";
          <?php } ?>
      </select>

      <label>Район:</label>
      <select onchange="districtSelected(this.options[this.selectedIndex])" id="district_id" name="district_id">
          <option value="0" selected style="display: none;"></option>
      </select>

      <label>Учреждение:</label>
      <select name="school_id"
              onchange="schoolSelected(this.options[this.selectedIndex])"
              id="school_id">
          <option value="0" selected style="display: none;"></option>
      </select>

      <label>Участник:</label>
      <select name="teacher_id" id="teacher_id">
          <option value="0" selected style="display: none;"></option>
      </select>
    </div>

    <p>Если участника семинара невозможно выбрать, введите его данные вручную.</p>
    <p><i>Например: Фамилия, Имя, Отчество, должность, учреждение.</i></p>
    <input type="text" name="tcinfo" id="teacherinfo_id" onkeyup='onTIEdit(this); '/>

  </form>
</div>

<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2calendar.js"></script>
<script>
  var tlws = new sc2TableList('listformtablewbsections');
  tlws.addField('number');
  tlws.addField('order', 'wbscpriority');
  tlws.addField('label', 'Название');
  tlws.addField('text', 'wbscname', '200px', 1);
  tlws.addField('label', 'Описание');
  tlws.addField('text', 'wbscdesc', '200px');
  tlws.addField('buttons');

  <?php if(isset($webinarsections)) { foreach($webinarsections as $rec) { ?>
  tlws.addRecord({id: '<?=$rec['id']?>',
                 wbscname: new String('<?=$rec['wbscname']?>'),
                 wbscdesc: new String('<?=$rec['wbscdesc']?>')});
  <?php } } ?>
  tlws.addEmpty({});

  var tlwm = new sc2TableList('listformtablewbmembers');
  tlwm.addField('number');
  tlwm.addField('order', 'wbmbpriority');
  tlwm.addField('hidden', 'wbmembers');
  tlwm.addField('hidden', 'wbmemberinfo');
  tlwm.addField('label', 'Участник');
  tlwm.addField('innerHTML', 'wbmbname', '150px');
  tlwm.addField('label', 'Тема');
  tlwm.addField('text', 'wbmbtopic', '250px', 1);
  tlwm.addField('buttons');

  <?php if(isset($webinarmembers)) { foreach($webinarmembers as $rec) { ?>
  tlwm.addRecord({id: '<?=$rec['wid']?>',
                 wbmembers: new String('<?=$rec['wbmember_id']==0 ? $rec['wbmemberinfo'] : $rec['wbmember_id']?>'),
                 wbmemberinfo: new String('<?=$rec['wbmemberinfo']?>'),
                 wbmbname: new String('<?=$rec['wbmember_id']==0 ? $rec['wbmemberinfo'] : $rec['usname']?>').replace(/^(.+?)\s+(.).+?\s+(.).+?$/i, '$1 $2.$3.'),
                 wbmbtopic: new String('<?=$rec['wbmbtopic']?>'),
                 funcClick: onSelectTeacher});
  <?php } } ?>

  tlwm.addEmpty({value: 'Нажмите чтобы выбрать', funcClick: onSelectTeacher});

  var oSelectTeacher = document.getElementById('selectteacher');
  oSelectTeacher = oSelectTeacher.parentNode.removeChild(oSelectTeacher);

  function onTIEdit(curObj) {
    var obj = document.getElementById('teacherChoose');
    if(curObj.value.trim() == '') {
      obj.style.display = 'block';
    } else {
      obj.style.display = 'none';
    }
  }

  function regionSelected(obj) {
      SMPAjaxGet('/index.php?section=users&action=getregions&id='+obj.value, function(res) {
          clearSelect('district_id');
          x = res.documentElement.getElementsByTagName('region');
          for(var i = 0; i < x.length; i++) {
              addOption('district_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
          }
      }, true);
  }

  function districtSelected(obj) {
      SMPAjaxGet('/index.php?section=users&action=getschools&id='+obj.value, function(res) {
          clearSelect('school_id');
          x = res.documentElement.getElementsByTagName('school');
          for(var i = 0; i < x.length; i++) {
              addOption('school_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
          }
      }, true);
  }

  function schoolSelected(obj) {
    SMPAjaxGet('/index.php?section=users&action=getteachers&id='+obj.value, function(res) {
        clearSelect('teacher_id');
        x = res.documentElement.getElementsByTagName('teacher');
        for(var i = 0; i < x.length; i++) {
            addOption('teacher_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
        }
    }, true);
  }

  function onTeacherSelected(tr) {
    // input2 (teacher_info) saves selectedIndex if teacher is choosen (not written)
    var input = tr.children[2].children[0];
    input.value = document.getElementById('teacherinfo_id').value.trim();
    var input2 = tr.children[3].children[0];
    if(input.value == '') { // teacher choosen
      input.value = document.getElementById('teacher_id').options[document.getElementById('teacher_id').selectedIndex].value;
      tr.children[5].children[0].innerHTML = document.getElementById('teacher_id').options[document.getElementById('teacher_id').selectedIndex].innerHTML;
      input2.value = document.getElementById('teacher_id').selectedIndex;
    } else {  // teacher written
      tr.children[5].children[0].innerHTML = document.getElementById('teacherinfo_id').value;
      input2.value = tr.children[5].children[0].innerHTML;
    }
    // shorten button label
    tr.children[5].children[0].innerHTML = tr.children[5].children[0].innerHTML.replace(/^(.+?)\s+(.).+?\s+(.).+?$/i, '$1 $2.$3.');
  }

  function onSelectTeacher(tr, row) {
    var popup = new sc2Popup();
    popup.showModal('Выбрать участника семинара', oSelectTeacher, 'Закрыть', 'Выбрать',
                    function() { onTeacherSelected(tr); });

    if(tr.children[2].children[0].value !== undefined) {  // edited
      if(isNaN(tr.children[2].children[0].value) || tr.children[2].children[0].value == 0) {
        document.getElementById('teacherChoose').display = 'none';
        document.getElementById('teacherinfo_id').value = tr.children[3].children[0].value;
      } else {
        document.getElementById('teacherChoose').display = 'block';
        document.getElementById('teacherinfo_id').value = '';
        if(isNaN(tr.children[3].children[0].value)) {
          document.getElementById('teacher_id').selectedIndex = 0;
        } else {
          document.getElementById('teacher_id').selectedIndex = tr.children[3].children[0].value;
        }
        clearSelect('district_id');
        clearSelect('school_id');
      }
    } else {
      document.getElementById('teacherinfo_id').value = '';
      clearSelect('district_id');
      clearSelect('school_id');
      clearSelect('teacher_id');
    }


  }

  function checkInput() {
    var popup = new sc2Popup();
    if(id('webinarNameInput').value.trim() == '') {
      popup.showMessage('Ошибка при создании семинара', 'Не указано название семинара', 'Закрыть');
      seterror('webinarNameInput');
      return;
    } else if(id('wbstart').value == 0) {
      popup.showMessage('Ошибка при создании семинара', 'Не указана дата начала семинара', 'Закрыть');
      seterror('wbstartlabel');
      return;
    } else if(id('wbend').value == 0) {
      popup.showMessage('Ошибка при создании семинара', 'Не указана дата окончания семинара', 'Закрыть');
      seterror('wbendlabel');
      return;
    } else if(id('wbend').value < id('wbstart').value) {
      popup.showMessage('Ошибка при создании семинара', 'Дата окончания семинара раньше чем дата начала семинара', 'Закрыть');
      seterror('wbendlabel');
      return;
    } else if(!checked('specialization_id[]')) {
      popup.showMessage('Ошибка при создании семинара', 'Не выбрана ни одна специализация', 'Закрыть');
      return;
    } else if(!filled('newwbscname') && !filled('wbscname')) {
      popup.showMessage('Ошибка при создании семинара', 'Не указан ни один раздел семинара', 'Закрыть');
      return;
    }

    document.forms['webinarForm'].submit();
  }

  var cal = null;
  function selectStart() {
    cal = new sc2Calendar(new Date(<?=$_edit ? $webinar['wbstart']*1000 : ''?>));
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('wbstartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('wbstart').value = cal.selectedDate/1000;
      document.getElementById('darker_id').style.display = 'none';
      document.getElementById('popup_id').style.display = 'none';
    });
    var popup = new sc2Popup();
    popup.showModal('Выберите дату начала семинара', obj, 'Закрыть');
  }

  function selectEnd() {
    cal = new sc2Calendar(new Date(<?=$_edit ? $webinar['wbend']*1000 : ''?>));
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('wbendlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('wbend').value = cal.selectedDate/1000;
      document.getElementById('darker_id').style.display = 'none';
      document.getElementById('popup_id').style.display = 'none';
    });
    var popup = new sc2Popup();
    popup.showModal('Выберите дату окончания семинара', obj, 'Закрыть');
  }

  function selectFreeStart() {
    cal = new sc2Calendar(new Date(<?=$_edit ? ($webinar['wbfreestart']>0 ? $webinar['wbfreestart']*1000 : '') : ''?>));
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('wbfreestartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('wbfreestart').value = cal.selectedDate/1000;
      document.getElementById('darker_id').style.display = 'none';
      document.getElementById('popup_id').style.display = 'none';
    });
    var popup = new sc2Popup();
    popup.showModal('Выберите дату начала свободного доступа к семинару', obj, 'Закрыть');
  }

  function selectFreeEnd() {
    cal = new sc2Calendar(new Date(<?=$_edit ? ($webinar['wbfreeend']>0 ? $webinar['wbfreeend']*1000 : '') : ''?>));
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('wbfreeendlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('wbfreeend').value = cal.selectedDate/1000;
      document.getElementById('darker_id').style.display = 'none';
      document.getElementById('popup_id').style.display = 'none';
    });
    var popup = new sc2Popup();
    popup.showModal('Выберите дату окончания свободного доступа к семинару', obj, 'Закрыть');
  }

  function back() {
    window.location = '<?=$_edit ? '/webinar/show/'.$webinar['id'] : '/webinar'?>';
  }
</script>

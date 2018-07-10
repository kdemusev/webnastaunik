<div class="mainframe">
  <div class="subheader">Установка дат каникул</div>

  <?php CTemplates::showMessage('saved', 'Изменения сохранены'); ?> 

  <form class="transpform" action="/ktp/setvacations" method="post"
        name="vacationsForm">
  <label>I четверть</label>
  с <input type="hidden" name="fststart" id="fststart"
           value="<?=isset($vacations['fststart']) ? $vacations['fststart'] : '0'?>" />
    <input type="button" id="fststartlabel" onclick="selectFstStart();"
           value="<?=isset($vacations['fststart']) ? date('d.m.Y',$vacations['fststart']) : 'Выбрать дату'?>" />
  по <input type="hidden" name="fstend" id="fstend"
          value="<?=isset($vacations['fstend']) ? $vacations['fstend'] : '0'?>" />
   <input type="button" id="fstendlabel" onclick="selectFstEnd();"
          value="<?=isset($vacations['fstend']) ? date('d.m.Y',$vacations['fstend']) : 'Выбрать дату'?>" /><br />

  <label>II четверть</label>
  с <input type="hidden" name="secstart" id="secstart"
           value="<?=isset($vacations['secstart']) ? $vacations['secstart'] : '0'?>" />
    <input type="button" id="secstartlabel" onclick="selectSecStart();"
           value="<?=isset($vacations['secstart']) ? date('d.m.Y',$vacations['secstart']) : 'Выбрать дату'?>" />
  по <input type="hidden" name="secend" id="secend"
          value="<?=isset($vacations['secend']) ? $vacations['secend'] : '0'?>" />
   <input type="button" id="secendlabel" onclick="selectSecEnd();"
          value="<?=isset($vacations['secend']) ? date('d.m.Y',$vacations['secend']) : 'Выбрать дату'?>" /><br />

  <label>III четверть</label>
  с <input type="hidden" name="thrstart" id="thrstart"
           value="<?=isset($vacations['thrstart']) ? $vacations['thrstart'] : '0'?>" />
    <input type="button" id="thrstartlabel" onclick="selectThrStart();"
           value="<?=isset($vacations['thrstart']) ? date('d.m.Y',$vacations['thrstart']) : 'Выбрать дату'?>" />
  по <input type="hidden" name="thrend" id="thrend"
          value="<?=isset($vacations['thrend']) ? $vacations['thrend'] : '0'?>" />
   <input type="button" id="threndlabel" onclick="selectThrEnd();"
          value="<?=isset($vacations['thrend']) ? date('d.m.Y',$vacations['thrend']) : 'Выбрать дату'?>" /><br />

  <label>IV четверть</label>
  с <input type="hidden" name="foustart" id="foustart"
           value="<?=isset($vacations['foustart']) ? $vacations['foustart'] : '0'?>" />
    <input type="button" id="foustartlabel" onclick="selectFouStart();"
           value="<?=isset($vacations['foustart']) ? date('d.m.Y',$vacations['foustart']) : 'Выбрать дату'?>" />
  по <input type="hidden" name="fouend" id="fouend"
          value="<?=isset($vacations['fouend']) ? $vacations['fouend'] : '0'?>" />
   <input type="button" id="fouendlabel" onclick="selectFouEnd();"
          value="<?=isset($vacations['fouend']) ? date('d.m.Y',$vacations['fouend']) : 'Выбрать дату'?>" /><br />

  <label>&nbsp;</label>
  <input type="button" name="newtask" onclick="document.forms['vacationsForm'].submit();" value="Сохранить изменения и пересчитать календарные даты" />

  </form>

</div>

<script src="/js/sc2calendar.js"></script>

<script>

function selectFstStart() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['fststart']) ? $vacations['fststart']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('fststartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('fststart').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату начала I четверти', obj, 'Закрыть');
}

function selectFstEnd() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['fstend']) ? $vacations['fstend']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('fstendlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('fstend').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату окончания I четверти', obj, 'Закрыть');
}

function selectSecStart() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['secstart']) ? $vacations['secstart']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('secstartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('secstart').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату начала II четверти', obj, 'Закрыть');
}

function selectSecEnd() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['secend']) ? $vacations['secend']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('secendlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('secend').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату окончания II четверти', obj, 'Закрыть');
}

function selectThrStart() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['thrstart']) ? $vacations['thrstart']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('thrstartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('thrstart').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату начала III четверти', obj, 'Закрыть');
}

function selectThrEnd() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['thrend']) ? $vacations['thrend']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('threndlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('thrend').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату окончания III четверти', obj, 'Закрыть');
}

function selectFouStart() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['foustart']) ? $vacations['foustart']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('foustartlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('foustart').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату начала IV четверти', obj, 'Закрыть');
}

function selectFouEnd() {
  var cal = new sc2Calendar(new Date(<?=isset($vacations['fouend']) ? $vacations['fouend']*1000 : ''?>));
  var obj = cal.showMonthCalendar(function() {
    var date = new Date(cal.selectedDate);
    document.getElementById('fouendlabel').value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
    document.getElementById('fouend').value = cal.selectedDate/1000;
    document.getElementById('darker_id').style.display = 'none';
    document.getElementById('popup_id').style.display = 'none';
  });
  var popup = new sc2Popup();
  popup.showModal('Выберите дату окончания IV четверти', obj, 'Закрыть');
}

</script>

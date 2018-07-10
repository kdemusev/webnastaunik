<div class="mainframe">
  <div class="subheader">Изменить задачу</div>

  <form class="transpform" action="/tasks/edit/<?=$data[0]['id']?>" method="post"
        name="editTaskForm">
    <label>Название задачи</label>
    <input type="text" id="taskNameInput" name="tsname"
           value="<?=$data[0]['tsname']?>" />

    <label>Когда</label>
    <input type="radio" id="rWhen1" class="customRadioButton" name="when"
           value="today"
           <?php if($data[0]['tsdate']==$today) { ?> checked="checked" <?php } ?> />
    <label for="rWhen1" class="button">Сегодня</label>
    <input type="radio" id="rWhen2"  class="customRadioButton" name="when"
           value="tomorrow"
           <?php if($data[0]['tsdate']==$today+86400) { ?> checked="checked" <?php } ?> />
    <label for="rWhen2" class="button">Завтра</label>
    <input type="radio" id="rWhen3"  class="customRadioButton" name="when"
           value="thisweek"
           <?php if($data[0]['tsdate']==$thisweek) { ?> checked="checked" <?php } ?> />
    <label for="rWhen3" class="button">На этой неделе</label>
    <input type="radio" id="rWhen4"  class="customRadioButton" name="when"
           value="nextweek"
           <?php if($data[0]['tsdate']==$thisweek+86400*7) { ?> checked="checked" <?php } ?> />
    <label for="rWhen4" class="button">На следующей неделе</label>
    <input type="radio" id="rWhen5"  class="customRadioButton" name="when"
           <?php if($todate==1) { ?> value="<?=$data[0]['tsdate']?>" checked="checked"
           <?php } else { ?> value="date" <?php } ?> />
    <label for="rWhen5" id="rWhen5Label" class="button"
           onclick="showCalendar();">
           <?php if($todate==1) {
             print date('d.m.Y',$data[0]['tsdate']);
           } else {
             print 'Выбрать дату';
           } ?>
    </label>
    <input type="radio" id="rWhen6"  class="customRadioButton" name="when"
           value="whenever"
           <?php if($data[0]['tsdate']==0) { ?> checked="checked" <?php } ?> />
    <label for="rWhen6" class="button">Когда-нибудь</label>

    <label>Напомнить</label>

    <input type="radio" id="rBell1" class="customRadioButton" name="bell"
           value="noremind"
           <?php if($data[0]['tsremind']==0) { ?> checked="checked" <?php } ?> />
    <label for="rBell1" class="button">Не напоминать</label>

    <input type="radio" id="rBell2"  class="customRadioButton" name="bell"
           value="<?=$data[0]['tsremind']?>"
           <?php if($data[0]['tsremind']!=0) { ?> checked="checked" <?php } ?> />
    <label for="rBell2" id="rBell2Label" class="button"
           onclick="showTimeCalendar();">
           <?php if($data[0]['tsremind']!=0) {
             print date('d.m.Y',$data[0]['tsremind']);
           } else {
             print 'Выбрать время';
           } ?>
    </label>

    <label>Заметки</label>
    <textarea name="tsnotes"><?=$data[0]['tsnotes']?></textarea>

    <label>Цветовой маркер</label>
    <input type="radio" id="rMark1" class="customRadioMarker" name="tscolor" value="0" <?php if($data[0]['tscolor']==0) { ?> checked="checked" <?php } ?> />
    <label for="rMark1" class="buttonmarker" style="background-color: #FFFFFF;"></label>
    <input type="radio" id="rMark2" class="customRadioMarker" name="tscolor" value="1" <?php if($data[0]['tscolor']==1) { ?> checked="checked" <?php } ?> />
    <label for="rMark2" class="buttonmarker" style="background-color: #E8C391;"></label>
    <input type="radio" id="rMark3" class="customRadioMarker" name="tscolor" value="2" <?php if($data[0]['tscolor']==2) { ?> checked="checked" <?php } ?> />
    <label for="rMark3" class="buttonmarker" style="background-color: #FFA395;"></label>
    <input type="radio" id="rMark4" class="customRadioMarker" name="tscolor" value="3" <?php if($data[0]['tscolor']==3) { ?> checked="checked" <?php } ?> />
    <label for="rMark4" class="buttonmarker" style="background-color: #E391E8;"></label>
    <input type="radio" id="rMark5" class="customRadioMarker" name="tscolor" value="4" <?php if($data[0]['tscolor']==4) { ?> checked="checked" <?php } ?> />
    <label for="rMark5" class="buttonmarker" style="background-color: #A8AFFF;"></label>
    <input type="radio" id="rMark6" class="customRadioMarker" name="tscolor" value="5" <?php if($data[0]['tscolor']==5) { ?> checked="checked" <?php } ?> />
    <label for="rMark6" class="buttonmarker" style="background-color: #A7F5FF;"></label>
    <input type="radio" id="rMark7" class="customRadioMarker" name="tscolor" value="6" <?php if($data[0]['tscolor']==6) { ?> checked="checked" <?php } ?> />
    <label for="rMark7" class="buttonmarker" style="background-color: #90FFB4;"></label>
    <input type="radio" id="rMark8" class="customRadioMarker" name="tscolor" value="7" <?php if($data[0]['tscolor']==7) { ?> checked="checked" <?php } ?> />
    <label for="rMark8" class="buttonmarker" style="background-color: #CBFF92;"></label>
    <input type="radio" id="rMark9" class="customRadioMarker" name="tscolor" value="8" <?php if($data[0]['tscolor']==8) { ?> checked="checked" <?php } ?> />
    <label for="rMark9" class="buttonmarker" style="background-color: #9795E8;"></label>
    <input type="radio" id="rMark10" class="customRadioMarker" name="tscolor" value="9" <?php if($data[0]['tscolor']==9) { ?> checked="checked" <?php } ?> />
    <label for="rMark10" class="buttonmarker" style="background-color: #FFFB97;"></label>

    <br />

    <input type="button" name="newtask" onclick="checkEditedTask();" value="Изменить" />
    <input type="button" name="newtask" onclick="back();" value="Вернуться без изменений" />
  </form>
</div>

<script src="/js/sc2calendar.js"></script>
<script>
  function checkEditedTask() {
    if(id('taskNameInput').value.trim() == '') {
      var popup = new sc2Popup();
      popup.showMessage('Ошибка при изменении задачи', 'Не указано название задачи', 'Закрыть');
      seterror('taskNameInput');
      return;
    }
    document.forms['editTaskForm'].submit();
  }

  var cal = null;
  function showCalendar() {
    cal = new sc2Calendar(new Date(<?=$todate==1 ? $data[0]['tsdate']*1000 : time()*1000?>));
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('rWhen5Label').innerHTML = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('rWhen5').value = cal.selectedDate/1000;
      document.getElementById('darker_id').style.display = 'none';
      document.getElementById('popup_id').style.display = 'none';
    });
    var popup = new sc2Popup();
    popup.showModal('Выберите дату', obj, 'Закрыть');
  }

  function onChooseDateTime() {
    var date = new Date(cal.selectedTime());
    id('rBell2Label').innerHTML = ("0"+date.getDate()).slice(-2)+
                                  '.'+('0'+(date.getMonth()+1)).slice(-2)+
                                  '.'+date.getFullYear()+' '+
                                  date.getHours()+':'+
                                  ("0"+date.getMinutes()).slice(-2);
    id('rBell2').value = date.getTime()/1000;
  }

  function showTimeCalendar() {
    cal = new sc2Calendar(new Date(<?=$data[0]['tsremind']==0 ? time()*1000 : $data[0]['tsremind']*1000?>));
    var popup = new sc2Popup();
    var obj = cal.showMonthCalendarWithTime();
    popup.showModal('Выберите время напоминания', obj, 'Отменить',
                    'Выбрать', onChooseDateTime);
  }

  function back() {
    window.location = '/tasks/view';
  }
</script>

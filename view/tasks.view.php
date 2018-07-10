<div class="mainframe">
  <div class="subheader">Задачи</div>

  <?php CTemplates::showMessage('added', 'Задача добавлена'); ?>
  <?php CTemplates::showMessage('edited', 'Задача изменена'); ?>

  <form class="transpform" action="/tasks/add" method="post" name="newTaskForm">
    <label>Добавить задачу</label>
    <input type="text" id="taskNameInput" name="tsname" value="" />
    <input type="checkbox" id="cbShowMore" class="showmorecheckbox"><label for="cbShowMore" class="showmore">+</label>
    <div class="showmorediv">
      <label>Когда</label>
      <input type="radio" id="rWhen1" class="customRadioButton" name="when" value="today" checked="checked" />
      <label for="rWhen1" class="button">Сегодня</label>
      <input type="radio" id="rWhen2"  class="customRadioButton" name="when" value="tomorrow" />
      <label for="rWhen2" class="button">Завтра</label>
      <input type="radio" id="rWhen3"  class="customRadioButton" name="when" value="thisweek" />
      <label for="rWhen3" class="button">На этой неделе</label>
      <input type="radio" id="rWhen4"  class="customRadioButton" name="when" value="nextweek" />
      <label for="rWhen4" class="button">На следующей неделе</label>
      <input type="radio" id="rWhen5"  class="customRadioButton" name="when" value="date" />
      <label for="rWhen5" id="rWhen5Label" class="button" onclick="showCalendar();">Выбрать дату</label>
      <input type="radio" id="rWhen6"  class="customRadioButton" name="when" value="whenever" />
      <label for="rWhen6" class="button">Когда-нибудь</label>
      <label>Напомнить</label>
      <input type="radio" id="rBell1" class="customRadioButton" name="bell" value="noremind" checked="checked" />
      <label for="rBell1" class="button">Не напоминать</label>
      <input type="radio" id="rBell2"  class="customRadioButton" name="bell" value="time" />
      <label for="rBell2" id="rBell2Label" class="button" onclick="showTimeCalendar();">Выбрать время</label>

      <label>Заметки</label>
      <textarea name="tsnotes"></textarea>

      <label>Цветовой маркер</label>
      <input type="radio" id="rMark1" class="customRadioMarker" name="tscolor" value="0" checked="checked" />
      <label for="rMark1" class="buttonmarker" style="background-color: #FFFFFF;"></label>
      <input type="radio" id="rMark2" class="customRadioMarker" name="tscolor" value="1" />
      <label for="rMark2" class="buttonmarker" style="background-color: #E8C391;"></label>
      <input type="radio" id="rMark3" class="customRadioMarker" name="tscolor" value="2" />
      <label for="rMark3" class="buttonmarker" style="background-color: #FFA395;"></label>
      <input type="radio" id="rMark4" class="customRadioMarker" name="tscolor" value="3" />
      <label for="rMark4" class="buttonmarker" style="background-color: #E391E8;"></label>
      <input type="radio" id="rMark5" class="customRadioMarker" name="tscolor" value="4" />
      <label for="rMark5" class="buttonmarker" style="background-color: #A8AFFF;"></label>
      <input type="radio" id="rMark6" class="customRadioMarker" name="tscolor" value="5" />
      <label for="rMark6" class="buttonmarker" style="background-color: #A7F5FF;"></label>
      <input type="radio" id="rMark7" class="customRadioMarker" name="tscolor" value="6" />
      <label for="rMark7" class="buttonmarker" style="background-color: #90FFB4;"></label>
      <input type="radio" id="rMark8" class="customRadioMarker" name="tscolor" value="7" />
      <label for="rMark8" class="buttonmarker" style="background-color: #CBFF92;"></label>
      <input type="radio" id="rMark9" class="customRadioMarker" name="tscolor" value="8" />
      <label for="rMark9" class="buttonmarker" style="background-color: #9795E8;"></label>
      <input type="radio" id="rMark10" class="customRadioMarker" name="tscolor" value="9" />
      <label for="rMark10" class="buttonmarker" style="background-color: #FFFB97;"></label>

      <br />
      <input type="button" name="newtask" onclick="checkNewTask();" value="Добавить" />
    </div>
  </form>
  <form class="transpform" action="/tasks/view" method="post" name="showchooseform">
    <label>Отображать задачи на</label>
    <input type="radio" id="rShowWhen1" class="customRadioButton"
           name="showwhen" value="today"
           onchange="document.forms['showchooseform'].submit()"
           <?php if(!isset($_POST['showwhen']) || $_POST['showwhen']=='today' ||
                    $_POST['showwhen']=='date') { ?> checked="checked" <?php } ?> />
    <label for="rShowWhen1" class="button">Сегодня</label>
    <input type="radio" id="rShowWhen2"  class="customRadioButton"
           name="showwhen" value="tomorrow"
           onchange="document.forms['showchooseform'].submit()"
           <?php if(isset($_POST['showwhen']) && $_POST['showwhen']=='tomorrow') { ?> checked="checked" <?php } ?> />
    <label for="rShowWhen2" class="button">Завтра</label>
    <input type="radio" id="rShowWhen3"  class="customRadioButton"
           name="showwhen" value="thisweek"
           onchange="document.forms['showchooseform'].submit()"
           <?php if(isset($_POST['showwhen']) && $_POST['showwhen']=='thisweek') { ?> checked="checked" <?php } ?> />
    <label for="rShowWhen3" class="button">На этой неделе</label>
    <input type="radio" id="rShowWhen4"  class="customRadioButton"
           name="showwhen" value="nextweek"
           onchange="document.forms['showchooseform'].submit()"
           <?php if(isset($_POST['showwhen']) && $_POST['showwhen']=='nextweek') { ?> checked="checked" <?php } ?> />
    <label for="rShowWhen4" class="button">На следующей неделе</label>
    <input type="radio" id="rShowWhen5"  class="customRadioButton"
           name="showwhen"
           <?php if(isset($showdate)) { ?> value="<?=$_POST['showwhen']?>" checked="checked" <?php } else { ?>
            value="date" <?php } ?> />
    <label for="rShowWhen5" id="rShowWhen5Label" class="button" onclick="showCalendarForShow();">
           <?php if(isset($showdate)) { ?>
             <?=date('d.m.Y', (int)$_POST['showwhen'])?>
           <?php } else { ?>
            Выбрать дату
           <?php } ?>
    </label>
    <input type="radio" id="rShowWhen6"  class="customRadioButton"
           name="showwhen" value="whenever"
           onchange="document.forms['showchooseform'].submit()"
           <?php if(isset($_POST['showwhen']) && $_POST['showwhen']=='whenever') { ?> checked="checked" <?php } ?> />
    <label for="rShowWhen6" class="button">Когда-нибудь</label>
  </form>


  <?php if(isset($overdue) && count($overdue)) { ?>
  <h3>Просроченные задачи</h3>
  <?php CTemplates::formList(array('','','',''),
                             array(),
                             "listformtableoverdue", false); ?>
  <?php } ?>

  <h3>
  <?php if(isset($_POST['showwhen']) && $_POST['showwhen']=='tomorrow') {
    print 'Завтра';
  } else if(isset($_POST['showwhen']) && $_POST['showwhen']=='thisweek') {
    print 'На этой неделе';
  } else if(isset($_POST['showwhen']) && $_POST['showwhen']=='nextweek') {
    print 'На следующей неделе';
  } else if(isset($_POST['showwhen']) && $_POST['showwhen']=='whenever') {
    print 'Когда-нибудь';
  } else if((isset($_POST['showwhen']) && $_POST['showwhen']=='today') ||
            !isset($_POST['showwhen'])) {
    print 'Сегодня';
  } else {
    print 'На '.date('d.m.Y', $_POST['showwhen']);
  }
  ?>
  </h3>

  <?php if(count($main) == 0) { ?>
    <div class="emptylist">Задач нет</div>
  <?php } ?>

  <?php CTemplates::formList(array('','','',''),
                             array(),
                             "listformtablemain", false); ?>



  <?php if((isset($thisweek) && count($thisweek)) ||
           (isset($soon) && count($soon))) { ?>
  <h3>
  <?php if((isset($_POST['showwhen']) && $_POST['showwhen']=='today') ||
            !isset($_POST['showwhen'])) {
    print 'Скоро';
  } else if(isset($_POST['showwhen']) && $_POST['showwhen']=='thisweek') {
    print 'Также на этой неделе';
  } else if(isset($_POST['showwhen']) && $_POST['showwhen']=='nextweek'){
    print 'Также на следующей неделе';
  }
  ?>
  </h3>

  <?php CTemplates::formList(array('','','',''),
                             array(),
                             "listformtablesoon", false); ?>
  <?php } ?>

</div>

<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2calendar.js"></script>
<script>
  function checkNewTask() {
    if(id('taskNameInput').value.trim() == '') {
      var popup = new sc2Popup();
      popup.showMessage('Ошибка при добавлении задачи', 'Не указано название задачи', 'Закрыть');
      seterror('taskNameInput');
      return;
    }
    document.forms['newTaskForm'].submit();
  }

  var cal = null;
  function showCalendar() {
    cal = new sc2Calendar(new Date());
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

  function showCalendarForShow() {
    cal = new sc2Calendar(new Date());
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('rShowWhen5Label').innerHTML = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('rShowWhen5').value = cal.selectedDate/1000;
      document.forms['showchooseform'].submit();
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
    cal = new sc2Calendar(new Date());
    var popup = new sc2Popup();
    var obj = cal.showMonthCalendarWithTime();
    popup.showModal('Выберите время напоминания', obj, 'Отменить',
                    'Выбрать', onChooseDateTime);
  }

  function onChangeTask(row) {
    window.location = '/tasks/change/'+row.id;
  }

  function onShowMore(row) {
    var popup = new sc2Popup();
    popup.showMessage(row.value, row.tsnotes, 'Закрыть',
                      'Изменить', function() {onChangeTask(row);});
  }

  function onOrderChange(c) {
    var trs = document.getElementById(c.table_id).children;
    var data = '';
    for(var i = 0; i < trs.length; i++) {
      if(trs[i].children[c.orderingColumnNumber] &&
         trs[i].children[c.orderingColumnNumber].children[0] &&
         trs[i].children[c.orderingColumnNumber].children[0].tagName.toLowerCase() == 'input') {
           data += trs[i].children[c.orderingColumnNumber].children[0].name + '=' +
                   trs[i].children[c.orderingColumnNumber].children[0].value + '&';
         }
    }
    data += 'dummy=1';
    SMPAjaxPost('/tasks/setpriority', data, null, null);
  }

  function onDoneSaved(c, o) {
    if(o.checked) {
      c.onStrike(o);
    } else {
      c.onStrikeOut(o);
    }
  }

  function onDone(row, c, o) {
    var tsdone = o.checked ? 1 : 0;
    var data = 'id='+row.id+'&tsdone='+tsdone;
    SMPAjaxPost('/tasks/setdone', data, function(r) {onDoneSaved(c,o);},
                null);
  }

  function onDeleteSaved(c, o) {
    c.onDelete(o);
  }

  function confirmDelete(row, c, o) {
    var data = 'id='+row.id;
    SMPAjaxPost('/tasks/del', data, function(r) {onDeleteSaved(c,o);},
                null);
  }

  function onDelete(row, c, o) {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение', 'Вы действительно желаете удалить задачу?', 'Нет',
                    'Да', function() { confirmDelete(row, c, o); });
  }

  <?php if(isset($overdue) && count($overdue)) { ?>
  var tlod = new sc2TableList('listformtableoverdue');
  tlod.addField('checkbox', 'tsdone');
  tlod.addField('order', 'tspriority');
  tlod.addField('innerHTML', 'tsname', '250px', 1);
  tlod.addField('buttons');

  <?php foreach($overdue as $rec) {
    switch($rec['tscolor']) {
      case '0': default: $bgcolor = '#FFFFFF'; break;
      case '1': $bgcolor = '#E8C391'; break;
      case '2': $bgcolor = '#FFA395'; break;
      case '3': $bgcolor = '#E391E8'; break;
      case '4': $bgcolor = '#A8AFFF'; break;
      case '5': $bgcolor = '#A7F5FF'; break;
      case '6': $bgcolor = '#90FFB4'; break;
      case '7': $bgcolor = '#CBFF92'; break;
      case '8': $bgcolor = '#9795E8'; break;
      case '9': $bgcolor = '#FFFB97'; break;
    } ?>
    tlod.addRecord({id:'<?=$rec['id']?>', value:'<?=$rec['tsname']?>', bgcolor: '<?=$bgcolor?>',
                  checked: '<?=$rec['tsdone']?>', funcCheck: onDone,
                  funcDel: onDelete, funcOrder: onOrderChange,
                  funcClick: onShowMore, tsnotes: '<?=$rec['tsnotes']?>'});
  <?php } } ?>

  <?php if(isset($main) && count($main)) { ?>
  var tl = new sc2TableList('listformtablemain');
  tl.addField('checkbox', 'tsdone');
  tl.addField('order', 'tspriority');
  tl.addField('innerHTML', 'tsname', '250px', 1);
  tl.addField('buttons');

  <?php foreach($main as $rec) {
    switch($rec['tscolor']) {
      case '0': default: $bgcolor = '#FFFFFF'; break;
      case '1': $bgcolor = '#E8C391'; break;
      case '2': $bgcolor = '#FFA395'; break;
      case '3': $bgcolor = '#E391E8'; break;
      case '4': $bgcolor = '#A8AFFF'; break;
      case '5': $bgcolor = '#A7F5FF'; break;
      case '6': $bgcolor = '#90FFB4'; break;
      case '7': $bgcolor = '#CBFF92'; break;
      case '8': $bgcolor = '#9795E8'; break;
      case '9': $bgcolor = '#FFFB97'; break;
    }
    ?>
  tl.addRecord({id:'<?=$rec['id']?>', value:'<?=$rec['tsname']?>', bgcolor: '<?=$bgcolor?>',
                checked: '<?=$rec['tsdone']?>', funcCheck: onDone,
                funcDel: onDelete, funcOrder: onOrderChange,
                funcClick: onShowMore, tsnotes: '<?=$rec['tsnotes']?>'});
  <?php } ?>
    tl.strikeAll();
  <?php } ?>


  <?php if((isset($thisweek) && count($thisweek)) ||
           (isset($soon) && count($soon))) { ?>
  var tl2 = new sc2TableList('listformtablesoon');
  tl2.addField('checkbox', 'tsdone');
  tl2.addField('innerHTML', 'tsname', '250px', 1);

  <?php if(isset($thisweek)) { foreach($thisweek as $rec) {
    switch($rec['tscolor']) {
      case '0': default: $bgcolor = '#FFFFFF'; break;
      case '1': $bgcolor = '#E8C391'; break;
      case '2': $bgcolor = '#FFA395'; break;
      case '3': $bgcolor = '#E391E8'; break;
      case '4': $bgcolor = '#A8AFFF'; break;
      case '5': $bgcolor = '#A7F5FF'; break;
      case '6': $bgcolor = '#90FFB4'; break;
      case '7': $bgcolor = '#CBFF92'; break;
      case '8': $bgcolor = '#9795E8'; break;
      case '9': $bgcolor = '#FFFB97'; break;
    } ?>
  tl2.addRecord({id:'<?=$rec['id']?>', value:'<?=$rec['tsname']?>', bgcolor: '<?=$bgcolor?>',
                funcCheck: onDone, funcClick: onShowMore, tsnotes: '<?=$rec['tsnotes']?>'});
  <?php } } ?>
  <?php if(isset($soon)) { foreach($soon as $rec) {
    switch($rec['tscolor']) {
      case '0': default: $bgcolor = '#FFFFFF'; break;
      case '1': $bgcolor = '#E8C391'; break;
      case '2': $bgcolor = '#FFA395'; break;
      case '3': $bgcolor = '#E391E8'; break;
      case '4': $bgcolor = '#A8AFFF'; break;
      case '5': $bgcolor = '#A7F5FF'; break;
      case '6': $bgcolor = '#90FFB4'; break;
      case '7': $bgcolor = '#CBFF92'; break;
      case '8': $bgcolor = '#9795E8'; break;
      case '9': $bgcolor = '#FFFB97'; break;
    } ?>
  tl2.addRecord({id:'<?=$rec['id']?>', value:'<?=$rec['tsname']?>', bgcolor: '<?=$bgcolor?>',
                funcCheck: onDone, funcClick: onShowMore, tsnotes: '<?=$rec['tsnotes']?>'});
  <?php } } ?>

  <?php } ?>

</script>

<div class="mainframe">

  <div class="subheader">Написать сообщение</div>

  <form class="transpform" action="/messages/post" method="post" id="postform">
    <label>Получатель</label>
    <input type="hidden" name="user_id" id="user_id" value="<?=$answer?$message['sender_id']:0?>" />
    <input type="button" id="usname" value="<?=$answer?$message['usname']:'Выбрать получателя'?>"
            onclick="onSelectTeacher();" />
    <br /><br />
    <label>Тема сообщения</label>
    <input type="text" id="mstopic" name="mstopic"
           value="<?=$answer?'Ответ на: '.$message['mstopic']:''?>" />
    <label>Текст сообщения</label>
    <textarea name="mstext" id="mstext" class="autoresizable"></textarea>
    <input type="button" value="Отправить" onclick="onSave();" />
    <input type="button" value="Отменить" onclick="window.location='/messages';" />
  </form>

  <form class="transpform" id="selectteacher">
    <div id="teacherChoose">
      <label>Область:</label>
      <select onchange="regionSelected(this.options[this.selectedIndex])" id="region_id">
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

      <label>Получатель:</label>
      <select name="teacher_id" id="teacher_id">
          <option value="0" selected style="display: none;"></option>
      </select>
    </div>

    <p>Если вы знаете учетную запись получателя, вы можете записать ее в поле ниже,
    чтобы не выбирать из списка</p>
    <div class="message" id="message_id"></div>
    <input type="text" name="tcinfo" id="uslogin" />

  </form>

</div>

<script>

  var oSelectTeacher = document.getElementById('selectteacher');
  oSelectTeacher = oSelectTeacher.parentNode.removeChild(oSelectTeacher);

  function onSelectTeacher() {
    var popup = new sc2Popup();
    popup.showModal('Выбрать получателя', oSelectTeacher, 'Закрыть', 'Выбрать',
                    function() { checkuser(); });

    if(document.getElementById('user_id').value != 0) {  // edited
      findteacher(document.getElementById('user_id').value);
    } else {
      document.getElementById('uslogin').value = '';
      clearSelect('district_id');
      clearSelect('school_id');
      clearSelect('teacher_id');
    }
  }

  function regionSelected(obj) {
      SMPAjaxGet('/index.php?section=users&action=getdistricts&id='+obj.value, function(res) {
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

  function findteacher(teacher_id) {
    SMPAjaxGet('/index.php?section=users&action=findteacher&id='+teacher_id, function(res) {
        clearSelect('teacher_id');
        clearSelect('district_id');
        clearSelect('school_id');
        var x = res.documentElement.getElementsByTagName('selected');
        var school_id = x[0].getAttribute('school_id');
        var district_id = x[0].getAttribute('district_id');
        var region_id = x[0].getAttribute('region_id');

        x = res.documentElement.getElementsByTagName('teacher');
        for(var i = 0; i < x.length; i++) {
            addOption('teacher_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue, teacher_id);
        }
        x = res.documentElement.getElementsByTagName('school');
        for(var i = 0; i < x.length; i++) {
            addOption('school_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue, school_id);
        }
        x = res.documentElement.getElementsByTagName('region');
        for(var i = 0; i < x.length; i++) {
            addOption('district_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue, district_id);
        }

        var obj = document.getElementById("region_id");
        var l = obj.children.length;
        var i;
        for(i = 0; i < l; i++) {
          if(obj.children[i].value && obj.children[i].value == region_id) {
            obj.children[i].selected = 'selected';
          }
        }

    }, true);
  }

  function checkuser() {
    if(value('uslogin') != '') {
      SMPAjaxPost('/index.php?section=users&action=checklogin',
                  'uslogin='+encodeURIComponent(value('uslogin')), function(res) {
        var x = res.documentElement;
        if(x.getAttribute('isexists')=="true") {
          document.getElementById('user_id').value = res.documentElement.getElementsByTagName('id')[0].firstChild.nodeValue;
          document.getElementById('usname').value = res.documentElement.getElementsByTagName('usname')[0].firstChild.nodeValue;
        } else {
          seterror('uslogin');
          var popup2 = new sc2Popup();
          popup2.showMessage('Ошибка', 'Введенная учетная запись не существует',
                             'Закрыть', null, null, onSelectTeacher);
        }
      }, true);
    } else {
      if(selvalue('teacher_id') == 0) {
        seterror('teacher_id');
        var popup2 = new sc2Popup();
        popup2.showMessage('Ошибка', 'Не выбран получатель',
                           'Закрыть', null, null, onSelectTeacher);
      } else {
        document.getElementById('user_id').value = selvalue('teacher_id');
        document.getElementById('usname').value = document.getElementById('teacher_id').options[document.getElementById('teacher_id').selectedIndex].innerHTML;
      }
    }
  }

  function onSave() {
    var popup = new sc2Popup();
    if(value('user_id')==0) {
      popup.showMessage('Ошибка отправки сообщения', 'Не указан получатель сообщения', 'Закрыть');
      seterror('user_id');
      return;
    } else if(value('mstopic').trim()=='') {
      popup.showMessage('Ошибка отправки сообщения', 'Не указана тема сообщения', 'Закрыть');
      seterror('mstopic');
      return;
    }

    document.getElementById('postform').submit();
  }

  makeTextareaAutoresizable();
</script>

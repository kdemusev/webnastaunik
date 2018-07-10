<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Учащиеся учреждения образования</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

    <?php CTemplates::formList(array('','','Фамилия, имя, отчество', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
                               array("form_id" => $form_id)); ?>

    <div id="additional_info" class="transpform" style="display: none;">
      <label>Фамилия, имя</label>
      <input type="text" readonly id="add_ppname" />
      <label>Отчество</label>
      <input type="text" id="add_ppsurname" />
      <label>Пол</label>
      <div class="checkboxes">
        <input type="radio" id="add_sexm" class="customRadioButton" name="rSex"
               value="0"/>
        <label for="add_sexm" class="button">мужской</label>
        <input type="radio" id="add_sexw"  class="customRadioButton" name="rSex"
               value="1"/>
        <label for="add_sexw" class="button">женский</label>
      </div>
      <label>Дата рождения</label>
      <div class="checkboxes">
        <input type="hidden" id="add_ppbirth" />
        <input type="button" id="add_ppbirthshow" onclick="chooseDate(this);" />
      </div>
      <label>Домашний адрес</label>
      <input type="text" id="add_ppaddress" />
      <label>Фамилия, имя, отчество матери</label>
      <input type="text" id="add_ppmother" />
      <label>Фамилия, имя, отчество отца</label>
      <input type="text" id="add_ppfather" />
      <label>Место работы матери</label>
      <input type="text" id="add_ppmotherplace" />
      <label>Место работы отца</label>
      <input type="text" id="add_ppfatherplace" />
      <label>Мобильный телефон учащегося</label>
      <input type="text" id="add_ppphone" />
      <label>Мобильный телефон матери</label>
      <input type="text" id="add_ppmotherphone" />
      <label>Мобильный телефон отца</label>
      <input type="text" id="add_ppfatherphone" />
      <label>Домашний телефон</label>
      <input type="text" id="add_pphomephone" />
      <label>Группа здоровья</label>
      <select id="add_pphealth">
        <option value="1">I</option>
        <option value="2">II</option>
        <option value="3">III</option>
        <option value="4">IV</option>
        <option value="5">V</option>
      </select>
      <label>Медицинская группа</label>
      <select id="add_ppphyz">
        <option value="1">основная</option>
        <option value="2">подготовительная</option>
        <option value="3">специальная подготовительная</option>
      </select>
      <label>Примечания</label>
      <textarea id="add_ppnotes" class="autoresizable"></textarea>
    </div>

</div>

<script src="/js/sc2calendar.js"></script>
<script src="/js/sc2tablelist.js"></script>
<script>
  makeTextareaAutoresizable();

  var addinfo = document.getElementById('additional_info').parentNode.removeChild(document.getElementById('additional_info'));

  var tl = new sc2TableList('listformtable');
  tl.addField('number');
  tl.addField('order', 'pppriority');
  tl.addField('text', 'ppname', '250px', 1);
  tl.addField('editbuttons');
  tl.addField('hidden', 'ppsurname');
  tl.addField('hidden', 'ppsex');
  tl.addField('hidden', 'ppbirth');
  tl.addField('hidden', 'ppaddress');
  tl.addField('hidden', 'ppmother');
  tl.addField('hidden', 'ppfather');
  tl.addField('hidden', 'ppmotherplace');
  tl.addField('hidden', 'ppfatherplace');
  tl.addField('hidden', 'ppphone');
  tl.addField('hidden', 'ppmotherphone');
  tl.addField('hidden', 'ppfatherphone');
  tl.addField('hidden', 'pphomephone');
  tl.addField('hidden', 'pphealth');
  tl.addField('hidden', 'ppphyz');
  tl.addField('hidden', 'ppnotes');

  <?php
  if(isset($_data) && count($_data) > 0) {
    foreach($_data as $rec) {?>
      tl.addRecord({id:'<?=$rec['id']?>', value: '<?=s_q($rec['ppname'])?>',
                    ppsurname: new String('<?=s_q($rec['ppsurname'])?>'),
                    ppsex: new String('<?=s_q($rec['ppsex'])?>'),
                    ppbirth: new String('<?=s_q($rec['ppbirth'])?>'),
                    ppaddress: new String('<?=s_q($rec['ppaddress'])?>'),
                    ppmother: new String('<?=s_q($rec['ppmother'])?>'),
                    ppfather: new String('<?=s_q($rec['ppfather'])?>'),
                    ppmotherplace: new String('<?=s_q($rec['ppmotherplace'])?>'),
                    ppfatherplace: new String('<?=s_q($rec['ppfatherplace'])?>'),
                    ppphone: new String('<?=s_q($rec['ppphone'])?>'),
                    ppmotherphone: new String('<?=s_q($rec['ppmotherphone'])?>'),
                    ppfatherphone: new String('<?=s_q($rec['ppfatherphone'])?>'),
                    pphomephone: new String('<?=s_q($rec['pphomephone'])?>'),
                    pphealth: new String('<?=s_q($rec['pphealth'])?>'),
                    ppphyz: new String('<?=s_q($rec['ppphyz'])?>'),
                    ppnotes: new String('<?=s_q($rec['ppnotes'])?>'),
                    funcEdit: onEdit});
    <?php }
  }?>

  tl.addEmpty({funcEdit: onEdit, isnew: 1});

  function onSave(row, o, el) {
    var tr = el.parentNode.parentNode;
    tr.children[4].children[0].value = document.getElementById('add_ppsurname').value;
    tr.children[5].children[0].value = document.getElementById('add_sexm').checked ? 0 : 1;
    tr.children[6].children[0].value = document.getElementById('add_ppbirth').value;
    tr.children[7].children[0].value = document.getElementById('add_ppaddress').value;
    tr.children[8].children[0].value = document.getElementById('add_ppmother').value;
    tr.children[9].children[0].value = document.getElementById('add_ppfather').value;
    tr.children[10].children[0].value = document.getElementById('add_ppmotherplace').value;
    tr.children[11].children[0].value = document.getElementById('add_ppfatherplace').value;
    tr.children[12].children[0].value = document.getElementById('add_ppphone').value;
    tr.children[13].children[0].value = document.getElementById('add_ppmotherphone').value;
    tr.children[14].children[0].value = document.getElementById('add_ppfatherphone').value;
    tr.children[15].children[0].value = document.getElementById('add_pphomephone').value;
    tr.children[16].children[0].value = document.getElementById('add_pphealth').selectedIndex+1;
    tr.children[17].children[0].value = document.getElementById('add_ppphyz').selectedIndex+1;
    tr.children[18].children[0].value = document.getElementById('add_ppnotes').value;
  }

  function onEdit(row, o, el) {
    addinfo.style.display = 'block';

    var popup = new sc2Popup();
    popup.showModal('Дополнительные сведения', addinfo, 'Отменить', 'Сохранить', function() { onSave(row, o, el); });
    var tr = el.parentNode.parentNode;
    if(row.isnew) {
      document.getElementById('add_ppname').value = tr.children[2].children[0].value;
      document.getElementById('add_ppsurname').value = '';
      document.getElementById('add_sexm').checked = 'true';
      document.getElementById('add_ppbirth').value = '0';
      document.getElementById('add_ppbirthshow').value = 'Выбрать дату';
      document.getElementById('add_ppaddress').value = '';
      document.getElementById('add_ppmother').value = '';
      document.getElementById('add_ppfather').value = '';
      document.getElementById('add_ppmotherplace').value = '';
      document.getElementById('add_ppfatherplace').value = '';
      document.getElementById('add_ppphone').value = '';
      document.getElementById('add_ppmotherphone').value = '';
      document.getElementById('add_ppfatherphone').value = '';
      document.getElementById('add_pphomephone').value = '';
      document.getElementById('add_pphealth').selectedIndex = 0;
      document.getElementById('add_ppphyz').selectedIndex = 0;
      document.getElementById('add_ppnotes').value = '';
    } else {
      document.getElementById('add_ppname').value = tr.children[2].children[0].value;
      document.getElementById('add_ppsurname').value = tr.children[4].children[0].value;
      if(tr.children[5].children[0].value == '0') {
        document.getElementById('add_sexm').checked = 'true';
      } else {
        document.getElementById('add_sexw').checked = 'true';
      }
      var dbirth = new Date(tr.children[6].children[0].value*1000);
      document.getElementById('add_ppbirth').value = tr.children[6].children[0].value*1000;
      if(tr.children[6].children[0].value > 0) {
        document.getElementById('add_ppbirthshow').value = dbirth.getDate()+
                                      '.'+('0'+(dbirth.getMonth()+1)).slice(-2)+
                                      '.'+dbirth.getFullYear();
      } else {
        document.getElementById('add_ppbirthshow').value = 'Выбрать дату';
      }
      document.getElementById('add_ppaddress').value = tr.children[7].children[0].value;
      document.getElementById('add_ppmother').value = tr.children[8].children[0].value;
      document.getElementById('add_ppfather').value = tr.children[9].children[0].value;
      document.getElementById('add_ppmotherplace').value = tr.children[10].children[0].value;
      document.getElementById('add_ppfatherplace').value = tr.children[11].children[0].value;
      document.getElementById('add_ppphone').value = tr.children[12].children[0].value;
      document.getElementById('add_ppmotherphone').value = tr.children[13].children[0].value;
      document.getElementById('add_ppfatherphone').value = tr.children[14].children[0].value;
      document.getElementById('add_pphomephone').value = tr.children[15].children[0].value;
      document.getElementById('add_pphealth').selectedIndex = tr.children[16].children[0].value-1;
      document.getElementById('add_ppphyz').selectedIndex = tr.children[17].children[0].value-1;
      document.getElementById('add_ppnotes').value = tr.children[18].children[0].value;
    }
  }

  function chooseDate(after) {
    var cal;
    if(document.getElementById('add_ppbirth').value > 0) {
      var dt = new Date();
      dt.setTime(document.getElementById('add_ppbirth').value);
      cal = new sc2Calendar(dt);
    } else {
      cal = new sc2Calendar(new Date());
    }
    var obj = cal.showMonthCalendar(function() {
      var date = new Date(cal.selectedDate);
      id('add_ppbirthshow').value = date.getDate()+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
      id('add_ppbirth').value = cal.selectedDate/1000;
      obj.parentNode.removeChild(obj);
    });
    after.parentNode.insertBefore(obj, after.nextSibling);
  }
</script>

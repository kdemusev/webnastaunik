<div class="mainframe">

  <div class="subheader">Результаты участия в олимпиадах</div>

  <?php CTemplates::showMessage('added', 'Информация добавлена'); ?>
  <?php CTemplates::showMessage('subjdeleted', 'Информация о предмете удалена'); ?>
  <?php CTemplates::showMessage('subjchanged', 'Информация о предмете изменена'); ?>
  <?php CTemplates::showMessage('fiochanged', 'Фамилия, имя учащегося изменены'); ?>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/dbolymp/add';" value="Добавить результат II этапа" />
  <input type="button" name="newtask" onclick="window.location='/dbolymp/reports';" value="Сводная информация" />
  </form>

  <?php
    $olymptypes[0]['id'] = 1; $olymptypes[0]['otname'] = 'Республиканская';
    $olymptypes[1]['id'] = 2; $olymptypes[1]['otname'] = 'Областная';
    $olymptypes[2]['id'] = 3; $olymptypes[2]['otname'] = 'Районная';
    CTemplates::chooseBar('Тип олимпиады', 'olymptype', $olymptypes, $_SESSION['olymptype'], 'otname'); ?>
  <?php CTemplates::chooseBar('Учебный год', 'olymp_year_id', $years, $_SESSION['olymp_year_id'], 'oyname'); ?>

  <form class="transpform">
    <label>Фильтр:</label>
    <input type="radio" id="rf1" class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="fio" checked="checked" />
    <label for="rf1" class="button">По фамилии, имени ученика</label>
    <input type="radio" id="rf2"  class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="school" />
    <label for="rf2" class="button">По учреждению образования</label><br />
    <input type="text" id="filterbox" onkeyup="filter(this);" onchange="filter(this);" autocomplete="off" />
  </form>

  <table class="sc3table overablerow" id="olymptable">
    <thead>
      <tr>
        <th>Количество предметов</th>
        <th>Фамилия, имя ученика</th>
        <th>Класс</th>
        <th>Учреждение образования</th>
        <th>Предметы</th>
      </tr>
    </thead>
    <?php if(count($olymp_data)) { foreach($olymp_data as $rec) { ?>
      <tr>
        <td align="center">
          <?=$rec['subjcount']?>
        </td>
        <td>
          <?=$rec['opname']?> <img src="/style/icons/admin.edit.full.png" onclick="changeFio('<?=$rec['olymp_id']?>')" />
        </td>
        <td align="center">
          <?=$rec['ofname']?>
        </td>
        <td nowrap>
          <?=$rec['oscname']?>
        </td>
        <td nowrap>
          <?php foreach($olymp_subjects as $rec2) { ?>
            <?php if($rec2['olymp_pupil_id']==$rec['olymp_pupil_id']) { ?>
              <?=$rec2['osname']?> - учитель <?=$rec2['otname']?>
              <img src="/style/icons/admin.edit.full.png" onclick="changeSubject('<?=$rec2['olymp_id']?>')" />
              <img src="/style/icons/admin.delete.full.png" onclick="deleteSubject('<?=$rec2['olymp_id']?>')" /><br />
            <?php } ?>
          <?php } ?>
        </td>
      </tr>
    <?php } } else { ?>
      <tr>
        <td colspan="5" align="center">
          <i>Не найдено записей по заданному условию</i>
        </td>
      </tr>
    <?php } ?>
  </table>

</div>


<script>
  function changeSubject(s_id) {
    window.location = '/dbolymp/editsubj/' + s_id;
  }

  function changeFio(s_id) {
    window.location = '/dbolymp/editfio/' + s_id;
  }

  function deleteSubject(s_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление предмета', 'Вы действительно хотите удалить эти результаты олимпиады?',
                      'Нет', 'Да', function() { onDeleteSubject(s_id); });
  }

  function onDeleteSubject(s_id) {
    window.location = '/dbolymp/delsubj/' + s_id;
  }

  function filter(obj) {
    var coln = document.getElementById('rf1').checked ? 1 : 3;
    var text = obj.value.toLowerCase().trim();
    var tbl = document.getElementById('olymptable');
    var i;
    var l = tbl.children[1].children.length;
    var td;

    for(i = 0; i < l; i++) {
        td = tbl.children[1].children[i].children[coln];
        if(td.textContent.toLowerCase().indexOf(text) >= 0) {
          td.parentNode.style.display = 'table-row';
        } else {
          td.parentNode.style.display = 'none';
        }
    }

  }
</script>

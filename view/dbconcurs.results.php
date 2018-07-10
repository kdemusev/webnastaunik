<div class="mainframe">

  <div class="subheader">Результаты участия в конкурсах</div>

  <?php CTemplates::showMessage('added', 'Информация добавлена'); ?>
  <?php CTemplates::showMessage('pupiladded', 'Участник конкурса добавлен'); ?>
  <?php CTemplates::showMessage('concursdeleted', 'Информация о конкурсе удалена'); ?>
  <?php CTemplates::showMessage('concurschanged', 'Информация о конкурсе изменена'); ?>
  <?php CTemplates::showMessage('fiochanged', 'Фамилия, имя учащегося изменены'); ?>
  <?php CTemplates::showMessage('fiodeleted', 'Участник конкурса удален'); ?>

  <form class="transpform">
  <input type="button" name="newtask" onclick="window.location='/dbconcurs/add';" value="Добавить результат конкурса" />
  <input type="button" name="newtask" onclick="window.location='/dbconcurs/reports';" value="Сводная информация" />
  </form>

  <?php
    $olymptypes[0]['id'] = 0; $olymptypes[0]['сtname'] = 'Все';
    $olymptypes[1]['id'] = 1; $olymptypes[1]['сtname'] = 'Районный';
    $olymptypes[2]['id'] = 2; $olymptypes[2]['сtname'] = 'Региональный';
    $olymptypes[3]['id'] = 3; $olymptypes[3]['сtname'] = 'Областной';
    $olymptypes[4]['id'] = 4; $olymptypes[4]['сtname'] = 'Республиканский';
    CTemplates::chooseBar('Уровень проведения', 'concurstype', $olymptypes, $_SESSION['concurstype'], 'сtname'); ?>
  <?php CTemplates::chooseBar('Учебный год', 'olymp_year_id', $years, $_SESSION['olymp_year_id'], 'oyname'); ?>

  <form class="transpform">
    <label>Фильтр:</label>
    <input type="radio" id="rf1" class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="fio" checked="checked" />
    <label for="rf1" class="button">По фамилии, имени учащегося</label>
    <input type="radio" id="rf2"  class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="school" />
    <label for="rf2" class="button">По учреждению образования</label>
    <input type="radio" id="rf3"  class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="school" />
    <label for="rf3" class="button">По названию конкурса</label><br />
    <input type="text" id="filterbox" onkeyup="filter(this);" onchange="filter(this);" autocomplete="off" />
  </form>

  <table class="sc3table overablerow" id="olymptable">
    <thead>
      <tr>
        <th>Название конкурса</th>
        <th>Секция</th>
        <th>Участники конкурса</th>
        <th>Учреждение образования</th>
        <th>Название работы</th>
      </tr>
    </thead>
    <?php if(count($data)) { foreach($data as $rec) { ?>
      <tr>
        <td>
          <?=$rec['ctname']?>
        </td>
        <td align="center">
          <?=$rec['csname']?>
        </td>
        <td align="center">
          <?php foreach($data_pupils[$rec['ctid']] as $rec2) { ?>
            <?=$rec2['opname']?> (<?=$rec2['ofname']?> класс)
            <img src="/style/icons/admin.edit.full.png" onclick="changeFio('<?=$rec2['cpid']?>')" />
            <img src="/style/icons/admin.delete.full.png" onclick="delFio('<?=$rec2['cpid']?>')" />
            <br>
          <?php } ?>
          <a href="/dbconcurs/addpupil/<?=$rec['ctid']?>">добавить участника</a>
        </td>
        <td nowrap>
          <?=$rec['oscname']?>
        </td>
        <td nowrap>
          <?=$rec['cnname']?>
          <img src="/style/icons/admin.edit.full.png" onclick="changeConcurs('<?=$rec['ctid']?>')" />
          <img src="/style/icons/admin.delete.full.png" onclick="deleteConcurs('<?=$rec['ctid']?>')" />
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
  function changeConcurs(s_id) {
    window.location = '/dbconcurs/editconcurs/' + s_id;
  }

  function changeFio(s_id) {
    window.location = '/dbconcurs/editfio/' + s_id;
  }

  function delFio(s_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление участника конкурса', 'Вы действительно хотите удалить этого участника конкурса?',
                      'Нет', 'Да', function() { onDeleteFio(s_id); });
  }

  function onDeleteFio(s_id) {
    window.location = '/dbconcurs/delfio/' + s_id;
  }

  function deleteConcurs(s_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление конкурса', 'Вы действительно хотите удалить этот конкурс?',
                      'Нет', 'Да', function() { onDeleteConcurs(s_id); });
  }

  function onDeleteConcurs(s_id) {
    window.location = '/dbconcurs/delconcurs/' + s_id;
  }

  function filter(obj) {
    var coln = document.getElementById('rf1').checked ? 2 : (document.getElementById('rf2').checked ? 3 : 0);
    var text = obj.value.toLowerCase().trim();
    var tbl = document.getElementById('olymptable');
    var i;
    var l = tbl.children[1].children.length;
    var td;

    for(i = 0; i < l; i++) {
        td = tbl.children[1].children[i].children[coln];
        if(!td) { continue }
        if(td.textContent.toLowerCase().indexOf(text) >= 0) {
          td.parentNode.style.display = 'table-row';
        } else {
          td.parentNode.style.display = 'none';
        }
    }

  }
</script>

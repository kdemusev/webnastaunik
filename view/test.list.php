<div class="mainframe">
  <div class="subheader">Тестирование</div>

  <?php CTemplates::showMessage('testadded', 'Новый тест создан'); ?>
  <?php CTemplates::showMessage('testchanged', 'Тест изменен'); ?>
  <?php CTemplates::showMessage('testdeleted', 'Тест удален'); ?>

  <form class="transpform">
    <input type="button" value="Список тестов" class="checked" />
    <input type="button" onclick="window.location='/test/add';" value="Создать Тест" />
  </form>

  <?php if(count($testdata) > 0) { ?>
  <div class="topics">
    <?php foreach($testdata as $rec) { ?>
    <div class="topic">
      <div class="desc">
        <?php if($user_rights == 99 || $rec['user_id'] == $user_id) { ?>
        <button class="adminbuttonnew" onclick="deleteTest(<?=$rec['id']?>);">Удалить</button>
        <button class="adminbuttonnew" onclick="window.location='/test/edit/<?=$rec['id']?>';">Изменить</button>
        <?php } ?>
        <a href="/test/show/<?=$rec['id']?>"><?=$rec['tsname']?> (код: <?=$rec['tscode']?>)</a>
        <?=$rec['tsdesc']?>
        <br />
      </div>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <div class="emptylist">Не создано ни одного теста</div>
  <?php } ?>
</div>



<script>
  function deleteTest(test_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление теста', 'Вы действительно хотите удалить тест и все его результаты?',
                      'Нет', 'Да', function() { window.location = '/test/delete/'+test_id; });
  }

</script>

<div class="mainframe">
  <div class="subheader">Результаты тестирования &quot;<?=$data[0]['tsname']?>&quot;</div>

  <?php CTemplates::showMessage('deleted', 'Результат удален'); ?>

  <form class="transpform">
    <input type="button" onclick="window.location='/test/showlist';" value="Список тестов" />
    <input type="button" onclick="window.location='/test/showanalys/<?=$data[0]['id']?>';" value="Сводный анализ выполнения теста" />
  </form>

  <p>Всего ответов: <b><?=count($results)?></b></p>

  <?php if(count($results) > 0) { ?>
    <?php foreach($results as $rec) { ?>
      <?=$rec['usname']?> (<?=$rec['usplace']?>, <?=$rec['scname']?>) <?=$rec['trcount']?> из <?=$total?> (<b><?=$rec['trpercent']?>%</b>)
      <a href="/test/showdetails/<?=$rec['trid']?>">просмотр</a> <a onclick="delResult('<?=$rec['trid']?>');">удалить</a><br>
    <?php } ?>
  <?php } else { ?>
    <div class="emptylist">Тест не проходил ни один педагогический работник</div>
  <?php } ?>
</div>

<script>
  function delResult(result_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление результата', 'Вы действительно хотите удалить результат пользователя?',
                      'Нет', 'Да', function() { window.location = '/test/delresult/'+result_id; });
  }
</script>

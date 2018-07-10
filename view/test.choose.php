<div class="mainframe">
  <div class="subheader">Выбор теста</div>

  <?php CTemplates::showMessage('wrongcode', 'Неправильно введен код теста'); ?>
  <?php CTemplates::showMessage('nomore', 'Тест Вами уже выполнялся'); ?>

  <form class="transpform" method="post" action="/test/view" >
    <label>Введите код теста:</label>
    <input type="text" name="tscode" />

    <label></label>

    <input type="submit" name="save" value="Продолжить" />
  </form>

</div>

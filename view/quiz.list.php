<div class="mainframe">
  <div class="subheader">Опросы</div>

  <?php CTemplates::showMessage('quizadded', 'Новый опрос создан'); ?>
  <?php CTemplates::showMessage('quizchanged', 'Опрос успешно изменен'); ?>
  <?php CTemplates::showMessage('quizdeleted', 'Опрос удален'); ?>

  <form class="transpform">
    <input type="button" value="Список опросов" class="checked" />
    <input type="button" onclick="window.location='/quiz/add';" value="Создать опрос" />
  </form>

  <?php if(count($quizdata) > 0) { ?>
  <div class="topics">
    <?php foreach($quizdata as $rec) { ?>
    <div class="topic">
      <div class="desc">
        <?php if($user_rights == 99 || $rec['user_id'] == $user_id) { ?>
        <button class="adminbuttonnew" onclick="deleteQuiz(<?=$rec['id']?>);">Удалить</button>
        <button class="adminbuttonnew" onclick="window.location='/quiz/edit/<?=$rec['id']?>';">Изменить</button>
        <?php } ?>
        <a href="/quiz/show/<?=$rec['id']?>"><?=$rec['qzname']?> <?=(($rec['qzonce']==0) ? '(один ответ)' : '(без ограничений)')?></a>
        <?=$rec['qzdesc']?>
        <br />
      </div>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <div class="emptylist">Не создано ни одного опроса</div>
  <?php } ?>
</div>



</div>

<script>
  function deleteQuiz(quiz_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление опроса', 'Вы действительно хотите удалить опрос и все его результаты?',
                      'Нет', 'Да', function() { window.location = '/quiz/delete/'+quiz_id; });
  }

</script>

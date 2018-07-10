<div class="mainframe">
  <div class="subheader">Опрос "<?=$quizdata[0]['qzname']?>"</div>

<p>Для вставки опроса на страничку воспользуйтесь следующим кодом:</p>
<pre>&lt;iframe src="/quiz/showiframe/<?=$quizdata[0]['id']?>" style="border:0px;width:100%;" onload="this.style.height=this.contentDocument.body.scrollHeight +'px';"&gt;&lt;/iframe&gt;</pre>

  <?php CTemplates::showMessage('quizaccepted', 'Ваши ответы приняты'); ?>

  <form class="transpform">
    <input type="button" onclick="window.location='/quiz/showlist';"  value="Список опросов"/>
    <input type="button" onclick="window.location='/quiz/edit/<?=$quizdata[0]['id']?>';" value="Изменить опрос"  />
    <input type="button" onclick="deleteQuiz('<?=$quizdata[0]['id']?>');" value="Удалить опрос" />
  </form>

  <p><?=$quizdata[0]['qzdesc']?></p>

  <form class="transpform" action="/quiz/answer" method="post"
        name="quizForm">

    <input type="hidden" name="quiz_id" value="<?=$quizdata[0]['id']?>" />

    <?php foreach($qzquestions as $rec) { ?>
      <label><?=$rec['qqtext']?></label>
      <?php foreach($rec['answers'] as $rec2) { ?>
      <input type="radio" name="results[<?=$rec['id']?>]" value="<?=$rec2['qaid']?>" /><?=$rec2['qatext']?> - <b><?=$rec2['cnt']?> голосов (<?=$rec2['total']?>%)</b><br />
      <?php } ?><br />
    <?php } ?>

    <input type="button" onclick="checkInput();" value="Отправить результаты" />
  </form>


</div>

<script>

  function checkInput() {
    var iResult = 1;
    var i;
    var inp;
    var onechecked;
    <?php foreach($qzquestions as $rec) { ?>
    inp = document.getElementsByName('results[<?=$rec['id']?>]');
    onechecked = 0;
    for(i = 0; i < inp.length; i++) {
      if(inp[i].type == 'radio' && inp[i].checked) {
        onechecked = 1;
      }
    }
    iResult = iResult * onechecked;
    <?php } ?>

    if(iResult == 0) {
      var popup = new sc2Popup();
      popup.showMessage('Невозможно сохранить результаты', 'Вы не ответили на все вопросы',
                        'Закрыть', null, null);
      return false;
    }

    document.forms['quizForm'].submit();
    return true;
  }

  function back() {
    window.location = '/quiz/showlist';
  }

  function deleteQuiz(quiz_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление опроса', 'Вы действительно хотите удалить опрос и все его результаты?',
                      'Нет', 'Да', function() { window.location = '/quiz/delete/'+quiz_id; });
  }
</script>

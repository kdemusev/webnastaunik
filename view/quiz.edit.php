<div class="mainframe">
  <div class="subheader">Изменить опрос</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/quiz/showlist';"  value="Список опросов"/>
    <input type="button" onclick="window.location='/quiz/show/<?=$quizdata[0]['id']?>';" value="Просмотр опроса" />
  </form>

  <form class="transpform" action="/quiz/change" method="post"
        name="quizForm">

    <input type="hidden" name="quiz_id" value="<?=$quizdata[0]['id']?>" />

    <label>Название</label>
    <input type="text" id="quizNameInput" name="qzname" value="<?=$quizdata[0]['qzname']?>" />

    <label>Описание опроса</label>
    <textarea name="qzdesc"><?=$quizdata[0]['qzdesc']?></textarea>

    <label>Ответ одного пользователя</label>
    <input type="radio" id="rType1" class="customRadioButton" name="qzonce"
           value="0" <?=$quizdata[0]['qzonce']==0 ? 'checked' : ''?>/>
    <label for="rType1" class="button">Один раз</label>
    <input type="radio" id="rType2"  class="customRadioButton" name="qzonce"
           value="1" <?=$quizdata[0]['qzonce']==1 ? 'checked' : ''?>/>
    <label for="rType2" class="button">Много раз</label>
    <br /><br />

    <label>Показывать результаты</label>
    <input type="radio" id="rType3" class="customRadioButton" name="qzshowresults"
           value="0" <?=$quizdata[0]['qzshowresults']==0 ? 'checked' : ''?> />
    <label for="rType3" class="button">После ответа пользователям</label>
    <input type="radio" id="rType4"  class="customRadioButton" name="qzshowresults"
           value="1" <?=$quizdata[0]['qzshowresults']==1 ? 'checked' : ''?> />
    <label for="rType4" class="button">Только администратору</label>
    <br /><br />

    <label>Сообщение после сохранения результатов</label>
    <textarea name="qzthank"><?=$quizdata[0]['qzthank']?></textarea>

    <h3>Вопросы:</h3>
    <?php $i = 1; foreach($qzquestions as $rec) { ?>
    <label>Вопрос №<?=$i++?></label>
    <input type="text" name="qqtext[<?=$rec['id']?>]" value="<?=$rec['qqtext']?>" />
    <label>Варианты ответа</label>
    <?php foreach($rec['answers'] as $rec2) { ?>
    <input type="text" name="qatext[<?=$rec['id']?>][<?=$rec2['id']?>]" value="<?=$rec2['qatext']?>" /><br />
    <?php } ?>
    <input type="text" name="qatextnew[<?=$rec['id']?>][]" onkeydown="newAnswer(<?=$rec['id']?>, this);"/><br />
    <?php } ?>
    <label>Вопрос №<?=$i?></label>
    <input type="text" name="qqtextnew[0]" onkeydown="newQuestion(0, this);" />
    <label>Варианты ответа</label>
    <input type="text" name="qatextnew[0][]" onkeydown="newAnswer(0, this);" /><br />
    <div id="addQuestionBeforeIt"></div>

    <input type="button" onclick="checkInput();" value="Сохранить" />
    <input type="button" onclick="back();" value="Отменить и вернуться к списку" />

  </form>


</div>

<script>

  function checkInput() {
    var popup = new sc2Popup();
    if(id('quizNameInput').value.trim() == '') {
      popup.showMessage('Ошибка при изменении опроса', 'Не указано название опроса', 'Закрыть');
      seterror('quizNameInput');
      return;
    }

    document.forms['quizForm'].submit();
  }

  function back() {
    window.location = '/quiz/showlist';
  }


  function newQuestion(i, obj) {
    var ins = document.getElementById('addQuestionBeforeIt');
    var l = document.createElement('label');
    l.innerHTML = 'Вопрос №'+(i+2);
    ins.parentNode.insertBefore(l, ins);
    var input = document.createElement('input');
    input.type = 'text';
    input.name = 'qqtextnew['+(i+1)+']';
    input.onkeydown = function() { newQuestion(i+1, this); };
    ins.parentNode.insertBefore(input, ins);
    l = document.createElement('label');
    l.innerHTML = 'Варианты ответа';
    ins.parentNode.insertBefore(l, ins);
    input = document.createElement('input');
    input.type = 'text';
    input.name = 'qatextnew['+(i+1)+'][]';
    input.onkeydown = function() { newAnswer(i+1, this); };
    ins.parentNode.insertBefore(input, ins);
    obj.onkeydown = function() {};
  }

  function newAnswer(i, obj) {
    var input = document.createElement('input');
    input.type = 'text';
    input.name = 'qatextnew['+i+'][]';
    input.onkeydown = function() { newAnswer(i, this); };
    obj.parentNode.insertBefore(input, obj.nextElementSibling);
    var br = document.createElement('br');
    obj.parentNode.insertBefore(br, obj.nextElementSibling);
    obj.onkeydown = function() {};
  }
</script>

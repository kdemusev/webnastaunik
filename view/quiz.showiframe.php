<!DOCTYPE html>
<html>
  <head>
    <title>Ежедневник учителя | Белорусские открытые образовательные технологии</title>
    <meta charset="UTF-8">


  </head>
  <body style="margin: 0; padding: 0;">

<h2><?=$quizdata[0]['qzname']?></h2>

<p><?=$quizdata[0]['qzdesc']?></p>

  <form class="transpform" action="/quiz/answeriframe" method="post"
        name="quizForm">

    <input type="hidden" name="quiz_id" value="<?=$quizdata[0]['id']?>" />

    <?php foreach($qzquestions as $rec) { ?>
      <label><?=$rec['qqtext']?></label><br />
      <?php foreach($rec['answers'] as $rec2) { ?>
      <input type="radio" name="results[<?=$rec['id']?>]" value="<?=$rec2['qaid']?>" /><?=$rec2['qatext']?><br />
      <?php } ?><br />
    <?php } ?>

    <input type="button" onclick="checkInput();" value="Отправить результаты" />
  </form>

</div>

</body>
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
      if(typeof sc2Popup == 'object') {
        var popup = new sc2Popup();
        popup.showMessage('Невозможно сохранить результаты', 'Вы не ответили на все вопросы',
                          'Закрыть', null, null);
      } else {
        alert('Вы не ответили на все вопросы. Просим Вас ответить на все вопросы анкетирования');
      }
      return false;
    }

    document.forms['quizForm'].submit();
    return true;
  }

  function styleMe(linkrel) {
    var small_head = document.getElementsByTagName('head').item(0);

    var thestyle = document.createElement('link');
    thestyle.rel = 'stylesheet';
    thestyle.type = 'text/css';
    thestyle.href = linkrel;
    small_head.appendChild(thestyle);
  }

  function onmessage(event) {
    var data = event.data.split('|');

    if(data[0]=='styleme') {
      styleMe(data[1]);
    }
  }

  window.addEventListener("message", onmessage);

  top.postMessage('webnastaunik_iframe_onload|'+document.body.scrollHeight +'px', '*');

</script>
</html>

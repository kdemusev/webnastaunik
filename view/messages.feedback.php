<div class="mainframe">

  <div class="subheader">Обратная связь</div>

  <?php CTemplates::showMessage('sended', 'Сообщение отправлено и будет рассмотрено в кратчайшие сроки. Ответ Вы получите в личные сообщения'); ?>

  <p>На этой странице Вы можете задать вопрос администрации портала "Ежедневник учителя", сообщить об ошибке в работе с порталом, предложить расширение функциональных возможностей,
  адаптацию отчетных ведомостей под формат вашего учреждения.</p>

  <p>При сообщении об ошибке, пожалуйста, сообщите как можно детальнее последовательность своих действий перед тем как возникла ошибка.</p>

  <form class="transpform" action="/messages/sendfeedback" method="post" id="postform">
    <input type="hidden" name="mstopic" value="Обратная связь" />
    <label>Текст сообщения</label>
    <textarea name="mstext" id="mstext" class="autoresizable"></textarea>
    <input type="button" value="Отправить" onclick="onSave();" />
    <input type="button" value="Отменить" onclick="window.location='/';" />
  </form>

</div>

<script>
  function onSave() {
    var popup = new sc2Popup();
    if(value('mstext').trim()=='') {
      popup.showMessage('Ошибка отправки сообщения', 'Не заполнен текст сообщения', 'Закрыть');
      seterror('mstopic');
      return;
    }

    document.getElementById('postform').submit();
  }

  makeTextareaAutoresizable();
</script>

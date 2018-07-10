<div class="mainframe">

  <div class="subheader"><?=$message['mstopic']?></div>

  <form class="transpform">
  <input type="button" onclick="window.location='/messages';" value="Входящие" />
  <input type="button" onclick="window.location='/messages/compose';" value="Написать сообщение" />
  <input type="button" onclick="window.location='/messages/answer/<?=$message['message_id']?>';" value="Ответить на это сообщение" />
  <input type="button" onclick="onDelete();" value="Удалить это сообщение" />
  </form>

  <div class="post">
    <div class="author">
      <b><?=$message['usname']?></b>, <?=$message['usplace']?> (<?=$message['scname']?>)<br>
       <small><?=CUtilities::date_like_gmail($message['mstime'])?></small>
    </div>
    <?=nl2br($message['mstext'])?>
  </div>

</div>

<script>
  function onDelete() {
    var popup = new sc2Popup();
    popup.showMessage('Удаление сообщения', 'Вы уверены, что хотите удалить сообщение', 'Нет', 'Да',
                      function() { window.location='/messages/delete/<?=$message['message_id']?>'; });
  }
</script>

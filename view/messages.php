<div class="mainframe">

  <div class="subheader"><?=$justnew?'Новые сообщения':'Сообщения'?></div>

  <form class="transpform">
  <input type="button" onclick="window.location='/messages/compose';" value="Написать сообщение" />
  <?=$justnew?'<input type="button" onclick="window.location=\'/messages\';" value="Входящие" />':
  '<input type="button" onclick="window.location=\'/messages/justnew\';" value="Только новые" />'?>
  <input type="button" onclick="window.location='/messages/sended';" value="Отправленные" />
  </form>

  <?php CTemplates::showMessage('added', 'Сообщение отправлено'); ?>
  <?php CTemplates::showMessage('deleted', 'Сообщение удалено'); ?>

<?php if(count($messages) > 0) { ?>
<div class="largelist overable">
  <?php foreach($messages as $rec) { ?>
  <div class="<?=$rec['msreaded']==0?'tr marked':'tr'?>">
    <div class="col1" onclick="window.location='/messages/show/<?=$rec['message_id']?>';"><b><?=$rec['usname']?></b></div>
    <div class="col2" onclick="window.location='/messages/show/<?=$rec['message_id']?>';"><b><?=$rec['mstopic']?></b> - <?=CUtilities::truncate($rec['mstext'], 255)?></div>
    <div class="col3" onclick="window.location='/messages/show/<?=$rec['message_id']?>';"><b><?=CUtilities::date_like_gmail($rec['mstime'])?></b></div>
    <div class="col4"><img src="/style/icons/trash.png" onclick="onDelete('<?=$rec['message_id']?>');" title="удалить сообщение"/></div>
  </div>
  <?php } ?>
</div>
<?php } else { ?>
<div class="emptylist">
  <?=$justnew?'Новых сообщений нет':'Сообщений нет'?>
</div>
<?php } ?>

</div>

<script>
  function onDelete(msid) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление сообщения', 'Вы уверены, что хотите удалить сообщение', 'Нет', 'Да',
                      function() { window.location='/messages/delete/'+msid; });
  }
</script>

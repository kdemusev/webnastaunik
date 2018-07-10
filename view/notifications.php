<div class="mainframe">

  <div class="subheader">Уведомления</div>

  <form class="transpform">
  <input type="button" onclick="onDelete();" value="Удалить все уведомления" />
  </form>

  <?php CTemplates::showMessage('deletedall', 'Все уведомления удалены'); ?>
  <?php CTemplates::showMessage('deleted', 'Уведомление удалено'); ?>

<?php if(count($notifications) > 0) { ?>
<div class="largelist overable">
  <?php foreach($notifications as $rec) { ?>
  <div class="tr">
    <div class="col2" style="left: 0;" onclick="window.location='/notifications/goforit/<?=$rec['id']?>';"><b><?=$rec['nttopic']?></b></div>
    <div class="col3" onclick="window.location='/notifications/goforit/<?=$rec['id']?>';"><b><?=CUtilities::date_like_gmail($rec['nttime'])?></b></div>
    <div class="col4"><img src="/style/icons/trash.png" onclick="window.location='/notifications/delete/<?=$rec['id']?>';" title="удалить уведомление"/></div>
  </div>
  <?php } ?>
</div>
<?php } else { ?>
<div class="emptylist">
  В настоящее время уведомления отсутствуют
</div>
<?php } ?>


</div>

<script>
  function onDelete() {
    var popup = new sc2Popup();
    popup.showMessage('Удаление уведомлений', 'Вы уверены, что хотите удалить все уведомления', 'Нет', 'Да',
                      function() { window.location='/notifications/deleteall'; });
  }
</script>

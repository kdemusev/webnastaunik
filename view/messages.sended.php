<div class="mainframe">

  <div class="subheader">Отправленные сообщения</div>

  <form class="transpform">
  <input type="button" onclick="window.location='/messages';" value="Входящие" />
  <input type="button" onclick="window.location='/messages/compose';" value="Написать сообщение" />
  </form>

<?php if(count($messages) > 0) { ?>
<div class="largelist overable">
  <?php foreach($messages as $rec) { ?>
  <div class="<?=$rec['msreaded']==0?'tr marked':'tr'?>">
    <div class="col1" onclick="window.location='/messages/sendedshow/<?=$rec['message_id']?>';"><b><?=$rec['usname']?></b></div>
    <div class="col2" onclick="window.location='/messages/sendedshow/<?=$rec['message_id']?>';"><b><?=$rec['mstopic']?></b> - <?=CUtilities::truncate($rec['mstext'], 255)?></div>
    <div class="col3" style="right: 0;" onclick="window.location='/messages/sendedshow/<?=$rec['message_id']?>';"><b><?=CUtilities::date_like_gmail($rec['mstime'])?></b></div>
  </div>
  <?php } ?>
</div>
<?php } else { ?>
<div class="emptylist">
  Отправленных сообщений нет
</div>
<?php } ?>

</div>

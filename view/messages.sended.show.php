<div class="mainframe">

  <div class="subheader"><?=$message['mstopic']?></div>

  <form class="transpform">
  <input type="button" onclick="window.location='/messages/sended';" value="Отправленные" />
  <input type="button" onclick="window.location='/messages';" value="Входящие" />
  </form>

  <div class="post">
    <div class="author">
      Кому: <b><?=$message['usname']?></b>, <?=$message['usplace']?> (<?=$message['scname']?>)<br>
       <small><?=CUtilities::date_like_gmail($message['mstime'])?></small>
    </div>
    <?=$message['mstext']?>
  </div>

</div>

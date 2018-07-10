<!DOCTYPE html>
<html>
  <head>
    <title>Ежедневник учителя | Белорусские открытые образовательные технологии</title>
    <meta charset="UTF-8">


  </head>
  <body style="margin: 0; padding: 0;">

<h2><?=$quizdata[0]['qzname']?></h2>

<p><?=$quizdata[0]['qzdesc']?></p>

<p><b>Ваши ответы приняты</b></p>

<p><?=$quizdata[0]['qzthank']?></p>

<?php if($quizdata[0]['qzshowresults']==0) { ?>

    <?php foreach($qzquestions as $rec) { ?>
      <b><?=$rec['qqtext']?></b><br />
      <?php foreach($rec['answers'] as $rec2) { ?>
      <?=$rec2['qatext']?> - <b><?=$rec2['cnt']?> голосов (<?=$rec2['total']?>%)</b><br />
      <?php } ?><br />
    <?php } ?>
<?php } ?>

</body>
<script>
    
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

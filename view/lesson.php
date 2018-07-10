<div class="mainframe">

  <div class="subheader">Урок &quot;<?=$_subject['sbname']?>&quot; в <?=$_subject['fmname']?> классе
    <?php if($_now) { ?>(<span id="lessontime"></span>)<?php } ?></div>

  <form class="transpform" method="post" action="/ktp/view">
  <input type="button" onclick="window.location='/lesson/choose';" value="Выбрать другой урок" />
  <input type="button" onclick="window.location='/lesson/plan';" value="Запланировать" />
  <input type="hidden" name="subject_id" value="<?=$_subject['id']?>" />
  <input type="submit" value="Просмотр КТП" />
  </form>

  <?php CTemplates::showMessage('added', 'Сообщение отправлено'); ?>
  <?php CTemplates::showMessage('deleted', 'Сообщение удалено'); ?>

  <h2>Тема: <?=$_ktp['kttopic']?>&nbsp;
  <div style="float: right; margin-left: 50px;"><?=date('d.m.Y',$_ktp['ktdate'])?></div>  <br style="clear: both;" /></h2>


<form class="transpform" method="post" action="/lesson/over">
  <input type="hidden" name="ktp_id" value="<?=$_ktp['id']?>" />
  <input type="hidden" name="ktpnext_id" value="<?=$_ktpnext['id']?>" />
  <div class="columncontainer">
    <div class="column50">
      <div class="incolumnfirst">
        <h4>Домашнее задание:</h4>
        <?=$lesson['lphometask']!=''?'<p>'.$lesson['lphometask'].'</p>':'<div class="emptylist">На текущее занятие ничего не задано</div>'?>
        <h4>Запланировано:</h4>
        <?=$lesson['lptext']!=''?'<p>'.$lesson['lptext'].'</p>':'<div class="emptylist">На текущее занятие ничего не запланировано</div>'?>
        <h4>Заметки:</h4>
        <?=$lesson['lpnotes']!=''?'<p>'.$lesson['lpnotes'].'</p>':'<div class="emptylist">На текущее занятие ничего не записано</div>'?>
      </div>
    </div>
    <div class="column50">
      <div class="incolumnlast">
        <h3>Следующая тема: <br />
        <?=$_ktpnext['kttopic']?>&nbsp;
        <div style="float: right; margin-left: 50px;"><?=date('d.m.Y',$_ktpnext['ktdate'])?></div><br style="clear: both;" />
        </h3>
        <h4>Запланировать на следующий урок: <span id='lptextanimate'></span></h4>
        <textarea name="lptext" class="autoresizable" id="lptext"><?=$nextlesson['lptext']?></textarea>
        <h4>Домашнее задание на следующий урок: <span id='lphometaskanimate'></span></h4>
        <textarea name="lphometask" class="autoresizable" id="lphometask"><?=$nextlesson['lphometask']?></textarea>
        <h4>Заметки для следующего урока: <span id='lpnotesanimate'></span></h4>
        <textarea name="lpnotes" class="autoresizable" id="lpnotes"><?=$nextlesson['lpnotes']?></textarea>
      </div>
    </div>
    <br class="clear" />
  </div>

  <h4>Оценивание:</h4>
  <?php CTemplates::formList(array('','', ''),
                             array(), 'listformjournal', false, true); ?>

  <?php if(count($rc) > 0) { ?>
  <h2>Рейтинговая система оценки знаний учащихся <span id='ratinganimate'></span></h2>
  <div class="transpform">
      <table class='smptable overable'>
        <tr>
          <th></th>
          <th></th>
          <?php foreach($rc as $rec) { ?>
            <th>
              <?=$rec['rcname']?> (<?=$rec['rcrating']?>)
            </th>
          <?php } ?>
          <th>Всего</th>
          <th>Балл</th>
        </tr>
        <?php foreach($pupils as $pprec) { ?>
          <tr>
            <td><?=$pprec['pppriority']?></td>
            <td><?=$pprec['ppname']?></td>
            <?php foreach($rc as $rec) { ?>
              <td class="rating_<?=$pprec['pupil_id']?>">
                <input type="number" style="width: 100%; padding: 0; text-align: center; border-bottom: none;" value="<?=isset($rating[$pprec['pupil_id']][$rec['id']])?$rating[$pprec['pupil_id']][$rec['id']]:''?>"
                        onkeyup="recalculate();"
                        id="input_<?=$pprec['pupil_id']?>_<?=$rec['id']?>" />
              </td>
            <?php } ?>
            <td id="total_<?=$pprec['pupil_id']?>"></td>
            <td id="mark_<?=$pprec['pupil_id']?>"></td>
          </tr>
        <?php } ?>
      </table>
    </div>
    <div class="transpform">
      <input type="button" value="Завершить период и перенести отметки в журнал"
             onclick="window.location='/journal/ratingover/<?=$_subject['id']?>'" />
      <input type="button" value="Очистить рейтинг"
                    onclick="onDelete()" />
    </div>
  <?php } ?>
</form>

</div>

<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2autosave.js"></script>
<script>
  var tl = new sc2TableList('listformjournal');
  tl.setAutosavable('journal', 'jrmark', 'pupil_id', 'ktp_id', '<?=$_ktp['ktp_id']?>');
  tl.addField('number');
  tl.addField('innerHTML', 'ppname', '250px');
  tl.addField('text', 'jrmark', '30px', 0, 1);

  <?php foreach($pupils as $rec) { ?>
    tl.addRecord({ppname: '<?=s_q($rec['ppname'])?>', id: '<?=$rec['pupil_id']?>',
               jrmark: new String('<?=$rec['jrmark']?>')});
  <?php } ?>

  function initAutosave() {
    var as_lptext = new sc2Autosave('lptext', 'lessons', 'lptext', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lptextanimate');
    var as_lphometask = new sc2Autosave('lphometask', 'lessons', 'lphometask', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lphometaskanimate');
    var as_lpnotes = new sc2Autosave('lpnotes', 'lessons', 'lpnotes', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lpnotesanimate');
  }

  initAutosave();

  function onDelete() {
    var popup = new sc2Popup();
    popup.showMessage('Очистка рейтинга', 'Вы уверены, что хотите очистить рейтинг?', 'Нет', 'Да', function() {window.location='/journal/deleterating/<?=$_subject['id']?>'; });
  }

  makeTextareaAutoresizable();

<?php if($_now) { ?>
  var timerField = document.getElementById('lessontime');
  var timerLast = <?=$timerLast?>;
  function updateTimer() {
    var min = Math.floor(timerLast/60);
    var sec = (timerLast - min * 60);
    timerField.innerHTML =  ('0'+min).slice(-2) + ':' + ('0'+sec).slice(-2);
    timerLast -= 1;
    if(timerLast < 0) {
      clearInterval(timer);
    }
  }
  updateTimer();
  var timer = setInterval(updateTimer, 1000);
<?php } ?>

</script>

<?php if(count($rc) > 0) { ?>
<script>
  var pp_id = [];
  <?php foreach($pupils as $rec) { ?>
    pp_id.push(<?=$rec['pupil_id']?>);
  <?php } ?>

  recalculate();

  <?php foreach($rc as $rec) {
    foreach($pupils as $pprec) { ?>
      new sc2Autosave('input_<?=$pprec['pupil_id']?>_<?=$rec['id']?>', 'rating', 'rating', 'pupil_id', '<?=$pprec['pupil_id']?>', 'ratinganimate', 'rc_id', '<?=$rec['id']?>', 1);
  <?php } } ?>

  function recalculate() {
    var i, i2, l2;
    var l = pp_id.length;
    var pprats;
    var total;
    for(i = 0; i < l; i++) {
      pprats = document.getElementsByClassName('rating_'+pp_id[i]);
      l2 = pprats.length;
      total = 0;
      for(i2 = 0; i2 < l2; i2++) {
        if(!isNaN(pprats[i2].children[0].value)) {
          total += pprats[i2].children[0].value*1;
        }
      }

      document.getElementById('total_'+pp_id[i]).innerHTML = total;
      document.getElementById('mark_'+pp_id[i]).innerHTML = Math.round(total/<?=$maxrating?>*10);
    }
  }
</script>
<?php } ?>

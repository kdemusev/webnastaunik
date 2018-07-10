<div class="mainframe">

  <div class="subheader">Планирование урока &quot;<?=$_subject['sbname']?>&quot; в <?=$_subject['fmname']?> классе</div>

  <form class="transpform">
  <input type="button" onclick="window.location='/lesson/plan';" value="Выбрать другой урок" />
  <input type="button" onclick="window.location='/lesson';" value="Проведение урока" />
  </form>

<form class="transpform">
  <h2>Тема: <?=$_ktpnext['kttopic']?>&nbsp;
  <div style="float: right; margin-left: 50px;"><?=date('d.m.Y',$_ktpnext['ktdate'])?></div></h2>

  <h4>Запланировать: <span id='lptextanimate'></span></h4>
  <textarea name="lptext" class="autoresizable" id="lptext"><?=$nextlesson['lptext']?></textarea>
  <h4>Домашнее задание: <span id='lphometaskanimate'></span></h4>
  <textarea name="lphometask" class="autoresizable" id="lphometask"><?=$nextlesson['lphometask']?></textarea>
  <h4>Заметки: <span id='lpnotesanimate'></span></h4>
  <textarea name="lpnotes" class="autoresizable" id="lpnotes"><?=$nextlesson['lpnotes']?></textarea>
</form>
</div>

<script src="/js/sc2tablelist.js"></script>
<script src="/js/sc2autosave.js"></script>
<script>

  function initAutosave() {
    var as_lptext = new sc2Autosave('lptext', 'lessons', 'lptext', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lptextanimate');
    var as_lphometask = new sc2Autosave('lphometask', 'lessons', 'lphometask', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lphometaskanimate');
    var as_lpnotes = new sc2Autosave('lpnotes', 'lessons', 'lpnotes', 'ktp_id', '<?=$_ktpnext['ktp_id']?>', 'lpnotesanimate');
  }


  makeTextareaAutoresizable();
  initAutosave();
</script>

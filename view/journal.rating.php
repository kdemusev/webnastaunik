<div class="mainframe">

    <div class="subheader">Оценивание по рейтинговой системе <span id='ratinganimate'></span></div>

    <?php CTemplates::showMessage('deleted', 'Рейтинг очищен'); ?>
    <?php CTemplates::showMessage('marked', 'Отметки выставлены, рейтинг очищен'); ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

<?php if(count($pupils) > 0) {
  if(count($rc) > 0) { ?>
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
            <td class="rating_<?=$pprec['id']?>">
              <input type="text" style="width: 100%; padding: 0; text-align: center; border-bottom: none;" value="<?=isset($rating[$pprec['id']][$rec['id']])?$rating[$pprec['id']][$rec['id']]:''?>"
                      onkeyup="recalculate();"
                      id="input_<?=$pprec['id']?>_<?=$rec['id']?>" />
            </td>
          <?php } ?>
          <td id="total_<?=$pprec['id']?>"></td>
          <td id="mark_<?=$pprec['id']?>"></td>
        </tr>
      <?php } ?>
    </table>
</div>
<div class="transpform">
  <input type="button" value="Завершить период и перенести отметки в журнал"
         onclick="window.location='/journal/ratingover/<?=$subject_id?>'" />
  <input type="button" value="Очистить рейтинг"
                onclick="onDelete()" />
</div>
<?php } else { ?>
  <div class="emptylist">Для выбранных предмета и класса не создана рейтинговая система</div>
<?php } } else { ?>
  <div class="emptylist">Для выбранного класса отсутствует список учащихся</div>
<?php } ?>
</div>

<script>
  function onDelete() {
    var popup = new sc2Popup();
    popup.showMessage('Очистка рейтинга', 'Вы уверены, что хотите очистить рейтинг?', 'Нет', 'Да', function() {window.location='/journal/deleterating/<?=$subject_id?>'; });
  }
</script>

<?php if(count($pupils) > 0) {
  if(count($rc) > 0) { ?>

<script src="/js/sc2autosave.js"></script>
<script>
  var pp_id = [];
  <?php foreach($pupils as $rec) { ?>
    pp_id.push(<?=$rec['id']?>);
  <?php } ?>

  recalculate();

  <?php foreach($rc as $rec) {
    foreach($pupils as $pprec) { ?>
      new sc2Autosave('input_<?=$pprec['id']?>_<?=$rec['id']?>', 'rating', 'rating', 'pupil_id', '<?=$pprec['id']?>', 'ratinganimate', 'rc_id', '<?=$rec['id']?>', 1);
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
<?php } } ?>

<div class="mainframe">

    <div class="subheader">Учет посещаемости занятий учащимися</div>

    <?php CTemplates::showMessage('saved', 'Изменения сохранены'); ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <br />
    <form method="post" class="transpform" action="/journal/attendance">
      <input type="hidden" name="form_id" value="<?=$form_id?>" />
      <label>Период</label>
      <select name="month" style="width: 100px;" onchange="this.parentNode.submit();">
        <option value="1" <?php if($month == 1) { print 'selected'; }?>>январь</option>
        <option value="2" <?php if($month == 2) { print 'selected'; }?>>февраль</option>
        <option value="3" <?php if($month == 3) { print 'selected'; }?>>март</option>
        <option value="4" <?php if($month == 4) { print 'selected'; }?>>апрель</option>
        <option value="5" <?php if($month == 5) { print 'selected'; }?>>май</option>
        <option value="6" <?php if($month == 6) { print 'selected'; }?>>июнь</option>
        <option value="7" <?php if($month == 7) { print 'selected'; }?>>июль</option>
        <option value="8" <?php if($month == 8) { print 'selected'; }?>>август</option>
        <option value="9" <?php if($month == 9) { print 'selected'; }?>>сентябрь</option>
        <option value="10" <?php if($month == 10) { print 'selected'; }?>>октябрь</option>
        <option value="11" <?php if($month == 11) { print 'selected'; }?>>ноябрь</option>
        <option value="12" <?php if($month == 12) { print 'selected'; }?>>декабрь</option>
      </select>
      <select name="year" style="width: 100px;" onchange="this.parentNode.submit();">
        <?php $selyear = date('Y')-1; ?>
        <option value="<?=$selyear?>" <?php if($year == $selyear) { print 'selected'; }?>><?=$selyear++?></option>
        <option value="<?=$selyear?>" <?php if($year == $selyear) { print 'selected'; }?>><?=$selyear++?></option>
        <option value="<?=$selyear?>" <?php if($year == $selyear) { print 'selected'; }?>><?=$selyear++?></option>
      </select>
    </form>

<?php if(count($pupils) > 0) { ?>

    <div class="transpform">
    <button onclick="onPrint();">Распечатать</button>
    </div>

<div id="toprint">
  <div class="printable">
    <center>Государственное учреждение образования "<?=$scname?>"</center>
		<h1 align="center">Учет посещаемости занятий учащимися <?=$form?> класса<br />
      в
      <?=$month==1?'январе':($month==2?'феврале':($month==3?'марте':($month==4?'апреле':($month==5?'мае':
      ($month==6?'июне':($month==7?'июле':($month==8?'августе':($month==9?'сентябре':($month==10?'октябре':($month==11?'ноябре':'декабре'))))))))))?>
      <?=$month<9?($year-1).'/'.$year:$year.'/'.($year+1)?> учебного года<br>
    </h1>
  </div>

    <table class="smptable overable">
      <thead>
        <tr>
          <th>№ п/п</th>
          <th>Фамилия, имя учащегося</th>
          <?php $dayscnt = date('t', mktime(0,0,0,$month, 1, $year));
          for($i = 1; $i <= $dayscnt; $i++) { $cycletime = mktime(0,0,0,$month, $i, $year); ?>
            <?php if(date('N', $cycletime) == 7 ||
                     (isset($ktpdayoff[$cycletime]) &&
                     $ktpdayoff[$cycletime] == 0)) { ?>
            <th width="20px;" style="background-color: rgb(255, 209, 213);"><?=$i?></th>
            <?php } else if(date('N', $cycletime) == 6 ||
                     (isset($ktpdayoff[$cycletime]) &&
                     $ktpdayoff[$cycletime] == 1)) { ?>
            <th width="20px;" style="background-color: rgb(214, 212, 156);"><?=$i?></th>
            <?php } else { ?>
            <th width="20px;"><?=$i?></th>
            <?php } ?>
          <?php } ?>
          <th width="20px;">Б</th>
          <th width="20px;">У</th>
          <th width="20px;">Н</th>
        </tr>
      </thead>
      <?php foreach($pupils as $k => $rec) { ?>
        <tr>
          <td><?=$k+1?></td>
          <td><?=$rec['ppname']?></td>
          <?php for($i = 1; $i <= $dayscnt; $i++) { $cycletime = mktime(0,0,0,$month, $i, $year);  ?>
            <?php if(date('N', $cycletime) == 7 ||
                     (isset($ktpdayoff[$cycletime]) &&
                     $ktpdayoff[$cycletime] == 0)) { ?>
            <td style="background-color: rgb(255, 209, 213);"></td>
            <?php } else if(date('N', $cycletime) == 6 ||
                     (isset($ktpdayoff[$cycletime]) &&
                     $ktpdayoff[$cycletime] == 1)) { ?>
            <td id="sat_<?=$i?>_<?=$rec['id']?>" style="background-color: rgb(214, 212, 156);" class="journalcell" onclick="setSatMark(this)"></td>
            <?php } else { ?>
            <td id="day_<?=$i?>_<?=$rec['id']?>" class="journalcell" onclick="setMark(this)"></td>
            <?php } ?>
          <?php } ?>
          <td id="b_<?=$rec['id']?>" style="font-weight: bold; text-align: center;"></td>
          <td id="u_<?=$rec['id']?>" style="font-weight: bold; text-align: center;"></td>
          <td id="n_<?=$rec['id']?>" style="font-weight: bold; text-align: center;"></td>
        </tr>
      <?php } ?>
      <tr>
        <td colspan="2" style="text-align: right;"><b>Всего:</b></td>
        <?php for($i = 1; $i <= $dayscnt; $i++) { $cycletime = mktime(0,0,0,$month, $i, $year); ?>
          <?php if(date('N', $cycletime) == 7 ||
                   (isset($ktpdayoff[$cycletime]) &&
                   $ktpdayoff[$cycletime] == 0)) { ?>
          <td style="background-color: rgb(255, 209, 213);"></td>
          <?php } else if(date('N', $cycletime) == 6 ||
                   (isset($ktpdayoff[$cycletime]) &&
                   $ktpdayoff[$cycletime] == 1)) { ?>
          <td id="totalsat_<?=$i?>" style="background-color: rgb(214, 212, 156); font-weight: bold; text-align: center;"></td>
          <?php } else { ?>
          <td id="totalday_<?=$i?>" style="font-weight: bold; text-align: center;"></td>
          <?php } ?>
        <?php } ?>
        <td id="total_b" style="font-weight: bold; text-align: center;"></td>
        <td id="total_u" style="font-weight: bold; text-align: center;"></td>
        <td id="total_n" style="font-weight: bold; text-align: center;"></td>
      </tr>
    </table>
  </div>
  <br />

    <form method="post" name="attform" action="/journal/attendance" class="transpform">
      <input type="hidden" name="form_id" value="<?=$form_id?>" />
      <input type="hidden" name="month" value="<?=$month?>" />
      <input type="hidden" name="year" value="<?=$year?>" />
      <input type="hidden" name="save" value="1" />
      <input type="button" value="Сохранить" onclick="onSave();"/>
    </form>

<?php } else { ?>
  <div class="emptylist">В выбранный класс не введены учащиеся</div>
<?php } ?>

</div>


<script>
  var apupil_ids = [];
  <?php foreach($pupils as $rec) { ?>
  apupil_ids.push(<?=$rec['id']?>);
  <?php } ?>

  <?php if(count($pupils) > 0) { ?>
    init();
  <?php } ?>
  
  function onPrint() {
		var objbody = document.body.innerHTML;
		var els = document.getElementsByClassName('printable');

		var i = 0;
		var l = els.length;
		for(i = 0; i < l; i++) {
			els[i].style.display = 'block';
		}

		var objtoprint = document.getElementById('toprint').innerHTML;

		document.body.innerHTML = objtoprint;

		window.print();

		document.body.innerHTML = objbody;
	}

  function addInput(pid, day, val) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'att['+pid+']['+day+']';
    input.value = val;
    document.forms['attform'].appendChild(input);
  }

  function onSave() {
    var l = <?=date('t', mktime(0,0,0,$month,1,$year))?>;
    var i;
    var p;
    var pl = apupil_ids.length;

    for(i = 1; i <= l; i++) {
      for(p = 0; p < pl; p++) {
        if(!document.getElementById('day_'+i+'_'+apupil_ids[p])) {
          if(document.getElementById('sat_'+i+'_'+apupil_ids[p]) &&
             document.getElementById('sat_'+i+'_'+apupil_ids[p]).innerHTML == '+') {
            addInput(apupil_ids[p], i, 0);
          }
          continue;
        }

        if(document.getElementById('day_'+i+'_'+apupil_ids[p]).innerHTML == '') {
          continue;
        } else if(document.getElementById('day_'+i+'_'+apupil_ids[p]).innerHTML == 'Б') {
          addInput(apupil_ids[p], i, 1);
        } else if(document.getElementById('day_'+i+'_'+apupil_ids[p]).innerHTML == 'У') {
          addInput(apupil_ids[p], i, 2);
        } else if(document.getElementById('day_'+i+'_'+apupil_ids[p]).innerHTML == 'Н') {
          addInput(apupil_ids[p], i, 3);
        }

      }
    }

    document.forms['attform'].submit();
  }

  function init() {
    var l = <?=date('t', mktime(0,0,0,$month,1,$year))?>;

    <?php
    $d = 1;
    $dl = date('t', mktime(0,0,0,$month,1,$year));
    $p = 0;
    $pl = count($pupils);
    for($d = 1; $d <= $dl; $d++) {
      for($p = 0; $p < $pl; $p++) {
        $id = $pupils[$p]['id'];
        if(isset($att[$id][$d]) && $att[$id][$d] == 1) { ?>
          if(document.getElementById('day_<?=$d?>_<?=$id?>')) {
            document.getElementById('day_<?=$d?>_<?=$id?>').innerHTML = 'Б';
          }
        <?php } else if(isset($att[$id][$d]) && $att[$id][$d] == 2) { ?>
          if(document.getElementById('day_<?=$d?>_<?=$id?>')) {
            document.getElementById('day_<?=$d?>_<?=$id?>').innerHTML = 'У';
          }
        <?php } else if(isset($att[$id][$d]) && $att[$id][$d] == 3) { ?>
          if(document.getElementById('day_<?=$d?>_<?=$id?>')) {
            document.getElementById('day_<?=$d?>_<?=$id?>').innerHTML = 'Н';
          }
        <?php } else if(isset($att[$id][$d]) && $att[$id][$d] == 0) { ?>
          if(document.getElementById('sat_<?=$d?>_<?=$id?>')) {
            document.getElementById('sat_<?=$d?>_<?=$id?>').innerHTML = '+';
          }
        <?php }
      }
    }
    ?>

    var i = 1;
    for(i = 1; i <= l; i++) {
      recalcDay(i);
      recalcSat(i);
    }

    l = apupil_ids.length;
    for(i = 0; i < l; i++) {
      recalcPupil(apupil_ids[i]);
    }

    recalcTotal();
  }

  function setMark(obj) {
    var day = obj.id.split('_')[1];
    var pupil_id = obj.id.split('_')[2];

    if(obj.innerHTML == '') {
      obj.innerHTML = 'Б';
    } else if(obj.innerHTML == 'Б') {
      obj.innerHTML = 'У';
    } else if(obj.innerHTML == 'У') {
      obj.innerHTML = 'Н';
    } else if(obj.innerHTML == 'Н') {
      obj.innerHTML = '';
    }

    recalcDay(day);
    recalcPupil(pupil_id);
    recalcTotal();
  }

  function setSatMark(obj) {
    var day = obj.id.split('_')[1];
    var pupil_id = obj.id.split('_')[2];

    if(obj.innerHTML == '') {
      obj.innerHTML = '+';
    } else if(obj.innerHTML == '+') {
      obj.innerHTML = '';
    }

    recalcSat(day);
  }

  function recalcDay(day) {
    var i = 0;
    var l = apupil_ids.length;
    var pupil_id = 0;
    var what = '';
    var total = 0;

    for(i = 0; i < l; i++) {
      pupil_id = apupil_ids[i];
      if(document.getElementById('day_'+day+'_'+pupil_id) == undefined) {
        return;
      }
      what = document.getElementById('day_'+day+'_'+pupil_id).innerHTML;
      if(what == '') {
        total++;
      }
    }

    if(document.getElementById('totalday_'+day)) {
      document.getElementById('totalday_'+day).innerHTML = total;
    }
  }

  function recalcSat(day) {
    var i = 0;
    var l = apupil_ids.length;
    var pupil_id = 0;
    var what = '';
    var total = 0;

    for(i = 0; i < l; i++) {
      pupil_id = apupil_ids[i];
      if(document.getElementById('sat_'+day+'_'+pupil_id) == undefined) {
        return;
      }
      what = document.getElementById('sat_'+day+'_'+pupil_id).innerHTML;
      if(what == '+') {
        total++;
      }
    }

    if(document.getElementById('totalsat_'+day)) {
      document.getElementById('totalsat_'+day).innerHTML = total;
    }
  }

  function recalcPupil(pupil_id) {
    var i = 1;
    var l = <?=date('t', mktime(0,0,0,$month,1,$year))?>;

    var b = 0;
    var u = 0;
    var n = 0;

    for(i = 0; i < l; i++) {
      if(document.getElementById('day_'+i+'_'+pupil_id) == undefined) {
        continue;
      }
      what = document.getElementById('day_'+i+'_'+pupil_id).innerHTML;
      if(what == 'Б') {
        b++;
      } else if(what == 'У') {
        u++;
      } else if(what == 'Н') {
        n++;
      }
    }

    if(document.getElementById('b_'+pupil_id)) {
      document.getElementById('b_'+pupil_id).innerHTML = b;
      document.getElementById('u_'+pupil_id).innerHTML = u;
      document.getElementById('n_'+pupil_id).innerHTML = n;
    }
  }

  function recalcTotal() {
    var i = 0;
    var l = apupil_ids.length;
    var pupil_id = 0;
    var total_b = 0;
    var total_u = 0;
    var total_n = 0;

    for(i = 0; i < l; i++) {
      pupil_id = apupil_ids[i];

      total_b += parseInt(document.getElementById('b_'+pupil_id).innerHTML,10);
      total_u += parseInt(document.getElementById('u_'+pupil_id).innerHTML,10);
      total_n += parseInt(document.getElementById('n_'+pupil_id).innerHTML,10);
    }

    document.getElementById('total_b').innerHTML = total_b;
    document.getElementById('total_u').innerHTML = total_u;
    document.getElementById('total_n').innerHTML = total_n;
  }
</script>

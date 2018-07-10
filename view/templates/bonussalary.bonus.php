<div class="mainframe">

    <div class="subheader">Премирование из основного фонда</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <form class="transpform" name="formform" action="/bonussalary/bonus" method="post">
      <label>Период:</label>
  		<select name="bspdate">
        <?php $cycledate = mktime(0,0,0,1,1,2016); while($cycledate < $maxbspdate || $cycledate < strtotime('+1 month', mktime(0,0,0,date('n'),1,date('Y')))) { ?>
        <option value="<?=$cycledate?>" <?php if($cycledate == $bspdate) {?>selected<?php } ?>>за <?=CUtilities::text_month($cycledate)?> <?=date('Y', $cycledate)?></option>
        <?php $cycledate = strtotime('+1 month', $cycledate); } ?>
  		</select>
      <input type="submit" name="chooseperiod" value="Выбрать" />
    </form>

    <form class="transpform" action="/bonussalary/savebonus" method="post">
      <input type="hidden" name="bspdate" value="<?=$bspdate?>" />
      <label>Базовая величина:</label>
      <input type="text" name="bspbasevalue" value="<?=isset($_bspdata[0]) ? $_bspdata[0]['bspbasevalue'] : ''?>" id="bpbasevalue" onkeyup="recalculateall();" />
      <label>Премиальный фонд:</label>
      <input type="text" name="bsppursebonus" value="<?=isset($_bspdata[0]) ? $_bspdata[0]['bsppursebonus'] : ''?>" id="bppursebonus" onkeyup="recalculatetotal();" />
      <label>Остаток фонда:</label>
      <input type="text" name="bpbalance" id="bpbalance" readonly />

    <?php if(count($_bsadata) == 0) { ?>
      <div class="emptylist">Не создан ни один пункт положения о премировании</div>
    <?php } else if(count($_bsgdata) == 0) { ?>
      <div class="emptylist">Не создана ни одна группа работников учреждения</div>
    <?php } else if(count($_bsedata) == 0) { ?>
      <div class="emptylist">Не добавлен ни один работник</div>
    <?php } else { ?>

<div style="position: relative">
  <div id="toprint">
		<div class="printable">
			<center>Государственное учреждение образования "<?=$scname?>"</center>
			<h1 align="center">Сводная ведомость<br>состояния преподавания предметов в <?=$year?> учебном году</h1>
		</div>
<table class='smptable overable'>
  <tr>
    <th rowspan="3" nowrap>№<br>п/п</th>
    <th rowspan="3" nowrap>Фамилия И.О.</th>
    <th rowspan="3" >Должность</th>
    <th colspan="<?=count($_bsadata)?>">Показатели премирования для всех категорий работников</th>
    <th rowspan="3">Сумма премирования</th>
  </tr>
  <tr>
    <?php foreach($_bsadata as $rec) { ?>
      <th><?=$rec['bsanumber']?></th>
    <?php } ?>
  </tr>
  <tr>
    <?php foreach($_bsadata as $rec) { ?>
      <th class="vertical"  style="height: 110px;" align="center">
        <div>
            <?php if($rec['bsafrom']>0) { ?>от <?=$rec['bsafrom']?> <?php } ?>до <?=$rec['bsato']?> б.в.
        </div>
      </th>
    <?php } ?>
  </tr>

<?php $ig = 0; foreach($_bsgdata as $recg) { ?>
  <tr class="trow">
    <td colspan="<?=count($_bsadata)+4?>" class="trow"><b><?php if($ig++>0) {?><br /><?php } ?><?=$recg['bsgname']?></b></td>
  </tr>
  <?php $i = 1; foreach($_bsedata[$recg['id']] as $rec) { ?>
  <tr>
    <td align="center"><?=$i++?></td>
    <td nowrap><?=CUtilities::initials($rec['bsename'])?></td>
    <td nowrap><?=$rec['bseplace']?></td>
    <?php foreach($_bsadata as $rec2) { ?>
      <td><input type="text" class="borderless" name="bsvalue[<?=$rec['id']?>][<?=$rec2['id']?>]"
                 id="agr_<?=$rec['id']?>_<?=$rec2['id']?>" onkeyup="recalculate(this, '<?=$recg['id']?>', event);"
                 value="<?=isset($_bspaydata[$rec['id']][$rec2['id']]) ? (float)$_bspaydata[$rec['id']][$rec2['id']] : ''?>" /></td>
    <?php } ?>
    <td><input type="text" sc2group="<?=$recg['id']?>" class="borderless" id="sum_<?=$rec['id']?>" style="text-align: right;"
               onchange="this.style.backgroundColor = 'yellow';" name="bspays[<?=$rec['id']?>" readonly onkeyup="upAndDown(this, event);" /></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="<?=count($_bsadata)+3?>" class="trow">Итого по группе работников</td>
    <td><input type="text" class="borderless" id="groupsum<?=$recg['id']?>" style="text-align: right; font-weight: bold;" readonly /></td>
  </tr>
<?php } ?>
</table>
</div>
</div>
<?php } ?>

<label>&nbsp;</label>
<input type="submit" name="savetable" value="Сохранить" />
</form>

</div>

<script>
  var g_bpbasevalue = document.getElementById('bpbasevalue');
  var g_bppursebonus = document.getElementById('bppursebonus');
  var g_bpbalance = document.getElementById('bpbalance');

  function findElementNumber(obj) {
    var parent = obj.parentNode;
    var l = parent.children.length;
    var i;
    for(i = 0; i < l; i++) {
      if(parent.children[i]==obj) {
        return i;
      }
    }
    return 0;
  }

  function upAndDown(obj, event) {
    var elNum;
    if(event.keyCode==40) {
      if(obj.parentNode.parentNode.nextElementSibling &&
         obj.parentNode.parentNode.nextElementSibling.children.length > 2) {
        elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.nextElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.nextElementSibling.children[elNum].children[0].select();
      } else if(obj.parentNode.parentNode.nextElementSibling &&
                obj.parentNode.parentNode.nextElementSibling.children.length == 2 &&
                obj.parentNode.parentNode.nextElementSibling.nextElementSibling &&
                obj.parentNode.parentNode.nextElementSibling.nextElementSibling.nextElementSibling) {
        elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.nextElementSibling.nextElementSibling.nextElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.nextElementSibling.nextElementSibling.nextElementSibling.children[elNum].children[0].select();
      }
    } else if(event.keyCode==38) {
      if(obj.parentNode.parentNode.previousElementSibling &&
         obj.parentNode.parentNode.previousElementSibling.children.length > 1) {
        elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.previousElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.previousElementSibling.children[elNum].children[0].select();
      } else if (obj.parentNode.parentNode.previousElementSibling &&
                 obj.parentNode.parentNode.previousElementSibling.children.length == 1 &&
                 obj.parentNode.parentNode.previousElementSibling.previousElementSibling.children.length == 2) {
                   elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.children[elNum].children[0].select();
      }
    }
  }

  function recalculate(obj, group_id, event) {
    if(event) {
      upAndDown(obj, event);
    }
    var bpemployee_id = obj.id.split('_')[1];
    var amount = document.getElementById('sum_' + bpemployee_id);
    var cnt = obj.parentNode.parentNode.children.length;
    var i;
    var sum = 0;
    var t;
    for(i = 3; i < cnt-1; i++) {
      t = obj.parentNode.parentNode.children[i].children[0].value.replace(',','.');
      sum += (t == '' || isNaN(t)) ? 0 : parseFloat(t);
    }
    amount.value = sum * g_bpbasevalue.value;

    recalculatesum(obj, group_id);
  }

  function recalculatesum(obj, group_id) {
    var cnt = obj.parentNode.parentNode.parentNode.children.length;
    var i;
    var sum = 0;
    var o;
    for(i = 3; i < cnt-1; i++) {
      o = obj.parentNode.parentNode.parentNode.children[i].lastElementChild.children[0];
      if(o.getAttribute('sc2group')==group_id) {
        t = o.value;
        sum += (t == '' || isNaN(t)) ? 0 : parseFloat(t);
      }
    }
    document.getElementById('groupsum'+group_id).value = sum;

    recalculatetotal();
  }

  function recalculatetotal() {
    var sum = 0;
    var i;
    var groups = [];
    var t;
    <?php foreach($_bsgdata as $rec) { ?>
    groups.push(<?=$rec['id']?>);
    <?php } ?>

    for(i=0; i<groups.length; i++) {
      t = document.getElementById('groupsum'+groups[i]).value;
      sum += (t == '' || isNaN(t)) ? 0 : parseFloat(t);
    }

    g_bpbalance.value = (g_bppursebonus.value - sum).toFixed(2);
  }

  function recalculateall() {
    var groups = [];
    <?php if(count($_bsadata) > 0 && count($_bsedata) > 0) { ?>
      var firstagr = <?=$_bsadata[0]['id']?>;
      var employes = [];
      <?php foreach($_bsgdata as $rec) { ?>
        <?php foreach($_bsedata[$rec['id']] as $rece) { ?>
        recalculate(document.getElementById('agr_<?=$rece['id']?>_'+firstagr), '<?=$rec['id']?>', null);
        <?php } ?>
      <?php } ?>
    <?php } ?>
  }

  recalculateall();

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
</script>

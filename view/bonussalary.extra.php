<div class="mainframe">

    <div class="subheader">Распределение надбавки</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <form class="transpform" name="formform" action="/bonussalary/extra" method="post">
      <label>Период:</label>
  		<select name="bspdate">
        <?php $cycledate = mktime(0,0,0,1,1,2016); while($cycledate < $maxbspdate || $cycledate < strtotime('+1 month', mktime(0,0,0,date('n'),1,date('Y')))) { ?>
        <option value="<?=$cycledate?>" <?php if($cycledate == $bspdate) {?>selected<?php } ?>>за <?=CUtilities::text_month($cycledate)?> <?=date('Y', $cycledate)?></option>
        <?php $cycledate = strtotime('+1 month', $cycledate); } ?>
  		</select>
      <input type="submit" name="chooseperiod" value="Выбрать" />
    </form>

    <form class="transpform" action="/bonussalary/saveextra" method="post">
      <input type="hidden" name="bspdate" value="<?=$bspdate?>" />
      <label>Базовая величина:</label>
      <input type="text" name="bspbasevalue" value="<?=isset($_bspdata[0]) ? $_bspdata[0]['bspbasevalue'] : ''?>" id="bpbasevalue" onkeyup="recalculateall();" />
      <label>Фонд для распределения надбавок:</label>
      <input type="text" name="bsppurseextra" value="<?=isset($_bspdata[0]) ? $_bspdata[0]['bsppurseextra'] : ''?>" id="bppursebonus" onkeyup="recalculatetotal();" />
      <label>Остаток фонда:</label>
      <input type="text" name="bpbalance" id="bpbalance" readonly />

    <?php if(count($_bsgdata) == 0) { ?>
      <div class="emptylist">Не создана ни одна группа работников учреждения</div>
    <?php } else if(count($_bsedata) == 0) { ?>
      <div class="emptylist">Не добавлен ни один работник</div>
    <?php } else { ?>

<div style="position: relative">
<div  style="">
<table class='smptable overable'>
  <tr>
    <th nowrap>№<br>п/п</th>
    <th nowrap>Фамилия И.О.</th>
    <th>Должность</th>
    <th nowrap>Процент надбавки</th>
    <th nowrap>Сумма надбавки</th>
    <th>Основание</th>
  </tr>

<?php $ig = 0; foreach($_bsgdata as $recg) { ?>
  <tr class="trow">
    <td colspan="6" class="trow"><b><?php if($ig++>0) {?><br /><?php } ?><?=$recg['bsgname']?></b></td>
  </tr>
  <?php $i = 1; foreach($_bsedata[$recg['id']] as $rec) { ?>
  <tr>
    <td align="center"><?=$i++?></td>
    <td nowrap><?=CUtilities::initials($rec['bsename'])?></td>
    <td nowrap><?=$rec['bseplace']?></td>
    <td><input type="text" class="borderless" style="text-align: сутеук;"
               onkeyup="upAndDown(this, event);" name="bspepercent[<?=$rec['id']?>]" value="<?=isset($_bspaydata[$rec['id']]['bspepercent'])?$_bspaydata[$rec['id']]['bspepercent']:''?>"
               /></td>

    <td><input type="text" sc2group="<?=$recg['id']?>" class="borderless" id="sum_<?=$rec['id']?>" style="text-align: right;"
               onkeyup="upAndDown(this, event);" name="bsvalue[<?=$rec['id']?>]" value="<?=isset($_bspaydata[$rec['id']]['bspesum'])?$_bspaydata[$rec['id']]['bspesum']:''?>"
               onchange="recalculatesum(this, '<?=$recg['id']?>')"/></td>
    <td width="100%"><input type="text" class="borderless" style="text-align: left;" name="bsreason[<?=$rec['id']?>]"
               onkeyup="upAndDown(this, event);"
               value="<?=isset($_bspaydata[$rec['id']]['bsreason'])?$_bspaydata[$rec['id']]['bsreason']:''?>" /></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="4" class="trow">Итого по группе работников</td>
    <td><input type="text" class="borderless" id="groupsum<?=$recg['id']?>" style="text-align: right; font-weight: bold;" readonly /></td>
    <td></td>
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
         obj.parentNode.parentNode.nextElementSibling.children.length > 3) {
        elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.nextElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.nextElementSibling.children[elNum].children[0].select();
      } else if(obj.parentNode.parentNode.nextElementSibling &&
                obj.parentNode.parentNode.nextElementSibling.children.length == 3 &&
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
                 obj.parentNode.parentNode.previousElementSibling.previousElementSibling.children.length == 3) {
                   elNum = findElementNumber(obj.parentNode);
        obj.parentNode.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.children[elNum].children[0].focus();
        obj.parentNode.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.children[elNum].children[0].select();
      }
    }
  }

  function recalculatesum(obj, group_id) {
    var cnt = obj.parentNode.parentNode.parentNode.children.length;
    var i;
    var sum = 0;
    var o;
    for(i = 1; i < cnt-1; i++) {
      if(obj.parentNode.parentNode.parentNode.children[i].children.length < 4) { continue; }
      o = obj.parentNode.parentNode.parentNode.children[i].children[3].children[0];
      if(o.getAttribute('sc2group')==group_id) {
        t = o.value;
        sum += (t == '' || isNaN(t)) ? 0 : parseFloat(t);
      }
    }
    document.getElementById('groupsum'+group_id).value = sum.toFixed(2);

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
        recalculatesum(document.getElementById('sum_<?=$rece['id']?>'), '<?=$rec['id']?>');
        <?php } ?>
      <?php } ?>
    <?php } ?>
  }

  recalculateall();
</script>

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


    <?php $ig = 0; foreach($_bsgdata as $recg) { ?>


    <div class="extable" style="height: 148px; overflow-y: scroll">
      <div style="left: 0; top: 0; width: 230px;">
        <table style="height: 148px;">
          <tr>
            <th nowrap style="width: 30px;">№<br>п/п</th>
            <th nowrap>Фамилия И.О.</th>
            <th nowrap style="80px; overflow: hidden;">Должность</th>
          </tr>
        </table>
      </div>

      <div style="left: 230px; top: 0; right: 130px; height: 148px; overflow: hidden;" id="topLeftPanel">
        <table style="position: absolute; left: 0; top: 0; right: 0; bottom: 0;"  >
          <tr>
            <th colspan="<?=count($_bsadata)?>" align="left">Показатели премирования для всех категорий работников</th>
          </tr>
          <tr>
            <?php foreach($_bsadata as $rec) { ?>
              <th style="width: 45px;" nowrap><?=$rec['bsanumber']?></th>
            <?php } ?>
          </tr>
          <tr>
            <?php foreach($_bsadata as $rec) { ?>
              <th class="vertical" style="height: 110px;" align="center">
                <span>
                    <?php if($rec['bsafrom']>0) { ?>от <?=$rec['bsafrom']?> <?php } ?>до <?=$rec['bsato']?> б.в.
                </span>
              </th>
            <?php } ?>
          </tr>
        </table>
      </div>

      <div style="position: absolute; top: 0px; right: 0px; width: 130px; overflow: auto;">
        <table style="height: 148px;">
          <tr>
            <th>
              Сумма премирования
            </th>
          </tr>
        </table>
      </div>

    </div>

    <div class="extable" style="height: 350px; overflow: scroll;" onscroll="setColsBack();" id="scrollContainer">

      <div style="left: 0; top: 0; width: 230px; z-index: 2;" id="leftPanel">
        <table>
          <?php $i = 1; foreach($_bsedata[$recg['id']] as $rec) { ?>
            <tr>
              <td align="center" style="width: 30px;"><?=$i++?></td>
              <td nowrap><?=CUtilities::initials($rec['bsename'])?></td>
              <td nowrap><?=$rec['bseplace']?></td>
            </tr>
          <?php } ?>
        </table>
      </div>


      <div style="position: absolute; margin-left: 230px; left: 0px; top: 0; right: 0px; z-index: 1;">
        <table> <!-- style="table-layout: fixed;">-->
          <?php $i = 1; foreach($_bsedata[$recg['id']] as $rec) { ?>
            <tr>
              <?php foreach($_bsadata as $rec2) { ?>
                <td style="width: 45px;" nowrap align="center"><input type="text" class="input" style="width: 100%;" name="bsvalue[<?=$rec['id']?>][<?=$rec2['id']?>]"
                          id="agr_<?=$rec['id']?>_<?=$rec2['id']?>" onkeyup="recalculate(this, '<?=$recg['id']?>', '<?=$rec['id']?>', '<?=$rec2['id']?>', event);"
                          value="<?=isset($_bspaydata[$rec['id']][$rec2['id']]) ? (float)$_bspaydata[$rec['id']][$rec2['id']] : ''?>" onfocus="selectme(this);"
                           onblur="unselectme(this);"/></td>
              <?php } ?>
                <td style="width: 130px;" nowrap>
                </td>
            </tr>
          <?php } ?>
          <tr>
            <td colspan="<?=count($_bsadata)+1?>" class="trow">Итого по группе работников</td>
          </tr>
        </table>
      </div>

      <div style="position: absolute; right: 0; top: 0; width: 130px; z-index: 2;" id="rightPanel">
        <table>
          <?php foreach($_bsedata[$recg['id']] as $rec) { ?>
          <tr>
            <td><input type="text" sc2group="<?=$recg['id']?>" class="input" style="width: 100%;" id="sum_<?=$rec['id']?>" style="text-align: right;"
                  onchange="this.style.backgroundColor = 'yellow';" name="bspays[<?=$rec['id']?>" readonly onkeyup="upAndDown(this, event);" /></td>
          </tr>
          <?php } ?>

          <tr>
            <td><input type="text" class="input" id="groupsum<?=$recg['id']?>"  style="width: 100%;" style="text-align: right; font-weight: bold;" readonly /></td>
          </tr>
        </table>
      </div>
    </div>

    <?php } ?>

<?php } ?>

<label>&nbsp;</label>
<input type="submit" name="savetable" value="Сохранить" />
</form>

<form class="transpform" action="/bonussalary/printbonus" method="post">
  <input type="hidden" name="bspdate" value="<?=$bspdate?>" />
  <input type="submit" name="savetable" value="Распечатать" />
</form>



</div>

<script>
  var g_bpbasevalue = document.getElementById('bpbasevalue');
  var g_bppursebonus = document.getElementById('bppursebonus');
  var g_bpbalance = document.getElementById('bpbalance');

  var extbl = new Object();
  var extblsum = new Object();
  <?php foreach($_bsgdata as $recg) { ?>
    extbl['<?=$recg['id']?>'] = new Object();
    extblsum['<?=$recg['id']?>'] = new Object();
    <?php foreach($_bsedata[$recg['id']] as $rec) { ?>
      extbl['<?=$recg['id']?>']['<?=$rec['id']?>'] = new Object();
      <?php foreach($_bsadata as $rec2) { ?>
        extbl['<?=$recg['id']?>']['<?=$rec['id']?>']['<?=$rec2['id']?>'] = <?=isset($_bspaydata[$rec['id']][$rec2['id']]) ? (float)$_bspaydata[$rec['id']][$rec2['id']] : 0?>;
      <?php } ?>
    <?php } ?>
  <?php } ?>


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

  function recalculate(obj, group_id, bpemployee_id, bpagreement_id, event) {
    if(event) {
      upAndDown(obj, event);
    }

    var t = obj.value.replace(',','.');
    extbl[group_id][bpemployee_id][bpagreement_id] = (t == '' || isNaN(t)) ? 0 : parseFloat(t);
    var sum = 0;

    for(var k in extbl[group_id][bpemployee_id]) {
      sum += extbl[group_id][bpemployee_id][k]*1;
    }

    extblsum[group_id][bpemployee_id] = sum;

    var amount = document.getElementById('sum_' + bpemployee_id);
    amount.value = sum * g_bpbasevalue.value;
    extblsum[group_id][bpemployee_id] = amount.value;

    recalculatesum(amount, group_id);
  }

  function recalculatesum(obj, group_id) {
    var i;
    var sum = 0;

    for(var k in extblsum[group_id]) {
      sum += extblsum[group_id][k]*1;
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
        recalculate(document.getElementById('agr_<?=$rece['id']?>_'+firstagr), '<?=$rec['id']?>', '<?=$rece['id']?>', firstagr, null);
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

  function setColsBack() {
    var x = document.getElementById('scrollContainer').scrollLeft;
    document.getElementById('topLeftPanel').scrollLeft = x;
    document.getElementById('leftPanel').style.left = x+'px';
    document.getElementById('rightPanel').style.right = -x+'px';
  }

  function selectme(obj) {
    obj.parentNode.parentNode.style.borderBottom = "2px solid black";
  }

  function unselectme(obj) {
    obj.parentNode.parentNode.style.borderBottom = "none";
  }

  function resizeExtable() {
    document.getElementById('scrollContainer').style.height = (window.innerHeight - 250)+'px';
  }

  resizeExtable();
  window.onresize = function() { resizeExtable(); };
</script>

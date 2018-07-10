<div class="mainframe">

    <div class="subheader">Календарно-тематическое планирование</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>
    <?php CTemplates::showMessage('imported', 'КТП составлено из типового. Проверьте и сохраните изменения'); ?>
    <?php if(count($_days) > 0 && !isset($ktp_import)) { CTemplates::showMessage('auto', 'КТП составлено автоматически'); } ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

    <form class="transpform">
      <label>Загрузить из набора типовых КТП</label>
      <input type="button" onclick="loadFromTypical();"
             value="Выбрать и загрузить" />
    </form>

    <?php if(count($_days) > 0) { ?>
    <form class="transpform" action="/ktp/fill" method="post" name="ktpform">
        <!--<div class="largetables">-->
        <table class="noborder" width="100%" id="ktptable">
            <tr>
                <th>№ занятия:</th>
                <th nowrap>Дата</th>
                <th width="100%">Тема урока</th>
                <th nowrap></th>
            </tr>
            <?php
            $tbln=1; $impi = 0;
            foreach($_ktp as $rec) { ?>
            <?php if(isset($ktp_import)) {
              if(isset($impktp[$impi])) {
                $rec['kttopic'] = $impktp[$impi]['ktttopic'];
                $rec['ktcolor'] = $impktp[$impi]['kttcolor'];
              } else {
                $rec['kttopic'] = '';
                $rec['ktcolor'] = 0;
              }
              $impi++;
            } ?>
            <tr>
              <td align="center"><?=$tbln++?> (<?=date('w', $rec['ktdate'])==1?'пн':(date('w', $rec['ktdate'])==2?'вт':(date('w', $rec['ktdate'])==3?'ср':(date('w', $rec['ktdate'])==4?'чт':(date('w', $rec['ktdate'])==5?'пт':'сб'))))?>)</td>
              <td><input type="text" name="ktdate[<?=$rec['id']?>]"
                         value="<?=date('d.m.Y', $rec['ktdate'])?>"
                         style="width: 80px; " /></td>
              <td><input type="text" name="kttopic[<?=$rec['id']?>]"
                         value="<?=$rec['kttopic']?>"
<?php if($rec['ktcolor']==0) { ?>
                         style="width: 100%; box-sizing: border-box;"
<?php } else if($rec['ktcolor']==1) { ?>
                         style="width: 100%; box-sizing: border-box; color: red; font-weight: bold;"
<?php } else if($rec['ktcolor']==2) { ?>
                         style="width: 100%; box-sizing: border-box; color: green; font-style: italic;"
<?php } else if($rec['ktcolor']==3) { ?>
                        style="width: 100%; box-sizing: border-box; color: blue; font-weight: bold;"
<?php } ?>

                         /></td>
              <td nowrap>
                <img src="/style/icons/table.delete.png" title="удалить" onclick="deleteLesson(this);"/>
                <img src="/style/icons/table.up.png" title="поднять вверх" onclick="upLesson(this);" />
                <img src="/style/icons/table.down.png" title="опустить вниз" onclick="downLesson(this);" />
                <img src="/style/icons/table.edit.png" title="изменить цвет" onclick="changeColor(this);"/>
                <input type="hidden" name="ktcolor[<?=$rec['id']?>]" value="<?=$rec['ktcolor']?>" />
              </td>
            </tr>
            <?php } ?>
        </table>
        <!--</div>-->

            <div id="notallimport" style="display: none;">
              <h2>Внимание! Остались нераспределенными следующие темы:</h2>
            </div>
            <label></label>
            <input type="hidden" name="form_id" value="<?=$form_id?>" />
            <input type="hidden" name="subject_id" value="<?=$subject_id?>" />
            <input type="submit" name='save' value="Сохранить" />
            <input type="submit" name='saveastypical' value="Сохранить как типовое планирование" />
        </form>
      <?php } else { ?>
      <div class="emptylist">Для выбранного класса и предмета не составлено расписание</div>
      <?php } ?>

      <div id="additional_box" class="transpform" style="display: none;">
        <label>Типовое календарно-тематическое планирование</label>
        <select name="ktp_typical_info_id" id="ktp_id">
          <?php foreach($ktptypical as $rec) { ?>
            <option value="<?=$rec['kttiid']?>"><?=$rec['kttiform']?> <?=$rec['kttiname']?> (<?=$rec['tcname']?>)</option>
          <?php } ?>
        </select>
        <label></label>
        <input type="button" onclick="onView(this);" value="Просмотреть в новом окне" />
        <label></label>
        <p>Внимание! При загрузке типового КТП, ваше текущее КТП для выбранного класса и предмета будет удалено. Даты занятий автоматически будут проставлены в новом КТП. Если количество занятий в новом КТП не совпадает с Вашим, остануться пустые строки, либо не все записи из нового КТП будут перенесены. Вы действительно желаете продолжить?</p>
      </div>

      <form action="/ktp/import" method="post" id="ktp_import_form" class="transpform">
        <input type="hidden" name="ktp_typical_info_id" value="" id="ktp_import_new_id" />
        <input type="hidden" name="form_id" value="<?=$form_id?>" />
        <input type="hidden" name="subject_id" value="<?=$subject_id?>" />
      </form>


</div>

<script>
  function upLesson(obj) {
    if(obj.parentNode.parentNode.previousElementSibling==obj.parentNode.parentNode.parentNode.children[0]) {
      return;
    }

    var thisInput = obj.parentNode.parentNode.children[2].children[0];
    var prevInput = obj.parentNode.parentNode.previousElementSibling.children[2].children[0];

    var tmp = prevInput.value;
    prevInput.value = thisInput.value;
    thisInput.value = tmp;
  }

  function downLesson(obj) {
    if(obj.parentNode.parentNode.nextElementSibling==undefined) {
      return;
    }

    var thisInput = obj.parentNode.parentNode.children[2].children[0];
    var nextInput = obj.parentNode.parentNode.nextElementSibling.children[2].children[0];

    var tmp = nextInput.value;
    nextInput.value = thisInput.value;
    thisInput.value = tmp;
  }

  function deleteLesson(obj) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление урока', 'Вы действительно хотите удалить урок, планирование и все оценки, которые с ним связаны?',
                      'Нет', 'Да', function() { onDeleteLesson(obj); });
  }

  function onDeleteLesson(obj) {
    obj.parentNode.parentNode.children[1].children[0].value = '';
    obj.parentNode.parentNode.style.display = 'none';
  }

  function changeColor(obj) {
    var colorField = obj.nextElementSibling;
    colorField.value++;
    if(colorField.value > 3) {
      colorField.value=0;
    }

    switch(colorField.value) {
      case '0':
        obj.parentNode.parentNode.children[2].children[0].style.color = 'black';
        obj.parentNode.parentNode.children[2].children[0].style.fontWeight = 'normal';
        obj.parentNode.parentNode.children[2].children[0].style.fontStyle = 'normal';
        break;
      case '1':
        obj.parentNode.parentNode.children[2].children[0].style.color = 'red';
        obj.parentNode.parentNode.children[2].children[0].style.fontWeight = 'bold';
        obj.parentNode.parentNode.children[2].children[0].style.fontStyle = 'normal';
        break;
      case '2':
        obj.parentNode.parentNode.children[2].children[0].style.color = 'green';
        obj.parentNode.parentNode.children[2].children[0].style.fontWeight = 'normal';
        obj.parentNode.parentNode.children[2].children[0].style.fontStyle = 'italic';
        break;
      case '3':
        obj.parentNode.parentNode.children[2].children[0].style.color = 'blue';
        obj.parentNode.parentNode.children[2].children[0].style.fontWeight = 'bold';
        obj.parentNode.parentNode.children[2].children[0].style.fontStyle = 'normal';
        break;
    }
  }

  <?php if(isset($ktp_import) && $ktp_import) { ?>
    var ktp_imp_topic = new Array();
    var ktp_imp_color = new Array();
    var k = 0;
    <?php foreach($impktp as $rec) { ?>
      ktp_imp_topic[k] = new String('<?=s_q($rec['ktttopic'])?>');
      ktp_imp_color[k] = '<?=$rec['kttcolor']-1?>';
      k++;
    <?php } ?>
    var impi = <?=$impi?>;
  <?php } ?>

  function autoMake() {
    var curdate = new Date();
    var curyear = curdate.getMonth() >=7 ? curdate.getFullYear() : curdate.getFullYear() - 1;
    var date = new Date(curyear, 8, 1, 0, 0, 0);
    var dateend = new Date(curyear + 1, 4, 31, 23, 59, 59);
    var offs = new Array();
    var vacs = new Array();
    <?php if(isset($_dayoffs) && count($_dayoffs) > 0) {
        $io = 0;
        $iv = 0;
        foreach($_dayoffs as $rec) {
            if($rec['kdotype']==0) {
                echo "offs[$io] = {$rec['kdodate']};";
                $io++;
            } else if($rec['kdotype']==1) {
                echo "vacs[$iv] = {$rec['kdodate']};";
                $iv++;
            }
        }
    }?>
    var days = new Array();
    <?php
    if(isset($_days) && count($_days)>0) {
        $i = 0;
        foreach($_days as $rec) {
            $rec = $rec['ttday'] + 1;
            echo "days[$i] = $rec;";
            $i++;
        }
    }?>

    var isvac = false;
    var num = 1;
    var table = id('ktptable');
    while((date.getMonth()+date.getFullYear()*100) <= (dateend.getMonth()+dateend.getFullYear()*100)) {
        if(offs.indexOf(date.getTime()/1000)<0 && (isvac || vacs.indexOf(date.getTime()/1000)<0) &&
           days.indexOf(date.getDay()) >= 0) {
           var tr = document.createElement('tr');
           var td = document.createElement('td');
           td.innerHTML = num + ' (';
           if(date.getDay() === 1) {
               td.innerHTML += 'пн';
           } else if(date.getDay() === 2) {
               td.innerHTML += 'вт';
           } else if(date.getDay() === 3) {
               td.innerHTML += 'ср';
           } else if(date.getDay() === 4) {
               td.innerHTML += 'чт';
           } else if(date.getDay() === 5) {
               td.innerHTML += 'пт';
           } else if(date.getDay() === 6) {
               td.innerHTML += 'сб';
           }
           td.innerHTML += ')';
           td.align="center";
           tr.appendChild(td);
           td = document.createElement('td');

           var input = document.createElement('input');
           input.type = 'text';
           input.name = 'newktdate['+num+']';
           input.style.width = '80px';
           input.value = ("0"+date.getDate()).slice(-2)+'.'+('0'+(date.getMonth()+1)).slice(-2)+'.'+date.getFullYear();
           td.appendChild(input);

           tr.appendChild(td);
           td = document.createElement('td');
           input = document.createElement('input');
           input.type = 'text';
           input.name = 'newkttopic['+num+']';
           <?php if(isset($ktp_import) && $ktp_import) { ?>
             input.value = ktp_imp_topic[impi];
           <?php } else { ?>
             input.value = '';
           <?php } ?>
           input.style.boxSizing = 'border-box';
           input.style.width = '100%';
           num++;
           td.appendChild(input);
           tr.appendChild(td);

           td = document.createElement('td');
           var img = document.createElement('img');
           img.src = '/style/icons/table.delete.png';
           img.title = 'удалить';
           img.onclick = function() { deleteLesson(img); };
           td.appendChild(img);
           img = document.createElement('img');
           img.src = '/style/icons/table.up.png';
           img.title = 'поднять вверх';
           img.onclick = function() { upLesson(img); };
           td.appendChild(img);
           img = document.createElement('img');
           img.src = '/style/icons/table.down.png';
           img.title = 'опустить вниз';
           img.onclick = function() { downLesson(img); };
           td.appendChild(img);
           img = document.createElement('img');
           img.src = '/style/icons/table.edit.png';
           img.title = 'изменить цвет';
           img.onclick = function() { changeColor(img); };
           td.appendChild(img);
           input = document.createElement('input');
           input.type = 'hidden';
           input.name = 'newktcolor['+num+']';
           <?php if(isset($ktp_import) && $ktp_import) { ?>
             input.value = ktp_imp_color[impi];
           <?php } else { ?>
             input.value = 0;
           <?php } ?>
           td.appendChild(input);
           td.style.whiteSpace = 'nowrap';
           tr.appendChild(td);

           table.appendChild(tr);
           <?php if(isset($ktp_import) && $ktp_import) { ?>
             changeColor(img);
           <?php } ?>
          <?php if(isset($ktp_import) && $ktp_import) { ?> impi++; <?php } ?>
        }
        date.setDate(date.getDate() + 1);
    }
  }
  <?php if(count($_ktp) == 0 && count($_days) > 0) { ?>
    autoMake();
  <?php } ?>

  <?php if(isset($ktp_import)) { ?>
    var objp;
    for(i = impi; i < <?=count($impktp)?>; i++) {
      objp = document.createElement('p');
      objp.innerHTML = (i+1)+'. '+ktp_imp_topic[i];
      document.getElementById('notallimport').appendChild(objp);
    }
    document.getElementById('notallimport').style.display = 'block';
  <?php } ?>

  function loadFromTypical() {
    addinfo = document.getElementById('additional_box');
    addinfo.style.display = 'block';

    var popup = new sc2Popup();
    popup.showModal('Дополнительные сведения', addinfo, 'Отменить', 'Загрузить', function() { onSave(); });
  }

  function onSave() {
    var ktp_id = selvalue('ktp_id');
    document.getElementById('ktp_import_new_id').value = ktp_id;
    document.getElementById('ktp_import_form').submit();
  }

  function onView(obj) {
    obj = obj.parentNode.children[1];
    var ktp_typical_info_id = obj.options[obj.selectedIndex].value;
    if(parseInt(ktp_typical_info_id) > 0) {
      window.open('/ktp/viewtypical/'+parseInt(ktp_typical_info_id), '_blank');
    }
  }

</script>

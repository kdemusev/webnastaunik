<div class="mainframe">
  <div class="subheader">Рейтинги учащихся по предметам</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reportrating" method="post">
    <label>Тип олимпиады:</label>
    <select name="setolymptype" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['olymptype']==1) {?>selected<?php } ?> >все</option>
      <option value="1" <?php if($_SESSION['olymptype']==1) {?>selected<?php } ?> >республиканская</option>
      <option value="2" <?php if($_SESSION['olymptype']==2) {?>selected<?php } ?> >областная</option>
      <option value="3" <?php if($_SESSION['olymptype']==3) {?>selected<?php } ?> >районная</option>
    </select>

    <label>Учебный год:</label>
    <select name="setolymp_year_id" onchange="document.formform.submit();">
    <?php foreach($years as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_year_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['oyname']?></option>
    <?php } ?>
    </select>

    <label>Предмет:</label>
    <select name="setolymp_subject_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['olymp_subject_id']==0) {?>selected<?php } ?> >Все предметы</option>
    <?php foreach($subjects as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_subject_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['osname']?></option>
    <?php } ?>
    </select>

    <label>Класс:</label>
    <select name="setolymp_form_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['olymp_form_id']==0) {?>selected<?php } ?> >Все классы</option>
    <?php foreach($forms as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_form_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['ofname']?></option>
    <?php } ?>
    </select>

  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>

  <form class="transpform">
    <label>Фильтр:</label>
    <input type="radio" id="rf1" class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="fio" checked="checked" />
    <label for="rf1" class="button">По фамилии, имени ученика</label>
    <input type="radio" id="rf2"  class="customRadioButton"
           onchange="filter(document.getElementById('filterbox'));"
           name="filt" value="school" />
    <label for="rf2" class="button">По учреждению образования</label><br />
    <input type="text" id="filterbox" onkeyup="filter(this);" onchange="filter(this);" autocomplete="off" />
  </form>


  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Рейтинг участников II этапа
        <?php if($_SESSION['olymptype']==1) { ?>республиканской
        <?php } else if($_SESSION['olymptype']==2) { ?>областной
        <?php } else if($_SESSION['olymptype']==3) { ?>районной
        <?php } ?>олимпиады<br>
        <?php if($_SESSION['olymp_subject_id']>0) { ?>
          по предмету &quot;<?=$osname?>&quot;<br />
        <?php } ?>
        в <?=$oyname?> учебном году
      </h1>
  	</div>

    <table class="smptable overablerow" id="olymptable">
  		<thead>
  			<tr>
          <?php if($_SESSION['olymp_subject_id']==0) { ?><th>Предмет</th><?php } ?>
  				<th>Место в рейтинге</th>
  				<th>Фамилия, имя ученика</th>
  				<th>Класс</th>
  				<th>Учреждение образования</th>
          <th>Учитель</th>
          <th>Максимальное количество баллов</th>
          <th>Количество баллов</th>
          <th>Процент выполнения</th>
          <th>Диплом</th>
          <th>Примечание</th>
  			</tr>
  		</thead>
  		<?php if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <?php if($_SESSION['olymp_subject_id']==0) { ?>
            <td align="center">
              <?=$rec['osname']?>
            </td>
          <?php } ?>
  				<td align="center">
  					<?=$rec['olrating']==0 ? '' :$rec['olrating']?>
  				</td>
  				<td>
  					<?=$rec['opname']?>
  				</td>
  				<td align="center">
  					<?=$rec['ofname']?>
  				</td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td>
  					<?=$rec['otname']?>
  				</td>
          <td align="center">
  					<?=$rec['olmaxpoints']?>
  				</td>
          <td align="center">
  					<?=$rec['olpoints']?>
  				</td>
          <td align="center">
            <?=$rec['olpercent']?>
          </td>
          <td align="center" nowrap>
  					<?php if(isset($rec['oldiploma']) && $rec['oldiploma']==1) { ?>
              I степени
            <?php } else if(isset($rec['oldiploma']) && $rec['oldiploma']==2) { ?>
              II степени
            <?php } else if(isset($rec['oldiploma']) && $rec['oldiploma']==3) { ?>
              III степени
            <?php } ?>
  				</td>
  				<td>
            <?php if(isset($rec['olnopassport']) && $rec['olnopassport']==1) { ?>
              отсутствует документ <br />
            <?php } ?>
            <?php if(isset($rec['olabsend']) && $rec['olabsend']==1) { ?>
              не участвовал <br />
            <?php } ?>
            <?php if(isset($rec['olnoinapplication']) && $rec['olnoinapplication']==1) { ?>
              нет в заявке <br />
            <?php } ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="<?php if($_SESSION['olymp_subject_id']==0) { ?>11<?php } else {?>10<?php } ?>" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>
  </div>



</div>

<script src="/js/sc2print.js"></script>

<script>
  function filter(obj) {
    var coln = document.getElementById('rf1').checked ? 2 : 4;
    <?php if($_SESSION['olymp_subject_id']>0) { ?>
      coln--;
    <?php } ?>
    var text = obj.value.toLowerCase().trim();
    var tbl = document.getElementById('olymptable');
    var i;
    var l = tbl.children[1].children.length;
    var td;

    for(i = 0; i < l; i++) {
        td = tbl.children[1].children[i].children[coln];
        if(td.textContent.toLowerCase().indexOf(text) >= 0) {
          td.parentNode.style.display = 'table-row';
        } else {
          td.parentNode.style.display = 'none';
        }
    }

  }
</script>

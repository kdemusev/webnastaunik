<div class="mainframe">
  <div class="subheader">Участие в конкурсах</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbconcurs/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbconcurs/reportrating" method="post">
    <label>Уровень проведения:</label>
    <select name="setconcurstype" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['concurstype']==0) {?>selected<?php } ?> >все уровни</option>
      <option value="1" <?php if($_SESSION['concurstype']==1) {?>selected<?php } ?> >районный</option>
      <option value="2" <?php if($_SESSION['concurstype']==2) {?>selected<?php } ?> >региональный</option>
      <option value="3" <?php if($_SESSION['concurstype']==3) {?>selected<?php } ?> >областной</option>
      <option value="4" <?php if($_SESSION['concurstype']==4) {?>selected<?php } ?> >республиканский</option>
    </select>

    <label>Учебный год:</label>
    <select name="setolymp_year_id" onchange="document.formform.submit();">
    <?php foreach($years as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_year_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['oyname']?></option>
    <?php } ?>
    </select>

    <label>Название конкурса:</label>
    <select name="setconcurs_type_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['concurs_type_id']==0) {?>selected<?php } ?> >Все конкурсы</option>
    <?php foreach($concurses as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['concurs_type_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['ctname']?></option>
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
  		<h1 align="center">Информация об участии в конкурсах
        <?php if($_SESSION['concurstype']==1) { ?>районного уровня<br />
        <?php } else if($_SESSION['concurstype']==2) { ?>регионального уровня<br />
        <?php } else if($_SESSION['concurstype']==3) { ?>областного уровня<br />
        <?php } else if($_SESSION['concurstype']==4) { ?>республиканского уровня<br />
        <?php } ?>
        в <?=$oyname?> учебном году
      </h1>
  	</div>

    <table class="smptable overablerow" id="olymptable">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <?php if($_SESSION['concurstype']==0) { ?><th>Уровень проведения</th><?php } ?>
          <th>Название конкурса</th>
          <th>Секция</th>
          <th>Учреждение образования</th>
          <th>Учитель</th>
          <th>Сведения об учащихся</th>
          <th>Название конкурсной работы</th>
          <th>Диплом</th>
          <th>Приглашение дальнейшего участия</th>
  			</tr>
  		</thead>
  		<?php $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <td align="center">
            <?=$i++?>
          </td>
          <?php if($_SESSION['concurstype']==0) { ?>
            <td align="center">
              <?php if($rec['concurstype']==1) { ?>районный<br />
              <?php } else if($rec['concurstype']==2) { ?>региональный<br />
              <?php } else if($rec['concurstype']==3) { ?>областной<br />
              <?php } else if($rec['concurstype']==4) { ?>республиканский<br />
              <?php } ?>
            </td>
          <?php } ?>
  				<td>
  					<?=$rec['ctname']?>
  				</td>
          <td>
  					<?=$rec['csname']?>
  				</td>
  				<td>
  					<?=$rec['oscname']?>
  				</td>
          <td>
  					<?=$rec['otname']?>
  				</td>
          <td>
            <?php foreach($data_pupils[$rec['ctid']] as $rec2) { ?>
              <?=$rec2['opname']?> (<?=$rec2['ofname']?> класс)<br />
            <?php } ?>
          </td>
          <td>
  					<?=$rec['cnname']?>
  				</td>
          <td align="center" nowrap>
  					<?php if($rec['ctdiploma']==1) { ?>
              I степени
            <?php } else if($rec['ctdiploma']==2) { ?>
              II степени
            <?php } else if($rec['ctdiploma']==3) { ?>
              III степени
            <?php } ?>
  				</td>
  				<td align="center">
            <?php if($rec['ctismore']==1) { ?>
              да <br />
            <?php } ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="<?=9+($_SESSION['concurstype']==0 ? 1 : 0)?>" align="center">
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
    var coln = document.getElementById('rf1').checked ? 6 : 4;
    <?php if($_SESSION['concurstype']>0) { ?>
      coln--;
    <?php } ?>
    var text = obj.value.toLowerCase().trim();
    var tbl = document.getElementById('olymptable');
    var i;
    var l = tbl.children[1].children.length;
    var td;

    for(i = 0; i < l; i++) {
        td = tbl.children[1].children[i].children[coln];
        if(!td) { continue; }
        if(td.textContent.toLowerCase().indexOf(text) >= 0) {
          td.parentNode.style.display = 'table-row';
        } else {
          td.parentNode.style.display = 'none';
        }
    }

  }
</script>

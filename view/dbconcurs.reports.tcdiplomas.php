<div class="mainframe">
  <div class="subheader">Информация о подготовке победителей конкурсов педагогами</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbconcurs/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbconcurs/reporttcdiplomas" method="post">
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
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>

  <form class="transpform">
    <label>Фильтр по учреждению образования:</label>
    <input type="text" id="filterbox" onkeyup="filter(this);" onchange="filter(this);" autocomplete="off" />
  </form>


  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Информация о подготовке победителей конкурсов
        <?php if($_SESSION['concurstype']==1) { ?>районного уровня<br />
        <?php } else if($_SESSION['concurstype']==2) { ?>регионального уровня
        <?php } else if($_SESSION['concurstype']==3) { ?>областного уровня
        <?php } else if($_SESSION['concurstype']==4) { ?>республиканского уровня
        <?php } ?> педагогами<br />
        в <?=$oyname?> учебном году
      </h1>
  	</div>

    <table class="smptable overablerow" id="olymptable">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <?php if($_SESSION['concurstype']==0) { ?><th>Уровень проведения</th><?php } ?>
          <th>Фамилия, имя, отчество педагога</th>
          <th>Учреждение образования</th>
          <th>Конкурсы</th>
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
            <?=$rec['otname']?>
          </td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td>
            <?php $d = 0; foreach($data_works[$rec['olymp_teacher_id']] as $rec2) { ?>
              <?=$rec2['ctname']?>: дипломов <?=$rec2['cntdipl']?><br>
              <?php $d+=$rec2['cntdipl']; ?>
            <?php } ?>
            <b>Всего: <?=$d?></b>
          </td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="<?=4+($_SESSION['concurstype']==0 ? 1 : 0)?>" align="center">
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
    var coln = 3;
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

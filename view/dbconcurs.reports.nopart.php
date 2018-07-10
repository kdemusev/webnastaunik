<div class="mainframe">
  <div class="subheader">Перечень учреждений, которые не принимали участие в конкурсах</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbconcurs/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbconcurs/reportnopart" method="post">
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
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Перечень учреждений, которые не принимали участие в конкурсах
          в <?=$oyname?> учебном году
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <th>Учреждение образования</th>
          <th>Перечень конкурсов, по которым отсутствовали участники</th>
  			</tr>
  		</thead>
  		<?php $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
  				<td align="center">
  					<?=$i++?>
  				</td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td align="center">
            <?php if(isset($datasubjects[$rec['id']]) && count($datasubjects[$rec['id']])) { ?>
              <?php foreach($datasubjects[$rec['id']] as $rec2) { ?>
              <?=$rec2?><br />
            <?php } ?>
            <?php } else { ?>
              По всем выбранным конкурсам представлены работы
            <?php } ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="3" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>
  </div>



</div>

<script src="/js/sc2print.js"></script>

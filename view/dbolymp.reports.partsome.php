<div class="mainframe">
  <div class="subheader">Список участников олимпиады по нескольким предметам</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reportpartsome" method="post">
    <label>Тип олимпиады:</label>
    <select name="setolymptype" onchange="document.formform.submit();">
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
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Список участников II этапа
        <?php if($_SESSION['olymptype']==1) { ?>республиканской
        <?php } else if($_SESSION['olymptype']==2) { ?>областной
        <?php } else if($_SESSION['olymptype']==3) { ?>районной
        <?php } ?>олимпиады<br>
        в <?=$oyname?> учебном году, <br />
        принимавших участие в олимпиадах по нескольким предметам
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <th>Количество предметов</th>
          <th>Перечень предметов</th>
  				<th>Фамилия, имя ученика</th>
  				<th>Класс</th>
  				<th>Учреждение образования</th>
          <th>Достижения</th>
  			</tr>
  		</thead>
  		<?php $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <td align="center">
            <?=$i++?>
          </td>
          <td align="center">
            <?=$rec['cnt']?>
          </td>
          <td>
            <?php foreach($datasubjects[$rec['olymp_pupil_id']] as $rec2) { ?>
              <?=$rec2['osname']?><br />
            <?php } ?>
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
            <?php foreach($datasubjects[$rec['olymp_pupil_id']] as $rec2) { ?>
              <?php if($rec2['oldiploma']==1) { ?>
                диплом I степени по предмету &quot;<?=$rec2['osname']?>&quot;<br />
              <?php } else if($rec2['oldiploma']==2) { ?>
                диплом II степени по предмету &quot;<?=$rec2['osname']?>&quot;<br />
              <?php } else if($rec2['oldiploma']==3) { ?>
                диплом III степени по предмету &quot;<?=$rec2['osname']?>&quot;<br />
              <?php } ?>

            <?php } ?>
          </td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="7" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>

  </div>



</div>

<script src="/js/sc2print.js"></script>

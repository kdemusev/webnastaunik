<div class="mainframe">
  <div class="subheader">Список дипломов по результатам проведения олимпиады</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reportdiplomas" method="post">
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
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Список дипломов по результатам проведения II этапа
        <?php if($_SESSION['olymptype']==1) { ?>республиканской
        <?php } else if($_SESSION['olymptype']==2) { ?>областной
        <?php } else if($_SESSION['olymptype']==3) { ?>районной
        <?php } ?>олимпиады<br>
        <?php if($_SESSION['olymp_subject_id']>0) { ?>
          по предмету &quot;<?=$oyname?>&quot;<br />
        <?php } ?>
        в <?=$data[0]['oyname']?> учебном году
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <?php if($_SESSION['olymp_subject_id']==0) { ?><th>Предмет</th><?php } ?>
          <th>Диплом</th>
  				<th>Фамилия, имя ученика</th>
  				<th>Класс</th>
  				<th>Учреждение образования</th>
          <th>Учитель</th>
  			</tr>
  		</thead>
  		<?php foreach($subjects as $rec) {
              $d1[$rec['id']]=0;
              $d2[$rec['id']]=0;
              $d3[$rec['id']]=0;
            }
            $td1 = 0;
            $td2 = 0;
            $td3 = 0;
            $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <td align="center">
            <?=$i++?>
          </td>
          <?php if($_SESSION['olymp_subject_id']==0) { ?>
            <td align="center">
              <?=$rec['osname']?>
            </td>
          <?php } ?>
          <td align="center" nowrap>
  					<?php if(isset($rec['oldiploma']) && $rec['oldiploma']==1) { $d1[$rec['olymp_subject_id']]++; $td1++; ?>
              I степени
            <?php } else if(isset($rec['oldiploma']) && $rec['oldiploma']==2) { $d2[$rec['olymp_subject_id']]++; $td2++; ?>
              II степени
            <?php } else if(isset($rec['oldiploma']) && $rec['oldiploma']==3) { $d3[$rec['olymp_subject_id']]++; $td3++; ?>
              III степени
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
  					<?=$rec['otname']?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="<?php if($_SESSION['olymp_subject_id']==0) { ?>7<?php } else {?>6<?php } ?>" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>

    <br />
    Всего дипломов: <br />
    &nbsp;&nbsp;&nbsp;I степени - <b><?=$td1?></b>, <br />
    &nbsp;&nbsp;&nbsp;II степени - <b><?=$td2?></b>, <br />
    &nbsp;&nbsp;&nbsp;III степени - <b><?=$td3?></b> <br /><br />
    <?php if($_SESSION['olymp_subject_id']==0) { ?>
      в том числе:<br />
      <?php foreach($subjects as $rec) { ?>
        по предмету &quot;<?=$rec['osname']?>&quot;: <br />
        &nbsp;&nbsp;&nbsp;I степени - <b><?=$d1[$rec['id']]?></b>, <br />
        &nbsp;&nbsp;&nbsp;II степени - <b><?=$d2[$rec['id']]?></b>, <br />
        &nbsp;&nbsp;&nbsp;III степени - <b><?=$d3[$rec['id']]?></b> <br /><br />
      <?php } ?>
    <?php } ?>
  </div>



</div>

<script src="/js/sc2print.js"></script>

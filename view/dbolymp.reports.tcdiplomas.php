<div class="mainframe">
  <div class="subheader">Рейтинг педагогов по количеству дипломов</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reporttcdiplomas" method="post">
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

    <label>Предмет:</label>
    <select name="setolymp_subject_id" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['olymp_subject_id']==0) {?>selected<?php } ?> >Все предметы</option>
    <?php foreach($subjects as $rec) { ?>
      <option value="<?=$rec['id']?>" <?php if($_SESSION['olymp_subject_id']==$rec['id']) {?>selected<?php } ?> ><?=$rec['osname']?></option>
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
  		<h1 align="center">Рейтинг педагогов по количеству дипломов по результатам проведения II этапа
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

    <table class="smptable overablerow">
  		<thead>
  			<tr>
  				<th>Место в рейтинге</th>
          <th>Количество дипломов</th>
          <th>Фамилия, имя, отчество педагога</th>
  				<th>Учреждение образования</th>
          <th>Всего участников от педагога</th>
          <th>Предметы, по которым педагог подготовил победителей</th>
  			</tr>
  		</thead>
  		<?php $i = 0; $lastcnt = -1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
  				<td align="center">
            <?php if($lastcnt != $rec['cnt']) {
              $i++;
              $lastcnt = $rec['cnt'];
            } ?>
            <?=$i?>
  				</td>
          <td align="center">
            Всего: <b><?=$rec['cnt']?></b><br />
            <?php if($rec['cnt1']>0) { ?>
              I степени - <?=$rec['cnt1']?><br />
            <?php } if($rec['cnt2']>0) { ?>
              II степени - <?=$rec['cnt2']?><br />
            <?php } if($rec['cnt3']>0) { ?>
              III степени - <?=$rec['cnt3']?><br />
            <?php } ?>
  				</td>
          <td>
  					<?=$rec['otname']?>
  				</td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td align="center">
  					<?=$rec['pupcnt']?>
  				</td>
          <td>
  					<?php if(isset($datasubjects[$rec['olymp_teacher_id']])) { foreach($datasubjects[$rec['olymp_teacher_id']] as $rec2) { ?>
              <?=$rec2['osname']?> (<?=$rec2['cnt']?>)<br />
            <?php } } ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="6" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>
  </div>

</div>

<script src="/js/sc2print.js"></script>

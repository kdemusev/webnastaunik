<div class="mainframe">
  <div class="subheader">Результаты III этапа олимпиады</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reportresreg" method="post">
    <label>Тип олимпиады:</label>
    <select name="setolymptype" onchange="document.formform.submit();">
      <option value="1" <?php if($_SESSION['olymptype']==1) {?>selected<?php } ?> >республиканская</option>
      <option value="2" <?php if($_SESSION['olymptype']==2) {?>selected<?php } ?> >областная</option>
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
  		<h1 align="center">Результаты III этапа
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
          <th>№ п/п</th>
          <?php if($_SESSION['olymp_subject_id']==0) { ?><th>Предмет</th><?php } ?>
          <th>Рейтинг</th>
          <th>Диплом</th>
  				<th>Фамилия, имя ученика</th>
  				<th>Класс</th>
  				<th>Учреждение образования</th>
          <th>Учитель</th>
          <th>Примечание</th>
  			</tr>
  		</thead>
  		<?php $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <td align="center">
            <?=$i++?>
          </td>
          <?php if($_SESSION['olymp_subject_id']==0) { ?>
            <td align="center">
              <?=$rec['osname']?>
            </td>
          <?php } ?>
          <td align="center">
  					<?=$rec['olregrating']?>
  				</td>
          <td align="center" nowrap>
            <?php if(isset($rec['olregdiploma']) && $rec['olregdiploma']==1) { ?>
              I степени
            <?php } else if(isset($rec['olregdiploma']) && $rec['olregdiploma']==2) { ?>
              II степени
            <?php } else if(isset($rec['olregdiploma']) && $rec['olregdiploma']==3) { ?>
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
  				<td>
            <?php if(isset($rec['olregabsend']) && $rec['olregabsend']==1) { ?>
              не явился
            <?php } ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="<?php if($_SESSION['olymp_subject_id']==0) { ?>9<?php } else {?>8<?php } ?>" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>

  </div>



</div>

<script src="/js/sc2print.js"></script>

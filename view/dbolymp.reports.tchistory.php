<div class="mainframe">
  <div class="subheader">История подготовки к олимпиадному движению педагогами учреждений</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbolymp/reporttchistory" method="post">
    <label>Тип олимпиады:</label>
    <select name="setolymptype" onchange="document.formform.submit();">
      <option value="1" <?php if($_SESSION['olymptype']==1) {?>selected<?php } ?> >республиканская</option>
      <option value="2" <?php if($_SESSION['olymptype']==2) {?>selected<?php } ?> >областная</option>
      <option value="3" <?php if($_SESSION['olymptype']==3) {?>selected<?php } ?> >районная</option>
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
  		<h1 align="center">История подготовки педагогами учреждений учащихся ко II этапу
        <?php if($_SESSION['olymptype']==1) { ?>республиканской
        <?php } else if($_SESSION['olymptype']==2) { ?>областной
        <?php } else if($_SESSION['olymptype']==3) { ?>районной
        <?php } ?>олимпиады<br>
        <?php if($_SESSION['olymp_subject_id']>0) { ?>
          по предмету &quot;<?=$osname?>&quot;<br />
        <?php } ?>
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <th>Фамилия, имя, отчество педагога</th>
          <th>Учреждение образования</th>
          <th>Количество дипломов</th>
          <th>Результат участия</th>
  			</tr>
  		</thead>
  		<?php $i = 1; $k = 0; if(count($data)) { while(isset($data[$k])) { $rec = $data[$k]; ?>
  			<tr>
  				<td align="center">
  					<?=$i++?>
  				</td>
          <td>
            <?=$rec['otname']?>
          </td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td align="center">
            <?php $kt = $k; do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=$rec['cnt']?><br />
            <?php $k++; } while(isset($data[$k]) && $data[$k-1]['olymp_teacher_id']==$data[$k]['olymp_teacher_id']); $k = $kt; ?>
  				</td>
          <td align="center">
            <?php do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=round($rec['percent'])?>%<br />
            <?php $k++; } while(isset($data[$k]) && $data[$k-1]['olymp_teacher_id']==$data[$k]['olymp_teacher_id']); ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="5" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>
  </div>



</div>

<script src="/js/sc2print.js"></script>

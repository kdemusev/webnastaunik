<div class="mainframe">
  <div class="subheader">История участия учреждений в конкурсах</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbconcurs/reports'"
           value="Вернуться к списку" />
  </form>

  <form class="transpform" name="formform"
        action="/dbconcurs/reportschistory" method="post">
    <label>Уровень проведения:</label>
    <select name="setconcurstype" onchange="document.formform.submit();">
      <option value="0" <?php if($_SESSION['concurstype']==0) {?>selected<?php } ?> >все уровни</option>
      <option value="1" <?php if($_SESSION['concurstype']==1) {?>selected<?php } ?> >районный</option>
      <option value="2" <?php if($_SESSION['concurstype']==2) {?>selected<?php } ?> >региональный</option>
      <option value="3" <?php if($_SESSION['concurstype']==3) {?>selected<?php } ?> >областной</option>
      <option value="4" <?php if($_SESSION['concurstype']==4) {?>selected<?php } ?> >республиканский</option>
    </select>
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">История участия учреждений в конкурсах
        <?php if($_SESSION['concurstype']==1) { ?>районного уровня<br />
        <?php } else if($_SESSION['concurstype']==2) { ?>регионального уровня<br />
        <?php } else if($_SESSION['concurstype']==3) { ?>областного уровня<br />
        <?php } else if($_SESSION['concurstype']==4) { ?>республиканского уровня<br />
        <?php } ?>
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <th>Учреждение образования</th>
          <th>Количество работ</th>
          <th>Количество побед</th>
  			</tr>
  		</thead>
  		<?php $i = 1; $k = 0; if(count($data)) { while(isset($data[$k])) { $rec = $data[$k]; ?>
  			<tr>
  				<td align="center">
  					<?=$i++?>
  				</td>
          <td>
  					<?=$rec['oscname']?>
  				</td>
          <td align="center">
            <?php $kt = $k; do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=$rec['cnt']?><br />
            <?php $k++; } while(isset($data[$k]) && $data[$k-1]['olymp_school_id']==$data[$k]['olymp_school_id']); $k = $kt; ?>
  				</td>
          <td align="center">
            <?php do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=$rec['cntdipl']?><br />
            <?php $k++; } while(isset($data[$k]) && $data[$k-1]['olymp_school_id']==$data[$k]['olymp_school_id']); ?>
  				</td>
  			</tr>
  		<?php } } else { ?>
        <tr>
          <td colspan="4" align="center">
            <i>Не найдено записей по заданному условию</i>
          </td>
        </tr>
      <?php } ?>
  	</table>
  </div>



</div>

<script src="/js/sc2print.js"></script>

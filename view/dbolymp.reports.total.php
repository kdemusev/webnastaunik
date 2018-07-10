<div class="mainframe">
  <div class="subheader">История одаренных учащихся</div>

  <form class="transpform" name="formform"
        action="/dbolymp/total" method="post">
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">История одаренных учащихся
      </h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
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
  					<?=$rec['oscname']?>
  				</td>
          <td align="center">
            <?php $kt = $k; do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=$rec['cnt']?><br />
            <?php $k++; } while(isset($data[$k]) && $data[$k-1]['olymp_school_id']==$data[$k]['olymp_school_id']); $k = $kt; ?>
  				</td>
          <td align="center">
            <?php do {  $rec = $data[$k]; ?>
              <?=$rec['oyname']?> - <?=round($rec['percent'])?>%<br />
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

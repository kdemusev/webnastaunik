<div class="mainframe">
  <div class="subheader">Список учащихся, участвующих в олимпиадах по одним и тем же предметам из года в год</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbolymp/reports'"
           value="Вернуться к списку" />
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>
  <br />

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Список учащихся, участвующих в олимпиадах по одним и тем же предметам из года в год</h1>
  	</div>

    <table class="smptable overablerow">
  		<thead>
  			<tr>
          <th>№ п/п</th>
  				<th>Фамилия, имя учащегося</th>
  				<th>Класс</th>
  				<th>Учреждение образования</th>
          <th>История участия</th>
  			</tr>
  		</thead>
  		<?php $i = 1; if(count($data)) { foreach($data as $rec) { ?>
  			<tr>
          <td align="center">
            <?=$i++?>
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
              <?=$rec2['oyname']?>: <?=$rec2['osname']?> (<?=$rec2['olrating']?> в рейтинге<?php if($rec2['oldiploma']==1) { ?>, диплом I степени<?php } else if($rec2['oldiploma']==2) { ?>, диплом II степени<?php } else  if($rec2['oldiploma']==3) { ?>, диплом III степени<?php } ?>)<br />
            <?php } ?>
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

<div class="mainframe">
  <div class="subheader">Информация об учащихся, вышедших из конкурсного движения</div>

  <form class="transpform">
    <input type="button" onclick="window.location='/dbconcurs/reports'"
           value="Вернуться к списку" />
  </form>

  <div class="transpform">
  <button onclick="sc2Print();">Распечатать</button>
  </div>

  <div id="toprint">
  	<div class="printable">
  		<center><?=$uoname?></center>
  		<h1 align="center">Информация об учащихся, вышедших из конкурсного движения
      </h1>
  	</div>

    <table class="smptable overablerow" id="olymptable">
  		<thead>
  			<tr>
          <th>№ п/п</th>
          <th>Фамилия, имя, отчество учащегося</th>
          <th>Класс</th>
          <th>Учреждение образования</th>
          <th>Конкурсы</th>
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
            <?php foreach($data_concurses[$rec['olymp_pupil_id']] as $rec2) { ?>
              <?=$rec2['oyname']?>: <?=$rec2['ctname']?> (<?=$rec2['csname']?>)
              <?php if($rec2['ctdiploma']==1) { ?>
                - диплом I степени
              <?php } else if($rec2['ctdiploma']==2) { ?>
                - диплом II степени
              <?php } else if($rec2['ctdiploma']==3) { ?>
                - диплом III степени
              <?php } ?><br />
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

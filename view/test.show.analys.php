<div class="mainframe">
  <div class="subheader">Анализ выполнения теста <?=$data[0]['tsname']?></div>

  <form class="transpform">
    <input type="button" onclick="window.location='/test/showlist';" value="Список тестов" />
    <input type="button" onclick="window.location='/test/show/<?=$data[0]['id']?>';" value="Общие результаты данного теста" />
  </form>

  <p>Всего ответов: <b><?=$total?></b></p>

  <p>Анализ выполнения:<br>
  0%-10%: <b><?=$percanalys[0]?>%</b> тестируемых<br>
  11%-20%: <b><?=$percanalys[1]?>%</b> тестируемых<br>
  21%-30%: <b><?=$percanalys[2]?>%</b> тестируемых<br>
  31%-40%: <b><?=$percanalys[3]?>%</b> тестируемых<br>
  41%-50%: <b><?=$percanalys[4]?>%</b> тестируемых<br>
  51%-60%: <b><?=$percanalys[5]?>%</b> тестируемых<br>
  61%-70%: <b><?=$percanalys[6]?>%</b> тестируемых<br>
  71%-80%: <b><?=$percanalys[7]?>%</b> тестируемых<br>
  81%-90%: <b><?=$percanalys[8]?>%</b> тестируемых<br>
  91%-100%: <b><?=$percanalys[9]?>%</b> тестируемых<br>
  </p>

  <?php if($total == 0) { $total = 1; } ?>

  <form class="transpform">
    <?php $testnumber = 1; foreach($test as $rec) { ?>
    	<?=$testnumber++?>. <?=$rec['tttask']?><br />
        Правильных ответов:
        <?php if($rec['tttype'] == 1) { ?>
          <?php if(!isset($analys[$rec['id']])) { $analys[$rec['id']]['rightcnt'] = $analys[$rec['id']]['totalcnt'] = 0; } ?>
          <b><?=$analys[$rec['id']]['rightcnt']?> из <?=$analys[$rec['id']]['totalcnt']?> (<?=round($analys[$rec['id']]['rightcnt']/$analys[$rec['id']]['totalcnt']*100)?>%)</b>
          <br /><br />
        <?php } ?>
        <?php if($rec['tttype'] == 2) { ?>
          <?php if(!isset($analys[$rec['id']])) { $analys[$rec['id']]['rightcnt'] = $analys[$rec['id']]['totalcnt'] = 0; } ?>
          <b><?=$analys[$rec['id']]['rightcnt']?> из <?=$analys[$rec['id']]['totalcnt']?> (<?=round($analys[$rec['id']]['rightcnt']/$analys[$rec['id']]['totalcnt']*100)?>%)</b>
          <br /><br />
        <?php } ?>
    <?php } ?>
  </form>

</div>

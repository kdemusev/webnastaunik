<div class="mainframe">
  <div class="subheader">Результаты теста <?=$data[0]['tsname']?></div>

  <p>Ваш результат: <?=$user[0]['trcount']?> из <?=$total?> (<b><?=$user[0]['trpercent']?>%</b>)</p>

  <form class="transpform">
    <?php $testnumber = 1; foreach($test as $rec) { ?>
    	<?=$testnumber++?>. <?=$rec['tttask']?><br />
      	<?php if($rec['tttype'] == 1) { ?>
          <?php foreach($testvars[$rec['id']] as $vars) { ?>
            <input type="radio" name="results[<?=$rec['id']?>]"
                   value="<?=$vars['id']?>"
                   <?php if($details[$rec['id']]==$vars['id']) { ?>checked<?php } ?> disabled/><?=$vars['tvtrue']==1?'<b>':''?><?=$vars['tvvar']?><?=$vars['tvtrue']==1?'</b>':''?><br />
          <?php } ?>
          <br /><br />
        <?php } ?>
        <?php if($rec['tttype'] == 2) { ?>
          <?php foreach($testvars[$rec['id']] as $vars) { ?>
            <input type="checkbox" name="results[<?=$rec['id']?>]"
                   value="<?=$vars['id']?>"
                   <?php if(in_array($vars['id'], $details[$rec['id']])) { ?>checked<?php } ?> disabled/><?=$vars['tvtrue']==1?'<b>':''?><?=$vars['tvvar']?><?=$vars['tvtrue']==1?'</b>':''?><br />
          <?php } ?>
          <br /><br />
        <?php } ?>
    <?php } ?>
  </form>

</div>

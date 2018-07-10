<div class="mainframe">
  <div class="subheader">Тест <?=$data[0]['tsname']?></div>

  <p><?=$data[0]['tsdesc']?></p>

  <form class="transpform" method="post" action="/test/complete/<?=$data[0]['id']?>" >

    <?php $testnumber = 1; foreach($test as $rec) { ?>
      <input type="hidden" name="testtask_id[]" value="<?=$rec['id']?>" />
    	<?=$testnumber++?>. <?=$rec['tttask']?><br />
      	<?php if($rec['tttype'] == 1) { ?>
          <?php foreach($testvars[$rec['id']] as $vars) { ?>
            <input type="radio" name="results[<?=$rec['id']?>]" value="<?=$vars['id']?>" /><?=$vars['tvvar']?><br />
          <?php } ?>
          <br /><br />
        <?php } ?>
        <?php if($rec['tttype'] == 2) { ?>
          <?php foreach($testvars[$rec['id']] as $vars) { ?>
            <input type="checkbox" style="width: 16px; height: 16px;" name="results[<?=$rec['id']?>][]" value="<?=$vars['id']?>" /><?=$vars['tvvar']?><br />
          <?php } ?>
          <br /><br />
        <?php } ?>
    <?php } ?>
    <input type="submit" name="save" value="Отправить ответы" />
  </form>

</div>

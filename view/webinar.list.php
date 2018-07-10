<div class="mainframe">
  <div class="subheader">Дистанционные семинары</div>

  <?php CTemplates::showMessage('added', 'Дистанционный семинар создан'); ?>
  <?php CTemplates::showMessage('deleted', 'Дистанционный семинар удален'); ?>

<form class="transpform" action="/webinar/wblist" method="post">
  <label>Уровень проведения</label>
  <input type="radio" id="rType1" class="customRadioButton" name="wbtype"
         value="0"
         <?php if($wbtype==0) { ?> checked="checked" <?php } ?>
         onchange='this.parentNode.submit();' />
  <label for="rType1" class="button">Районные семинары</label>
  <input type="radio" id="rType2"  class="customRadioButton" name="wbtype"
         value="1"
         <?php if($wbtype==1) { ?> checked="checked" <?php } ?>
         onchange='this.parentNode.submit();' />
  <label for="rType2" class="button">Областные семинары</label>
  <input type="radio" id="rType3"  class="customRadioButton" name="wbtype"
         value="2"
         <?php if($wbtype==2) { ?> checked="checked" <?php } ?>
         onchange='this.parentNode.submit();' />
  <label for="rType3" class="button">Все семинары</label>
</form>
  <br /><br />

<?php if(count($webinars) > 0) { ?>
<div class="topics">
  <?php foreach($webinars as $rec) { ?>
  <div class="topic">
    <div class="desc">
      <a href="/webinar/show/<?=$rec['wid']?>"><?=$rec['wbname']?></a>
      <span style="font-size: 12px;"><?=nl2br(CUtilities::truncate($rec['wbdesc'], 512))?></span>
      <br />
      <small>
      Специализации участников:
      <?php if(isset($specializations[$rec['wid']])) { $speccnt = count($specializations[$rec['wid']]);
        $i = 1;
        foreach($specializations[$rec['wid']] as $rs) {
          print "&quot;$rs&quot;";
          if($i++ != $speccnt) {
            print ', ';
          }
      } } else { ?>
        все заинтересованные
      <?php } ?>
      </small>
    </div>
    <div class="info">
      <div class="num"><?=$rec['wbposts']?></div>
      ответа
    </div>
    <div class="info2">
      <div class="num"><?=$rec['wbviews']?></div>
      просмотров
    </div>
    <div class="when">
      Время проведения:<br /> c <?=date('d.m.Y', $rec['wbstart'])?>
      <br /> по <?=date('d.m.Y', $rec['wbend'])?>
    </div>
  </div>
  <?php } ?>
</div>
<?php } else { ?>
  <div class="emptylist">В данном разделе ни одного семинара не проводилось</div>
<?php } ?>

</div>

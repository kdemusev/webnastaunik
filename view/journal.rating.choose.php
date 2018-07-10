<div class="mainframe">

    <div class="subheader">Выбор урока для выставления отметок</div>

    <p>Выбрать возможно только тот урок, на котором ни у одного из учащихся не выставлены отметки</p>

    <?php if(count($_ktp) > 0) { ?>
    <div class="largelist overable">
      <?php $ktnum = 1; foreach($_ktp as $rec) { ?>
      <?php if($rec['cnt']==0) { ?>
      <div class="tr">
        <div class="col1" onclick="window.location='/journal/ratingoverconfirm/<?=$rec['id']?>';"><b><?=$ktnum++?></b></div>
        <div class="col3" style="left: 40px; text-align: left;" onclick="window.location='/journal/ratingoverconfirm/<?=$rec['id']?>';"><b><?=date('d.m.Y', $rec['ktdate'])?></b></div>
        <div class="col2" style="right: 0px; left: 140px;" onclick="window.location='/journal/ratingoverconfirm/<?=$rec['id']?>';"><b><?=$rec['kttopic']?></b></div>
      </div>
      <?php } else { ?>
        <div class="tr" style="cursor: default; color: rgb(185, 185, 185); ">
          <div class="col1"><b><?=$ktnum++?></b></div>
          <div class="col3" style="left: 40px; text-align: left;"><b><?=date('d.m.Y', $rec['ktdate'])?></b></div>
          <div class="col2" style="right: 0px; left: 140px;"><b><?=$rec['kttopic']?></b></div>
        </div>
      <?php } ?>
      <?php } ?>
    </div>
    <?php } else { ?>
    <div class="emptylist">
      Не составлено календарно-тематическое планирование для выбранного класса и предмета
    </div>
    <?php } ?>


</div>

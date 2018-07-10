<div class="mainframe">
  <div class="subheader"><?=$news['nsname']?>
<?php if($user_rights >= 99 || $news['user_id'] == $user_id) { ?>
    <img src="/style/icons/admin.edit.png" class="adminbutton" title="изменить"
          onclick="window.location='/content/editnews/<?=$news['news_id']?>';"/>
    <img src="/style/icons/admin.delete.png" class="adminbutton" title="удалить"
          onclick="deleteNews('<?=$news['news_id']?>');"/>
<?php } ?>
  </div>

  <?php CTemplates::showMessage('edited', 'Новость изменена'); ?>

  <img src="/newsfiles/<?=$news['news_id']?>" class="newsimg"  onerror="this.style.display='none';" />
  <?=$news['nstext']?>
  <br style="clear: both;" />

  <div class="credits">
    <?php if($news['nstype']==0) { ?>
     <span class="marker" style="background-color: rgb(67, 108, 33);"></span> районная новость
    <?php } else if($news['nstype']==1) { ?>
     <span class="marker" style="background-color: rgb(228, 120, 86);"></span> областная новость
    <?php } else { ?>
     <span class="marker" style="background-color: grey;"></span> новость портала
    <?php } ?>
     <br /><?=date('d.m.y', $news['nstime'])?>
     <br /><?=$news['usname']?> <?=$news['usplace']?> (<?=$news['scname']?>)
  </div>
</div>

<script>
  function onDeleteNews(news_id) {
    window.location = '/content/delnews/'+news_id;
  }

  function deleteNews(news_id) {
    var popup = new sc2Popup();
    popup.showMessage('Удаление новости', 'Вы действительно хотите удалить новость?',
                      'Нет', 'Да', function() { onDeleteNews(news_id); });
  }
</script>

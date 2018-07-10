<div class="mainframe">
  <div class="subheader">Методический блог &quot;<?=$methodblog['mbname']?>&quot;</div>

  <?php CTemplates::showMessage('newsadded', 'Новость добавлена'); ?>
  <?php CTemplates::showMessage('newsdeleted', 'Новость удалена'); ?>
  <?php CTemplates::showMessage('newschanged', 'Новость изменена'); ?>

<form class="transpform">
  <input type="button" onclick="window.location='/methodblog/show/<?=$methodblog['id']?>';" value="Новости и объявления" class="checked" />
  <input type="button" onclick="window.location='/methodblog/dialog/<?=$methodblog['id']?>';" value="Методический диалог" />
  <?php if($blog_owner || $blog_author) { ?>
    <input type="button" onclick="window.location='/methodblog/addnews/<?=$methodblog['id']?>';" value="Добавить новость" />
  <?php } ?>
  <?php if($blog_owner) { ?>
    <input type="button" onclick="window.location='/methodblog/settings/<?=$methodblog['id']?>';" value="Настройки" />
  <?php } ?>
</form>

<?php if(count($data) > 0) { ?>
<?php foreach($data as $rec) { ?>
<div class="post">
  <div class="author">

    <h2>
      <?php if($blog_owner || $blog_author) { ?>
      <button class="adminbuttonnew" onclick="deleteMBNews(<?=$rec['mbn_id']?>)">Удалить</button>
      <button class="adminbuttonnew" onclick="window.location='/methodblog/editnews/<?=$rec['mbn_id']?>';">Изменить</button>
      <?php } ?>
      <?=$rec['mbnname']?></h2>

  </div>
  <?=$rec['mbntext']?>
  <?php if(isset($wbfiles[$rec['mbn_id']])) { ?>
  <h4>Прикрепленные файлы:</h4>
  <?php foreach($wbfiles[$rec['wid']] as $recfile) { ?>
    <a href="/webinar/file/<?=$recfile['id']?>"><?=$recfile['wbflsource']?></a><br>
  <?php } } ?>

  <div class="subauthor">
  <small><b><?=$rec['usname']?></b>, <?=$rec['usplace']?> (<?=$rec['scname']?>)
   <?=CUtilities::date_like_gmail($rec['mbntime'])?></small>
 </div>
</div>
<?php } ?>
<?php } else { ?>
  <div class="emptylist">
    Новостей и объявлений в данном методическом блоге нет
  </div>
<?php } ?>


</div>

<script>

function deleteMBNews(mbnid) {
  var popup = new sc2Popup();
  popup.showMessage('Удаление новости из методического блога', 'Вы действительно хотите удалить новость из методического блога?',
                    'Нет', 'Да', function() { window.location = '/methodblog/deletenews/'+mbnid; });
}


</script>

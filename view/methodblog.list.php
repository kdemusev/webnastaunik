<div class="mainframe">
  <div class="subheader">Методические блоги</div>

  <?php CTemplates::showMessage('added', 'Методический блог создан'); ?>
  <?php CTemplates::showMessage('changed', 'Методический блог изменен'); ?>
  <?php CTemplates::showMessage('deleted', 'Методический блог удален'); ?>

<?php if($user_rights >= 88) { ?>
  <form class="transpform">
    <input type="button" onclick="window.location='/methodblog/add';" value="Новый методический блог" />
  </form>
<?php } ?>

  <?php if(count($methodblogs) > 0) { ?>
  <div class="topics">
    <?php foreach($methodblogs as $rec) { ?>
    <div class="topic">
      <div class="desc">
        <?php if($user_rights == 99 || $rec['user_id'] == $user_id) { ?>
        <button class="adminbuttonnew" onclick="deleteMethodblog(<?=$rec['id']?>);">Удалить</button>
        <button class="adminbuttonnew" onclick="window.location='/methodblog/edit/<?=$rec['id']?>';">Изменить</button>
        <?php } ?>
        <a href="/methodblog/show/<?=$rec['id']?>"><?=$rec['mbname']?></a>
        <?=$rec['mbdesc']?>
        <br />
        <small>
          <?php if($rec['mbtype']==0) { ?>
          <span class="marker" style="background-color: rgb(67, 108, 33);"></span> районный методический блог
          <?php } else if($rec['mbtype']==1) { ?>
          <span class="marker" style="background-color: rgb(228, 120, 86);"></span> областной методический блог только для методистов районов
          <?php } else { ?>
          <span class="marker" style="background-color: rgb(120, 86, 228);"></span> областной открытый методический блог
          <?php } ?>

          <br>
        Специализации:
        <?php $speccnt = count($specializations[$rec['id']]);
          $i = 1;
          foreach($specializations[$rec['id']] as $rs) {
            print "&quot;".$rs['spname']."&quot;";
            if($i++ != $speccnt) {
              print ', ';
            }
        } ?>
        </small>
      </div>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <div class="emptylist">Не создано ни одного методического блога</div>
  <?php } ?>
</div>

<script>

function deleteMethodblog(mbid) {
  var popup = new sc2Popup();
  popup.showMessage('Удаление методического блога', 'Вы действительно хотите удалить методический блог и его содержимое?',
                    'Нет', 'Да', function() { window.location = '/methodblog/delete/'+mbid; });
}


</script>

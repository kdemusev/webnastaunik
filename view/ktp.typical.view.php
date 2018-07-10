<div class="mainframe">

    <div class="subheader">Типовое календарно-тематическое планирование по учебному предмету &quot;<?=$info[0]['kttiname']?>&quot; в <?=$info[0]['kttiform']?> классе</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <form class="transpform">
      <input type="button" onclick="window.location='/ktp/typicallist'"
             value="Вернуться к списку" />
      <?php if($canedit) { ?>
      <input type="button" onclick="window.location='/ktp/edittypical/<?=$info[0]['id']?>'"
             value="Изменить" />
      <input type="button" onclick="deleteKtp();"
             value="Удалить" />
      <?php } ?>
    </form>

    <p><?=$info[0]['kttidesc']?></p>

    <div class="transpform">
    <button onclick="sc2Print();">Распечатать</button>
    </div>
    <br />

    <div id="toprint">
    	<div class="printable">
    		<center>Государственное учреждение образования "<?=$scname?>"</center>
    		<h1 align="center">Календарно-тематическое планирование по учебному предмету "<?=$info[0]['kttiname']?>"<br>
        в <?=$info[0]['kttiform']?> &nbsp;&nbsp;&nbsp; классе
    		</h1>
    	</div>

      <div class="largetables">
      <table width="100%" id="ktptable">
          <tr>
              <th>№ п/п:</th>
              <th width="200" nowrap>Дата</th>
              <th width="100%">Тема урока</th>
          </tr>
          <?php
          $tbln=1;
          foreach($_ktp as $rec) { ?>
          <tr>
            <td align="center"><?=$tbln++?></td>
            <td width="200" nowrap>
            </td>
            <td
            <?php if($rec['kttcolor']==0) { ?>
            <?php } else if($rec['kttcolor']==1) { ?>
                                     style="color: red; font-weight: bold;"
            <?php } else if($rec['kttcolor']==2) { ?>
                                     style="color: green; font-style: italic;"
            <?php } else if($rec['kttcolor']==3) { ?>
                                     style="color: blue; font-weight: bold;"
            <?php } ?>
            ><?=$rec['ktttopic']?></td>
          </tr>
          <?php } ?>
      </table>
      </div>

    </div>
</div>

<script src="/js/sc2print.js"></script>

<script>
  function deleteKtp() {
    var popup = new sc2Popup();
    popup.showMessage('Подтверждение удаления', 'Вы действительно хотите удалить свое типовое календарно-тематическое планирование?',
                      'Нет', 'Да', function() { onDeleteKtp(); });
  }

  function onDeleteKtp() {
    window.location='/ktp/deltypical/<?=$info[0]['id']?>';
  }
</script>

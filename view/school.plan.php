<div class="mainframe">
  <div class="subheader">Учебные планы</div>

  <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

  <div class="largetables">
    <table id="tables">
        <tr>
            <th>№ п/п</th>
            <th>Предмет</th>
            <th>Количество часов</th>
        </tr>
        <?php $i = 1; foreach($_data as $rec) {?>
        <tr>
          <td align="center"><?=$i++?></td>
          <td><?=$rec['sbname']?></td>
          <td align="center"><?=$rec['sbhours']?></td>
        </tr>
        <?php } ?>
    </table>
  </div>
</div>

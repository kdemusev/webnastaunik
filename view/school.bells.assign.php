<!-- checked 4 -->
<div class="mainframe">

    <div class="subheader">Применить расписание звонков &quot;<?=$bellsgroup['bgname']?>&quot;</div>

    <form class="transpform">
    <input type="button" name="newtask" onclick="window.location='/school/bellsgroups';" value="Все расписания звонков" />
    <input type="button" name="newtask" onclick="window.location='/school/bells/<?=$bellsgroup['id']?>';" value="Расписание &quot;<?=$bellsgroup['bgname']?>&quot;" />
    </form>

    <?php if(count($forms) > 0) { ?>
      <form method="post" class="transpform" action="/school/assignbells/<?=$bellsgroup['id']?>">
    <table class="smptable overable">
      <tr>
        <th>Класс</th>
        <th>ПН</th>
        <th>ВТ</th>
        <th>СР</th>
        <th>ЧТ</th>
        <th>ПТ</th>
        <th>СБ</th>
      </tr>
      <?php foreach($forms as $rec) { ?>
        <tr>
          <td align="center"><?=$rec['fmname']?></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][0]" /></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][1]" /></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][2]" /></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][3]" /></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][4]" /></td>
          <td align="center"><input type="checkbox" name="bells[<?=$rec['id']?>][5]" /></td>
        </tr>
      <?php } ?>
    </table>
    <br />
      <input type="submit" name="save" value="Применить" />
    </form>
    <?php } else { ?>
      <div class="emptylist">В данном учреждении образования не создано ни одного класса</div>
    <?php } ?>
</div>

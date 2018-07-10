<div class="mainframe">
  <div class="subheader">Пользователи</div>

  <?php CTemplates::showMessage('edited', 'Изменения сохранены'); ?>

  <form class="transpform" action="/users/show" method="post" style="margin-bottom: 0;">
    <label>Область:</label>
    <select name="region_id" onchange="this.parentNode.submit();">
      <option value="0" <?php if(!isset($region_id)) { print 'selected'; }?>>Все области</option>
      <?php foreach($_regions as $rec) { ?>
        <option value="<?=$rec['id'];?>"
          <?php if(isset($region_id) && $region_id == $rec['id']) { print 'selected'; }?>
          ><?=$rec['rgname'];?></option>
      <?php } ?>
    </select>
  </form>

  <?php if(isset($_districts)) { ?>
    <form class="transpform" action="/users/show" method="post" style="margin-bottom: 0;">
      <input type="hidden" name="region_id" value="<?=$region_id?>">
      <label>Район:</label>
      <select onchange="this.parentNode.submit();" name="district_id">
        <option value="0" <?php if(!isset($district_id)) { print 'selected'; }?>>Все районы</option>
        <?php foreach($_districts as $rec) { ?>
          <option value="<?=$rec['id'];?>"
            <?php if(isset($district_id) && $district_id == $rec['id']) { print 'selected'; }?>
            ><?=$rec['dtname'];?></option>
        <?php } ?>
      </select>
    </form>
  <?php } ?>

  <?php if(isset($_schools)) { ?>
    <form class="transpform" action="/users/show" method="post" style="margin-bottom: 0;">
      <input type="hidden" name="region_id" value="<?=$region_id?>">
      <input type="hidden" name="district_id" value="<?=$district_id?>">
      <label>Учреждение:</label>
      <select name="school_id" onchange="this.parentNode.submit();">
        <option value="0" <?php if(!isset($school_id)) { print 'selected'; }?>>Все учреждения</option>
        <?php foreach($_schools as $rec) { ?>
          <option value="<?=$rec['id'];?>"
            <?php if(isset($school_id) && $school_id == $rec['id']) { print 'selected'; }?>
            ><?=$rec['scname'];?></option>
        <?php } ?>
      </select>
    </form>
  <?php } ?>

  <?php CTemplates::formList(array('', 'Тип', 'Фамилия, имя, отчество', 'Должность', 'Учреждение образования', ''),
                             array('region_id' => $region_id, 'district_id' => $district_id, 'school_id' => $school_id),
                             "listformtableusers"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableusers');
  tlus.addField('number');
  tlus.addField('innerHTML', 'ustype', '25px');
  tlus.addField('text', 'usname', '250px', 1);
  tlus.addField('innerHTML', 'usplace', '70px');
  tlus.addField('innerHTML', 'scname', '250px');
  tlus.addField('editbuttons');

  <?php foreach($users as $rec) { ?>
    tlus.addRecord({usname: '<?=$rec['usname']?>', id: '<?=$rec['user_id']?>',
                    ustype: '<?=$rec['ustype']?>', usplace: '<?=$rec['usplace']?>',
                    scname: '<?=$rec['scname']?>', funcEdit: function() { window.location='/user/edit/<?=$rec['user_id']?>'; }});
  <?php } ?>
</script>

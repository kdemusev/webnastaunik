<div class="mainframe">
  <div class="subheader">Учреждения образования</div>

  <?php CTemplates::showMessage('edited', 'Изменения сохранены'); ?>

  <form class="transpform" action="/school/show" method="post" style="margin-bottom: 0;">
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
    <form class="transpform" action="/school/show" method="post" style="margin-bottom: 0;">
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

  <?php CTemplates::formList(array('', 'Фамилия, имя, отчество', 'Район', ''),
                             array('region_id' => $region_id, 'district_id' => $district_id),
                             "listformtableschools"); ?>
</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlus = new sc2TableList('listformtableschools');
  tlus.addField('number');
  tlus.addField('text', 'scname', '350px', 1);
  tlus.addField('innerHTML', 'dtname', '250px');
  tlus.addField('delbutton');

  <?php foreach($schools as $rec) { ?>
    tlus.addRecord({scname: '<?=$rec['scname']?>', id: '<?=$rec['school_id']?>',
                    dtname: '<?=$rec['dtname']?>'});
  <?php } ?>

  <?php if($district_id > 0) { ?>
    tlus.addEmpty({});
  <?php } ?>

</script>

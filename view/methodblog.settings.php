<div class="mainframe">
  <div class="subheader">Настройки методического блога &quot;<?=$methodblog['mbname']?>&quot;</div>

  <?php CTemplates::showMessage('changed', 'Настройки изменены'); ?>

<form class="transpform">
  <input type="button" onclick="window.location='/methodblog/show/<?=$methodblog['mbid']?>';" value="Новости и объявления" />
  <input type="button" onclick="window.location='/methodblog/dialog/<?=$methodblog['mbid']?>';" value="Методический диалог" />
  <?php if($blog_owner) { ?>
    <input type="button" onclick="window.location='/methodblog/addnews/<?=$methodblog['mbid']?>';" value="Добавить новость" />
  <?php } ?>
  <?php if($blog_owner) { ?>
    <input type="button" onclick="window.location='/methodblog/settings/<?=$methodblog['mbid']?>';" value="Настройки" class="checked" />
  <?php } ?>
</form>

<form class="transpform" action="/methodblog/savesettings" method="post"
      name="settingsForm">
  <input type="hidden" name="methodblog_id" value="<?=$methodblog['mbid']?>" />

  <label>Авторы новостей (администраторы методического блога):</label>

  <?php CTemplates::formList(array('','','','','',''),
                             array(),
                             "listformtablembauthors", false, true); ?>

 <input type="submit" value="Сохранить изменения" />

</form>

<form class="transpform" id="selectteacher">
  <div id="teacherChoose">
    <label>Учреждение:</label>
    <select name="school_id"
            onchange="schoolSelected(this.options[this.selectedIndex])"
            id="school_id">
        <option value="0" selected style="display: none;"></option>
        <?php foreach($schools as $rec) { ?>
            <option value="<?=$rec['id'];?>"><?=$rec['scname'];?></option>";
        <?php } ?>
    </select>

    <label>Педагог:</label>
    <select name="teacher_id" id="teacher_id">
        <option value="0" selected style="display: none;"></option>
    </select>
  </div>
</form>

</div>

<script src="/js/sc2tablelist.js"></script>
<script>
  var tlma = new sc2TableList('listformtablembauthors');
  tlma.addField('number');
  tlma.addField('hidden', 'mbauthors', '');
  tlma.addField('hidden', 'mbauthor_info', '', 1);
  tlma.addField('label', 'Педагог');
  tlma.addField('innerHTML', 'usname', '150px');
  tlma.addField('delbutton');

  <?php if(isset($mbauthors)) { foreach($mbauthors as $rec) { ?>
  tlma.addRecord({id: '<?=$rec['user_id']?>',
                 mbauthors: new String('<?=$rec['user_id']?>'),
                 mbauthor_info: new String('<?=$rec['user_id']?>'),
                 usname: new String('<?=$rec['usname']?>').replace(/^(.+?)\s+(.).+?\s+(.).+?$/i, '$1 $2.$3.'),
                 funcClick: onSelectTeacher});
  <?php } } ?>

  tlma.addEmpty({value: 'Нажмите чтобы добавить', funcClick: onSelectTeacher});

  var oSelectTeacher = document.getElementById('selectteacher');
  oSelectTeacher = oSelectTeacher.parentNode.removeChild(oSelectTeacher);

  function schoolSelected(obj) {
    SMPAjaxGet('/index.php?section=users&action=getteachers&id='+obj.value, function(res) {
        clearSelect('teacher_id');
        x = res.documentElement.getElementsByTagName('teacher');
        for(var i = 0; i < x.length; i++) {
            addOption('teacher_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
        }
    }, true);
  }

  function onTeacherSelected(tr) {
    // input2 is for deleting
    var input = tr.children[1].children[0];
    var input2 = tr.children[2].children[0];

    var sel_id = document.getElementById('teacher_id');

    input.value = sel_id.options[sel_id.selectedIndex].value;
    tr.children[4].children[0].innerHTML = sel_id.options[sel_id.selectedIndex].innerHTML;
    input2.value = input.value;

    // shorten button label
    tr.children[4].children[0].innerHTML = tr.children[4].children[0].innerHTML.replace(/^(.+?)\s+(.).+?\s+(.).+?$/i, '$1 $2.$3.');

    tlma.addEmpty({value: 'Нажмите чтобы добавить', funcClick: onSelectTeacher});
  }

  function onSelectTeacher(tr, row) {
    var popup = new sc2Popup();
    popup.showModal('Добавить администратора', oSelectTeacher, 'Закрыть', 'Выбрать',
                    function() { onTeacherSelected(tr); });

    document.getElementById('school_id').selectedIndex = 0;
    clearSelect('teacher_id');
  }

</script>

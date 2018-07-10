<div class="leftpicture">
  <?php if(isset($freewebinars) && count($freewebinars)) { ?>
  <div class="announces">
    <div class="subheader">Объявления</div>
    <?php foreach($freewebinars as $rec) { ?>
    C <?=date("d.m.Y", $rec['wbfreestart'])?> по <?=date("d.m.Y", $rec['wbfreeend'])?> проходит дистанционный семинар &quot;<?=$rec['wbname']?>&quot;. Просим принять участие всех заинтересованных.
    <form class="transpform">
      <input type="button" value="Войти на семинар" onclick="window.location='/webinar/show/<?=$rec['id']?>'">
    </form>
    <?php } ?>
  </div>
  <?php } ?>
</div>
<div class="rightblock">
    <div class="subheader">Вход</div>

    <?php CTemplates::showMessage('registered', 'Вы успешно зарегистрированы. Вы можете войти сейчас используя свои данные'); ?>
    <?php CTemplates::showMessage('registeredadmin', 'Вы успешно зарегистрированы. Вы можете войти сейчас используя свои данные. Дополнительные права доступа появятся после связи с Вами администрацией портала'); ?>
    <?php CTemplates::showMessage('wronglogin', 'Невозможно войти. Неправильно введена учетная запись'); ?>
    <?php CTemplates::showMessage('wrongpass', 'Невозможно войти. Неправильно введен пароль'); ?>

    <form class="transpform" action='/users/login' method='post'>
      <label>Учетная запись:</label>
      <input type="text" name="uslogin" id="uslogin" />
      <label>Пароль:</label>
      <input type="password" name="uspassword" />
      <div class="checkboxes">
        <label><input type="checkbox" name="usremember" /> оставаться в системе</label>
      </div>
      <hr />
      <input type="submit" value="Войти" />
      <input type="button" onclick="go('/users/register');" value="Зарегистрироваться" />
      <hr />
      <a onclick="rememberUser();">Не удаётся вспомнить учетную запись?</a><br>
      <a href="">Не удаётся вспомнить пароль?</a><br>

    </form>

    <form class="transpform" id="selectteacher" style="display: none;">
      <div id="teacherChoose">
        <label>Область:</label>
        <select onchange="regionSelected(this.options[this.selectedIndex])" id="region_id">
            <option value="0" selected style="display: none;"></option>
            <?php foreach($_regions as $rec) { ?>
                <option value="<?=$rec['id'];?>"><?=$rec['rgname'];?></option>";
            <?php } ?>
        </select>

        <label>Район:</label>
        <select onchange="districtSelected(this.options[this.selectedIndex])" id="district_id" name="district_id">
            <option value="0" selected style="display: none;"></option>
        </select>

        <label>Учреждение:</label>
        <select name="school_id"
                onchange="schoolSelected(this.options[this.selectedIndex])"
                id="school_id">
            <option value="0" selected style="display: none;"></option>
        </select>

        <label>Фамилия, имя, отчество:</label>
        <select name="teacher_id" id="teacher_id">
            <option value="0" selected style="display: none;"></option>
        </select>
      </div>
    </form>

</div>

<script>
  function onSelectTeacher() {
    if(selvalue('teacher_id') && selvalue('teacher_id') > 0) {
      console.log('/users/getlogin/'+selvalue('teacher_id'));
      SMPAjaxGet('/users/getlogin/'+selvalue('teacher_id'), function(res) {
        var x = res.documentElement;
        sc2_id('uslogin').value = x.firstChild.nodeValue;
      }, true);
    }
  }

  function rememberUser() {
    var p = new sc2Popup();
    p.showModal('Помощник поиска учетной записи', sc2_id('selectteacher'), 'Закрыть', 'Выбрать', onSelectTeacher);
    sc2_id('selectteacher').style.display = 'block';
  }
</script>

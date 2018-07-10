<div class="mainframe">
  <div class="subheader">Изменение данных пользователя</div>

  <form class="transpform" action="/users/adminedit" method="post" id="registerform_id">
    <input type="hidden" name="user_id" value="<?=$user['id']?>" />

    <label>Фамилия, имя, отчество:</label>
    <input type="text" name="usname" id="usname_id" value="<?=$user['usname']?>"/>

    <label>Пароль:</label>
    <input type="text" name="uspass" id="uspass_id" value="<?=$user['uspassword']?>" />

    <label>Основная должность:</label>
    <select name="usplace" id="place_id" onchange="regPlaceSelected(this.options[this.selectedIndex])">
        <option value="1" <?php if($user['usplace']=='учитель') { ?>selected<?php } ?>>учитель</option>
        <option value="2" <?php if($user['usplace']=='заместитель директора') { ?>selected<?php } ?>>заместитель директора</option>
        <option value="3" <?php if($user['usplace']=='директор') { ?>selected<?php } ?>>директор</option>
        <option value="4" <?php if($user['usplace']=='методист') { ?>selected<?php } ?>>методист</option>
        <option value="-1" <?php if($user['usplace']!='учитель' &&
                                    $user['usplace']!='заместитель директора' &&
                                    $user['usplace']!='директор' &&
                                    $user['usplace']!='методист') { ?>selected<?php } ?>>моей должности нет в списке</option>
    </select>

    <div style="display: none;" id="newusplace_id">
        <label>Название должности:</label>
        <input type="text" name="newusplace" id="usplace_id" value="<?=$user['usplace']?>" />
    </div>

    <label>Специализации:</label>
    <div class="checkboxes">
    <?php foreach($_specializations as $rec) { ?>
        <label><input type="checkbox" name="specialization_id[]" value="<?=$rec['id']?>"
                      <?php if(isset($specs[$rec['id']])) { ?>checked<?php } ?>/><?=$rec['spname']?></label>
    <?php } ?>
    </div>

    <label>Телефон для связи в формате 291112233:</label>
    <input type="text" name="usphone" value="<?=$user['usphone']?>" />
    <label>Адрес электронной почты:</label>
    <input type="text" name="usemail" value="<?=$user['usemail']?>" />

    <label>Расширенные права доступа:</label>
    <select name="ustype">
        <option value="1" <?php if($user['ustype']==1) { ?>selected<?php } ?>>нет</option>
        <option value="87" <?php if($user['ustype']==87) { ?>selected<?php } ?>>администратор учреждения</option>
        <option value="88" <?php if($user['ustype']==88) { ?>selected<?php } ?>>администратор районного уровня</option>
        <option value="89" <?php if($user['ustype']==89) { ?>selected<?php } ?>>администратор областного уровня</option>
        <option value="99" <?php if($user['ustype']==99) { ?>selected<?php } ?>>администратор портала</option>
    </select>

    <label>Запрошенные права доступа:</label>
    <input type="text" readonly="readonly" value="<?=$user['usrights']==87?'администратор учреждения':($user['usrights']==88?'администратор районного уровня':($user['usrights']==89?'администратор областного уровня':'не запрошены'))?>" />

    <label></label>
    <input type="button" onclick="check();" value="Изменить данные" />
    <input type="button" onclick="go('/user/show');" value="Отменить" />

  </form>
</div>

<script>
  if(selvalue('place_id')==-1) {
    sc2_id('newusplace_id').style.display = 'block';
  }

  function regPlaceSelected(obj) {
      if(obj.value == -1) {
          show('newusplace_id');
      } else {
          hide('newusplace_id');
      }
  }

  function check() {
      var popup = new sc2Popup();
      if(id('usname_id').value.trim() == '') {
          popup.showMessage('Ошибка заполнения формы', 'Не введены Ваши фамилия, имя, отчество', 'Закрыть');
          seterror('usname_id');
          return;
      }
      if(id('uspass_id').value.trim() == '') {
          popup.showMessage('Ошибка заполнения формы', 'Не введен пароль', 'Закрыть');
          seterror('uspass_id');
          return;
      }
      if(selvalue('place_id') == 0) {
          popup.showMessage('Ошибка заполнения формы', 'Не выбрана основная должность', 'Закрыть');
          seterror('place_id');
          return;
      }
      if(selvalue('place_id') == -1 &&
         id('usplace_id').value.trim() == '') {
          popup.showMessage('Ошибка заполнения формы', 'Не введено название Вашей должности', 'Закрыть');
          seterror('usplace_id');
          return;
      }

      if(!checked('specialization_id[]')) {
          popup.showMessage('Ошибка заполнения формы', 'Не выбрана ни одна специализация', 'Закрыть');
          return;
      }

      sc2_id('registerform_id').submit();
  }

</script>

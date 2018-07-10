<script>
function regDistrictSelected(obj) {
    SMPAjaxGet('/index.php?section=user&action=getschools&id='+obj.value, function(res) {
        clearSelect('school_id');
        x = res.documentElement.getElementsByTagName('school');
        for(var i = 0; i < x.length; i++) {
            addOption('school_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
        }
        addOption('school_id', '-1', 'Моего учреждения нет в списке');
    }, true);
}

function regSchoolSelected(obj) {
    if(obj.value == -1) {
        show('newschool_id');
    } else {
        hide('newschool_id');
    }
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
    if(selvalue('school_id') == 0) {
        popup.showMessage('Ошибка заполнения формы', 'Не выбрано учреждение образования', 'Закрыть');
        seterror('school_id');
        return;
    }
    if(selvalue('school_id') == -1 &&
       id('scname_id').value.trim() == '') {
        popup.showMessage('Ошибка заполнения формы', 'Не введено название Вашего учреждения образования', 'Закрыть');
        seterror('scname_id');
        return;
    }
    if(id('usname_id').value.trim() == '') {
        popup.showMessage('Ошибка заполнения формы', 'Не введены Ваши фамилия, имя, отчество', 'Закрыть');
        seterror('usname_id');
        return;
    }
    if(id('uslogin_id').value.trim() == '') {
        popup.showMessage('Ошибка заполнения формы', 'Не введено название учетной записи. Рекомендуется использовать номер мобильного телефона', 'Закрыть');
        seterror('uslogin_id');
        return;
    }
    if(id('uspass_id').value == '') {
        popup.showMessage('Ошибка заполнения формы', 'Не задан пароль, который будет использоваться для входа в систему', 'Закрыть');
        seterror('uspass_id');
        return;
    }
    if(value('uspass_id') != value('uspass2_id')) {
        popup.showMessage('Ошибка заполнения формы', 'Введенные пароли не совпадают', 'Закрыть');
        seterror('uspass_id');
        seterror('uspass2_id');
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
    // check login exist
    SMPAjaxPost('/index.php?section=user&action=checklogin',
                'uslogin='+encodeURIComponent(value('uslogin_id')), function(res) {
        x = res.documentElement.getElementsByTagName('answer');
        var x = res.documentElement;
        if(x.getAttribute('isexists')=="true") {
          popup.showMessage('Ошибка заполнения формы', 'Введенная учетная запись уже зарегистрирована на другого пользователя. Попробуйте использовать номер мобильного телефона для названия учетной записи', 'Закрыть');
          seterror('uslogin_id');
          return;
        } else {
            id('registerform_id').submit();
        }
    }, true);

}
</script>

<div class="fullframe">

    <div class="subheader">Регистрация</div>

    <p>Обратите внимание, что при неправильном заполнении полей регистрации, либо нарушении
        правил пользования системой, регистрация будет удалена без предупреждения</p>
    <p>Заполняя регистрационную форму вы автоматически соглашаетесь с <a onclick="sc2_gethelp('terms');">условиями использования
        сервиса и ограниченной ответственностью</a></p>

    <form class="transpform" action="/users/register" method="post" id="registerform_id">
        <hr />

        <label>Область:</label>
        <select onchange="regionSelected(this.options[this.selectedIndex])">
            <option value="0" selected style="display: none;"></option>
            <?php foreach($_regions as $rec) { ?>
                <option value="<?=$rec['id'];?>"><?=$rec['rgname'];?></option>
            <?php } ?>
        </select>

        <label>Район:</label>
        <select onchange="regDistrictSelected(this.options[this.selectedIndex])" id="district_id" name="district_id">
            <option value="0" selected style="display: none;"></option>
        </select>

        <label>Учреждение:</label>
        <select name="school_id"
                onchange="regSchoolSelected(this.options[this.selectedIndex])"
                id="school_id">
            <option value="0" selected style="display: none;"></option>
        </select>

        <div style="display: none;" id="newschool_id">
            <label>Название учреждения в сокращенной форме:</label>
            <input type="text" name="scname" id="scname_id" />
        </div>

        <label>Фамилия, имя, отчество:</label>
        <input type="text" name="usname" id="usname_id"/>
        <label>Учетная запись:</label>
        <input type="text" name="uslogin" id="uslogin_id"/>
        <label>Пароль:</label>
        <input type="password" name="uspass" id="uspass_id" />
        <label>Повторите ввод пароля:</label>
        <input type="password" id="uspass2_id" />

        <label>Основная должность:</label>
        <select name="usplace" id="place_id" onchange="regPlaceSelected(this.options[this.selectedIndex])">
            <option value="0" selected disabled hidden></option>
            <option value="1">учитель</option>
            <option value="2">заместитель директора</option>
            <option value="3">директор</option>
            <option value="4">методист</option>
            <option value="-1">моей должности нет в списке</option>
        </select>

        <div style="display: none;" id="newusplace_id">
            <label>Название должности:</label>
            <input type="text" name="newusplace" id="usplace_id" />
        </div>

        <label>Специализации:</label>
        <div class="checkboxes">
        <?php foreach($_specializations as $rec) { ?>
            <label><input type="checkbox" name="specialization_id[]" value="<?=$rec['id']?>"/><?=$rec['spname']?></label>
        <?php } ?>
        </div>

        <p>Контактная информация необязательна к заполнению, однако данные сведения позволят вспомнить забытый пароль либо получить расширенные права доступа к порталу</p>

        <label>Телефон для связи в формате 291112233:</label>
        <input type="text" name="usphone" />
        <label>Адрес электронной почты:</label>
        <input type="text" name="usemail" />

        <p>Расширенные права доступа необходимы для организации полноценной работы портала определенной группой лиц. Изучите раздел помощи <a onclick="sc2_gethelp('userrights');">права доступа</a>, если вы
        явяляетесь администратором учреждения образования, методистом либо работником учреждения областного уровня</p>

        <label>Запросить расширенные права доступа:</label>
        <select name="usrights">
            <option value="1" selected>не запрашивать</option>
            <option value="87">администратор учреждения</option>
            <option value="88">администратор районного уровня</option>
            <option value="89">администратор областного уровня</option>
        </select>


        <label></label>
        <input type="button" onclick="check();" value="Зарегистрироваться" />
        <input type="button" onclick="go('/');" value="Отменить" />

    </form>

</div>

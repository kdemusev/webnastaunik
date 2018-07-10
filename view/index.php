<!DOCTYPE html>
<html>
  <head>
    <title>Ежедневник учителя | Белорусские открытые образовательные технологии</title>
    <meta charset="UTF-8">
    <meta name="description" content="Организуйте свою педагогическую деятельность эффективно. Гомельский областной педагогический форум.">
    <meta name="keywords" content="Ежедневник учителя, информационные технологии, образование, школа, педагогический форум">
    <meta name="viewport" content="width=device-width, maximum-scale=2, minimum-scale=1" />

    <link rel="stylesheet" href="/style/school.css" type="text/css"
          media="screen and (orientation:landscape) and (min-width: 601px)" />

    <link rel="stylesheet" href="/style/school_vertical.css" type="text/css"
          media="screen and (orientation:portrait), screen and (max-width: 600px)" />

<link rel="stylesheet" href="/style/sc2editor.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="/style/print.css" media="print" />

    <script src="/js/school2.js"></script>
    <script src="/js/smpajax.js"></script>
    <script src="/js/sc2popup.js"></script>
    <script>
        function showmenu(which) {
            if(which==='main') {
                hideAnimated('ktpmenu', 'hidemenu', '0.5s');
                hideAnimated('shedulemenu', 'hidemenu', '0.5s');
                hideAnimated('schoolmenu', 'hidemenu', '0.5s');
                hideAnimated('webinarmenu', 'hidemenu', '0.5s');
                hideAnimated('journalmenu', 'hidemenu', '0.5s');
                hideAnimated('analysismenu', 'hidemenu', '0.5s');
                hideAnimated('adminmenu', 'hidemenu', '0.5s');
                hideAnimated('dbmenu', 'hidemenu', '0.5s');
                hideAnimated('dbolympmenu', 'hidemenu', '0.5s');
                hideAnimated('web20menu', 'hidemenu', '0.5s');
                hideAnimated('ratingmenu', 'hidemenu', '0.5s');
                hideAnimated('bonussalarymenu', 'hidemenu', '0.5s');
                hideAnimated('methodmenu', 'hidemenu', '0.5s');
                showAnimated('mainmenu','leftmenu','0.5s');
            } else {
                showAnimated(which+'menu','leftmenu','0.5s');
            }
        }
    </script>
  </head>
  <body>
      <div class="header">
        <div class="btn" style="padding-right: 0px; margin-right: 0px;"><img src="/style/menu.png" id="menubutton" onclick="showmenu('main');" /></div><div class="mainlogo"></div>
<?php if($user_id > 0) { ?>
          <div class="right">
            <div class="btn" tabindex="0" onclick="window.location='/notifications';"><img src="/style/icons/notification.png" id="notif_icon" title="" /><span>Уведомления</span></div>
            <div class="btn" tabindex="1"><img src="/style/icons/email.png" id="message_icon" /><span>Сообщения</span>
              <div class="popupmenu">
                <a href="/messages">Входящие</a>
                <a href="/messages/compose">Написать сообщение</a>
                <a href="/messages/justnew">Новые сообщения</a>
                <a href="/messages/sended">Отправленные</a>

              </div>
            </div>
            <div class="btn" tabindex="2"><img src="/style/icons/user.png" /><span><?=$g_usname?></span>
              <div class="popupmenu">
                <a href="/user/settings">Настройки</a>
                <a href="/webnastaunik.apk">Приложение для Android</a>
                <a href="/">Помощь</a>
                <a href="/users/logout">Выход</a>
              </div>
            </div>
          </div>
          <?php } ?>
      </div>



      <?php if($user_id > 0) { ?>


        <div class="leftpanel" style='z-index: 6; display: none;' id='ktpmenu'>
            <h3>Планирование</h3>
            <a href="/ktp/view">Просмотр КТП</a>
            <a href="/ktp/dates">Даты занятий</a>
            <a href="/ktp/fill">КТП</a>
            <a href="/ktp/typicallist">Типовые КТП</a>
            <?php if($user_rights >= 99) { ?><a href="/ktp/setup">Календарь</a><?php } ?>
            <?php if($user_rights >= 99) { ?><a href="/ktp/vacations">Даты каникул</a><?php } ?>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='shedulemenu'>
            <h3>Расписание</h3>
            <?php if($user_rights >= 87) { ?><a href="/timetable/compose">Составить расписание</a><?php } ?>
            <a href="/timetable/view">Просмотр расписания</a>
            <a href="/timetable/viewbyteacher">Расписание учителя</a>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='schoolmenu'>
            <h3>Школа</h3>
            <?php if($user_rights >= 87) { ?><a href="/school/teachers">Учителя</a>
            <a href="/school/rooms">Кабинеты</a>
            <a href="/school/forms">Классы</a>
            <a href="/school/subjects">Предметы</a>
            <a href="/school/bellsgroups">Звонки</a><?php } ?>
            <a href="/school/pupils">Учащиеся</a>
            <a href="/school/showpupils">Список учащихся</a>
            <a href="/school/plan">Учебные планы</a>
            <a onclick="showmenu('bonussalary');">Премирование</a>
        </div>

        <div class="leftpanel" style='z-index: 6; display: none;' id='bonussalarymenu'>
            <h3>Премирование</h3>
            <?php if($user_rights >= 87) { ?><a href="/bonussalary/agreement">Положение</a>
            <a href="/bonussalary/groups">Группы работников</a>
            <a href="/bonussalary/employes">Работники учреждения</a>
            <a href="/bonussalary/bonus">Премиальный фонд</a>
            <a href="/bonussalary/economy">Фонд экономии</a>
            <a href="/bonussalary/extra">Надбавки</a><?php } ?>
            <a href="/bonussalary/view">Просмотр</a>
        </div>

        <div class="leftpanel" style='z-index: 6; display: none;' id='webinarmenu'>
            <h3>Семинары</h3>
            <?php if($user_rights >= 88) { ?><a href="/webinar/open">Открыть семинар</a><?php } ?>
            <a href="/webinar/district">Районные семинары</a>
            <a href="/webinar/region">Областные семинары</a>
        </div>
        <div class="leftpanel" style='z-index: 7; display: none;' id='ratingmenu'>
            <h3>Рейтинговая система</h3>
            <a href="/journal/rating">Оценивание</a>
            <a href="/journal/makerating">Создать рейтинг</a>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='adminmenu'>
            <h3>Администрирование</h3>
            <a href="/school/show">Учреждения</a>
            <a href="/users/show">Пользователи</a>
            <a href="/notifications/delivery">Рассылка</a>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='web20menu'>
            <h3>Сервисы</h3>
            <a href="/quiz/showlist">Опросы</a>
            <a href="/test/showlist">Тестирование</a>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='dbmenu'>
            <h3>Базы данных</h3>
            <a onclick="showmenu('dbolymp');">Одаренные дети</a>
        </div>

        <div class="leftpanel" style='z-index: 6; display: none;' id='dbolympmenu'>
            <h3>Одаренные дети</h3>
            <a href="/dbolymp/results">Результаты олимпиад</a>
            <a href="/dbconcurs/results">Результаты конкурсов</a>
            <a href="/dbolymp/reports">Отчеты по олимпиадам</a>
            <a href="/dbconcurs/reports">Отчеты по конкурсам</a>
            <a href="/dbolymp/total">Истории учащихся</a>
            <a href="/dbolymp/schoolsdb">База учреждений</a>
            <a href="/dbolymp/subjectsdb">База предметов</a>
            <a href="/dbconcurs/concursesdb">База конкурсов</a>
            <a href="/dbconcurs/sectionsdb">База секций</a>
            <a href="/dbolymp/pupilsdb">База учащихся</a>
            <a href="/dbolymp/formsdb">База классов</a>
        </div>

        <div class="leftpanel" style='z-index: 7; display: none;' id='analysismenu'>
            <h3>Анализ качества</h3>
            <a href="/analysis/edit">Ведомость оценок</a>
            <a href="/analysis/subjects">Качество образования</a>
            <a href="/analysis/form">Учет успеваемости</a>
            <a href="/analysis/rating">Рейтинг учащихся</a>
            <a href="/analysis/analysissubjects">Анализ качества</a>
            <a href="/analysis/analysisform">Анализ успеваемости</a>
            <?php if($user_rights >= 99) { ?><a href="/analysis/settings">Настройки</a><?php } ?>
        </div>
        <div class="leftpanel" style='z-index: 6; display: none;' id='journalmenu'>
            <h3>Журнал</h3>
            <a href="/journal/view">Просмотр журнала</a>
            <a href="/journal/attendance">Учет посещаемости</a>
            <a onclick="showmenu('rating');">Рейтинговая система</a>
            <a onclick="showmenu('analysis');">Анализ качества</a>
        </div>

        <div class="leftpanel" style='z-index: 6; display: none;' id='methodmenu'>
            <h3>Методическая работа</h3>
          <a href="/methodblog">Методический блог</a>
          <a href="/test/choose">Тестирование</a>
        </div>

        <div class="leftpanel" id="mainmenu">
            <a href="/">Сейчас</a>
            <a href="/lesson">Урок</a>
            <a href="/tasks/view">Задачи</a>
            <a onclick="showmenu('ktp');">Планирование</a>
            <a onclick="showmenu('shedule');">Расписание</a>
            <a onclick="showmenu('journal');">Журнал</a>
            <a onclick="showmenu('school');">Школа</a>
            <a onclick="showmenu('webinar');">Семинары</a>
            <a onclick="showmenu('method');">Методическая работа</a>
            <a href="/forum">Форум</a>
            <?php if($user_rights >= 99) { ?><a onclick="showmenu('admin');">Администрирование</a><?php } ?>
            <?php if($user_rights >= 87) { ?><a onclick="showmenu('db');">Базы данных</a><?php } ?>
            <?php if($user_rights >= 87) { ?><a onclick="showmenu('web20');">Сервисы</a><?php } ?>
            <a href="/content/news">Новости</a>

        </div>

      <?php } ?>

     <?php include $this->folder.'/'.$__page.'.php'; ?>

      <div class="footer">
          <span>© 2017 Грабовский детский сад - средняя школа</span>
          <span>Условия использования и конфедициальность информации</span>
          <span><a href="/messages/feedback">Обратная связь</a></span>
      </div>

      <!-- POPUP BOX -->
      <div class="popup_background" id="darker_id"></div>
      <div class="popup" id="popup_id">
          <div class="popupheader" id="popup_header_id"></div>
          <div class="popuptext" id="popup_text_id"></div>
          <button class="popupbutton" id="popup_ok_id"></button>
          <button class="popupbutton" id="popup_cancel_id"></button>
      </div>
      <!-- END OF POPUP BOX -->
<script>
<?php if($user_id > 0 && $newnots) { ?>
  animateIcon('notif_icon');
<?php } ?>
<?php if($user_id > 0 && $newmsgs) { ?>
  animateIcon('message_icon');
<?php } ?>
</script>

  </body>
</html>

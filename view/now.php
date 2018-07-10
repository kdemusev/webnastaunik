<div class="mainframe">

    <div class="subheader">Сейчас</div>

<?php if(isset($startguide)) { ?>
    <div class="tile" onclick="window.open('webnastaunik.pdf');">
      <img src="/style/tiles/start.jpg" />
      <h2>С чего начать?</h2>
      <?php if($startguide == 'wait') { ?>
        Необходимо дождаться присвоения расширенных прав пользователя
      <?php } else if($startguide == 'teacher') { ?>
        Обратитесь к заместителю директора для
        присвоения Вам статуса учителя
      <?php } else { ?>
        Необходимо
        составить календарно-тематическое планирование
      <?php } ?><br>
      <a>Нажмите на эту плитку чтобы познакомиться с возможностями портала</a>
      <div class="dimmer"></div>
    </div>
<?php } ?>

    <div class="tile" onclick="window.location='/lesson';">
      <img src="/style/tiles/lesson.jpg" />
      <h2>Урок</h2>
      <?php if(count($lesson) > 0) { ?>
      <?php foreach($lesson as $rec) { ?>
        <?php if($rec['ttstart'] <= $timenow && $rec['ttend'] > $timenow ) { ?>
        <p>Сейчас: <?=$rec['sbname']?> в <?=$rec['fmname']?> классе</p>
        <?php } else { ?>
          <p><?=$rec['ttnumber']+1?>. <?=$rec['sbname']?> в <?=$rec['fmname']?> классе (<?=date('G:i', $rec['ttstart'])?>)</p>
        <?php } ?>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      На сегодня уроков нет
      <?php } ?>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/tasks/view';">
      <img src="/style/tiles/tasks.jpg" />
      <h2>Задачи</h2>
      <?php if(count($tasks) > 0) { ?>
      <?php foreach($tasks as $rec) { ?>
        <p><?=$rec['tsname']?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Задач на сегодня нет
      <?php } ?>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/content/news';">
      <img src="/style/tiles/news.jpg" />
      <h2>Новости</h2>
      <?php if(count($news) > 0) { ?>
      <?php foreach($news as $rec) { ?>
        <p><?=strip_tags($rec['nsname'])?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Новостей нет
      <?php } ?>
      <div class="dimmer"></div>
    </div>


    <div class="tile" onclick="window.location='/messages/justnew';">
      <img src="/style/tiles/messages.jpg" />
      <h2>Сообщения</h2>
      <?php if(count($messages) > 0) { ?>
      <?php foreach($messages as $rec) { ?>
        <p><?=$rec['mstopic']?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Новых сообщений нет
      <?php } ?>
      <a href="/messages/compose">Написать сообщение</a>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/forum';">
      <img src="/style/tiles/forum.jpg" />
      <h2>Форум</h2>
      <?php if(count($forum) > 0) { ?>
      <?php foreach($forum as $rec) { ?>
        <p><?=$rec['fmtpname']?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Сообщений на форуме нет
      <?php } ?>
      <a href="/forum">Задать вопрос</a>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/webinar/wblist';">
      <img src="/style/tiles/webinars.jpg" />
      <h2>Семинары</h2>
      <?php if(count($webinars) > 0) { ?>
      <?php foreach($webinars as $rec) { ?>
        <p><?=$rec['wbname']?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      В данное время дистанционные семинары по вашим специализациям не проводятся
      <?php } ?>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/timetable/viewbyteacher';">
      <img src="/style/tiles/timetable.jpg" />
      <h2>Расписание</h2>
      <?php if(count($timetable) > 0) { ?>
      <?php foreach($timetable as $rec) { ?>
        <p><?=$rec['ttnumber']+1?>. <?=$rec['sbname']?> (<?=$rec['fmname']?> класс)</p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Сегодня уроков нет
      <?php } ?>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.location='/notifications';">
      <img src="/style/tiles/notifications.jpg" />
      <h2>Уведомления</h2>
      <?php if(count($notifications) > 0) { ?>
      <?php foreach($notifications as $rec) { ?>
        <p><?=$rec['nttopic']?></p>
      <?php } ?>
        <p>...</p>
      <?php } else { ?>
      Уведомлений нет
      <?php } ?>
      <div class="dimmer"></div>
    </div>

    <div class="tile" onclick="window.open('webnastaunik.pdf');">
      <img src="/style/tiles/help.jpg" />
      <h2>Помощь</h2>
      В этом разделе можно получить помощь<br/>
      <a href="/messages/feedback">Либо задать вопрос администрации портала</a>
      <div class="dimmer"></div>
    </div>
</div>

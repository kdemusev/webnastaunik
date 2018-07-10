<div class="mainframe">

  <div class="subheader">Рассылка уведомлений</div>

  <?php CTemplates::showMessage('sended', 'Уведомления разосланы'); ?>

  <form class="transpform" action="/notifications/senddelivery" method="post" id="postform">
    <label>Ссылка</label>
    <input type="text" name="ntlink" value="" />
    <label>Текст сообщения</label>
    <input type="text" name="nttopic" />
    <hr />
    <input type="submit" value="Отправить"  />
    <input type="button" value="Отменить" onclick="window.location='/';" />
  </form>

</div>

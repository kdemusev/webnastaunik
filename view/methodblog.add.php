<div class="mainframe">
  <div class="subheader">Добавить методический блог</div>

  <form class="transpform" action="/methodblog/save" method="post"
        name="methodblogForm">

    <?php if($user_rights==99) { ?>
    <label>Уровень:</label>
    <input type="radio" id="rType1" class="customRadioButton" name="mbtype"
           value="0" checked/>
    <label for="rType1" class="button">Районный</label>
    <input type="radio" id="rType2"  class="customRadioButton" name="mbtype"
           value="1" />
    <label for="rType2" class="button">Областной для методистов районов</label>
    <input type="radio" id="rType3"  class="customRadioButton" name="mbtype"
           value="2" />
    <label for="rType3" class="button">Областной для всех педагогов</label>
    <br /><br />
    <?php } else if($user_rights==88) { ?>
    <input type="hidden" name="mbtype" value="0" />
    <?php } else { ?>
      <label>Уровень:</label>
      <input type="radio" id="rType2"  class="customRadioButton" name="mbtype"
             value="1" checked/>
      <label for="rType2" class="button">Областной для методистов районов</label>
      <input type="radio" id="rType3"  class="customRadioButton" name="mbtype"
             value="2" />
      <label for="rType3" class="button">Областной для всех педагогов</label>
      <br /><br />
    <?php } ?>

    <label>Название</label>
    <input type="text" id="methodblogNameInput" name="mbname" />

    <label>Специализации методистов:</label>
    <div class="checkboxes">
    <?php foreach($_specializations as $rec) { ?>
        <label><input type="checkbox" style="margin: 0;" name="specialization_id[]"
                      value="<?=$rec['id']?>" />
                      <?=$rec['spname']?></label>
    <?php } ?>
    </div>

    <label>Описание методического блога</label>
    <textarea name="mbdesc"></textarea>

    <input type="button" name="newtask" onclick="checkInput();" value="Создать методический блог" />
    <input type="button" name="newtask" onclick="back();" value="Отменить открытие" />

  </form>

</div>

<script>

  function checkInput() {
    var popup = new sc2Popup();
    if(id('methodblogNameInput').value.trim() == '') {
      popup.showMessage('Ошибка при создании методического блога', 'Не указано название методического блога', 'Закрыть');
      seterror('methodblogNameInput');
      return;
    } else if(!checked('specialization_id[]')) {
      popup.showMessage('Ошибка при создании методического блога', 'Не выбрана ни одна специализация', 'Закрыть');
      return;
    }

    document.forms['methodblogForm'].submit();
  }

  function back() {
    window.location = '/methodblog';
  }
</script>


<div class="sc2editor" id ="sc2editor">
  <div class="editor" contenteditable="true" id="sc2editorRich"></div>
  <div class="panel panel2" id="sc2editor_panel_2">
    <span class="separator">Размер заголовка:</span>
    <button class="button" id="sc2editorSize1">1</button>
    <button class="button" id="sc2editorSize2">2</button>
    <button class="button" id="sc2editorSize3">3</button>
    <button class="button" id="sc2editorSize4">4</button>
    <button class="button" id="sc2editorSize5">5</button>
    <button class="button" id="sc2editorSize6">6</button>
    <button class="button" id="sc2editorSize7">7</button>
  </div>

  <div class="panel">
    <img src="/style/icons/appbar.undo.png" class="button" id="sc2editorUndo" />
    <span class="separator"></span>
    <img src="/style/icons/appbar.text.bold.png" class="button" id="sc2editorBold" />
    <img src="/style/icons/appbar.text.italic.png" class="button" id="sc2editorItalic" />
    <img src="/style/icons/appbar.text.underline.png" class="button" id="sc2editorUnderline" />
    <img src="/style/icons/appbar.text.strikethrough.png" class="button" id="sc2editorStricked" />
    <img src="/style/icons/appbar.text.size.png" class="button" id="sc2editorSize" />
    <span class="separator"></span>
    <img src="/style/icons/appbar.text.align.left.png" class="button" id="sc2editorLeft" />
    <img src="/style/icons/appbar.text.align.center.png" class="button" id="sc2editorCenter" />
    <img src="/style/icons/appbar.text.align.right.png" class="button" id="sc2editorRight" />
    <img src="/style/icons/appbar.text.align.justify.png" class="button" id="sc2editorJustify" />
    <span class="separator"></span>
    <img src="/style/icons/appbar.image.png" class="button" id="sc2editorImage" />
    <img src="/style/icons/appbar.link.png" class="button" id="sc2editorLink" />
    <img src="/style/icons/appbar.paperclip.rotated.png" class="button" id="sc2editorFile" />
  </div>
  <div class="panel panel2" id="sc2editor_linkpanel">
    <span class="separator">Ссылка:</span>
    <input type="text" id="sc2editor_hyperlink" style="margin-bottom: 0;" value="http://" />
    <button type="button" class="button" id="sc2editorLinkConfirm" style="float: right; width: 80px; border-right: none; border-left: 1px solid #c9c9c9;" >Вставить</button>
  </div>
<?php if($_action != null) { ?>
<form class="transpform" action="<?=$_action?>" method="post"
      enctype="multipart/form-data" id="sc2editorSavedForm">
<?php } ?>
  <div class="panel panel2" id="sc2editor_filepanel">
    <span class="separator" style="border: none;">Прикрепить файл:</span><br />
    <?php CTemplates::formList(array('',''),
                               array(),
                               "listformtablefiles", false, true); ?>
  </div>
  <?php if($_action != null) { ?>
  <?php foreach($_hidden as $k => $rec) { ?>
  <input type="hidden" name="<?=$k?>" value="<?=$rec?>" />
  <?php } ?>
  <input type="hidden" name="edText" id="sc2editorSavedText"/>
  <div id="filesbox" style="display: none;"></div>
</form>
  <?php } ?>

  <?php if($_action != null) { ?>
<br>
  <div class="transpform">
    <input type="button" value="<?=$_buttonlabel?>" style="margin-bottom: 0px;"
           id="sc2editorSave" />
  </div>
  <?php } ?>

</div>

<script>
  var g_sc2Editor = document.getElementById('sc2editor').parentNode.removeChild(
    document.getElementById('sc2editor')
  );
</script>

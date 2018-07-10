function sc2Editor(sId) {
  this.editor = document.getElementById(sId);
  this.range = null; // for saving selection before inserting hyperlink
  this.editor.onkeydown = function() {
    document.getElementById('sc2editor_panel_2').style.display = 'none';

  };
}

sc2Editor.prototype.initEditor = function (aButtons, checkFunc) {
  var self = this;
  var i;
  if(aButtons === undefined || aButtons === null) {
    aButtons = ['Undo', 'Bold', 'Italic', 'Underline', 'Stricked', 'Size',
                'Left', 'Center', 'Right', 'Justify', 'Image', 'Link', 'File'];
  }

  if(document.getElementById('sc2editorSave')) {
    document.getElementById('sc2editorSave').onclick = function() { self.onSave(checkFunc); }
  }

  var l = aButtons.length;
  for(i = 0; i < l; i++) {
    document.getElementById('sc2editor'+aButtons[i]).onclick = function() {
      switch(this.id) {
        case 'sc2editorUndo': self.onUndo(); break;
        case 'sc2editorBold': self.onBold(); break;
        case 'sc2editorItalic': self.onItalic(); break;
        case 'sc2editorUnderline': self.onUnderline(); break;
        case 'sc2editorStricked': self.onStricked(); break;
        case 'sc2editorSize': self.onSize();

        break
        case 'sc2editorLeft': self.onLeft(); break;
        case 'sc2editorCenter': self.onCenter(); break;
        case 'sc2editorRight': self.onRight(); break;
        case 'sc2editorJustify': self.onJustify(); break;
        case 'sc2editorImage': self.onImage(); break;
        case 'sc2editorLink': self.onLink(); break;
        case 'sc2editorFile': self.onFile(); break;
        default: console.log(this.id); break;
      }
    };
  }
  this.editor.focus();
};

sc2Editor.prototype.onUndo = function () {
  document.execCommand('undo', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onBold = function () {
  document.execCommand('bold', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onItalic = function () {
  document.execCommand('italic', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onUnderline = function () {
  document.execCommand('underline', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onStricked = function () {
  document.execCommand('strikeThrough', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onSize = function () {
  document.getElementById('sc2editor_panel_2').style.display = 'block';
  var self = this;
  document.getElementById('sc2editorSize1').onclick = function(e) { self.onSizeSet(1); }
  document.getElementById('sc2editorSize2').onclick = function() { self.onSizeSet(2); }
  document.getElementById('sc2editorSize3').onclick = function() { self.onSizeSet(3); }
  document.getElementById('sc2editorSize4').onclick = function() { self.onSizeSet(4); }
  document.getElementById('sc2editorSize5').onclick = function() { self.onSizeSet(5); }
  document.getElementById('sc2editorSize6').onclick = function() { self.onSizeSet(6); }
  document.getElementById('sc2editorSize7').onclick = function() { self.onSizeSet(7); }
  this.editor.focus();
};

sc2Editor.prototype.onSizeSet = function (n) {
  document.execCommand('formatBlock', false, '<h'+n+'>');
  this.editor.focus();
};


sc2Editor.prototype.onLeft = function () {
  document.execCommand('justifyLeft', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onCenter = function () {
  document.execCommand('justifyCenter', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onRight = function () {
  document.execCommand('justifyRight', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onJustify = function () {
  document.execCommand('justifyFull', false, false);
  this.editor.focus();
};

sc2Editor.prototype.onImage = function () {

  this.editor.focus();
};

sc2Editor.prototype.onLink = function () {
  this.saveSelection();
  var self = this;
  document.getElementById('sc2editorLinkConfirm').onclick = function() {
    self.onLinkInsert();
  }
  document.getElementById('sc2editor_linkpanel').style.display = 'block';
  document.getElementById('sc2editor_hyperlink').focus();
};

sc2Editor.prototype.saveSelection = function() {
  if(window.getSelection) {
    var sel = window.getSelection();
    if(sel.getRangeAt && sel.rangeCount) {
      this.range = sel.getRangeAt(0);
    } else {
      this.range = null;
    }
  } else if(document.selection && document.selection.createRange) {
    this.range = document.selection.createRange();
  } else {
    this.range = null;
  }
}

sc2Editor.prototype.restoreSelection = function() {
  if(this.range) {
    if(window.getSelection) {
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(this.range);
    } else if(document.selection && this.range.select) {
      this.range.select();
    }
  }
  this.range = null;
}

sc2Editor.prototype.onLinkInsert = function() {
  var sLink = document.getElementById('sc2editor_hyperlink').value.trim();
  if(sLink == '' || sLink == 'http://') {
    return;
  }
  this.editor.focus();
  this.restoreSelection();
  document.execCommand('createLink', false, sLink);
  document.getElementById('sc2editor_linkpanel').style.display = 'none';
}


sc2Editor.prototype.onFile = function () {
  var fp = document.getElementById('sc2editor_filepanel');
  if(fp.style.display != 'block') {
    fp.style.display = 'block';
  } else {
    fp.style.display = 'none';
  }

  this.editor.focus();
};

sc2Editor.prototype.onSave = function (checkFunc) {
  document.getElementById('sc2editorSavedText').value = document.getElementById('sc2editorRich').innerHTML;
  if(checkFunc) {
    if(checkFunc()) {
      document.getElementById('sc2editorSavedForm').submit();
    } else {
      return;
    }
  }
  document.getElementById('sc2editorSavedForm').submit();

};

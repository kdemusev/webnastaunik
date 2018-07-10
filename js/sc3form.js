function sc3FormEditableSelect(psid, psoptions, psvalues, allowadd, onselect) {
  this.container = document.getElementById(psid);
  this.psoptions = psoptions;
  this.psvalues = psvalues;
  this.allowadd = (allowadd === undefined || allowadd == true) ? 1 : 0;
  this.onselect = onselect;
  this.selected = 0;

  // create hidden input for id
  this.inputid = document.createElement('input');
  this.inputid.type = 'hidden';
  this.inputid.name = this.container.getAttribute('data-name');
  this.inputid.value = '0';
  this.container.appendChild(this.inputid);

  // create hidden input for new item
  this.inputvalue = document.createElement('input');
  this.inputvalue.type = 'hidden';
  this.inputvalue.name = 'add_'+this.container.getAttribute('data-name');
  this.inputvalue.value = '';
  this.container.appendChild(this.inputvalue);

  // create text input
  this.inputtext = document.createElement('div');
  this.inputtext.contentEditable = 'true';
  this.inputtext.className = 'sc3inputtext';
  this.inputtext.setAttribute('spellcheck', 'false');

  var scparent = this;
  this.inputtext.onkeydown = function() { scparent.psKeyDown(this); };
  this.inputtext.onkeyup = function() { scparent.psAutoComplete(this); };
  this.inputtext.onfocus = function() { scparent.psFocus(this); };
  this.container.appendChild(this.inputtext);

  // create popup listbox
  this.select = document.createElement('div');
  this.select.className = 'sc3popuptext';
  this.container.appendChild(this.select);

  // if it is allowed to add new item put empty item at the top of the array
  if(this.allowadd) {
    this.psoptions.unshift('');
    this.psvalues.unshift(-1);
  }

  this.create();
}

// create options items
sc3FormEditableSelect.prototype.create = function() {
  var obj;
  var i;
  var l = this.psoptions.length;
  var scparent = this;

  for(i = 0; i < l; i++) {
    obj = document.createElement('div');
    obj.className = 'psoption';
    obj.setAttribute('number', i);
    obj.style.display = 'none';
    obj.innerHTML = this.psoptions[i];
    obj.onclick = function() { scparent.psSelect(this); };
    this.select.appendChild(obj);
  }

  // choose entered value
  this.putValue();
}

sc3FormEditableSelect.prototype.putValue = function() {
  var idval = this.container.getAttribute('data-id');
  if(idval == null || idval == '') {
    return;
  }

  var n = this.psvalues.indexOf(idval);
  if(n < 0) {
    return;
  }

  this.inputid.value = this.psvalues[n];
  this.inputvalue.value = this.psoptions[n];
  this.inputtext.innerHTML = this.psoptions[n];

  this.selected = n;
  this.select.children[n].className = 'selected';

}

// change options items
sc3FormEditableSelect.prototype.change = function(psoptions, psvalues) {
  this.psoptions = psoptions;
  this.psvalues = psvalues;

  if(this.allowadd) {
    this.psoptions.unshift('');
    this.psvalues.unshift(-1);
  }

  while(this.select.firstChild) {
    this.select.removeChild(this.select.firstChild);
  }

  this.create();
}

// select option item with the mouse
sc3FormEditableSelect.prototype.psSelect = function(obj) {
  var n = obj.getAttribute('number');
  this.inputid.value = this.psvalues[n];
  if(this.psvalues[n] == -1) {
    this.inputvalue.value = this.inputtext.textContent;
  } else {
    this.inputvalue.value = this.psoptions[n];
    this.inputtext.innerHTML = this.psoptions[n];
  }
  this.inputtext.focus();
  this.select.style.display = 'none';

  this.selected = n;  // when selected and then pressed tab it must save selected item

  if(this.onselect !== undefined) {
    (this.onselect)();
  }
}

// get id of the selected item
sc3FormEditableSelect.prototype.getIdValue = function() {
  return this.inputid.value;
}

// select option item with the keyboard
sc3FormEditableSelect.prototype.psKeyDown = function(obj) {
  if(event.keyCode == 13 || event.keyCode == 9) { // Enter key or Tab key
    var idattr = this.psvalues[this.selected];
    this.inputid.value = idattr;
    if(idattr == -1) {
      this.inputvalue.value = obj.textContent.trim();
    } else {
      this.inputtext.innerHTML = this.psoptions[this.selected];
    }

    if(event.keyCode == 13) {
      event.preventDefault();
    }

    this.select.style.display = 'none';

    if(this.onselect !== undefined) {
      (this.onselect)();
    }

    return false;
  }

  if(event.keyCode==40) {
    var i;
    var l = this.select.children.length;
    var was = -1;

    for(i = this.selected + 1; i < l; i++) {
      if(this.select.children[i].style.display == 'block') {
        this.select.children[i].className = 'selected';
        was = i;
        break;
      }
    }

    if(was >= 0) {
      this.select.children[this.selected].className = '';
      this.selected = was;
    }

    event.preventDefault();
    return false;
  }

  if(event.keyCode==38) {
    var i;
    var l = this.select.children.length;
    var was = -1;

    for(i = this.selected - 1; i >= 0; i--) {
      if(this.select.children[i].style.display == 'block') {
        this.select.children[i].className = 'selected';
        was = i;
        break;
      }
    }

    if(was >= 0) {
      this.select.children[this.selected].className = '';
      this.selected = was;
    }

    event.preventDefault();
    return false;
  }
}

// focus area
sc3FormEditableSelect.prototype.psFocus = function(obj) {
  // collapse all such select boxes
  var boxes = document.getElementsByClassName('sc3popuptext');
  var i;
  var l = boxes.length;
  for(i = 0; i < l; i++) {
    boxes[i].style.display = 'none';
  }

  var stFrom = this.allowadd ? 1 : 0;
  l = this.select.children.length;

  this.select.style.display = 'block';

  this.select.children[0].style.display = 'none'; // hide 'Add: _______'
  for(i = stFrom; i < l; i++) {
    this.select.children[i].innerHTML = this.psoptions[i];  // remove tags
    this.select.children[i].style.display = 'block';
    this.select.children[i].className = '';
  }

  if(stFrom < this.select.children.length) {
    this.selected = stFrom;
    this.select.children[this.selected].className = 'selected';
  }

  // select all text
  var sel = window.getSelection();
  var rng = document.createRange();
  rng.selectNodeContents(obj);
  sel.removeAllRanges();
  sel.addRange(rng);
}

// search alike items
sc3FormEditableSelect.prototype.psAutoComplete = function(obj) {
  var i;
  var l = this.select.children.length;
  var text = this.inputtext.textContent.trim();
  var stPos;
  var elValue;
  var found = false;
  var stFrom = this.allowadd ? 1 : 0;
  var selectedvisible = false;

  this.select.style.display = 'block';

  // if empty edit box - show all
  if(text == '') {
    this.select.children[0].style.display = 'none'; // hide 'Add: _______'
    for(i = stFrom; i < l; i++) {
      this.select.children[i].innerHTML = this.psoptions[i];  // remove tags
      this.select.children[i].style.display = 'block';
      this.select.children[i].className = '';
    }
    if(stFrom < this.select.children.length) {
      if(this.selected == 0) { this.selected = stFrom; }
      this.select.children[this.selected].className = 'selected';
    }
    return;
  }

  // hide all elements and clear from <b> </b>
  for(i = 0; i < l; i++) {
    this.select.children[i].innerHTML = this.psoptions[i];
    this.select.children[i].className = '';
    this.select.children[i].style.display = 'none';
  }

  // find full accordance
  for(i = 1; i < l; i++) {
    if(this.psoptions[i].toLowerCase() == text.toLowerCase()) {
      found = true;
      break;
    }
  }

  if(this.allowadd && !found) {
    this.select.children[0].innerHTML = '<i>Добавить: </i><b>'+text+'</b>';
    this.select.children[0].style.display = 'block';
    if(this.selected == 0) {
      selectedvisible = true;
    }
  }

  var atleastone = false;
  var firstvisible = -1;
  // find and show
  for(i = stFrom; i < l; i++) {
    elValue = this.psoptions[i];
    stPos = elValue.toLowerCase().indexOf(text.toLowerCase());
    if(stPos >= 0) {
      elValue = elValue.slice(0, stPos) + '<b>' + elValue.slice(stPos, stPos+text.length) +
                '</b>' + elValue.slice(stPos+text.length);
      this.select.children[i].innerHTML = elValue;
      this.select.children[i].style.display = 'block';
      if(this.selected == i) {
        selectedvisible = true;
      }
      atleastone = true;
      if(firstvisible < 0) {
        firstvisible = i;
      }
    }
  }

  if(atleastone == false && !this.allowadd) {
    for(i = stFrom; i < l; i++) {
      this.select.children[i].innerHTML = this.psoptions[i];  // remove tags
      this.select.children[i].style.display = 'block';
      this.select.children[i].className = '';
    }
    if(stFrom < this.select.children.length) {
      if(this.selected == 0) { this.selected = stFrom; }
      this.select.children[this.selected].className = 'selected';
    }
    return;
  }

  if(!selectedvisible) {
    if(firstvisible >= 0) {
      this.selected = firstvisible;
    } else {
      this.selected = 0;
    }
  }


  this.select.children[this.selected].className = 'selected';
}

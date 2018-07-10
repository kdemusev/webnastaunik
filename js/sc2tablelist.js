function sc2TableList(table_id, form_id) {
  this.table_id = table_id;
  this.form_id = form_id;
  this.fields = [];
  this.empty = false;
  this.marginright = '20px';
  this.order = 1;
  this.buttonsColumnNumber = -1;
  this.checkboxColumnNumber = -1;
  this.isOrdering = false;
  this.isNumbering = false;
  this.orderColumnNumber = 0;
  this.orderingColumnNumber = 0;
  this.mainInput = 0;
  this.funcAfterReorder = null;
}

sc2TableList.prototype.addField = function (type, name, size, ismain, autosave) {
  if (type === 'order') {
    this.isOrdering = true;
    this.orderingColumnNumber = this.fields.length;
  }

  if (type === 'number') {
    this.isNumbering = true;
    this.orderColumnNumber = this.fields.length;
  }

  if (ismain) {
    this.mainInput = this.fields.length;
  }

  if (ismain === undefined) {
    this.fields.push({type: type, name: name, size: size, autosave: autosave});
  } else {
    this.fields.push({type: type, name: name, size: size, ismain: true, autosave: autosave});
  }

};

sc2TableList.prototype.setAutosavable = function(table, field, where, where2, cmp_val2) {
  this.autotable = table;
  this.autofield = field;
  this.autowhere = where;
  this.autowhere2 = where2;
  this.autocmp_val2 = cmp_val2;
}

sc2TableList.prototype.addRecord = function(row) {
  var i;
  var tr = document.createElement('tr');
  tr.className = 'movabletr';

  for (i = 0; i < this.fields.length; i++) {
    var td = document.createElement('td');

    switch(this.fields[i].type) {
      case 'textnumber':
        td.appendChild(this.addText(i, row, true));
        if(this.fields[i].autosave) {
          td.appendChild(this.addAutosaveAnimate(i, row));
        }
        break;
      case 'text':
        td.appendChild(this.addText(i, row));
        td.style.width = this.fields[i].size;
        if(this.fields[i].autosave) {
          td.appendChild(this.addAutosaveAnimate(i, row));
        }
        break;
      case 'hidden':
        td.appendChild(this.addHidden(i, row));
        break;
      case 'file':
        td.appendChild(this.addFile(i, row));
        break;
      case 'select':
        td.appendChild(this.addSelect(i, row));
        break;
      case 'innerHTML':
        td.appendChild(this.addInnerHTML(i, row));
        break;
      case 'label':
        td.appendChild(this.addLabel(i));
        break;
      case 'number':
        td.appendChild(this.addNumber());
        break;
      case 'order':
        td.appendChild(this.addOrder(i, row));
        break;
      case 'checkbox':
        td.appendChild(this.addCheckbox(i, row));
        this.checkboxColumnNumber = i;
        break;
      case 'buttons':
        td.style.whiteSpace = 'nowrap';
        td.className = "cellBottom";
        this.buttonsColumnNumber = i;
        td.appendChild(this.addUp(row));
        td.appendChild(this.addDown(row));
        td.appendChild(this.addDelete(row));
        if(row && row.funcOrder) {
          this.funcAfterReorder = row.funcOrder;
        }
        break;
      case 'editbuttons':
        td.style.whiteSpace = 'nowrap';
        td.className = "cellBottom";
        this.buttonsColumnNumber = i;
        td.appendChild(this.addUp(row));
        td.appendChild(this.addDown(row));
        td.appendChild(this.addEdit(row));
        td.appendChild(this.addDelete(row));
        if(row && row.funcOrder) {
          this.funcAfterReorder = row.funcOrder;
        }
        break;
      case 'delbutton':
        td.className = "cellBottom";
        this.buttonsColumnNumber = i;
        td.appendChild(this.addDelete(row));
        if(row && row.funcOrder) {
          this.funcAfterReorder = row.funcOrder;
        }
        break;
      case 'deleditbuttons':
        td.style.whiteSpace = 'nowrap';
        td.className = "cellBottom";
        this.buttonsColumnNumber = i;
        td.appendChild(this.addEdit(row));
        td.appendChild(this.addDelete(row));
        if(row && row.funcOrder) {
          this.funcAfterReorder = row.funcOrder;
        }
        break;

    }

    tr.appendChild(td);
  }

  document.getElementById(this.table_id).appendChild(tr);

  for(var i = 0; i < this.fields.length; i++) {
    if(this.fields[i].autosave) {
      this.addAutosave(i, row);
    }
  }
};

sc2TableList.prototype.addEmpty = function(row) {
  this.empty = true;
  this.addRecord(row);
};

sc2TableList.prototype.addText = function(i, row, isNumber) {
  var inptype = isNumber ? 'number' : 'text';
  var inp = document.createElement('input');
  inp.type = inptype;
  if(!this.empty) {
      inp.name = this.fields[i].name+'['+row.id+']';
      inp.id = this.fields[i].name+'['+row.id+']';
      inp.value = row[this.fields[i].name] ? row[this.fields[i].name] : row.value;
  } else {
      inp.name = 'new'+this.fields[i].name+'[]';
  }

  inp.style.width = this.fields[i].size;
  inp.style.boxSizing = 'border-box';
  inp.style.marginRight = this.marginright;
  inp.style.marginBottom = '5px;';
  inp.setAttribute("autocomplete", "off");

  if(this.empty && this.fields[i].ismain) {
      var obj = this;
      inp.onkeyup = function() {
          if(this.value.trim() !== '') {
              obj.addEmpty(row);
              this.onkeyup = null;
          }
      };
  }

  inp.onkeydown = function(e) {
    switch (e.keyCode) {
      case 40:  // down
        if(this.parentNode.parentNode.nextSibling &&
           this.parentNode.parentNode.nextSibling.children[i]) {
          this.parentNode.parentNode.nextSibling.children[i].children[0].focus();
          this.parentNode.parentNode.nextSibling.children[i].children[0].select();
        }
        e.preventDefault();
        break;
      case 38:  // up
        if(this.parentNode.parentNode.previousSibling &&
           this.parentNode.parentNode.previousSibling.children[i]) {
          this.parentNode.parentNode.previousSibling.children[i].children[0].focus();
          this.parentNode.parentNode.previousSibling.children[i].children[0].select();
        }
        e.preventDefault();
        break;
    }
  };

  return inp;
};

sc2TableList.prototype.addAutosaveAnimate = function(i, row) {
  var span = document.createElement('span');
  span.id = 'animate'+this.fields[i].name+'['+row.id+']';

  return span;
}

sc2TableList.prototype.addAutosave = function(i, row) {
  var autosave = new sc2Autosave(this.fields[i].name+'['+row.id+']',
                                 this.autotable,
                                 this.fields[i].name,
                                 this.autowhere,
                                 row.id,
                                 'animate'+this.fields[i].name+'['+row.id+']',
                                 this.autowhere2,
                                 this.autocmp_val2,
                                 1);
}

sc2TableList.prototype.addHidden = function(i, row) {
  var inp = document.createElement('input');
  inp.type = 'hidden';
  if(!this.empty) {
      inp.name = this.fields[i].name+'['+row.id+']';
      inp.value = row[this.fields[i].name] ? row[this.fields[i].name] : row.value;
  } else {
      inp.name = 'new'+this.fields[i].name+'[]';
  }

  return inp;
};

sc2TableList.prototype.addFile = function(i, row) {
  var inp = document.createElement('input');
  if(!this.empty) {
    inp.type = 'text';
    inp.readonly = 'readonly';
    inp.name = this.fields[i].name+'['+row.id+']';
    inp.value = row[this.fields[i].name] ? row[this.fields[i].name] : row.value;
  } else {
    inp.type = 'file';
    inp.name = 'new'+this.fields[i].name+'[]';
  }

  inp.style.width = this.fields[i].size;

  if(this.empty && this.fields[i].ismain) {
      var obj = this;
      inp.onchange = function() {
        obj.addEmpty(row);
        this.onchange = null;
      };
  }

  return inp;
};


sc2TableList.prototype.addCheckbox = function (i, row) {
  var inp = document.createElement('input');
  inp.type = 'checkbox';
  if(!this.empty) {
      inp.name = this.fields[i].name+'['+row.id+']';
      inp.value = row.value;
  } else {
      inp.name = 'new'+this.fields[i].name+'[]';
  }

  if(row.funcCheck) {
    var self = this;
    inp.onchange = function() { row.funcCheck(row, self, this); };
  }

  if(row.checked==1) {
    inp.checked = "checked";
  }

  return inp;
};

sc2TableList.prototype.addSelect = function (i, row) {
  var sel = document.createElement('select');
  if(!this.empty) {
      sel.name = this.fields[i].name+'['+row.id+']';
  } else {
      sel.name = 'new'+this.fields[i].name+'[]';
  }
  sel.style.marginBottom = '5px;';
  var opt;
  var selected = false;
  for(var j=0; j<row.list.length; j++) {
      opt = document.createElement('option');
      opt.innerHTML = row.list[j].value;
      opt.value = row.list[j].id;

      if(row.list[j].id == row.value ||
         row.list[j].id == row[this.fields[i].name]) {
          opt.selected = 'selected';
          selected = true;
      }
      sel.appendChild(opt);
  }
  if(!selected) {
      sel.selectedIndex = '-1';
  }
  sel.style.width = this.fields[i].size;

  return sel;
};

sc2TableList.prototype.addNumber = function() {
  return document.createTextNode(this.order++);
};

sc2TableList.prototype.addOrder = function (i, row) {
  var hidden = document.createElement('input');
  hidden.type = 'hidden';
  if(!this.empty) {
      hidden.name = this.fields[i].name+'['+row.id+']';
  } else {
      hidden.name = 'new'+this.fields[i].name+'[]';
  }
  hidden.value = this.order-1;

  return hidden;
};

sc2TableList.prototype.addLabel = function(i) {
  var oLabel = document.createElement('label');
  oLabel.innerHTML = this.fields[i].name;
  return oLabel;
}

sc2TableList.prototype.addInnerHTML = function (i, row) {
  var oDiv = document.createElement('div');
  oDiv.innerHTML = row[this.fields[i].name] ? row[this.fields[i].name] : row.value;
  oDiv.style.width = this.fields[i].size;
  oDiv.style.backgroundColor = row.bgcolor;
  if(row.funcClick) {
    oDiv.style.cursor = 'pointer';
    oDiv.onclick = function() {
      row.funcClick(this.parentNode.parentNode, row);
    }
  }
  return oDiv;
};

sc2TableList.prototype.addUp = function(row) {
  var self = this;
  var button = document.createElement('img');
  button.src = '/style/icons/table.up.png';
  button.title = 'поднять вверх';
  button.onclick = function() { self.onUp(this); };
  return button;
};

sc2TableList.prototype.addDown = function(row) {
  var self = this;
  var button = document.createElement('img');
  button.src = '/style/icons/table.down.png';
  button.title = 'опустить вниз';
  button.onclick = function() { self.onDown(this); };
  return button;
};

sc2TableList.prototype.addEdit = function(row) {
  var self = this;
  var button = document.createElement('img');
  button.src = '/style/icons/table.edit.png';
  button.title = 'изменить';
  button.onclick = function() {
    if(row.funcEdit) {
      row.funcEdit(row, self, this);
    }
  };
  return button;
};

sc2TableList.prototype.addDelete = function(row) {
  var self = this;
  var button = document.createElement('img');
  button.src = '/style/icons/table.delete.png';
  button.title = 'удалить';
  button.onclick = function() {
    if(row.funcDel) {
      row.funcDel(row, self, this);
    } else {
      self.onDelete(this);
    }
  };
  return button;
};

sc2TableList.prototype.reorder = function () {
  var rows = document.getElementById(this.table_id).childNodes;
  var n = 1;
  for(var i = 1; i < rows.length; i++) {
      if(rows[i].className === 'movabletr') {
          if(this.isNumbering) {
            rows[i].childNodes[this.orderColumnNumber].innerHTML = n;
          }
          if(this.isOrdering) {
            rows[i].childNodes[this.orderingColumnNumber].childNodes[0].value = n;
          }
          n++;
      }
  }
  if(this.funcAfterReorder) {
    this.funcAfterReorder(this);
  }
};

sc2TableList.prototype.onUp = function (element) {
  var trtomove = element.parentNode.parentNode;

  // if at the top
  if(trtomove.previousSibling === null ||
     trtomove.previousSibling.className !== 'movabletr') {
      return;
  }

  var trbefore = trtomove.previousSibling;
  trtomove.parentNode.insertBefore(trtomove, trbefore);

  this.reorder();
};

sc2TableList.prototype.onDown = function (element) {
  var trtomove = element.parentNode.parentNode;

  // if at the bottom
  if(trtomove.nextSibling === null ||
     trtomove.nextSibling.className !== 'movabletr') {
      return;
  }

  // no sense to put after empty list item supposed to add
  if(trtomove.nextSibling !== null ) {
      var trbefore = trtomove.nextSibling.nextSibling;
      trtomove.parentNode.insertBefore(trtomove, trbefore);
  } else {
    trtomove.parentNode.appendChild(trtomove);
  }

  this.reorder();
};

sc2TableList.prototype.onDelete = function (element) {
  var trtoremove = element.parentNode.parentNode;

  // check if it is new
  if(trtoremove.childNodes[this.mainInput].childNodes[0].name &&
     trtoremove.childNodes[this.mainInput].childNodes[0].name.substring(0,3) == 'new' &&
     !trtoremove.nextSibling) {
    return;
  }

  trtoremove.childNodes[this.mainInput].childNodes[0].value = '';
  trtoremove.className = '';
  trtoremove.style.display = 'none';
  // move to top for sorting
  trtoremove.parentNode.insertBefore(trtoremove, trtoremove.parentNode.childNodes[0]);
  this.order--;
  this.reorder();
};

sc2TableList.prototype.strikeAll = function () {
  var trs = document.getElementById(this.table_id).children;
  for(var i = 0; i < trs.length; i++) {
    if(trs[i].children[this.checkboxColumnNumber].children[0] &&
       trs[i].children[this.checkboxColumnNumber].children[0].checked) {
      this.onStrike(trs[i].children[this.checkboxColumnNumber].children[0]);
    }
  }
}

sc2TableList.prototype.onStrike = function (element) {
  var trtostrike = element.parentNode.parentNode;
  trtostrike.className = 'strikedout';
  // move to bottom for sorting
  trtostrike.parentNode.appendChild(trtostrike);
  if(this.buttonsColumnNumber>=0) {
    trtostrike.children[this.buttonsColumnNumber].style.display = 'none';
  }
  this.reorder();
};

sc2TableList.prototype.onStrikeOut = function (element) {
  var trtostrike = element.parentNode.parentNode;
  trtostrike.className = 'movabletr';
  //search for last movabletr
  var lasttrtostrike = null;
  for(var i = 0; i < trtostrike.parentNode.children.length; i++) {
    if(trtostrike.parentNode.children[i].className == 'movabletr' &&
       trtostrike.parentNode.children[i] != trtostrike) {
      lasttrtostrike = trtostrike.parentNode.children[i];
    }
  }
  if(lasttrtostrike == null) {  // no movabletr
    trtostrike.parentNode.insertBefore(trtostrike,trtostrike.parentNode.firstChild);
  } else if(lasttrtostrike.nextSibling) { // usual
    trtostrike.parentNode.insertBefore(trtostrike,lasttrtostrike.nextSibling);
  } else {  // no more striked
    trtostrike.parentNode.appendChild(trtostrike);
  }
  if(this.buttonsColumnNumber>=0) {
    trtostrike.children[this.buttonsColumnNumber].style.display = 'table-cell';
  }
  this.reorder();
};

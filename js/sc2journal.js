function sc2Journal(container, headerHeight, cellHeight, padding) {
  this.container = container;
  this.borderColor = 'grey';
  this.highlightColor = '#e0e0e0';
  this.topHighlightColor = '#cbcbcb';

  this.headerHeight = headerHeight + 2*padding;
  this.cellHeight = cellHeight + 2*padding;

  // div arrays
  this.editableCells = new Array();
  this.namesCells = new Array();    // horisonral background in marks list
  this.idsCells = new Array();      // horisontal background in pupils list
  this.topDatesCells = new Array(); // vertical background in marks list
  this.topicsCells = new Array();   // horisontal background in topics list

  // data arrays
  this.names = new Array();
  this.dates = new Array();
  this.topics = new Array();
  this.pupil_ids = new Array();
  this.ktp_ids = new Array();
  this.types = new Array();     // lesson types
  this.subject_ids = new Array();
  this.marks = new Array();
  this.colors = new Array();    // ktp topic colors

  this.leftBlock;
  this.rightBlock;
  this.centerBlock;

  this.padding = padding*2;
};

sc2Journal.prototype.cell = function(container, x, y, w, h, innerText, align, editable, i, j) {
  var div = document.createElement('div');
  div.style.position = 'absolute';
  div.style.left = x+'px';
  div.style.top = y+'px';
  div.style.width = w+'px';
  div.style.height = h+'px';
  div.style.borderLeft = '1px solid ' + this.borderColor;
  div.style.borderTop = '1px solid ' + this.borderColor;
  div.innerHTML = innerText;
  if(align != undefined) {
    div.style.textAlign = align;
  }
  if(editable != undefined) {
    div.setAttribute('contenteditable', 'true');
    if(this.editableCells[i] == undefined) { this.editableCells[i] = new Array(); }
    this.editableCells[i][j] = div;
    div.setAttribute('data-i', i);
    div.setAttribute('data-j', j);
    div.setAttribute('data-changed', 'false');
    div.innerHTML = this.marks[this.pupil_ids[i]][this.ktp_ids[j]];

    var parent = this;
    div.onkeydown = function() {
      var infi = parseInt(this.getAttribute('data-i'));
      var infj = parseInt(this.getAttribute('data-j'));
      if(event.keyCode == 40 || event.keyCode == 13) { // down
        if(infi < parent.editableCells.length-1) {
          parent.editableCells[infi+1][infj].focus();
        }
        return false;
      }
      if(event.keyCode == 38) { // up
        if(infi > 0) {
          parent.editableCells[infi-1][infj].focus();
        }
        return false;
      }
      if(event.keyCode == 37) { // left
        if(infj > 0) {
          parent.editableCells[infi][infj-1].focus();
        }
        return false;
      }
      if(event.keyCode == 39) { // right
        if(infj < parent.editableCells[infi].length-1) {
          parent.editableCells[infi][infj+1].focus();
        }
        return false;
      }
    }
    div.onkeyup = function() {
      var allowedContent = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'н', 'з');
      if(allowedContent.indexOf(this.textContent.trim())==-1) {
        this.innerHTML = '';
      }
    }
    div.onfocus = function() {
      var infi = parseInt(this.getAttribute('data-i'));
      var infj = parseInt(this.getAttribute('data-j'));
      parent.topDatesCells[infj].style.backgroundColor = parent.highlightColor;
      parent.idsCells[infi].style.backgroundColor = parent.highlightColor;
      parent.namesCells[infi].style.backgroundColor = parent.highlightColor;
      parent.topicsCells[infj].style.backgroundColor = parent.highlightColor;
      this.style.backgroundColor = parent.topHighlightColor;
    }
    div.onblur = function() {
      var infi = parseInt(this.getAttribute('data-i'));
      var infj = parseInt(this.getAttribute('data-j'));
      parent.topDatesCells[infj].style.backgroundColor = parent.topDatesCells[infj].getAttribute('data-color');
      parent.idsCells[infi].style.backgroundColor = 'transparent';
      parent.namesCells[infi].style.backgroundColor = 'transparent';
      parent.topicsCells[infj].style.backgroundColor = parent.topicsCells[infj].getAttribute('data-color');
      this.style.backgroundColor = 'transparent';
    }
    div.onchange = function() {
      this.setAttribute('data-changed', 'true');
    }
  }
  div.style.overflow = 'hidden';
  div.onFocus = function() { this.style.backgroundColor = 'lightgrey'; }
  div.onBlur = function() { this.style.backgroundColor = 'transparent'; }
  container.appendChild(div);
  return div;
};

sc2Journal.prototype.horizontalBackground = function(container, y, h, color) {
  var div = document.createElement('div');
  if(color == undefined) { color = 'transparent'; }
  div.style.position = 'absolute';
  div.style.left = '0px';
  div.style.top = y+'px';
  div.style.right = '0px';
  div.style.height = (h+1)+'px';
  div.style.backgroundColor = color;
  div.setAttribute('data-color', color);
  container.appendChild(div);
  return div;
}

sc2Journal.prototype.verticalBackground = function(container, x, w, color) {
  var div = document.createElement('div');
  if(color == undefined) { color = 'transparent'; }
  div.style.position = 'absolute';
  div.style.left = x+'px';
  div.style.top = '0px';
  div.style.width = (w+1)+'px';
  div.style.bottom = '0px';
  div.style.backgroundColor = color;
  div.setAttribute('data-color', color);
  container.appendChild(div);
  return div;
}

sc2Journal.prototype.block = function(x, y, w, overflow) {
  var div = document.createElement('div');
  div.style.position = 'absolute';
  div.style.left = x+'px';
  div.style.top = y+'px';
  div.style.width = w+'px';
  div.style.height = '500px';
  div.style.borderRight = '1px solid ' + this.borderColor;
  if(overflow != undefined) { div.style.overflowX = 'auto'; div.style.overflowY = 'hidden'}
  this.container.appendChild(div);
  return div;
};

sc2Journal.prototype.addName = function(ppname) {
  this.names.push(ppname);
};

sc2Journal.prototype.addDate = function(date) {
  this.dates.push(date);
};

sc2Journal.prototype.addTopic = function(topic) {
  this.topics.push(topic);
};

sc2Journal.prototype.addPupilId = function(pupil_id) {
  this.pupil_ids.push(pupil_id);
}

sc2Journal.prototype.addKtpId = function(ktp_id) {
  this.ktp_ids.push(ktp_id);
}

sc2Journal.prototype.addType = function(type) {
  this.types.push(type);
}

sc2Journal.prototype.addMarks = function(pupil_id, ktp_id, mark) {
  if(this.marks[pupil_id] == undefined) {
    this.marks[pupil_id] = new Array();
  }
  this.marks[pupil_id][ktp_id] = mark;
}

sc2Journal.prototype.addColor = function(color) {
  this.colors.push(color);
}

sc2Journal.prototype.buildList = function() {
  this.leftBlock = this.block(0, 0, 242+this.padding);
  this.leftBlock.style.borderRight = '2px solid ' + this.borderColor;
  this.leftBlock.style.borderBottom = '1px solid ' + this.borderColor;
  this.leftBlock.style.height = (this.headerHeight+1 + (this.cellHeight)*(this.names.length+1) + this.padding*2) + 'px';
  this.cell(this.leftBlock, 0, 0, 30, this.headerHeight-this.padding, '<div style="position: absolute; left: 0; bottom: 0; right: 0; text-align: center; font-weight: bold;">№ п/п</div>', 'center');
  this.cell(this.leftBlock, 31+this.padding, 0, 210, this.headerHeight-this.padding, '<div style="position: absolute; left: 0; bottom: 0; right: 0; text-align: center; font-weight: bold;">Фамилия, имя учащегося</div>', 'center');

  var i;
  var l = this.names.length;
  for(i = 0; i < l; i++) {
    this.idsCells[i] = this.horizontalBackground(this.leftBlock, (this.headerHeight+1)+i*(this.cellHeight+1),this.cellHeight-this.padding);
    this.cell(this.leftBlock, 0, (this.headerHeight+1)+i*(this.cellHeight+1), 30, this.cellHeight-this.padding, i+1, 'right');
    this.cell(this.leftBlock, 31+this.padding, (this.headerHeight+1)+i*(this.cellHeight+1), 210, this.cellHeight-this.padding, this.names[i]);
  }
  var t = this.cell(this.leftBlock, 0, (this.headerHeight+1)+l*(this.cellHeight+1), 241+this.padding*2, this.cellHeight-this.padding, '');
  t.style.padding = '0';
  t.style.borderTop = '1px solid ' + this.borderColor;
};

sc2Journal.prototype.buildMarks = function() {
  this.centerBlock = this.block(244+this.padding*2, 0, 300, true);
  this.centerBlock.style.width = 'auto';
  this.centerBlock.style.right = '450px';
  this.centerBlock.style.height = (this.headerHeight+1 + (this.cellHeight)*(this.names.length+1) + this.padding*2) + 'px';
  this.centerBlock.style.borderBottom = '1px solid ' + this.borderColor;
  var i;
  var t;
  var ld = this.dates.length;
  var lp = this.names.length;
  for(i = 0; i < ld; i++) {
    this.topDatesCells[i] = this.verticalBackground(this.centerBlock, i*(31+this.padding), 30, this.colors[i]);
    this.cell(this.centerBlock, i*(31+this.padding), 0, 30, this.headerHeight-this.padding, '<div style="position: absolute; left: 0; bottom: 0; right: 0; font-weight: bold; transform: rotate(-90deg); padding-left: 13px;">'+this.dates[i]+'</div>', 'center');
  }

  for(t = 0; t < lp; t++) {
    this.namesCells[t] = this.horizontalBackground(this.centerBlock, (this.headerHeight+1)+t*(this.cellHeight+1),this.cellHeight-this.padding);
    this.namesCells[t].style.width = (ld*(31+this.padding))-this.padding+'px';
    this.namesCells[t].style.right = 'auto';
    for(i = 0; i < ld; i++) {
      this.cell(this.centerBlock, i*(31+this.padding), (this.headerHeight+1)+t*(this.cellHeight+1), 30, this.cellHeight-this.padding, '', 'center', true, t, i);
    }
  }

  var t = this.cell(this.centerBlock, 0, (this.headerHeight+1)+lp*(this.cellHeight+1), 0, 0, '');
  t.style.borderTop = '1px solid ' + this.borderColor;
  t.style.padding = '0';
  t.style.width = (ld*(31+this.padding)-1)+'px';

  // scroll to last date
  this.centerBlock.scrollLeft = 1000000;
};

sc2Journal.prototype.buildTopics = function() {
  this.rightBlock = this.block(0,0,450-this.padding-1);
  this.rightBlock.style.left = 'auto';
  this.rightBlock.style.right = '0px';
  this.rightBlock.style.height = (this.headerHeight+1 + (this.cellHeight)*(this.dates.length+1) + this.padding*2) + 'px';
  this.cell(this.rightBlock, 0, 0, 100, this.headerHeight-this.padding, '<div style="position: absolute; left: 0; bottom: 0; right: 0; text-align: center; font-weight: bold;">Дата</div>', 'center');
  this.cell(this.rightBlock, 101+this.padding, 0, 351-this.padding*3, this.headerHeight-this.padding, '<div style="position: absolute; left: 0; bottom: 0; right: 0; text-align: center; font-weight: bold;">Тема</div>', 'center');

  var ld = this.dates.length;
  for(i = 0; i < ld; i++) {
    this.topicsCells[i] = this.horizontalBackground(this.rightBlock, (this.cellHeight+1)*i+(this.headerHeight+1),this.cellHeight-this.padding, this.colors[i]);
    this.cell(this.rightBlock, 0, (this.cellHeight+1)*i+(this.headerHeight+1), 100, this.cellHeight-this.padding, this.dates[i], 'center');
    this.cell(this.rightBlock, 101+this.padding, (this.cellHeight+1)*i+(this.headerHeight+1), 351-this.padding*3, this.cellHeight-this.padding, this.topics[i], 'left');
  }

  var t = this.cell(this.rightBlock, 0, (this.headerHeight+1)+ld*(this.cellHeight+1), 0, 0, '');
  t.style.borderTop = '1px solid ' + this.borderColor;
  t.style.padding = '0';
  t.style.width = 'auto';
  t.style.right = '0';
};

sc2Journal.prototype.build = function() {
  this.buildList();
  this.buildMarks();
  this.buildTopics();
};

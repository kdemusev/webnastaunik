String.prototype.q = function() {
  return this.toString().replace(/'/g, '&quot;');
}

function occurrences(string) {
  string += "";
  var subString = "/";
  var n=0, pos=0;

  while (true) {
    pos=string.indexOf(subString,pos);
    if (pos >= 0) {
      n++;
      pos+=1;
    } else {
      break;
    }
  }
  return(n);
}

function go(url) {
    window.location = url;
}

function sc2_id(o) {
  return document.getElementById(o);
}

function id(o) {
    return document.getElementById(o);
}

function show(o) {
    id(o).style.display = 'block';
}

function animateIcon(icon_id) {
  setInterval(function() {
    if(document.getElementById(icon_id).style.width == '36px') {
      document.getElementById(icon_id).style.width = '48px';
      document.getElementById(icon_id).style.height = '48px';
      document.getElementById(icon_id).style.paddingLeft = '0px';
      document.getElementById(icon_id).style.paddingRight = '0px';
    } else {
      document.getElementById(icon_id).style.width = '36px';
      document.getElementById(icon_id).style.height = '36px';
      document.getElementById(icon_id).style.paddingLeft = '6px';
      document.getElementById(icon_id).style.paddingRight = '6px';
    }
  }, 1000);
}

function showAnimated(o, animation, time) {
    id(o).style.display = 'block';
    id(o).style.WebkitAnimation = animation+' '+time;
    id(o).style.animation = animation+' '+time;
}

function hideAnimated(o, animation, time) {
    if(id(o).style.display == 'block') {
      id(o).style.WebkitAnimation = animation+' '+time;
      id(o).style.animation = animation+' '+time;
      id(o).style.WebkitAnimationFillMode = 'forwards';
      id(o).style.animationFillMode = 'forwards';
    }
}

function hide(o) {
    id(o).style.display = 'none';
}

function value(o) {
    return id(o).value;
}

function selvalue(o) {
    return sc2_id(o).options[id(o).selectedIndex].value;
}

function seterror(o) {
    var oldClassName = id(o).className;
    id(o).className += ' seterror';
    id(o).onfocus = function() {
        id(o).className = oldClassName;
    };
}

function checked(name) {
    var checkboxes = document.getElementsByTagName('input');
    var is_checked = false;
    for(var i = 0; i < checkboxes.length; i++) {
        var checkbox = checkboxes[i];
        if(checkbox.name && checkbox.name == name && checkbox.checked) {
            is_checked = true;
            break;
        }
    }
    return is_checked;
}

function filled(name) {
    var inputs = document.getElementsByTagName('input');
    var is_filled = false;
    for(var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        if(input.name && input.name.replace(/([^\[]+)\[.*\]/, '$1') == name && input.value.trim() != '') {
            is_filled = true;
            break;
        }
    }
    console.log(is_filled);
    return is_filled;
}


function clearSelect(obj) {
    var o = id(obj);
    while (o.firstChild) {
        o.removeChild(o.firstChild);
    }
    var option = document.createElement('option');
    option.value = '0';
    option.innerHTML = '';
    option.selected = 'selected';
    option.disabled = 'disabled';
    option.style = 'display: none';
    id(obj).appendChild(option);
}

function addOption(o, value, text, selval) {
    option = document.createElement('option');
    option.value = value;
    if(selval && selval == value) {
      option.selected = "selected";
    }
    option.innerHTML = text;
    id(o).appendChild(option);
}

function makeTextareaAutoresizable() {
  var objs = document.getElementsByTagName('textarea');
  var i, j;
  var l = objs.length;
  var cl;
  var l2;
  var objfound;
  for(i = 0; i < l; i++) {
    cl = objs[i].classList;
    objound = -1;
    l2 = objs[i].classList.length;
    for(j = 0; j < l2; j++) {
      if(objs[i].classList[j] == 'autoresizable') {
        objs[i].addEventListener('keyup', function() {
          var myScrollHeight = this.value.split('\n').length * 25;
          if(myScrollHeight >= 75) {
            this.style.height = myScrollHeight + 'px';
          }
        });
        if ("createEvent" in document) {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("keyup", false, true);
            objs[i].dispatchEvent(evt);
        }
        else {
          element.fireEvent("onkeyup");
        }
      }
    }
  }
}

function regionSelected(obj) {
    SMPAjaxGet('/index.php?section=users&action=getdistricts&id='+obj.value, function(res) {
        clearSelect('district_id');
        x = res.documentElement.getElementsByTagName('region');
        for(var i = 0; i < x.length; i++) {
            addOption('district_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
        }
    }, true);
}

function districtSelected(obj) {
    SMPAjaxGet('/index.php?section=users&action=getschools&id='+obj.value, function(res) {
        clearSelect('school_id');
        x = res.documentElement.getElementsByTagName('school');
        for(var i = 0; i < x.length; i++) {
            addOption('school_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
        }
    }, true);
}

function schoolSelected(obj) {
  SMPAjaxGet('/index.php?section=users&action=getteachers&id='+obj.value, function(res) {
      clearSelect('teacher_id');
      x = res.documentElement.getElementsByTagName('teacher');
      for(var i = 0; i < x.length; i++) {
          addOption('teacher_id', x[i].getAttribute('id'), x[i].firstChild.nodeValue);
      }
  }, true);
}


function makeColorFromStr(str) {
  var hash = 0;
  var i;
  var l = str.length;
  for(i = 0; i < l; i++) {
    hash += str.charCodeAt(i)%1000;
  }
  var hue = hash % 360;
  var sat = (hash % 10) * 10;
  sat = sat < 50 ? 100 - sat : sat;
  var lig = Math.round(hash / 1000) * 10 + 70;
  return "hsl("+hue+","+sat+"%,"+lig+"%)";
}


// make list to edit database
// fields = { name, id, type, value, size, main(//for onkeyup) }
// types: innerHTML, text, select
var tableListNumber = 1;
function tableList(table_id, fields, empty) {
    var table = id(table_id);
    var tr = document.createElement('tr');
    tr.className = 'movabletr';

    for(var i=0; i<fields.length; i++) {
        var td = document.createElement('td');
        if(fields[i]['type'] === 'innerHTML') {
            td.innerHTML = fields[i]['value'];
        } else if(fields[i]['type'] === 'text') {
            var inp = document.createElement('input');
            inp.type = 'text';
            if(!empty) {
                inp.name = fields[i]['name']+'['+fields[i]['id']+']';
                inp.value = fields[i]['value'];
            } else {
                inp.name = 'new'+fields[i]['name']+'[]';
            }

            inp.style.width = fields[i]['size'];
            inp.style.marginRight = '20px';

            if(empty && fields[i]['main']) {
                inp.onkeyup = function() {
                    if(this.value.trim() !== '') {
                        tableList(table_id, fields, true);
                        this.onkeyup = null;
                    }
                };
            }

            td.appendChild(inp);
        } else if(fields[i]['type'] === 'select') {
            var sel = document.createElement('select');
            if(!empty) {
                sel.name = fields[i]['name']+'['+fields[i]['id']+']';
            } else {
                sel.name = 'new'+fields[i]['name']+'[]';
            }
            var opt;
            var selected = false;
            for(var j=0; j<fields[i]['list'].length; j++) {
                opt = document.createElement('option');
                opt.innerHTML = fields[i]['list'][j]['value'];
                opt.value = fields[i]['list'][j]['id'];
                if(fields[i]['list'][j]['id']==fields[i]['value']) {
                    opt.selected = 'selected';
                    selected = true;
                }
                sel.appendChild(opt);
            }
            if(!selected) {
                sel.selectedIndex = '-1';
            }
            td.appendChild(sel);
        } else if(fields[i]['type'] === 'number') {
            td.innerHTML = tableListNumber++;
        } else if(fields[i]['type'] === 'buttons') {
            var button = document.createElement('img');
            button.src = '/style/up.png';
            button.onclick = function() {
                var trtomove = this.parentNode.parentNode;
                if(trtomove.previousSibling === null ||
                   trtomove.previousSibling.className !== 'movabletr') {
                    return;
                }
                var trbefore = trtomove.previousSibling;
                trtomove.parentNode.insertBefore(trtomove, trbefore);
                var n = 1;
                for(var i = 1; i < trtomove.parentNode.childNodes.length; i++) {
                    if(trtomove.parentNode.childNodes[i].className === 'movabletr') {
                        trtomove.parentNode.childNodes[i].childNodes[0].innerHTML = n++;
                    }
                }
            };
            td.appendChild(button);
            button = document.createElement('img');
            button.src = '/style/down.png';
            button.onclick = function() {
                var trtomove = this.parentNode.parentNode;
                if(trtomove.nextSibling === null ||
                   trtomove.nextSibling.className !== 'movabletr') {
                    return;
                }
                if(trtomove.nextSibling.nextSibling !== null &&
                   trtomove.nextSibling.nextSibling.className === 'movabletr') {
                    var trbefore = trtomove.nextSibling.nextSibling;
                    trtomove.parentNode.insertBefore(trtomove, trbefore);
                } else {
                    // no sense to put after empty list item supposed to add
                }
                var n = 1;
                for(var i = 1; i < trtomove.parentNode.childNodes.length; i++) {
                    if(trtomove.parentNode.childNodes[i].className === 'movabletr') {
                        trtomove.parentNode.childNodes[i].childNodes[0].innerHTML = n++;
                    }
                }
            };
            td.appendChild(button);
            button = document.createElement('img');
            button.src = '/style/cross.png';
            button.onclick = function() {
                var trtoremove = this.parentNode.parentNode;
                trtoremove.parentNode.removeChild(trtoremove);
                var n = 1;
                for(var i = 1; i < trtoremove.parentNode.childNodes.length; i++) {
                    if(trtoremove.parentNode.childNodes[i].className === 'movabletr') {
                        trtoremove.parentNode.childNodes[i].childNodes[0].innerHTML = n++;
                    }
                }
            };
            td.appendChild(button);
            // todo after save button was pressed
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = fields[i]['name']+'['+fields[i]['id']+']';
            //hidden.value = td.parentNode.childNodes[0].innerHTML;
            td.appendChild(hidden);
        }


        tr.appendChild(td);
    }
    table.appendChild(tr);
}

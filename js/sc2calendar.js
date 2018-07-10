function sc2Calendar(curdate, showTime) {
  this.curdate = curdate;
  this.showTime = showTime;
  this.monthCalendar = null;
  this.selectedDate = 0;
  this.selectedColor = '#e9e9e9';
  this.monthTable = null;
  this.clickFunction = null;
  this.selectedMarker = document.createElement('div');
  this.selectedMarker.className = 'selectedMarker';
}

sc2Calendar.prototype.showMonthCalendar = function(clickFunction) {
  this.monthCalendar = document.createElement('div');
  this.monthCalendar.className = 'tables';
  this.clickFunction = clickFunction;
  this.monthCalendar.appendChild(this.month(true, clickFunction));
  this.monthCalendar.appendChild(this.clearFloat());
  return this.monthCalendar;
};

sc2Calendar.prototype.showMonthCalendarWithTime = function(clickFunction) {
  this.monthCalendar = document.createElement('div');
  this.monthCalendar.className = 'tables';
  this.monthCalendar.appendChild(this.month(true, clickFunction));
  this.monthCalendar.appendChild(this.clock());
  this.monthCalendar.appendChild(this.clearFloat());

  return this.monthCalendar;
}

sc2Calendar.prototype.clearFloat = function() {
  var br = document.createElement('br');
  br.style.clear = 'both';
  return br;
};

sc2Calendar.prototype.selectedTime = function () {
  var time = new Date(1970,0,1,
                      document.getElementById('sc2CalendarHours').value,
                      document.getElementById('sc2CalendarMinutes').value,
                      0);
  return this.selectedDate + time.getTime();
}

sc2Calendar.prototype.clock = function () {
  var table = document.createElement('table');
  var tr = document.createElement('tr');
  var td = document.createElement('td');
  td.align = 'center';
  td.style.textAlign = 'center';
  var div = document.createElement('div');
  div.innerHTML = '&uarr;';
  div.className = 'monthbuttons';
  var parentClass = this;
  div.onclick = function() {
    var obj = document.getElementById('sc2CalendarHours');
    obj.value = isNaN(obj.value) ? 0 : obj.value;
    if(obj.value == 23) {
      obj.value = 0;
    } else {
      obj.value = 1 + (+obj.value);
    }
  };
  td.appendChild(div);
  tr.appendChild(td);
  td = document.createElement('td');
  td.align = 'center';
  td.style.textAlign = 'center';
  div = document.createElement('div');
  div.innerHTML = '&uarr;';
  div.className = 'monthbuttons';
  div.onclick = function() {
    var obj = document.getElementById('sc2CalendarMinutes');
    obj.value = isNaN(obj.value) ? 0 : obj.value;
    if(obj.value == 59) {
      obj.value = 0;
    } else {
      obj.value = 1 + (+obj.value);
      obj.value = ("0"+obj.value).slice(-2);
    }
  };
  td.appendChild(div);
  tr.appendChild(td);
  table.appendChild(tr);
  tr = document.createElement('tr');
  td = document.createElement('td');
  var input = document.createElement('input');
  input.type = 'text';
  input.width = 2;
  input.size = 2;
  input.id = 'sc2CalendarHours';
  input.value = this.curdate.getHours();
  td.appendChild(input);
  tr.appendChild(td);

  td = document.createElement('td');
  input = document.createElement('input');
  input.type = 'text';
  input.width = 2;
  input.size = 2;
  input.id = 'sc2CalendarMinutes';
  var date = new Date();
  input.value = ("0"+this.curdate.getMinutes()).slice(-2);
  td.appendChild(input);
  td.appendChild(input);

  tr.appendChild(td);
  table.appendChild(tr);
  tr = document.createElement('tr');
  td = document.createElement('td');
  td.align = 'center';
  td.style.textAlign = 'center';
  div = document.createElement('div');
  div.innerHTML = '&darr;';
  div.className = 'monthbuttons';
  div.onclick = function() {
    var obj = document.getElementById('sc2CalendarHours');
    obj.value = isNaN(obj.value) ? 0 : obj.value;
    if(obj.value == 0) {
      obj.value = 23;
    } else {
      obj.value = (+obj.value) - 1;
    }
  };
  td.appendChild(div);
  tr.appendChild(td);
  td = document.createElement('td');
  td.align = 'center';
  td.style.textAlign = 'center';
  div = document.createElement('div');
  div.innerHTML = '&darr;';
  div.className = 'monthbuttons';
  div.onclick = function() {
    var obj = document.getElementById('sc2CalendarMinutes');
    obj.value = isNaN(obj.value) ? 0 : obj.value;
    if(obj.value == 0) {
      obj.value = 59;
    } else {
      obj.value = (+obj.value) - 1;
      obj.value = ("0"+obj.value).slice(-2);
    }
  };
  td.appendChild(div);
  tr.appendChild(td);
  table.appendChild(tr);
  return table;
};

sc2Calendar.prototype.month = function (nextPrev, clickFunction) {
  var offs = [];
  var vacs = [];
  var date = new Date(this.curdate.getFullYear(), this.curdate.getMonth(), 1);

  var table = document.createElement('table');
  var caption = document.createElement('caption');
  if(nextPrev) {
    var div = document.createElement('div');
    div.innerHTML = '<<';
    div.className = 'monthbuttons';
    div.style.float = 'left';
    var parentClass = this;
    div.onclick = function() {
      parentClass.curdate.setMonth(parentClass.curdate.getMonth()-1);
      parentClass.monthCalendar.replaceChild(parentClass.month(true, clickFunction), parentClass.monthCalendar.children[0]);
    };
    caption.appendChild(div);
  }

  if(date.getMonth()===0) { caption.appendChild(document.createTextNode('январь')); }
  else if(date.getMonth()===1) { caption.appendChild(document.createTextNode('февраль')); }
  else if(date.getMonth()===2) { caption.appendChild(document.createTextNode('март')); }
  else if(date.getMonth()===3) { caption.appendChild(document.createTextNode('апрель')); }
  else if(date.getMonth()===4) { caption.appendChild(document.createTextNode('май')); }
  else if(date.getMonth()===5) { caption.appendChild(document.createTextNode('июнь')); }
  else if(date.getMonth()===6) { caption.appendChild(document.createTextNode('июль')); }
  else if(date.getMonth()===7) { caption.appendChild(document.createTextNode('август')); }
  else if(date.getMonth()===8) { caption.appendChild(document.createTextNode('сентябрь')); }
  else if(date.getMonth()===9) { caption.appendChild(document.createTextNode('октябрь')); }
  else if(date.getMonth()===10) { caption.appendChild(document.createTextNode('ноябрь')); }
  else if(date.getMonth()===11) { caption.appendChild(document.createTextNode('декабрь')); }

  caption.appendChild(document.createTextNode(' '));
  var divyear = document.createElement('div');
  divyear.innerHTML = date.getFullYear();
  divyear.className = 'monthbuttons';
  divyear.style.width = 'auto';
  var self = this;
  divyear.onclick = function() {
    self.year();
  }
  caption.appendChild(divyear);

  if(nextPrev) {
    var div = document.createElement('div');
    div.innerHTML = '>>';
    div.className = 'monthbuttons';
    div.style.float = 'right';
    var parentClass = this;
    div.onclick = function() {
      parentClass.curdate.setMonth(parentClass.curdate.getMonth()+1);
      parentClass.monthCalendar.replaceChild(parentClass.month(true, clickFunction), parentClass.monthCalendar.children[0]);
    };
    caption.appendChild(div);
  }

  table.appendChild(caption);

  var tr = document.createElement('tr');
  var th = document.createElement('th');
  th.innerHTML = 'ПН';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'ВТ';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'СР';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'ЧТ';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'ПТ';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'СБ';
  tr.appendChild(th);
  th = document.createElement('th');
  th.innerHTML = 'ВС';
  tr.appendChild(th);
  table.appendChild(tr);

  var curmonth = date.getMonth();
  var curyear = date.getFullYear();

  // till monday
  while(date.getDay() !== 1) {
      date.setDate(date.getDate() - 1);
  }

  // month cycle
  while((curmonth-date.getMonth()) == 1 || (curmonth-date.getMonth()) == 0 ||
        (date.getMonth()===11 && curmonth===0) ) {
      tr = document.createElement('tr');
      for(var i = 0; i < 7; i++) {
          var td = document.createElement('td');
          td.innerHTML = date.getMonth() === curmonth ? date.getDate() : '';
          var span = document.createElement('span');
          span.style.display = 'none';
          span.innerHTML = date.getMonth() === curmonth ? (date.getTime()/1000) : '';
          td.appendChild(span);
          span = document.createElement('span');
          span.style.display = 'none';
          span.innerHTML = 'work';

          if(date.getMonth() === curmonth && offs.indexOf(date.getTime()/1000) >= 0) {
              td.style.backgroundColor = '#ffb0a1';
              span.innerHTML = 'off';
          }

          if( date.getMonth() === curmonth && vacs.indexOf(date.getTime()/1000) >= 0) {
              td.style.backgroundColor = '#d2fc79';
              span.innerHTML = 'vac';
          }

          if(date.getDay() == 0) {
              td.style.backgroundColor = '#ffb0a1';
              span.innerHTML = 'off';
          }

          if(date.getDate() == this.curdate.getDate() &&
             date.getMonth() == this.curdate.getMonth() &&
             date.getFullYear() == this.curdate.getFullYear()) {
               td.appendChild(this.selectedMarker);
          }

          td.appendChild(span);

          var pnt = this;
          td.onclick = function() {
            this.appendChild(pnt.selectedMarker);
            pnt.selectedDate = this.children[0].innerHTML*1000;
            if(clickFunction) {
              clickFunction();
            }
          }

          tr.appendChild(td);
          date.setDate(date.getDate() + 1);
      }
      table.appendChild(tr);
  }

  this.monthTable = table;

  return table;
};

sc2Calendar.prototype.backCalendar = function(year) {
  this.curdate = new Date(year, this.curdate.getMonth(), 1);
  this.monthCalendar.replaceChild(this.month(true, this.clickFunction),
      this.monthCalendar.firstChild);
}

sc2Calendar.prototype.year = function () {
  this.monthCalendar.removeChild(this.monthCalendar.firstChild);

  var table = document.createElement('table');
  var caption = document.createElement('caption');
  caption.innerHTML = 'Выберите год';
  table.appendChild(caption);
  var i, j, tr, td;
  var self = this;
  var year = this.curdate.getFullYear()-24;
  for(i = 0; i < 7; i++) {
    tr = document.createElement('tr');
    for(j = 0; j < 7; j++) {
      td = document.createElement('td');
      td.style.textAlign = 'center';
      td.innerHTML = year++;
      td.onclick = function() {
        self.backCalendar(this.innerHTML);
      }
      tr.appendChild(td);
    }
    table.appendChild(tr);
  }

  this.monthCalendar.insertBefore(table, this.monthCalendar.firstChild);

};

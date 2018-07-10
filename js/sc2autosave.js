function sc2Autosave(timer_id, table, field, where, cmp_val, animate_id, where2, cmp_val2, allowdelete, noevent) {
  this.timer_id = timer_id;
  this.timer = null;
  this.table = table;
  this.field = field;
  this.where = where;
  this.cmp_val = cmp_val;
  this.animate_id = animate_id;
  this.where2 = where2;
  this.cmp_val2 = cmp_val2
  this.allowdelete = 0;
  if(allowdelete) {
    this.allowdelete = allowdelete;
  }

  if(animate_id) {
    this.animate_id = document.getElementById(animate_id);
    this.load_img = document.createElement('img');
    this.load_img.src = '/style/loading.gif';
    this.load_img.width = '15';
    this.load_img.style.verticalAlign = 'middle';
  }

  if(noevent==undefined) {
    var self = this;
    document.getElementById(timer_id).addEventListener('keyup', function() {
      self.save(this.value, 5000);
    });
    document.getElementById(timer_id).addEventListener('onblur', function() {
      self.saveNow(this.value);
    });
  }
}

sc2Autosave.prototype.animateStart = function() {
  if(this.animate_id.firstChild) {
    this.animate_id.removeChild(this.animate_id.firstChild);
  }
  this.animate_id.appendChild(this.load_img);
}

sc2Autosave.prototype.animateEnd = function() {
  if(this.animate_id.firstChild) {
    this.animate_id.removeChild(this.animate_id.firstChild);
  }
}

sc2Autosave.prototype.save = function (val, period) {
  if(this.animate_id && this.timer == null) {
    this.animateStart();
  }

  if(this.timer != null) {
    clearTimeout(this.timer);
  }
  var self = this;
  this.timer = setTimeout(function() {
    SMPAjaxPost('/autosave',
                'table='+self.table+'&'+
                'field='+self.field+'&'+
                'where='+self.where+'&'+
                'cmp_val='+self.cmp_val+'&'+
                'where2='+self.where2+'&'+
                'cmp_val2='+self.cmp_val2+'&'+
                'allowdelete='+self.allowdelete+'&'+
                'val='+encodeURIComponent(val),
                function(res) {

      if(!res || res.documentElement.firstChild.nodeValue != 'ok') {
        var popup = new sc2Popup();
        popup.showMessage('Ошибка при сохранении', 'Возникла ошибка связи с сервером при сохранении данных. Проверьте соединение с интернетом. Чтобы избежать потерю несохраненных данных не закрывайте приложение и не совершайте переходы на другие страницы до подтверждения сохранения', 'Закрыть');
        self.save(val, 10000);
      } else {
        clearTimeout(self.timer);
        self.timer = null;
        if(self.animate_id) {
          self.animateEnd();
        }
      }
    }, true);
  }, period);
};

sc2Autosave.prototype.saveNow = function (val) {
  this.save(val, 0);
};

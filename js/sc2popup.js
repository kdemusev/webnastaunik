function sc2Popup() {
  this.bg = document.getElementById('darker_id');
  this.wnd = document.getElementById('popup_id');
  this.cancel = document.getElementById('popup_cancel_id');
  this.ok = document.getElementById('popup_ok_id');
  this.hdr = document.getElementById('popup_header_id');
  this.txt = document.getElementById('popup_text_id');
}

sc2Popup.prototype.showMessage = function (hdr, msg, cancelBtn, okBtn, okFunc, cancelFunc) {
  this.showModal(hdr, document.createTextNode(msg), cancelBtn, okBtn, okFunc, cancelFunc);
  this.wnd.style.top = '25%';
  this.wnd.style.bottom = '25%';
};

sc2Popup.prototype.showWaiting = function (hdr) {
  this.wnd.style.top = '25%';
  this.wnd.style.bottom = '25%';
  this.bg.style.display = 'block';
  this.wnd.style.display = 'block';
  this.hdr.innerHTML = hdr;
  this.txt.innerHTML = '';
  this.cancel.style.display = 'none';
  this.ok.style.display = 'none';
};

sc2Popup.prototype.hideWaiting = function () {
  this.bg.style.display = 'none';
  this.wnd.style.display = 'none';
};

sc2Popup.prototype.showModal = function (hdr, obj, cancelBtn, okBtn, okFunc, cancelFunc) {
  this.wnd.style.top = '10%';
  this.wnd.style.bottom = '10%';

  this.bg.style.display = 'block';
  this.wnd.style.display = 'block';

  this.hdr.innerHTML = hdr;
  this.txt.innerHTML = '';
  this.txt.appendChild(obj);
  this.cancel.style.display = 'inline-block';
  this.cancel.innerHTML = cancelBtn;
  var pnt = this;
  this.cancel.onclick = function() {
      pnt.bg.style.display = 'none';
      pnt.wnd.style.display = 'none';
      if(cancelFunc) {
        cancelFunc();
      }
  };

  if(okBtn) {
    this.ok.style.display = 'inline-block';
    this.ok.innerHTML = okBtn;
    var pnt = this;
    this.ok.onclick = function() {
      pnt.bg.style.display = 'none';
      pnt.wnd.style.display = 'none';
      okFunc(pnt);
    }
  } else {
      this.ok.style.display = 'none';
  }
};

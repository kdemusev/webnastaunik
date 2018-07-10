function sc2Print() {
  var objbody = document.body.innerHTML;
  var els = document.getElementsByClassName('printable');

  var i = 0;
  var l = els.length;
  for(i = 0; i < l; i++) {
    els[i].style.display = 'block';
  }

  var objtoprint = document.getElementById('toprint').innerHTML;

  document.body.innerHTML = objtoprint;

  window.print();

  document.body.innerHTML = objbody;
}

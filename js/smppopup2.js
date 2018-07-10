function SMPPopup(header, message, button) {
    var thisbackground = 'darker_id';
    var thiswindow = 'popup_id';
    var thisbutton = 'popup_button_id';
    var thisheader = 'popup_header_id';
    var thistext = 'popup_text_id';

    document.getElementById(thisbackground).style.display = 'block';
    document.getElementById(thiswindow).style.display = 'block';
    document.getElementById(thiswindow).style.top = '25%';
    document.getElementById(thiswindow).style.bottom = '25%';

    document.getElementById(thisheader).innerHTML = header;
    document.getElementById(thistext).innerHTML = message;
    document.getElementById(thisbutton).innerHTML = button;
    document.getElementById(thisbutton).onclick = function() {
        document.getElementById(thisbackground).style.display = 'none';
        document.getElementById(thiswindow).style.display = 'none';
    };
}

function SMPPopupObject(header, object, button, button2, button2Function) {
  var thisbackground = 'darker_id';
  var thiswindow = 'popup_id';
  var thisbutton = 'popup_button_id';
  var thisheader = 'popup_header_id';
  var thistext = 'popup_text_id';

  document.getElementById(thisbackground).style.display = 'block';
  document.getElementById(thiswindow).style.display = 'block';
  document.getElementById(thiswindow).style.top = '10%';
  document.getElementById(thiswindow).style.bottom = '10%';

  document.getElementById(thisheader).innerHTML = header;
  document.getElementById(thistext).innerHTML = '';
  document.getElementById(thistext).appendChild(object);
  document.getElementById(thisbutton).innerHTML = button;
  document.getElementById(thisbutton).onclick = function() {
      document.getElementById(thisbackground).style.display = 'none';
      document.getElementById(thiswindow).style.display = 'none';
  };
}

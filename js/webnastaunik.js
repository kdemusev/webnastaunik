function onmessage(event) {
  var data = event.data.split('|');

  if(data[0]=='webnastaunik_iframe_onload') {
    webnastaunik_iframe_onload('iframe_quiz', data[1]);
  }
}

function webnastaunik_iframe_onload(obj_name, px) {
  var obj = document.getElementById(obj_name);
  obj.style.height=px;

  // styleMe
  var linkrels = document.getElementsByTagName('link');

  for (var i = 0, max = linkrels.length; i < max; i++) {
    if (linkrels[i].rel && linkrels[i].rel == 'stylesheet') {
//      obj.contentWindow.postMessage('styleme|'+linkrels[i].href, '*');
    }
  }
}

window.addEventListener("message", onmessage);

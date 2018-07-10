// replace form elements with ownerdrawn
function sc2Form() {
  this.radioClassName = 'button';
  this.radioSelectedClassName = 'selectedButton';
}

// label is placed !right after element
sc2Form.prototype.radioButton = function (className) {
  var elems = document.getElementsByClassName(className);
  for(var i = 0; i < elems.length; i++) {
    var el = elems[i];
    var parent = el.parentNode;
    //el.style.display = 'none';
    var replace = document.createElement('div');
    replace.setAttribute('data-name', el.getAttribute('name'));
    replace.setAttribute('data-value', el.getAttribute('value'));
    replace.innerHTML = el.nextSibling.innerHTML;
    el.nextSibling.style.display = 'none';
    if(el.getAttribute('checked')) {
      replace.className = this.radioSelectedClassName;
    } else {
      replace.className = this.radioClassName;
    }
    var parentClass = this;
    replace.onclick = function() {
      el.checked = 'checked';
      this.className = parentClass.radioSelectedClassName;
      this.
    }
    parent.appendChild(replace);
  }
};

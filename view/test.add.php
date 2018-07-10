<div class="mainframe">
  <div class="subheader">Создание теста</div>

  <form class="transpform">
    <input type="button" value="Вернуться к списку тестов" onclick="window.location='/test/showlist'" />
  </form>

  <form class="transpform" method="post" action="/test/add" >
    <label>Название тестирования:</label>
    <input type="text" name="tsname" />
    <label>Пояснения к тесту:</label>
    <textarea name="tsdesc"></textarea>
    <label>Код доступа:</label>
    <input type="text" name="tscode" />
    <label>Количество вопросов (0 - неограниченно):</label>
    <input type="text" name="tsqnum" value="0" />
  <!--<p>Для задания типа "Соответствие" соответствующие варианты ответов записываются через символ &quot;|&quot;. Например вариант задания на составления соответствия названиям и обозначениям цифр будет записан как &quot;1|один&quot;</p>-->
    <table class="admintable" >
      <thead>
      <tr>
        <th>№</th>
        <th width="100%">Задание</th>
        <th>Тип</th>
        <th>Варианты ответов</th>
        <th></th>
      </tr>
      </thead>
      <tbody id="statementstable">

      </tbody>
    </table>

    <input type="submit" name="save" value="Сохранить" />
  </form>


</div>

<script>

function addRow(tableID) {
  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var cell1 = row.insertCell(0);
  cell1.vAlign = "top";
  cell1.innerHTML = rowCount + 1;

  var cell2 = row.insertCell(1);
  cell2.style.position = "relative";
  cell2.vAlign = "top";
  var element1 = document.createElement("input");
  element1.type = "text";
  element1.name = "task[]";
  element1.style.width = "100%";
  //element1.onfocus = function() { me(element1); };
  //element1.onblur = function() { unme(); };
  element1.onkeypress = function() { addLine(element1); };
  cell2.appendChild(element1);

  var cell3 = row.insertCell(2);
  cell3.vAlign = "top";
  var element2 = document.createElement("select");
  element2.name = "type[]";
  var subelement = document.createElement("option");
  subelement.value = "1";
  subelement.appendChild(document.createTextNode("Одиночный"));
  element2.appendChild(subelement);
  subelement = document.createElement("option");
  subelement.value = "2";
  subelement.appendChild(document.createTextNode("Множественный"));
  element2.appendChild(subelement);
  /*subelement = document.createElement("option");
  subelement.value = "3";
  subelement.appendChild(document.createTextNode("Порядок"));
  element2.appendChild(subelement);
  subelement = document.createElement("option");
  subelement.value = "4";
  subelement.appendChild(document.createTextNode("Соответствие"));
  element2.appendChild(subelement);
  subelement = document.createElement("option");
  subelement.value = "5";
  subelement.appendChild(document.createTextNode("Свободный"));
  element2.appendChild(subelement);*/
  cell3.appendChild(element2);

  var cell4 = row.insertCell(3);
  var element3 = document.createElement("input");
  element3.type = "text";
  element3.name = "vars"+(rowCount+1)+"[0]";
  element3.size = "25";
  //element3.onfocus = function() { me(element1); };
  //element3.onblur = function() { unme(); };
  element3.onkeypress = function() { addVar(element3); };
  cell4.appendChild(element3);

  var cell5 = row.insertCell(4);
  cell5.vAlign = "top";
  cell5.align = "center";
  var element4 = document.createElement("input");
  element4.type = "checkbox";
  element4.name = "trues"+(rowCount+1)+"[0]";
  element4.style.marginTop = "6px";
  element4.style.marginBottom = "5px";
  cell5.appendChild(element4);
}
function addLine(obj)	{
  obj.onkeypress = "";
  addRow('statementstable');
}
function addVar(obj) {
  obj.onkeypress = "";
  var num = obj.parentNode.parentNode.cells[0].innerHTML;
  var element = document.createElement("br");
  obj.parentNode.appendChild(element);
  element = document.createElement("input");
  element.type = "text";
  var curnum = obj.parentNode.childNodes.length/2;
  element.name = "vars"+num+"["+curnum+"]";
  element.size = "25";
  //element.onfocus = function() { me(element); };
  //element.onblur = function() { unme(); };
  element.onkeypress = function() { addVar(element); };
  obj.parentNode.appendChild(element);

  var element4 = document.createElement("br");
  obj.parentNode.parentNode.cells[4].appendChild(element4);
  element4 = document.createElement("input");
  element4.type = "checkbox";
  element4.name = "trues"+num+"["+curnum+"]";
  element4.style.marginTop = "6px";
  element4.style.marginBottom = "5px";
  obj.parentNode.parentNode.cells[4].appendChild(element4);
}

addRow('statementstable');
</script>

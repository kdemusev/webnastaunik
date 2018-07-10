<div class="mainframe">

    <div class="subheader">Составление расписания</div>

    <?php CTemplates::showMessage('updated', 'Изменения сохранены'); ?>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Смена', 'daypart_id', $_dayparts, $daypart_id, 'dpname', array('form_id' => $form_id)); ?>

    <h2 style="color: rgb(184, 3, 19);">Составление расписания для второй смены и с делением предмета на три подгруппы находится в тестовой эксплуатации. О выявленных ошибках, пожалуйста, сообщайте в обратную связь</h2>

    <form class="transpform" action="/timetable/compose" method="post" name="ttform">
        <div class="timetableleft">
            <label>Нераспределенные предметы:</label>
            <div class="subjectstodrag" id="subjectsListId"></div>
        </div>
        <div class="tables timetableright" id="timetableId">
            <label>Расписание:</label><br/>
        </div>
        <input type="hidden" name="form_id" value="<?=$form_id?>" />
        <input type="hidden" name="save" value="1" />
        <div id="timetable_result"></div>
        <br style="clear: both">
        <input type="button" onclick="save_timetable();" value="Сохранить" />
    </form>

    <br style="clear: both">
    <br>
    <form class="transpform">
      <input type="button" onclick="on_clean_timetable();" value="Очистить все расписание" />
      <input type="button" onclick="on_approve_timetable();" value="Утвердить расписание на начало полугодия" />
    </form>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/jquery.ui.touch-punch.min.js"></script>
<script>

    var tchinfo = [];   // tchinfo[id] = tcname;
    var sbjinfo = [];   // sbjinfo[id] = {sbname:, sbrating:, teacher_id:, tcname:};
    var subjfree = [];  // subjfree[n] = {id:, teacher_id:, subject_id:, sbhours:}
    var timetable = []; // timetable[n] = {subject_id:, day:, numberinday:}
    var teacherstt = []; // teacherstt[n] = {id:, teacher_id:, fmname:, day:, numberinday:};
    var teacherstt_remove = []; // teacherstt_remove[n] = {ids}
    var ttghosts = []; // for ghost timetable objects
    var g_changes = false;
    var daypart = 1;

    function init() {
      var iReal;    // for subject numbering in table (for daypart 2)
      var key, i;
        // fill in arrays
        <?php foreach($_teachers as $rec) { ?>
            tchinfo[<?=$rec['id']?>] = '<?=$rec['tcname']?>';
        <?php } ?>
        <?php foreach($_subjects as $rec) { ?>
            sbjinfo[<?=$rec['id']?>] = {sbname:'<?=$rec['sbname']?>',
                                        sbrating:'<?=$rec['sbrating']?>',
                                        teacher_id:<?=$rec['teacher_id']?>,
                                        tcname:'<?=$rec['tcname']?>',
                                        sbhours:<?=$rec['sbhours']?>};
        <?php } ?>
        <?php foreach($_timetable as $rec) { ?>
            timetable.push({subject_id:<?=$rec['subject_id']?>,
                           day:<?=$rec['ttday']?>,
                           num:'<?=$rec['ttnumber']?>'});
        <?php } ?>
        <?php foreach($_teacherstt as $rec) { ?>
            teacherstt.push({id:<?=$rec['id']?>,
                            day:<?=$rec['ttday']?>,
                            num:<?=$rec['ttnumber']?>,
                            teacher_id:<?=$rec['teacher_id']?>,
                            fmname:'<?=$rec['fmname']?>'});
        <?php } ?>
        <?php if($daypart_id == 2) { ?>
          daypart = 2;
        <?php } ?>

        // fill every subject in the left div
        var oSubjectsList = document.getElementById('subjectsListId');
        for(key in sbjinfo) {
          for(i=0; i<sbjinfo[key]['sbhours']; i++) {
            var div = document.createElement('div');
            div.style.backgroundColor = makeColorFromStr(sbjinfo[key]['sbname']);
            div.className = "subjectbutton";
            div.setAttribute('data-id', key);
            var divsubj = document.createElement('p');
            divsubj.className = 'subject';
            divsubj.innerHTML = sbjinfo[key]['sbname'];
            div.appendChild(divsubj);
            var divtch = document.createElement('p');
            divtch.className = 'teacher';
            var tchname = sbjinfo[key]['tcname'].split(' ');
            if(tchname.length > 2) {
              divtch.innerHTML = tchname[0]+' '+tchname[1][0]+'.'+tchname[2][0]+'.';
            } else {
              divtch.innerHTML = tchname[0];
            }
            div.appendChild(divtch);
            oSubjectsList.appendChild(div);
          }
        }

        // create timetable
        for(var d = 0; d < 6; d++) {
            var tbl = document.createElement('table');
            tbl.className = 'shedule';
            var tr = document.createElement('tr');
            var td = document.createElement('th');
            td.colSpan = '3';
            if(d==0) { td.innerHTML = 'Понедельник'; }
            else if(d==1) { td.innerHTML = 'Вторник'; }
            else if(d==2) { td.innerHTML = 'Среда'; }
            else if(d==3) { td.innerHTML = 'Четверг'; }
            else if(d==4) { td.innerHTML = 'Пятница'; }
            else if(d==5) { td.innerHTML = 'Суббота'; }
            tr.appendChild(td);
            tbl.appendChild(tr);
            iReal = 0;
            for(var i=(daypart == 1 ? 0 : 5); i<(daypart == 1 ? 7 : 12); i++) {
                tr = document.createElement('tr');
                td = document.createElement('td');
                td.className = 'number';
                if(daypart==2) {
                  td.innerHTML = (iReal++)+'<br>('+(i+1)+')';
                } else {
                  td.innerHTML = i+1;
                }
                tr.appendChild(td);
                td = document.createElement('td');
                td.className = 'subjectstodrag';
                td.setAttribute('data-day', d);
                td.setAttribute('data-num', i);
                td.setAttribute('data-rating1', '');
                td.setAttribute('data-rating2', '');
                td.setAttribute('data-rating3', '');
                td.id = 'tt_'+d+'_'+i;
                tr.appendChild(td);
                td = document.createElement('td');
                td.className = 'hours';
                tr.appendChild(td);
                tbl.appendChild(tr);
            }
            tr = document.createElement('tr');
            td = document.createElement('th');
            tr.appendChild(td);
            td = document.createElement('th');
            tr.appendChild(td);
            td = document.createElement('th');
            td.id = 'dayhours_' + d;
            td.innerHTML = '&nbsp;';
            tr.appendChild(td);
            tbl.appendChild(tr);

            id('timetableId').appendChild(tbl);
            if(d==2) {
                var br = document.createElement('br');
                br.style.clear = 'both';
                id('timetableId').appendChild(br);
            }
        }

        // fill timetable
        var l = timetable.length;
        var divs = oSubjectsList.children; // list of subjects
        var lDivs = 0;
        var objtomove;
        for(key = 0; key < l; key++) {
          // search div to move
          objtomove = null;
          lDivs = divs.length;
          for(i = 0; i < lDivs; i++) {
            if(divs[i].getAttribute('data-id') == timetable[key]['subject_id']) {
              objtomove = divs[i];
              break;
            }
          }
          if (objtomove == null) { continue; } // when timetable contains subjects which were deleted

          var tt = document.getElementById('tt_'+timetable[key]['day']+'_'+timetable[key]['num']);
          if (tt != null) {
            tt.appendChild(objtomove);
          } else {
            // TODO: display message to remove subjects from first daypart
          }
          putRating(tt);
        }

        var tObj;
        for(i = 0; i < 6; i++) {
          tObj = document.getElementById('dayhours_'+i).parentNode.parentNode;
          recalculateHours(tObj);
          recolorize(tObj);
        }

        // fill teachers ghost timetable
        for(var key in teacherstt) {   // key = teacher_id for every teacher in the form
          var tt = id('tt_'+teacherstt[key]['day']+'_'+teacherstt[key]['num']);
            if(tt == null) {
              continue;
            }
            var obj = document.createElement('p');
            obj.className = "ghost";
            var obj_teacher_id = document.createElement('p');
            obj_teacher_id.style.display = 'none';
            obj_teacher_id.innerHTML = teacherstt[key]['teacher_id'];
            obj.appendChild(obj_teacher_id);
            var obj_id = document.createElement('p');
            obj_id.style.display = 'none';
            obj_id.innerHTML = teacherstt[key]['id'];
            obj.appendChild(obj_id);
            var obj_time = document.createElement('p');
            obj_time.style.display = 'none';

            obj_time.innerHTML = 'tt_'+teacherstt[key]['day']+'_'+teacherstt[key]['num'];
            obj.appendChild(obj_time);
            var obj_text = document.createElement('p');
            obj_text.innerHTML = teacherstt[key]['fmname'];
            obj.appendChild(obj_text);
            tt.appendChild(obj);
        }
    }
// TODO ???
//1 2 3
// 9 8
//  5
//  4

//18 17

    function recalculateHours(table) {
      var hours = 0;
      var hours2 = 0;
      var hours3 = 0;
      var wasHours2 = false;
      var wasHours3 = false;
      var i;
      var tdCell;
      for (i = 1; i < 8; i++) {
        tdCell = table.children[i].children[1];
        if(tdCell.getAttribute('data-rating3') != '') {
          hours += tdCell.getAttribute('data-rating1')*1;
          hours2 += tdCell.getAttribute('data-rating2')*1;
          hours3 += tdCell.getAttribute('data-rating3')*1;
          wasHours3 = true;
        } else if(tdCell.getAttribute('data-rating2') != '') {
          hours += tdCell.getAttribute('data-rating1')*1;
          hours2 += tdCell.getAttribute('data-rating2')*1;
          wasHours2 = true;
        } else if(tdCell.getAttribute('data-rating1') != '') {
          hours += tdCell.getAttribute('data-rating1')*1;
          hours2 += tdCell.getAttribute('data-rating1')*1;
          hours3 += tdCell.getAttribute('data-rating1')*1;
        }
      }

      if(wasHours3) {
        table.children[8].children[2].innerHTML = hours + '/' + hours2 + '/' + hours3;
      } else if(wasHours2) {
        table.children[8].children[2].innerHTML = hours + '/' + hours2;
      } else {
        table.children[8].children[2].innerHTML = hours;
      }
    }

    function putRating(td, whichClear) {
      td.setAttribute('data-rating1', '');
      td.setAttribute('data-rating2', '');
      td.setAttribute('data-rating3', '');

      var rating = [];
      var subject_id;
      var ltd = td.children.length;
      var i;
      for (i = 0; i < ltd; i++) {
        if (td.children[i].className.indexOf('subjectbuttonplaceholder') < 0 &&
            td.children[i].className.indexOf('ghost') < 0 &&
            (whichClear == undefined || whichClear != td.children[i])) {
          subject_id = td.children[i].getAttribute('data-id');
          rating.push(sbjinfo[subject_id]['sbrating']);
        }
      }

      for (i = 1; i <= rating.length; i++) {
        td.setAttribute('data-rating'+i, rating[i-1]);
      }

      if(rating.length > 2) {
        td.nextSibling.innerHTML = rating[0] + '/' + rating[1] + '/' + rating[2];
      } else if(rating.length > 1) {
        td.nextSibling.innerHTML = rating[0] + '/' + rating[1];
      } else if(rating.length == 1){
        td.nextSibling.innerHTML = rating[0]
      } else {
        td.nextSibling.innerHTML = '';
      }
    }

    function recolorize(table) {
      var i;
      var lastRating = 0;
      var rating = 0;
      var tVal;
      var tCol;
      var redsLast = [];

      for(i = 1; i < table.children.length-1; i++) {
        tVal = table.children[i].children[1];
        rating = Math.max(tVal.getAttribute('data-rating1'),
                          tVal.getAttribute('data-rating2'),
                          tVal.getAttribute('data-rating3'));
        if(lastRating > 8 && rating > 8) {
          table.children[i].children[2].style.backgroundColor = 'red';
          redsLast.push(i);
        } else {
          table.children[i].children[2].style.backgroundColor = 'transparent';
        }
        lastRating = rating;
      }

      // first hard subject is just once a week
      var tables = document.getElementsByClassName('shedule');
      var was = false;
      for(i = 0; i < 6; i++) {
        tables[i].children[1].children[2].style.backgroundColor = 'transparent';
        tVal = tables[i].children[1].children[1];
        rating = Math.max(tVal.getAttribute('data-rating1'),
                          tVal.getAttribute('data-rating2'),
                          tVal.getAttribute('data-rating3'));
        if(rating > 8) {
          if(was) {
            tables[i].children[1].children[2].style.backgroundColor = 'red';
          } else {
            was = true;
          }
        }
      }

      // last hard subject is just once a week
      was = false;
      var j;
      var last;
      for(i = 0; i < 6; i++) {
        // find last index (because it can be 5th, 6th or 7th)
        last = 0;
        for(j = 7; j > 0; j--) {
          if(tables[i].children[j].children[1].getAttribute('data-rating1') != '') {
            last = j;
            break;
          }
        }
        if(last == 0) { continue; }
        if(tables[i]!=table || redsLast.indexOf(last) == -1) {
          tables[i].children[last].children[2].style.backgroundColor = 'transparent';
        }
        tVal = tables[i].children[last].children[1];
        rating = Math.max(tVal.getAttribute('data-rating1'),
                          tVal.getAttribute('data-rating2'),
                          tVal.getAttribute('data-rating3'));
        if(rating > 8) {
          if(was) {
            tables[i].children[last].children[2].style.backgroundColor = 'red';
          } else {
            was = true;
          }
        }
      }
    }

    init();

    $(".subjectstodrag").sortable({
      connectWith: ".subjectstodrag",
      placeholder: "subjectbuttonplaceholder",
      beforeStop: function(event, ui) {
        var td = ui.placeholder[0].parentNode;
        var isInShedule = td.tagName.toLowerCase() == 'td' ? true : false;
        var i;

        // hide all ghosts
        for(var key in ttghosts) {
          ttghosts[key].style.display = 'none';
        }

        // remove from teacher timetable replaced subject if exists??
        var techs = document.getElementsByClassName('ghost');
        var sbj_id = ui.item[0].getAttribute('data-id');
        var key;
        for(key = 0; key < techs.length; key++) {
            var teacher_dninfo = techs[key].childNodes[2].innerHTML.split('_');
            var teacher_id = techs[key].childNodes[0].innerHTML;
            var ttid = techs[key].childNodes[1].innerHTML;
            if(teacher_dninfo[1] == td.getAttribute('day') && teacher_dninfo[2] == td.getAttribute('num') &&
               teacher_id == sbjinfo[sbj_id]['teacher_id']) {
                teacherstt_remove.push(ttid);
                techs[key].parentNode.removeChild(techs[key]);
                break;
            }
        }
        ttghosts = [];


      },
      stop: function(event, ui) {
        g_changes = true;

        var td = ui.item[0].parentNode;
        var isInShedule = td.tagName.toLowerCase() == 'td' ? true : false;


        // if it is more then three subjects as division
        // then move them back to subjectlist (replace)
        if (isInShedule) {
          var subjectButtonCnt = 0;
          for (i = 0; i < td.children.length; i++) {
            if (td.children[i].className.indexOf('subjectbuttonplaceholder') < 0 &&
                td.children[i].className.indexOf('ghost') < 0 &&
                td.children[i] !== ui.item[0]) {
              subjectButtonCnt++;
            }
          }

          if(subjectButtonCnt > 2) {
            var tdcl = td.children.length;
            for (i = 0; i < tdcl; i++) {
              if(td.children[i] == undefined) { continue; }
              if (td.children[i].className.indexOf('subjectbuttonplaceholder') < 0 &&
                  td.children[i].className.indexOf('ghost') < 0 &&
                  td.children[i] !== ui.item[0]) {
                id('subjectsListId').appendChild(td.children[i]);
              }
            }
          }
        }

        // fill in rating
        if(isInShedule) {
          putRating(td);
          recalculateHours(td.parentNode.parentNode);
          recolorize(td.parentNode.parentNode);
        }

      },
      start: function(event, ui) {
        var td = ui.item[0].parentNode;
        var isInShedule = td.tagName.toLowerCase() == 'td' ? true : false;

        if(isInShedule) {
          putRating(td, ui.item[0]);
          recolorize(td.parentNode.parentNode);
        }

        // put ghosts
        var techs = document.getElementsByClassName('ghost');
        var key;
        for(key = 0; key < techs.length; key++) {
            var sbj_id = ui.item[0].getAttribute('data-id');
            var tch_id = techs[key].childNodes[0].innerHTML;
            if(tch_id == sbjinfo[sbj_id]['teacher_id']) {
              techs[key].style.display = 'block';
              ttghosts.push(techs[key]);
            }
        }
      },
      over: function(event, ui) {
        var td = ui.placeholder[0].parentNode;
        var isInShedule = td.tagName.toLowerCase() == 'td' ? true : false;
        var tVal;
        var tCol;

        // CHECK WHERE IS RATING NEXT OR PREVIOUS IS HIGH;
        if(isInShedule) {

          var subject_id = ui.item[0].getAttribute('data-id');
          if(sbjinfo[subject_id]['sbrating'] > 8) {
            if (td.parentNode.previousSibling &&
                td.parentNode.previousSibling.children[0].tagName.toLowerCase() == 'td') {
              tVal = Math.max(td.parentNode.previousSibling.children[1].getAttribute('data-rating1'),
                              td.parentNode.previousSibling.children[1].getAttribute('data-rating2'),
                              td.parentNode.previousSibling.children[1].getAttribute('data-rating3'));
              if(tVal > 8) {
                td.nextSibling.style.backgroundColor = 'red';
              }
            }
            if (td.parentNode.nextSibling &&
                td.parentNode.nextSibling.children[0].tagName.toLowerCase() == 'td') {
              tVal = Math.max(td.parentNode.nextSibling.children[1].getAttribute('data-rating1'),
                              td.parentNode.nextSibling.children[1].getAttribute('data-rating2'),
                              td.parentNode.nextSibling.children[1].getAttribute('data-rating3'));
              if(tVal > 8) {
                td.nextSibling.style.backgroundColor = 'red';
              }
            }
          }
        }
      },
      out: function(event, ui) {
        if(ui.placeholder[0].parentNode) {
          var td = ui.placeholder[0].parentNode;
          var isInShedule = td.tagName.toLowerCase() == 'td' ? true : false;

          if(isInShedule) {
            recolorize(td.parentNode.parentNode);
          }
        }
      }
    });

    function save_timetable() {
      var i, j, q;

      var res = id('timetable_result');
      res.innerHTML = '';
      for(var key in teacherstt_remove) {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ttremove[]';
          input.value = teacherstt_remove[key];
          res.appendChild(input);
      }

      for(i = 0; i < 6; i++) {
        for(var j=(daypart == 1 ? 0 : 5); j<(daypart == 1 ? 7 : 12); j++) {
          var cell = id('tt_'+i+'_'+j);
          for(q = 0; q < cell.children.length; q++) {
            if(cell.children[q].className.indexOf('subjectbutton') >= 0) {
              var input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'timetable['+i+']['+j+'][]';
              input.value = cell.children[q].getAttribute('data-id');
              res.appendChild(input);
            }
          }
        }
      }
    g_changes = false;
    document.forms['ttform'].submit();
    }

    window.onbeforeunload = function(e) {
      if(g_changes) {
        return "Вы не сохранили изменения в расписании"
      }
    };

    function on_clean_timetable() {
      var popup = new sc2Popup();
      popup.showMessage('Очистка расписания', 'Вы действительно хотите полностью очистить расписание? Рекомендуется делать это только перед началом нового учебного года',
                        'Нет', 'Да', function() { clean_timetable(); });
    }

    function clean_timetable() {
      window.location = '/timetable/clean';
    }

    function on_approve_timetable() {
      var popup = new sc2Popup();
      popup.showMessage('Утверждение расписания', 'Вы действительно хотите утвердить текущее расписание на начало полугодия для изменения дат календарного планирования учителей?',
                        'Нет', 'Да', function() { approve_timetable(); });
    }

    function approve_timetable() {
      window.location = '/timetable/approve';
    }
</script>

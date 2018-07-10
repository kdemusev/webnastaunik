<div class="mainframe">

    <div class="subheader">Просмотр расписания</div>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>

    <div class="transpform">
    <button onclick="onPrint();">Распечатать</button>
    </div>


    <div>
        <div id="toprint">
        <?php if($form_id == 0) { ?>
        <label>Расписание:</label><br/>
        <table class="smptable shed overable" style="width: auto; float: left; margin-bottom: 10px;">
          <tr>
            <th style="width: 20px; border-left-width: 3px; ">&nbsp;</th>
            <th style="width: 15px; border-right-width: 3px; "></th>
            <?php for($d = 0; $d < 6; $d++) { ?>
            <tr>
              <td rowspan="7" style="width: 20px; border-bottom-width: 3px; border-left-width: 3px; ">
                <div class="vertwrapper">
                  <div class="vert">
                    <?=$d==0?'понедельник':($d==1?'вторник':($d==2?'среда':($d==3?'четверг':($d==4?'пятница':'суббота'))))?>
                  </div>
                </div>
              </td>
              <?php for($i = 0; $i < 7; $i++) { ?>
                <?php if($i > 0) { ?><tr><?php } ?>
                <td align="center" style="height: 35px; width: 15px; border-right-width: 3px; <?=$i==6?'border-bottom-width: 3px;':''?>"><?=$i+1?></td>
                </tr>
              <?php } ?>
            <?php } ?>
        </table>

        <?php foreach($_ttforms as $rec) { ?>
          <table class="smptable shed overable" style="width: auto; float: left; margin-bottom: 10px;">
            <tr>
              <th style="border-right-width: 3px; "><?=$rec['fmname']?> класс</th>
            </tr>
            <?php for($d = 0; $d < 6; $d++) { ?>
            <tr>
              <?php for($i = 0; $i < 7; $i++) { ?>
                <?php if($i > 0) { ?><tr><?php } ?>
                  <?php if(isset($_timetable[$rec['id']][$d][$i])) { ?>
                    <td style="min-width: 150px; <?=$i==6?'border-bottom-width: 3px;':''?>">
                      <?php foreach($_timetable[$rec['id']][$d][$i] as $rt) { ?>
                      <div class="subjectbutton">
                        <p class="subject">
                          <?=$rt['sbname']?>
                        </p>
                        <p class="teacher">
                          <?php $tcnms = explode(' ',$rt['tcname']); print $tcnms[0].' '.substr($tcnms[1],0,2).'.'.substr($tcnms[2],0,2).'.'; ?>
                        </p>
                      </div>
                      <?php } ?>
                    </td>
                  <?php } else { ?>
                  <td style="min-width: 150px; <?=$i==6?'border-bottom-width: 3px;':''?>"><div class="subjectbutton"></div></td>
                  <!--<td style="min-width: 20px;  border-right-width: 3px; <?=$i==6?'border-bottom-width: 3px;':''?>"></td>-->
                  <?php } ?>
                </tr>
                <?php } ?>
                <?php } ?>
              </table>
              <?php } ?>
              <?php } else { ?>
                <div class="tables" id="timetableId"></div>
              <?php } ?>

      </div>
    </div>
</div>

<script>
  function onPrint() {
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

    <?php if($form_id > 0) { ?>
    var timetable = [];
    var daypart = 1;
    var iReal;
    var rating = [];

    function init() {
        <?php if(isset($_timetable) && count($_timetable) > 0) {
            foreach($_timetable as $rec) { ?>
            timetable.push({sbname:'<?=$rec['sbname']?>',
                            sbrating:'<?=$rec['sbrating']?>',
                            tcname:'<?=$rec['tcname']?>',
                            day:<?=$rec['ttday']?>,
                            num:'<?=$rec['ttnumber']?>'});
        <?php }
            } ?>

        <?php if($daypart == 2) { ?>
          daypart = 2;
        <?php } ?>

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
                td.id = 'tt_'+d+'_'+i;
                tr.appendChild(td);
                td = document.createElement('td');
                td.className = 'hours';
                tr.appendChild(td);
                tbl.appendChild(tr);
            }
            id('timetableId').appendChild(tbl);
            if(d==2) {
                var br = document.createElement('br');
                br.style.clear = 'both';
                id('timetableId').appendChild(br);
            }
        }

        // fill every subject in the left div
        for(var key = 0; key < timetable.length; key++) {
            var div = document.createElement('div');
            div.style.backgroundColor = makeColorFromStr(timetable[key]['sbname']);
            div.className = "subjectbutton";
            var divsubj = document.createElement('p');
            divsubj.className = 'subject';
            divsubj.innerHTML = timetable[key]['sbname'];
            div.appendChild(divsubj);
            var divfm = document.createElement('p');
            divfm.className = 'teacher';
            var tchname = timetable[key]['tcname'].split(' ');
            if(tchname.length > 2) {
              divfm.innerHTML = tchname[0]+' '+tchname[1][0]+'.'+tchname[2][0]+'.';
            } else {
              divfm.innerHTML = tchname[0];
            }
            div.appendChild(divfm);
            id('tt_'+timetable[key]['day']+'_'+timetable[key]['num']).appendChild(div);
            if(rating[timetable[key]['day']] == undefined) { rating[timetable[key]['day']] = []; }
            if(rating[timetable[key]['day']][timetable[key]['num']] == undefined) { rating[timetable[key]['day']][timetable[key]['num']] = []; }
            rating[timetable[key]['day']][timetable[key]['num']].push(timetable[key]['sbrating']);
        }

        for(var d = 0; d < 6; d++) {
          if(rating[d] == undefined) { continue; }
          for(var i=(daypart == 1 ? 0 : 5); i<(daypart == 1 ? 7 : 12); i++) {
            if(rating[d][i] == undefined) { continue; }
            if(rating[d][i].length > 2) {
              id('tt_'+d+'_'+i).nextSibling.innerHTML = rating[d][i][0] + '/' + rating[d][i][1] + '/' + rating[d][i][2];
            } else if(rating[d][i].length > 1) {
              id('tt_'+d+'_'+i).nextSibling.innerHTML = rating[d][i][0] + '/' + rating[d][i][1];
            } else if(rating[d][i].length == 1){
              id('tt_'+d+'_'+i).nextSibling.innerHTML = rating[d][i][0]
            }
          }
        }
    }


    init();
    <?php } ?>
</script>

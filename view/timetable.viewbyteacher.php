<div class="mainframe">

    <div class="subheader">Расписание учителя</div>

    <?php CTemplates::chooseBar('Учителя', 'teacher_id', $_teachers, $teacher_id, 'tcname'); ?>

    <div class="tables timetableright" id="timetableId">
        <label>Расписание:</label><br/>
    </div>
</div>

<script>
    var timetable = [];

    function init() {
        <?php if(isset($_timetable) && count($_timetable) > 0) {
            foreach($_timetable as $rec) { ?>
            timetable.push({sbname:'<?=$rec['sbname']?>',
                            fmname:'<?=$rec['fmname']?>',
                            day:<?=$rec['ttday']?>,
                            num:'<?=$rec['ttnumber']?>'});
        <?php }
            } ?>

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
            for(var i=<?=$minnumber?>; i<<?=$maxnumber?>; i++) {
                tr = document.createElement('tr');
                td = document.createElement('td');
                td.className = 'number';
                td.innerHTML = i+1;
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
            divfm.innerHTML = '';
            div.appendChild(divfm);
            id('tt_'+timetable[key]['day']+'_'+timetable[key]['num']).appendChild(div);
            id('tt_'+timetable[key]['day']+'_'+timetable[key]['num']).parentNode.childNodes[2].innerHTML = timetable[key]['fmname'];
        }
    }

    init();
</script>

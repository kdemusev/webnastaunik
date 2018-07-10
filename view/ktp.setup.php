<div class="mainframe">
    <div class="subheader">Настройка выходных дней и каникул</div>

    <form action="/ktp/setup" method="post" class="transpform">
        <label>Показать календарь:</label>
        с
        <select name="frommonth" style="width: auto;  display: inline-block;">
            <option value="0" <?php if($_SESSION['ktpfrommonth']==0) echo 'selected'; ?>>января</option>
            <option value="1" <?php if($_SESSION['ktpfrommonth']==1) echo 'selected'; ?>>февраля</option>
            <option value="2" <?php if($_SESSION['ktpfrommonth']==2) echo 'selected'; ?>>марта</option>
            <option value="3" <?php if($_SESSION['ktpfrommonth']==3) echo 'selected'; ?>>апреля</option>
            <option value="4" <?php if($_SESSION['ktpfrommonth']==4) echo 'selected'; ?>>мая</option>
            <option value="5" <?php if($_SESSION['ktpfrommonth']==5) echo 'selected'; ?>>июня</option>
            <option value="6" <?php if($_SESSION['ktpfrommonth']==6) echo 'selected'; ?>>июля</option>
            <option value="7" <?php if($_SESSION['ktpfrommonth']==7) echo 'selected'; ?>>августа</option>
            <option value="8" <?php if($_SESSION['ktpfrommonth']==8) echo 'selected'; ?>>сентября</option>
            <option value="9" <?php if($_SESSION['ktpfrommonth']==9) echo 'selected'; ?>>октября</option>
            <option value="10" <?php if($_SESSION['ktpfrommonth']==10) echo 'selected'; ?>>ноября</option>
            <option value="11" <?php if($_SESSION['ktpfrommonth']==11) echo 'selected'; ?>>декабря</option>
        </select>
        <select name="fromyear" style="width: auto;  display: inline-block;">
          <?php $yrstart = date("Y"); for($i = $yrstart - 1; $i < $yrstart + 2; $i++) { ?>
            <option value="<?=$i?>" <?php if($_SESSION['ktpfromyear']==$i) echo 'selected'; ?>><?=$i?></option>
          <?php } ?>
        </select>
        по
        <select name="tomonth" style="width: auto;  display: inline-block;">
            <option value="0" <?php if($_SESSION['ktptomonth']==0) echo 'selected'; ?>>январь</option>
            <option value="1" <?php if($_SESSION['ktptomonth']==1) echo 'selected'; ?>>февраль</option>
            <option value="2" <?php if($_SESSION['ktptomonth']==2) echo 'selected'; ?>>март</option>
            <option value="3" <?php if($_SESSION['ktptomonth']==3) echo 'selected'; ?>>апрель</option>
            <option value="4" <?php if($_SESSION['ktptomonth']==4) echo 'selected'; ?>>май</option>
            <option value="5" <?php if($_SESSION['ktptomonth']==5) echo 'selected'; ?>>июнь</option>
            <option value="6" <?php if($_SESSION['ktptomonth']==6) echo 'selected'; ?>>июль</option>
            <option value="7" <?php if($_SESSION['ktptomonth']==7) echo 'selected'; ?>>август</option>
            <option value="8" <?php if($_SESSION['ktptomonth']==8) echo 'selected'; ?>>сентябрь</option>
            <option value="9" <?php if($_SESSION['ktptomonth']==9) echo 'selected'; ?>>октябрь</option>
            <option value="10" <?php if($_SESSION['ktptomonth']==10) echo 'selected'; ?>>ноябрь</option>
            <option value="11" <?php if($_SESSION['ktptomonth']==11) echo 'selected'; ?>>декабрь</option>
        </select>
        <select name="toyear" style="width: auto;  display: inline-block;">
          <?php $yrend = date("Y"); for($i = $yrend; $i < $yrend + 3; $i++) { ?>
            <option value="<?=$i?>" <?php if($_SESSION['ktptoyear']==$i) echo 'selected'; ?>><?=$i?></option>
          <?php } ?>

        </select>
        <input type="submit" value="Выбрать" />
        <hr>
    </form>

    <div class="tables" id="tables">

    </div>
    <br style='clear: both' />

    <form action='/ktp/setup' method='post' class='transpform' id='submitform'>
        <input type='button' onclick='submitTable()' value='Сохранить' />
    </form>
</div>


<script>
    function submitTable() {
        var cols = document.getElementsByTagName('td')
        for(var i = 0; i < cols.length; i++) {
            var td = cols[i];
            if(td.children.length && td.children.length > 0) {
                if(td.children[1].innerHTML == 'work') { continue; }
                if(td.children[0].innerHTML.trim() == '') { continue; }
                var inp = document.createElement('input');
                inp.type = 'hidden';
                if(td.children[1].innerHTML == 'vac') {
                    inp.name = 'vac[]';
                }
                if(td.children[1].innerHTML == 'off') {
                    inp.name = 'off[]';
                }
                inp.value = td.children[0].innerHTML;
                id('submitform').appendChild(inp);
            }
        }

        id('submitform').submit();
    }

    //add to array from database
    var date = new Date(<?=$_SESSION['ktpfromyear']?> , <?=$_SESSION['ktpfrommonth']?>, 1);
    var dateend = new Date(<?=$_SESSION['ktptoyear']?>, <?=$_SESSION['ktptomonth']?>,20);
    var offs = new Array();
    var vacs = new Array();
    <?php if($_data && count($_data) > 0) {
        $io = 0;
        $iv = 0;
        foreach($_data as $rec) {
            if($rec['kdotype']==0) {
                echo "offs[$io] = {$rec['kdodate']};";
                $io++;
            } else if($rec['kdotype']==1) {
                echo "vacs[$iv] = {$rec['kdodate']};";
                $iv++;
            }
        }
    }?>

    var container = document.getElementById('tables');

    while(date < dateend) {
        var table = document.createElement('table');
        var caption = document.createElement('caption');
        if(date.getMonth()===0) { caption.innerHTML = 'январь'; }
        else if(date.getMonth()===1) { caption.innerHTML = 'февраль'; }
        else if(date.getMonth()===2) { caption.innerHTML = 'март'; }
        else if(date.getMonth()===3) { caption.innerHTML = 'апрель'; }
        else if(date.getMonth()===4) { caption.innerHTML = 'май'; }
        else if(date.getMonth()===5) { caption.innerHTML = 'июнь'; }
        else if(date.getMonth()===6) { caption.innerHTML = 'июль'; }
        else if(date.getMonth()===7) { caption.innerHTML = 'август'; }
        else if(date.getMonth()===8) { caption.innerHTML = 'сентябрь'; }
        else if(date.getMonth()===9) { caption.innerHTML = 'октябрь'; }
        else if(date.getMonth()===10) { caption.innerHTML = 'ноябрь'; }
        else if(date.getMonth()===11) { caption.innerHTML = 'декабрь'; }
        caption.innerHTML += ' '+date.getFullYear();
        table.appendChild(caption);

        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.innerHTML = 'ПН';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'ВТ';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'СР';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'ЧТ';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'ПТ';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'СБ';
        tr.appendChild(th);
        th = document.createElement('th');
        th.innerHTML = 'ВС';
        tr.appendChild(th);
        table.appendChild(tr);

        var curmonth = date.getMonth();
        var curyear = date.getFullYear();
        // till monday
        while(date.getDay() !== 1) {
            date.setDate(date.getDate() - 1);
        }
        // month cycle
        while((curmonth-date.getMonth()) == 1 || (curmonth-date.getMonth()) == 0 ||
              (date.getMonth()===11 && curmonth===0) ) {
            tr = document.createElement('tr');
            for(var i = 0; i < 7; i++) {
                var td = document.createElement('td');
                td.innerHTML = date.getMonth() === curmonth ? date.getDate() : '';
                var span = document.createElement('span');
                span.style.display = 'none';
                span.innerHTML = date.getMonth() === curmonth ? (date.getTime()/1000) : '';
                td.appendChild(span);
                span = document.createElement('span');
                span.style.display = 'none';
                span.innerHTML = 'work';

                if(date.getMonth() === curmonth && offs.indexOf(date.getTime()/1000) >= 0) {
                    td.style.backgroundColor = '#ffb0a1';
                    span.innerHTML = 'off';
                }
                if( date.getMonth() === curmonth && vacs.indexOf(date.getTime()/1000) >= 0) {
                    td.style.backgroundColor = '#d2fc79';
                    span.innerHTML = 'vac';
                }

                if(date.getDay() == 0) {
                    td.style.backgroundColor = '#ffb0a1';
                    span.innerHTML = 'off';
                }

                td.appendChild(span);
                td.onclick = function() {
                    if(this.children[1].innerHTML == 'off') {
                        this.style.backgroundColor = '#d2fc79';
                        this.children[1].innerHTML = 'vac';
                    } else if(this.children[1].innerHTML == 'vac') {
                        this.style.backgroundColor = '';
                        this.children[1].innerHTML = 'work';
                    } else {
                        this.style.backgroundColor = '#ffb0a1';
                        this.children[1].innerHTML = 'off';
                    }
                }
                tr.appendChild(td);
                date.setDate(date.getDate() + 1);
            }
            table.appendChild(tr);
        }
        while(date.getDate() !== 1) {
            date.setDate(date.getDate() - 1);
        }

        container.appendChild(table);
    }

</script>

<div class="mainframe">
    <div class="subheader">Календарь проведения занятий</div>

    <form action="/ktp/dates" method="post" class="transpform">
        с
        <select name="frommonth" style="width: auto; display: inline-block; ">
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


        <label>Занятия проводятся по:</label>
        <label><input type="checkbox" name="days[]" value="1"
                      <?php if(isset($_POST['days']) && in_array(1,$_POST['days'])) echo 'checked'; ?>/>понедельникам</label>
        <label><input type="checkbox" name="days[]" value="2"
                      <?php if(isset($_POST['days']) && in_array(2,$_POST['days'])) echo 'checked'; ?>/>вторникам</label>
        <label><input type="checkbox" name="days[]" value="3"
                      <?php if(isset($_POST['days']) && in_array(3,$_POST['days'])) echo 'checked'; ?>/>средам</label>
        <label><input type="checkbox" name="days[]" value="4"
                      <?php if(isset($_POST['days']) && in_array(4,$_POST['days'])) echo 'checked'; ?>/>четвергам</label>
        <label><input type="checkbox" name="days[]" value="5"
                      <?php if(isset($_POST['days']) && in_array(5,$_POST['days'])) echo 'checked'; ?>/>пятницам</label>
        <label><input type="checkbox" name="days[]" value="6"
                      <?php if(isset($_POST['days']) && in_array(6,$_POST['days'])) echo 'checked'; ?>/>субботам</label>
        <hr/>
        <label><input type="checkbox" name="isvac" value="1"
                      <?php if(isset($_POST['isvac']) && $_POST['isvac']==1) echo 'checked'; ?>/>в том числе на каникулах</label>
        <hr />
        <input type="submit" value="Выбрать" />
        <hr>
    </form>

    <div class="largetables">
        <table id="tables">
            <caption>Дни проведения занятий</caption>
            <tr>
                <th>№ п/п</th>
                <th>день недели</th>
                <th>дата</th>
            </tr>
        </table>
    </div>



</div>

<script>
    var date = new Date(<?=$_SESSION['ktpfromyear']?> , <?=$_SESSION['ktpfrommonth']?>, 1);
    var dateend = new Date(<?=$_SESSION['ktptoyear']?>, <?=$_SESSION['ktptomonth']?>,20);
    var offs = new Array();
    var vacs = new Array();
    <?php if(isset($_data) && count($_data) > 0) {
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
    var days = new Array();
    <?php
    if(isset($_POST['days']) && count($_POST['days'])>0) {
        $i = 0;
        foreach($_POST['days'] as $rec) {
            echo "days[$i] = $rec;";
            $i++;
        }
    }?>

    var isvac = <?php if(isset($_POST['isvac']) && $_POST['isvac']==1) { echo 'true'; } else
                { echo 'false'; } ?>;

    var table = id('tables');
    var iLessonNum = 1;
    while((date.getMonth()+date.getFullYear()*100) <= (dateend.getMonth()+dateend.getFullYear()*100)) {
        if(offs.indexOf(date.getTime()/1000)<0 && (isvac || vacs.indexOf(date.getTime()/1000)<0) &&
           days.indexOf(date.getDay()) >= 0) {
           var tr = document.createElement('tr');
           var td = document.createElement('td');
           td.style.textAlign = 'center';
           td.innerHTML = iLessonNum++;
           tr.appendChild(td);
           td = document.createElement('td');
           td.style.textAlign = 'center';
           if(date.getDay() === 1) {
               td.innerHTML = 'пн';
           } else if(date.getDay() === 2) {
               td.innerHTML = 'вт';
           } else if(date.getDay() === 3) {
               td.innerHTML = 'ср';
           } else if(date.getDay() === 4) {
               td.innerHTML = 'чт';
           } else if(date.getDay() === 5) {
               td.innerHTML = 'пт';
           } else if(date.getDay() === 6) {
               td.innerHTML = 'сб';
           }
           tr.appendChild(td);
           td = document.createElement('td');
           td.innerHTML = ("0"+date.getDate()).slice(-2) + '.' + ("0" + (date.getMonth()+1)).slice(-2) + '.' + date.getFullYear();
           tr.appendChild(td);
           table.appendChild(tr);
        }

        date.setDate(date.getDate() + 1);
    }

</script>

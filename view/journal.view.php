<div class="mainframe">

    <div class="subheader">Журнал</div>

    <?php CTemplates::chooseBar('Классы', 'form_id', $_forms, $form_id, 'fmname'); ?>
    <?php CTemplates::chooseBar('Предметы', 'subject_id', $_subjects, $subject_id, 'sbname'); ?>

    <form action="/journal/save" method="post" name="marksform" class="transpform">
      <input type="hidden" name="form_id" value="<?=$form_id?>" />
      <input type="hidden" name="subject_id" value="<?=$subject_id?>" />

      <input type="button" name="save" value="Сохранить" onclick="marksSave();" />
    </form>

    <div class="journal" id="journal-id"></div>

</div>

<script src="/js/sc2journal.js"></script>

<script>
  var jrnl = new sc2Journal(document.getElementById('journal-id'), 100, 14, 2);
  <?php foreach($_pupils as $pupil) { ?>
    jrnl.addName('<?=s_q($pupil['ppname'])?>');
    jrnl.addPupilId('<?=s_q($pupil['id'])?>');
  <?php } ?>

  <?php $firstquart = isset($_vacations['fstend']) ? $_vacations['fstend'] : mktime(0,0,0,1,1,2016);
  $secondquart = isset($_vacations['secend']) ? $_vacations['secend'] : mktime(0,0,0,1,1,2016);
  $thirdquart = isset($_vacations['thrend']) ? $_vacations['thrend'] : mktime(0,0,0,1,1,2016);
  $_totalktp = count($_ktp); ?>

  <?php $k = count($_ktp); for($i = 0; $i < $k; $i++) { ?>
    <?php if(($_ktp[$i]['ktdate'] < $firstquart && $i < $k-1 && $firstquart < $_ktp[$i+1]['ktdate'] ) ||
             ($i == $k-1 && $_ktp[$i]['ktdate'] < $firstquart && $_ktp[$i]['ktdate'] + 604800 >= $firstquart)) { ?>
      jrnl.addDate('I четверть');
      jrnl.addTopic('');
      jrnl.addKtpId('-1');
      jrnl.addColor('#ffea00');
      jrnl.addType('91');
    <?php } else if(($_ktp[$i]['ktdate'] < $secondquart && $i < $k-1 && $secondquart < $_ktp[$i+1]['ktdate'] ) ||
                    ($i == $k-1 && $_ktp[$i]['ktdate'] < $secondquart && $_ktp[$i]['ktdate'] + 604800 >= $secondquart)) { ?>
      jrnl.addDate('II четверть');
      jrnl.addTopic('');
      jrnl.addKtpId('-2');
      jrnl.addColor('#ffea00');
      jrnl.addType('92');
    <?php } else if(($_ktp[$i]['ktdate'] < $thirdquart && $i < $k-1 && $thirdquart < $_ktp[$i+1]['ktdate'] ) ||
                    ($i == $k-1 && $_ktp[$i]['ktdate'] < $thirdquart &&  $_ktp[$i]['ktdate'] + 604800 >= $thirdquart)) { ?>
      jrnl.addDate('III четверть');
      jrnl.addTopic('');
      jrnl.addKtpId('-3');
      jrnl.addColor('#ffea00');
      jrnl.addType('93');
    <?php } else { $topics = $_ktp[$i]; ?>
      jrnl.addDate('<?=date('d.m.Y', $topics['ktdate'])?>');
      jrnl.addTopic('<?=s_q($topics['kttopic'])?>');
      jrnl.addKtpId('<?=s_q($topics['id'])?>');
      <?php if($topics['ktcolor']==1) { ?>
        jrnl.addColor('#ffa7a7');
      <?php } else if($topics['ktcolor']==2) { ?>
        jrnl.addColor('#cbffcb');
      <?php } else if($topics['ktcolor']==3) { ?>
        jrnl.addColor('#cfd0ff');
      <?php } else if($topics['ktcolor']==4) { ?>
        jrnl.addColor('#ffd0d0');
      <?php } else if($topics['ktcolor']==5) { ?>
        jrnl.addColor('#fff9ba');
      <?php } else { ?>
        jrnl.addColor('transparent');
      <?php } ?>
      jrnl.addType('0');
    <?php } ?>
  <?php } ?>
  <?php if($i == $_totalktp) { ?>
    jrnl.addDate('IV четверть');
    jrnl.addTopic('');
    jrnl.addKtpId('-4');
    jrnl.addColor('#ffea00');
    jrnl.addType('94');
    jrnl.addDate('годовая');
    jrnl.addTopic('');
    jrnl.addKtpId('-5');
    jrnl.addColor('#ffea00');
    jrnl.addType('95');
  <?php } ?>

  <?php foreach($_pupils as $pupil) { ?>
    <?php foreach($_ktp as $topics) { ?>
      jrnl.addMarks('<?=s_q($pupil['id'])?>','<?=s_q($topics['id'])?>','<?=isset($_journal[$pupil['id']][$topics['id']])?s_q($_journal[$pupil['id']][$topics['id']]):''?>');
    <?php } ?>
    // quarter marks
    jrnl.addMarks('<?=s_q($pupil['id'])?>','-1','<?=isset($_journal[$pupil['id']][-1])?s_q($_journal[$pupil['id']][-1]):''?>');
    jrnl.addMarks('<?=s_q($pupil['id'])?>','-2','<?=isset($_journal[$pupil['id']][-2])?s_q($_journal[$pupil['id']][-2]):''?>');
    jrnl.addMarks('<?=s_q($pupil['id'])?>','-3','<?=isset($_journal[$pupil['id']][-3])?s_q($_journal[$pupil['id']][-3]):''?>');
    jrnl.addMarks('<?=s_q($pupil['id'])?>','-4','<?=isset($_journal[$pupil['id']][-4])?s_q($_journal[$pupil['id']][-4]):''?>');
    jrnl.addMarks('<?=s_q($pupil['id'])?>','-5','<?=isset($_journal[$pupil['id']][-5])?s_q($_journal[$pupil['id']][-5]):''?>');
  <?php } ?>

  jrnl.build();

  function marksSave() {
    var cont = document.forms['marksform'];
    var i, j;
    var lpup = jrnl.editableCells.length
    var lktp;
    var mark, pupil_id, ktp_id, type;
    for(i = 0; i < lpup; i++) {
      lktp = jrnl.editableCells[i].length;
      for(j = 0; j < lktp; j++) {
        mark = jrnl.editableCells[i][j].textContent.trim();
        if(mark == '') { continue; }
        pupil_id = jrnl.pupil_ids[i];
        ktp_id = jrnl.ktp_ids[j];
        type = jrnl.types[j];
        var inp = document.createElement('input');
        inp.type = 'hidden';
        if(type < 90) {
          inp.name = 'mark['+pupil_id+']['+ktp_id+']';
        } else {
          inp.name = 'markquart['+pupil_id+']['+type+']';
        }
        inp.value = mark;
        cont.appendChild(inp);
      }
    }
    console.log(cont);
  }
  </script>

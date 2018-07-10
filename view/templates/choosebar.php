<form class="transpform" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <?php foreach($_hidden as $k => $rec) { ?>
      <input type="hidden" name="<?=$k?>" value="<?=$rec?>" />
    <?php } ?>


    <input type="hidden" name="<?=$_var?>" value="<?=$_curvar?>" id="<?=$_var?>"/>        <!-- for select box interface -->
    <input type="hidden" name="set<?=$_var?>" value="<?=$_curvar?>" id="set<?=$_var?>"/>  <!-- for buttons interface -->
    <label><?=$_name?>:</label>
    <?php
    if(isset($_array) && count($_array) > 0) {
        foreach($_array as $rec) {
            if($rec['id'] != $_curvar) { ?>
                <input type="button" onclick="document.getElementById('<?=$_var?>').value = document.getElementById('set<?=$_var?>').value = '<?=$rec['id']?>';
                this.parentNode.submit();"
                       value="<?=$rec[$_fieldname]?>" />
            <?php } else { ?>
                <input type="button" class="selectedButton"
                       value="<?=$rec[$_fieldname]?>" />
            <?php }
        }
    }?>
</form>

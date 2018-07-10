<?php if(!$withoutform) { ?>
  <form class="transpform" action='<?=$_SERVER['REQUEST_URI']?>' method='post'>
<?php } ?>
    <table class="noborder" id="<?=$_formlistid?>">
        <tr>
            <?php foreach($_columns as $rec) { ?>
            <td><label><?=$rec?></label></td>
            <?php } ?>
        </tr>
    </table>
    <label></label>
    <?php foreach($_hidden as $k => $rec) { ?>
    <input type="hidden" name="<?=$k?>" value="<?=$rec?>" />
    <?php } ?>
    <?php if($_showbutton) { ?>
    <input type="submit" name='save' value="Сохранить" />
    <?php } ?>

<?php if(!$withoutform) { ?>
</form>
<?php } ?>

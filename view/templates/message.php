<?php if(isset($_SESSION['message']) && $_SESSION['message']==$msg_textid) { ?>
<div class="information">
    <?=$msg_text?>
</div>
<?php } ?>

<div class="god">
    <p>
        <?php echo lang('soslaundry:register_form_title');?>
    </p>
    <p>
        <?php echo lang('soslaundry:register_form_text');?>
    </p>
    <h1 class="luck">
        <?php echo lang('soslaundry:register_form_good_luck');?>
    </h1>
</div>
<form action="<?php echo base_url('soslaundry/form');?>" method="post" id="#resign" enctype="multipart/form-data" accept-charset="utf-8">
    <div class="message" id="messages">
        <?php
        if(isset($_GET['message'])){
            $code = (int) $_GET['message'];
            if($code == 1){
                $msg = lang('soslaundry:register_success');
            }elseif($code == 2){
                $msg = lang('soslaundry:register_error');
            }elseif($code == 3){
                $msg = lang('soslaundry:register_email_exist_error');
            }elseif($code == 4){
                $msg = lang('soslaundry:register_email_format_error');
            }elseif($code == 5){
                $msg = lang('soslaundry:register_phone_error');
            }else{
                $msg = '';
            }
            ?>
            <?php if($code == 1){?>
                <span style="font-size: 16px;color: green;"><?php echo $msg;?></span>
            <?php }else{?>
                <span style="font-size: 16px;color: red;"><?php echo $msg;?></span>
            <?php }?>
        <?php }?>
    </div>
    <div class="fieldset name">
        <div class="first_name">
            <label><?php echo lang('soslaundry:name'); ?></label><br />
            <input id="txtFirstName" type="text" name="first_name"/><br />
            <label class="note"><?php echo lang('soslaundry:first_name'); ?></label>
        </div>
        <div class="last_name">
            <label>&nbsp;</label><br />
            <input id="txtLastName" type="text" name="last_name"/><br />
            <label class="note"><?php echo lang('soslaundry:last_name'); ?></label>
        </div>
    </div>
    <div class="fieldset">
        <div class="email">
            <label><?php echo lang('soslaundry:email'); ?></label><br />
            <input id="txtEmail" type="text" name="email"/>
        </div>
        <div class="mobile">
            <label><?php echo lang('soslaundry:phone'); ?></label><br />
            <input id="txtPhone1" type="text" name="phone[]"/>
            <label class="note">-</label>
            <input id="txtPhone2" type="text" name="phone[]"/>
            <label class="note">-</label>
            <input id="txtPhone3" type="text" name="phone[]"/>
        </div>
    </div>
</form>
<div class="god">
    <p>
        <?php echo lang('soslaundry:register_form_title');?>
    </p>
    <p style="font-size: 24px;">
        <?php echo lang('soslaundry:register_form_text');?>
    </p>
    <h1 class="luck">
        <?php echo lang('soslaundry:register_form_good_luck');?>
    </h1>
</div>
<?php echo form_open('soslaundry/index',array('id' => 'register-form')); ?>
<div class="message" id="messages">
    <div class="msg_error"><?php echo validation_errors(); ?></div>
    <?php if(isset($msg) && $msg != null && $msg != ''):?>
        <div class="msg_success"><p><?php echo $msg;?></p></div>
    <?php endif;?>
</div>
<div class="fieldset name">
    <div class="first_name">
        <label><?php echo lang('soslaundry:name'); ?></label><br />
        <input id="txtFirstName" value="<?php echo set_value('first_name'); ?>" type="text" name="first_name"/><br />
        <label class="note"><?php echo lang('soslaundry:first_name'); ?></label>
    </div>
    <div class="last_name">
        <label>&nbsp;</label><br />
        <input id="txtLastName" type="text" value="<?php echo set_value('last_name'); ?>" name="last_name"/><br />
        <label class="note"><?php echo lang('soslaundry:last_name'); ?></label>
    </div>
</div>
<div class="fieldset">
    <div class="email">
        <label><?php echo lang('soslaundry:email'); ?></label><br />
        <input id="txtEmail" value="<?php echo set_value('email'); ?>" type="email" name="email"/>
    </div>
    <div class="mobile">
        <label><?php echo lang('soslaundry:phone'); ?></label><br />
        <input id="txtPhone1" type="text" maxlength="3" value="<?php echo set_value('phone1'); ?>" name="phone1"/>
        <label class="note">-</label>
        <input id="txtPhone2" type="text" maxlength="3" value="<?php echo set_value('phone2'); ?>" name="phone2"/>
        <label class="note">-</label>
        <input id="txtPhone3" type="text" maxlength="4" value="<?php echo set_value('phone3'); ?>" name="phone3"/>
    </div>
</div>
<div class="fieldset">
    <p class="agree"><a target="_blank" href="<?php echo base_url('agree-to-the-rules');?>"><?php echo lang('soslaundry:agree_text'); ?></a><span class="checkbox"><input type="checkbox"  name="agree" id="txtRule"/></span></p>


    <div class="select_hotel">
        <select name="hotel" class="hotel" id="txtHotel">
            <option value="0"><?php echo lang('soslaundry:choose_hotel'); ?></option>
            <?php if(!empty($hotels)):?>
                <?php foreach($hotels as $hotel):?>
                    <option <?php if($hotel->id == set_value('hotel')){?>selected="selected" <?php }?> value="<?php echo $hotel->id;?>"><?php echo $hotel->name;?></option>
                <?php endforeach;?>
            <?php endif;?>
        </select>
    </div>
    <button class="right submit" title="Submit" type="submit">Submit</button>
</div>

</form>
<div class="clear">&nbsp;</div>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->

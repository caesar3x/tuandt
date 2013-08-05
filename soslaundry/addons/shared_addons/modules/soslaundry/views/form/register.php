<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/4/13
 */
?>
<header>
    <h2><?php echo lang('soslaundry:form_title'); ?></h2>
</header>
<form id="register-form" action="<?php echo base_url('winner/form');?>" enctype="multipart/form-data" accept-charset="utf-8" method="post">

    <div>
        <label class="desc" for="txtFirstName" id="title_txtFirstName"><?php echo lang('soslaundry:first_name'); ?></label>
        <div>
            <input class="field text fn" id="txtFirstName" name="first_name" size="8" tabindex="1" type="text" value="" />
            <span class="status" id="firstNameStatus"></span>
        </div>

    </div>
    <div>
        <label class="desc" for="txtLastName" id="title_txtLastName"><?php echo lang('soslaundry:last_name'); ?></label>
        <div>
            <input class="field text fn" id="txtLastName" name="last_name" size="8" tabindex="1" type="text" value="" />
            <span class="status" id="lastNameStatus"></span>
        </div>
    </div>
    <div>
        <label class="desc" for="txtEmail" id="title_txtEmail"><?php echo lang('soslaundry:email'); ?> </label>
        <div>
            <input id="txtEmail" maxlength="255" name="email" spellcheck="false" tabindex="3" type="email" value="" />
            <span class="status" id="emailStatus"></span>
        </div>
    </div>
    <div>
        <label class="desc" for="txtPhone" id="title_txtPhone"><?php echo lang('soslaundry:phone'); ?> </label>
        <div>
            <input id="txtPhone" maxlength="255" name="phone" spellcheck="false" tabindex="3" type="text" value="" />
            <span class="status" id="phoneStatus"></span>
        </div>
    </div>
    <div>
        <label class="desc" for="textHotel" id="title_textHotel"><?php echo lang('soslaundry:choose_hotel'); ?></label>
        <div>
            <select class="field select medium" id="textHotel" name="hotel" tabindex="11">
                <option value="0"><?php echo lang('soslaundry:choose_hotel'); ?></option>
                <?php if(!empty($hotels)):?>
                <?php foreach($hotels as $hotel):?>
                <option value="<?php echo $hotel->id;?>"><?php echo $hotel->name;?></option>
                <?php endforeach;?>
                <?php endif;?>
            </select>
            <span class="status" id="hotelStatus"></span>
        </div>
    </div>

    <div>
        <div><input id="saveForm" name="saveForm" type="submit" value="<?php echo lang('soslaundry:submit_label'); ?>" /></div>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        $('#txtPhone').blur(function(e) {
            e.stopPropagation();
            if (validatePhone('txtPhone')) {
                $('#phoneStatus').html('<?php echo lang('soslaundry:input_valid'); ?>');
                $('#phoneStatus').css('color', 'green');
            }
            else {
                $('#phoneStatus').html('<?php echo lang('soslaundry:phone_regex'); ?>');
                $('#phoneStatus').css('color', 'red');
            }
        });
        $('#register-form').submit(function() {
            var submitform = true;
            if (validateEmpty('txtFirstName')) {
                $('#firstNameStatus').html('<?php echo lang('soslaundry:input_valid'); ?>');
                $('#firstNameStatus').css('color', 'green');
                submitform = true;
            }
            else {
                $('#firstNameStatus').html('<?php echo lang('soslaundry:firstname_empty'); ?>');
                $('#firstNameStatus').css('color', 'red');
                submitform = false;
            }
            if (validateEmpty('title_txtLastName')) {
                $('#lastNameStatus').html('<?php echo lang('soslaundry:input_valid'); ?>');
                $('#lastNameStatus').css('color', 'green');
                submitform = true;
            }
            else {
                $('#lastNameStatus').html('<?php echo lang('soslaundry:lastname_empty'); ?>');
                $('#lastNameStatus').css('color', 'red');
                submitform = false;
            }
            if (validateEmpty('txtPhone')) {
                $('#phoneStatus').html('<?php echo lang('soslaundry:input_valid'); ?>');
                $('#phoneStatus').css('color', 'green');
                if (validatePhone('txtPhone')) {
                    $('#phoneStatus').html('<?php echo lang('soslaundry:input_valid'); ?>');
                    $('#phoneStatus').css('color', 'green');
                    submitform = true;
                }
                else {
                    $('#phoneStatus').html('<?php echo lang('soslaundry:phone_regex'); ?>');
                    $('#phoneStatus').css('color', 'red');
                    submitform = false;
                }
            }
            else {
                $('#phoneStatus').html('<?php echo lang('soslaundry:phone_empty'); ?>');
                $('#phoneStatus').css('color', 'red');
                submitform = false;
            }
            return submitform;
        });
    });
    function validatePhone(txtPhone) {
        var a = document.getElementById(txtPhone).value;
        var filter = /^[0-9-+]+$/;
        if (filter.test(a)) {
            return true;
        }
        else {
            return false;
        }
    }
    function validateEmpty(txtField) {
        var a = document.getElementById(txtField).value;
        if (a == null || a == "") {
            return false;
        }
        else {
            return true;
        }
    }
</script>
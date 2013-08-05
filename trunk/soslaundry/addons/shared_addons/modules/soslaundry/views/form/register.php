<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/4/13
 */
?>
<header>
    <h2><?php echo lang('soslaundry:form_title'); ?></h2>
</header>
<form id="register-form" action="<?php echo base_url('soslaundry/form');?>" enctype="multipart/form-data" accept-charset="utf-8" method="post">

    <div>
        <label class="desc" for="txtFirstName" id="title_txtFirstName"><?php echo lang('soslaundry:first_name'); ?></label>
        <div>
            <input class="field text fn" id="txtFirstName" name="first_name" size="8" tabindex="1" type="text" value="" />
        </div>

    </div>
    <div>
        <label class="desc" for="txtLastName" id="title_txtLastName"><?php echo lang('soslaundry:last_name'); ?></label>
        <div>
            <input class="field text fn" id="txtLastName" name="last_name" size="8" tabindex="1" type="text" value="" />
        </div>
    </div>
    <div>
        <label class="desc" for="txtEmail" id="title_txtEmail"><?php echo lang('soslaundry:email'); ?> </label>
        <div>
            <input id="txtEmail" maxlength="255" name="email" spellcheck="false" tabindex="3" type="email" value="" />
        </div>
    </div>
    <div>
        <label class="desc" for="txtPhone" id="title_txtPhone"><?php echo lang('soslaundry:phone'); ?> </label>
        <div>
            <input id="txtPhone" maxlength="255" name="phone" spellcheck="false" tabindex="3" type="text" value="" />
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
                $('#txtPhone').css('border', '1px solid green');
            }
            else {
                $('#txtPhone').attr('placeholder','<?php echo lang('soslaundry:phone_regex'); ?>');
                $('#txtPhone').css('border', '1px solid red');
            }
        });
        $('#register-form').submit(function() {
            var submitform = true;
            if (validateEmpty('txtFirstName')) {
                $('#txtFirstName').css('border', '1px solid green');
                submitform = true;
            }
            else {
                $('#txtFirstName').attr('placeholder','<?php echo lang('soslaundry:firstname_empty'); ?>');
                $('#txtFirstName').css('border', '1px solid red');
                submitform = false;
            }
            if (validateEmpty('txtLastName')) {
                $('#txtLastName').css('border', '1px solid green');
                submitform = true;
            }
            else {
                $('#txtLastName').attr('placeholder','<?php echo lang('soslaundry:lastname_empty'); ?>');
                $('#txtLastName').css('border', '1px solid red');
                submitform = false;
            }
            if (validateEmpty('txtEmail')) {
                $('#txtEmail').css('border', '1px solid green');
                submitform = true;
            }
            else {
                $('#txtEmail').attr('placeholder','<?php echo lang('soslaundry:email_empty'); ?>');
                $('#txtEmail').css('border', '1px solid red');
                submitform = false;
            }
            if (validateEmpty('txtPhone')) {
                $('#txtPhone').css('border', '1px solid green');
                if (validatePhone('txtPhone')) {
                    $('#txtPhone').css('border', '1px solid green');
                    submitform = true;
                }
                else {
                    $('#txtPhone').attr('placeholder','<?php echo lang('soslaundry:phone_regex'); ?>');
                    $('#txtPhone').css('border', '1px solid red');
                    submitform = false;
                }
            }
            else {
                $('#txtPhone').attr('placeholder','<?php echo lang('soslaundry:phone_empty'); ?>');
                $('#txtPhone').css('border', '1px solid red');
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
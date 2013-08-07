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
            <input id="txtPhone1" style="width: 30px;" maxlength="3" name="phone[]" spellcheck="false" tabindex="3" type="text" value="" />
            <?php echo ' - ';?><input id="txtPhone2" style="width: 30px;" maxlength="3" name="phone[]" spellcheck="false" tabindex="3" type="text" value="" />
            <?php echo ' - ';?><input id="txtPhone3" style="width: 40px;" maxlength="4" name="phone[]" spellcheck="false" tabindex="3" type="text" value="" />
        </div>
    </div>
    <div>
        <label class="desc" for="txtHotel" id="title_txtHotel"><?php echo lang('soslaundry:choose_hotel'); ?></label>
        <div>
            <select class="field select medium" id="txtHotel" name="hotel" tabindex="11">
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
        <div><input type="checkbox" name="rule" id="txtRule"><label for="txtRule">Agree to the rules. </label></div>
    </div>
    <div>
        <div><input id="saveForm" name="saveForm" type="submit" value="<?php echo lang('soslaundry:submit_label'); ?>" /></div>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        $('#txtPhone1').blur(function(e) {
            e.stopPropagation();
            if (validatePhone('txtPhone1')) {
                $('#txtPhone1').css('border', '1px solid green');
                var phone1 = $("#txtPhone1").val();
                if(phone1.length < 3){
                    $('#txtPhone1').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone1_length'); ?></span>');
                }else{
                    $("#messages").html("<span></span>");
                }
            }
            else {
                $('#txtPhone1').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
            }
        });
        $('#txtPhone2').blur(function(e) {
            e.stopPropagation();
            if (validatePhone('txtPhone2')) {
                $('#txtPhone2').css('border', '1px solid green');
                var phone2 = $("#txtPhone2").val();
                if(phone2.length < 3){
                    $('#txtPhone2').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone2_length'); ?></span>');
                }else{
                    $("#messages").html("<span></span>");
                }
            }
            else {
                $('#txtPhone2').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
            }
        });
        $('#txtPhone3').blur(function(e) {
            e.stopPropagation();
            if (validatePhone('txtPhone3')) {
                $('#txtPhone3').css('border', '1px solid green');
                var phone3 = $("#txtPhone3").val();
                if(phone3.length < 4){
                    $('#txtPhone3').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone3_length'); ?></span>');
                }else{
                    $("#messages").html("<span></span>");
                }
            }
            else {
                $('#txtPhone3').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
            }
        });

        $('#register-form').submit(function() {
            var submitform = true;
            if (validateEmpty('txtFirstName')) {
                $('#txtFirstName').css('border', '1px solid green');
                submitform = submitform && true;
            }
            else {
                $('#txtFirstName').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:firstname_empty'); ?></span>');
                return false;
            }
            if (validateEmpty('txtLastName')) {
                $('#txtLastName').css('border', '1px solid green');
                submitform = submitform && true;
            }
            else {
                $('#txtLastName').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:lastname_empty'); ?></span>');
                return false;
            }
            if (validateEmpty('txtEmail')) {
                $('#txtEmail').css('border', '1px solid green');
                submitform = submitform && true;
            }
            else {
                $('#txtEmail').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:email_empty'); ?></span>');
                return false;
            }
            if (validateEmpty('txtPhone1')) {
                $('#txtPhone1').css('border', '1px solid green');
                if (validatePhone('txtPhone1')) {
                    $('#txtPhone1').css('border', '1px solid green');
                    var phone1 = $("#txtPhone1").val();
                    if(phone1.length < 3){
                        $('#txtPhone1').css('border', '1px solid red');
                        $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone1_length'); ?></span>');
                        return false;
                    }else{
                        $("#messages").html("<span></span>");
                    }
                }
                else {
                    $('#txtPhone1').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
                    return false;
                }
            }
            else {
                $('#txtPhone1').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_empty'); ?></span>');
                return false;
            }
            if (validateEmpty('txtPhone2')) {
                $('#txtPhone2').css('border', '1px solid green');
                if (validatePhone('txtPhone2')) {
                    $('#txtPhone2').css('border', '1px solid green');
                    var phone2 = $("#txtPhone2").val();
                    if(phone2.length < 3){
                        $('#txtPhone2').css('border', '1px solid red');
                        $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone2_length'); ?></span>');
                        return false;
                    }else{
                        $("#messages").html("<span></span>");
                    }
                }
                else {
                    $('#txtPhone2').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
                    return false;
                }
            }
            else {
                $('#txtPhone2').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_empty'); ?></span>');
                return false;
            }
            if (validateEmpty('txtPhone3')) {
                $('#txtPhone3').css('border', '1px solid green');
                if (validatePhone('txtPhone3')) {
                    $('#txtPhone3').css('border', '1px solid green');
                    var phone3 = $("#txtPhone3").val();
                    if(phone3.length < 4){
                        $('#txtPhone3').css('border', '1px solid red');
                        $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone3_length'); ?></span>');
                        return false;
                    }else{
                        $("#messages").html("<span></span>");
                    }
                }
                else {
                    $('#txtPhone3').css('border', '1px solid red');
                    $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_regex'); ?></span>');
                    return false;
                }
            }
            else {
                $('#txtPhone3').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:phone_empty'); ?></span>');
                return false;
            }
            if(validHotel() == false){
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:hotel_valid'); ?></span>');
                return false;
            }
            if(validRule()){
                $('#txtRule').css('border', '1px solid green');
                submitform = submitform && true;
            }else{
                $('#txtRule').css('border', '1px solid red');
                $("#messages").html('<span style="font-size: 16px;color: red;"><?php echo lang('soslaundry:rule_accept'); ?></span>');
                return false;
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
    function validRule(){
        return document.getElementById("txtRule").checked;
    }
    function validHotel(){
        var e  = document.getElementById("txtHotel");
        var hotel = e.options[e.selectedIndex].value;
        if(hotel != null && hotel != '' && hotel != '0' && hotel != 0){
            return true;
        }
        return false;
    }
</script>
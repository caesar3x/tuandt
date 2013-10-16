<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
?>
<div class="container">
    <h2 class="page-title"><span><?php echo lang('sign_up');?></span></h2>
    <div class="row-fluid">
        <div class="col-main span9">
            <div class="page-content">
                <div class="row-fluid">
                    <div class="span12">
                        <?php echo form_open_multipart(current_url(),array('id' => 'signup-form','method' => 'post'));?>
                        <div class="msg_error"><?php echo validation_errors(); ?></div>
                        <label><?php echo lang('label:first_name')?></label>
                        <?php echo form_input(array('name' => 'first_name','id' => 'first-name','class' => 'input-text'))?>
                        <label><?php echo lang('label:last_name')?></label>
                        <?php echo form_input(array('name' => 'last_name','id' => 'last-name','class' => 'input-text'))?>
                        <label><?php echo lang('label:username')?></label>
                        <?php echo form_input(array('name' => 'username','id' => 'username','class' => 'input-text'))?>
                        <label><?php echo lang('label:password')?></label>
                        <?php echo form_password(array('name' => 'password','id' => 'password','class' => 'input-text'))?>
                        <label><?php echo lang('label:confirm_password')?></label>
                        <?php echo form_password(array('name' => 'confirm_password','id' => 'confirm_password','class' => 'input-text'))?>
                        <label><?php echo lang('label:job')?></label>
                        <?php echo form_dropdown('job',array(
                            0 => 'Chose a Job',
                            1 => 'Job 1',
                            2 => 'Job 2',
                            3 => 'Job 3',
                            4 => 'Job 4',
                        ),array(0),'class="large"')?>
                        <label><?php echo lang('label:email')?></label>
                        <?php echo form_input(array('name' => 'email','id' => 'email','class' => 'input-text'))?>
                        <label><?php echo lang('label:dob')?></label>
                        <?php echo form_input(array('name' => 'dob','id' => 'dob','class' => 'input-text'))?>
                        <label><?php echo lang('label:phone')?></label>
                        <?php echo form_input(array('name' => 'phone','id' => 'phone','class' => 'input-text'))?>
                        <label><?php echo lang('label:tax')?></label>
                        <?php echo form_input(array('name' => 'tax','id' => 'tax','class' => 'input-text'))?>
                        <label><?php echo lang('label:postcode')?></label>
                        <?php echo form_input(array('name' => 'postcode','id' => 'postcode','class' => 'input-text'))?>
                        <label><?php echo lang('label:street')?></label>
                        <?php echo form_input(array('name' => 'street','id' => 'street','class' => 'input-text'))?>
                        <label><?php echo lang('label:city')?></label>
                        <?php echo form_input(array('name' => 'city','id' => 'city','class' => 'input-text'))?>
                        <label><?php echo lang('label:state')?></label>
                        <?php echo form_input(array('name' => 'state','id' => 'state','class' => 'input-text'))?>
                        <label><?php echo lang('label:country')?></label>
                        <?php echo form_input(array('name' => 'country','id' => 'country','class' => 'input-text'))?>
                        <button class="button large right" title="Search" type="submit"><span>SUBMIT</span></button>
                        <?php echo form_close()?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-right span3">
            <div class="block blsssock-quicklinks">
                <ul>
                    <li class="first"><a href="#"><i class="icon-letter"></i> Contact & support</a></li>
                    <li class=""><a href="#"><i class="icon-info-sign"></i> Information & advertiser</a></li>
                    <li class=""><a href="#"><i class="icon-book"></i> Terms & conditions</a></li>
                    <li class=""><a href="#"><i class="icon-lock"></i> Privacy policy</a></li>
                    <li class=""><a href="#"><i class="icon-question-sign"></i> How it work</a></li>
                    <li class="last"><a href="#"><i class="icon-pencil"></i> FAQ</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
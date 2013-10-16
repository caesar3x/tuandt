<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
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
                        <label><?php echo lang('label:username')?></label>
                        <?php echo form_input(array('name' => 'username','id' => 'username','class' => 'input-text'))?>
                        <label><?php echo lang('label:password')?></label>
                        <?php echo form_password(array('name' => 'password','id' => 'password','class' => 'input-text'))?>
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
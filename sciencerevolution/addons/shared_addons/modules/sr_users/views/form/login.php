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
        {{ theme:partial name="block-quicklinks" }}
    </div>
</div>
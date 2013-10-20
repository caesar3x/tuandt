<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
 */
?>
<div class="container">
    <h2 class="page-title"><span><?php echo lang('Login');?></span></h2>
    <div class="row-fluid">
        <div class="col-main span12">
            <div class="page-content">
                <?php echo form_open_multipart(current_url(),array('id' => 'login-form','class' => 'validate sc-form','method' => 'post'));?>
                <div class="msg_error"><?php echo validation_errors(); ?></div>
                <div class="row-fluid">
                    <div class="span5">
                        <label><?php echo lang('Username')?></label>
                        <?php echo form_input(array('name' => 'username','id' => 'username','class' => 'input-text'),'','required')?>

                    </div>
                    <div class="span5">
                        <label><?php echo lang('Password')?></label>
                        <?php echo form_password(array('name' => 'password','id' => 'password','class' => 'input-text'),'','required')?>
                    </div>
                    <div class="span2">
                        <button class="button large right btn_login" title="<?php echo lang('LOGIN')?>" type="submit"><span><?php echo lang('LOGIN')?></span></button>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span5">
                        <?php echo form_checkbox(array('name' => 'remember_me','id' => 'remember_me'))?>
                        <label><?php echo lang('Remember me')?></label>
                    </div>
                    <div class="span5">
                        <a href="{{url:site}}forgotpass" class="forgot-pass"><?php echo lang('Forgotten username or password')?></a>
                    </div>
                </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>
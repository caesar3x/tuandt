<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
 */
?>
<div class="container">
    <h2 class="page-title"><span><?php echo lang('Signup');?></span></h2>
    <div class="row-fluid">
        <div class="col-main span12">
            <div class="page-content">
                <?php echo form_open_multipart(current_url(),array('id' => 'signup-form','class' => 'validate sc-form','method' => 'post'));?>
                <div class="row-fluid">
                    <div class="messages error"><?php echo validation_errors(); ?></div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <label class="required"><?php echo lang('First name')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'first_name','id' => 'first-name','class' => 'input-text'),set_value('first_name'),' required')?>

                    </div>
                    <div class="span6">
                        <label class="required"><?php echo lang('Last Name')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'last_name','id' => 'last-name','class' => 'input-text'),set_value('last_name'),'required')?>

                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <label class="required"><?php echo lang('Username')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'username','id' => 'username','class' => 'input-text'),set_value('username'),'required')?>
                    </div>
                    <div class="span6">
                        <label class="required"><?php echo lang('Email Address')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'email','class' => 'input-text'),set_value('email'),'required email')?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <label class="required"><?php echo lang('Password')?><em>*</em></label>
                        <?php echo form_password(array('name' => 'password','id' => 'password','class' => 'input-text'),'','required')?>
                    </div>
                    <div class="span6">
                        <label class="required"><?php echo lang('Confirm Password')?><em>*</em></label>
                        <?php echo form_password(array('name' => 'confirm_password','id' => 'confirm_password','class' => 'input-text'),'','required')?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div><strong><?php echo lang('Show alert & other settings')?></strong></div>
                </div>
                <div class="row-fluid">
                    <div class="row-fluid">
                        <div class="span6">
                            <label><?php echo lang('Other name')?></label>
                            <?php echo form_input(array('name' => 'other_name','id' => 'other_name','class' => 'input-text'),set_value('other_name'))?>
                        </div>
                        <div class="span2">
                            <label class="required"><?php echo lang('Date of Birth')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'dob','id' => 'dob','class' => 'input-text datepicker'),set_value('dob'),'required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Job Title')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'job_title','id' => 'job_title','class' => 'input-text'),set_value('job_title'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Phone Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'phone','id' => 'phone','class' => 'input-text'),set_value('phone'),'required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Street Name & Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'street','class' => 'input-text'),set_value('street'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('City')?><em>*</em></label>
                            <?php echo form_dropdown('city',array('' => lang('Choose a Country')),set_value('city'),'class="large" id="select-city" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('State')?><em>*</em></label>
                            <?php echo form_dropdown('state',array('' => lang('Choose a Country')),set_value('state'),'class="large" id="select-state" required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Country')?><em>*</em></label>
                            <!--countries data-->
                            <?php $countries = array_merge(array('' => lang('Choose a Country')),get_dropdown_format_countries())?>
                            <?php echo form_dropdown('country',$countries,set_value('country',0),'class="large" id="select-country" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Postal(zip) code')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'postcode','id' => 'postcode','class' => 'input-text'),set_value('postcode'),'required')?>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div>
                        <?php echo form_checkbox(array('name' => 'billing:residental','id' => 'billing-residental','class' => 'input-checkbox'),set_value('billing:residental',1))?>
                        <strong><?php echo lang('Billing address same as the residential')?></strong>
                    </div>
                </div>
                <div class="row-fluid" id="billing-box">
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Job Title')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'billing:job_title','id' => 'billing:job_title','class' => 'input-text'),set_value('billing:job_title'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Phone Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'billing:phone','id' => 'billing:phone','class' => 'input-text'),set_value('billing:phone'),'required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Street Name & Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'billing:street','class' => 'input-text'),set_value('billing:street'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('City')?><em>*</em></label>
                            <?php echo form_dropdown('billing:city',array('' => lang('Choose a Country')),set_value('billing:city'),'class="large" id="billing-city" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('State')?><em>*</em></label>
                            <?php echo form_dropdown('billing:state',array('' => lang('Choose a Country')),set_value('billing:state'),'class="large" id="billing-state" required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Country')?><em>*</em></label>
                            <!--countries data-->
                            <?php $countries = array_merge(array('' => lang('Choose a Country')),get_dropdown_format_countries())?>
                            <?php echo form_dropdown('billing:country',$countries,set_value('billing:country',0),'class="large" id="billing-country" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Postal(zip) code')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'billing:postcode','class' => 'input-text'),set_value('billing:postcode'),'required')?>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span7">
                        <?php echo form_checkbox(array('name' => 'accept','id' => 'accept','class' => 'input-checkbox'),set_value('accept'),false)?>
                        <?php echo form_hidden('user_type','personal');?>
                        <label class="required"><em>*</em><?php echo lang('I have read and understood the <a href="#">Registered User Agreement</a>')?></label>
                    </div>
                    <div class="span5">
                        <button class="button large left" title="<?php echo lang('REGISTER')?>" type="submit"><span><?php echo lang('REGISTER')?></span></button>
                    </div>
                </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>
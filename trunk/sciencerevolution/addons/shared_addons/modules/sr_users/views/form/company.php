<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
?>
<div class="container">
    <div class="page-title row-fluid">
        <div class="span8">
            <h2 class=""><span><?php echo lang('Signup');?></span></h2>
        </div>
        <div class="span4">
            <a href="{{url:site}}signup" class="button large right" title="<?php echo lang('SIGN UP AS PERSONAL')?>" ><span><?php echo lang('SIGN UP AS PERSONAL')?></span></a>
        </div>
    </div>
    <div class="row-fluid">
        <div class="col-main span12">
            <div class="page-content">
                <?php echo form_open_multipart(current_url(),array('id' => 'signup-form','class' => 'validate sc-form','method' => 'post'));?>
                <?php $error = validation_errors();if(!empty($error)):?>
                    <div class="row-fluid">
                        <div class="messages error"><?php echo validation_errors(); ?></div>
                    </div>
                <?php endif;?>
                <div class="row-fluid">
                    <div class="span6">
                        <label class="required"><?php echo lang('Representative name')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'representative_name','class' => 'input-text'),set_value('representative_name'),' required')?>

                    </div>
                    <div class="span6">
                        <label class="required"><?php echo lang('Representative Surname')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'representative_surname','class' => 'input-text'),set_value('representative_surname'),'required')?>

                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <label class="required"><?php echo lang('Username')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'username','id' => 'username','class' => 'input-text'),set_value('username'),'required')?>
                    </div>
                    <div class="span6">
                        <label class="required"><?php echo lang('Representative Email Address')?><em>*</em></label>
                        <?php echo form_input(array('name' => 'representative_email','class' => 'input-text'),set_value('representative_email'),'required email')?>
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
                    <div class="arrow arrow16"><strong class="title-16"><?php echo lang('Other Information')?></strong></div>
                </div>
                <div class="row-fluid">
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Phone Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'representative_phone_number','class' => 'input-text'),set_value('representative_phone_number'),'required')?>
                        </div>
                        <div class="span3">
                            <label class="required"><?php echo lang('Representative Date of Birth')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'representative_date_of_birth','class' => 'input-text datepicker'),set_value('representative_date_of_birth'),'required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label><?php echo lang('Company name')?></label>
                            <?php echo form_input(array('name' => 'company_name','class' => 'input-text'),set_value('company_name'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Company Tax number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'company_tax_number','class' => 'input-text'),set_value('company_tax_number'),'required')?>
                        </div>

                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('Street Name & Number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'company_street_address','class' => 'input-text'),set_value('company_street_address'),'required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('City')?><em>*</em></label>
                            <?php echo form_dropdown('company_city',array('' => lang('Choose a Country')),set_value('company_city'),'class="large" id="select-city" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="required"><?php echo lang('State')?><em>*</em></label>
                            <?php echo form_dropdown('company_state',array('' => lang('Choose a Country')),set_value('company_state'),'class="large" id="select-state" required')?>
                        </div>
                        <div class="span6">
                            <label class="required"><?php echo lang('Country')?><em>*</em></label>
                            <!--countries data-->
                            <?php $countries = array_merge(array('' => lang('Choose a Country')),get_dropdown_format_countries())?>
                            <?php echo form_dropdown('company_country',$countries,set_value('company_country'),'class="large" id="select-country" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span2">
                            <label class="required"><?php echo lang('Postal(zip) code')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'company_postcode','class' => 'input-text'),set_value('company_postcode'),'required')?>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div>
                        <?php echo form_checkbox(array('name' => 'billing:residental','id' => 'billing-residental','class' => 'input-checkbox'),set_value('billing:residental',1),(bool)$billing_check)?>
                        <strong class="title-16"><?php echo lang('Billing address same as the residential')?></strong>
                    </div>
                </div>
                <div class="row-fluid" id="billing-box">
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
                            <?php echo form_dropdown('billing:country',$countries,set_value('billing:country'),'class="large" id="billing-country" required')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span2">
                            <label class="required"><?php echo lang('Postal(zip) code')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'billing:postcode','class' => 'input-text'),set_value('billing:postcode'),'required')?>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span7">
                        <?php echo form_checkbox(array('name' => 'accept','id' => 'accept','class' => 'input-checkbox'),set_value('accept'),false)?>
                        <?php echo form_hidden('user_type','company');?>
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
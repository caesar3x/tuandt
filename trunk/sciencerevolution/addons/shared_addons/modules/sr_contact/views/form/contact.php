<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
?>
<div class="container">
    <h2 class="page-title"><span><?php echo lang('Contact us')?></span></h2>
    <div class="row-fluid">
        <div class="col-main span9">
            <div class="page-content">
                <div class="row-fluid">
                    <div class="span5 infor">
                        <?php echo lang('<p>We welcome any comments and suggestions which will help us to improve our service. Please see our FAQ if you have any common questions :</p><p>Address: Exhibition Road - South Kensington - SW7 2DD<br />Tell: 0598-548-985<br />Fax: 5498 - 841-5478<br />Email: sciencerevolution@gmail.com</p>')?>
                        <p>{{theme:image file="map.jpg"}}</p>
                    </div>
                    <div class="span7">
                        <?php echo form_open_multipart(current_url(),array('id' => 'contact-form','class' => 'validate sc-form','method' => 'post'));?>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Name')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'full_name','class' => 'input-text'),set_value('full_name'),' required')?>
                        </div>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Phone number')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'phone_number','class' => 'input-text'),set_value('phone_number'),' required')?>
                        </div>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Email address')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'email','class' => 'input-text'),set_value('email'),'required email')?>
                        </div>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Website')?><em>*</em></label>
                            <?php echo form_input(array('name' => 'website','class' => 'input-text'),set_value('website'),' required')?>
                        </div>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Reason')?><em>*</em></label>
                            <?php $reasons = array_merge(array('' => lang('Please select your reason')),get_dropdown_format_reasons());?>
                            <?php echo form_dropdown('reason',$reasons,set_value('reason'),'class="large" required')?>
                        </div>
                        <div class="row-fluid">
                            <label class="required"><?php echo lang('Messages')?><em>*</em></label>
                            <?php echo form_textarea(array('rows' => 4,'name' => 'message','placeholder' => lang('Any additional information about your location or inquiry')))?>
                        </div>
                        <button class="button large right" title="<?php echo lang('SUBMIT')?>" type="submit"><span><?php echo lang('SUBMIT')?></span></button>
                        <?php echo form_close()?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-right span3">
            {{ theme:partial name="block-quicklinks" }}
        </div>
    </div>
</div>
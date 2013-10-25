<section class="title">
    <!-- We'll use $this->method to switch between standard.create & standard.edit -->
    <h4><?php echo lang('news:'.$this->method); ?></h4>
</section>
<section class="item">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
        <div class="form_inputs">
            <fieldset>
                <ul>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="title"><?php echo lang('news:title'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('title', set_value('title'), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="sub_header"><?php echo lang('news:sub_header'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('sub_header', set_value('sub_header'), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="thumbnail"><?php echo lang('news:thumbnail'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('thumbnail', set_value('thumbnail'), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="content"><?php echo lang('news:content'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('content', set_value('content'), 'class="width-15"'); ?></div>
                    </li>
                </ul>
            </fieldset>
        </div>
        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</section>
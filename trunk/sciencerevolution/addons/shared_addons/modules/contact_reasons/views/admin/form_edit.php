<section class="title">
    <!-- We'll use $this->method to switch between standard.create & standard.edit -->
    <h4><?php echo lang('contact:'.$this->method); ?></h4>
</section>
<section class="item">
    <div class="content">
    <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
        <div class="form_inputs">
            <fieldset>
                <ul>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="name"><?php echo lang('contact:name'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('name', set_value('name', $name), 'class="width-15"'); ?></div>
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
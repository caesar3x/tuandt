<?php if ($this->method == 'edit'): ?>
	<section class="title">
    	<h4><?php echo sprintf(lang('referencesource.edit_title'), $referencesource->name); ?></h4>
	</section>
<?php else: ?>
	<section class="title">
    	<h4><?php echo lang('referencesource.add_title'); ?></h4>
	</section>
<?php endif; ?>

<section class="item">
<div class="content">
<?php echo form_open(uri_string(), 'class="crud"'); ?>
<div class="tabs">

	<ul class="tab-menu">
		<li><a href="#referencesource-content-tab"><span><?php echo lang('referencesource.tab_content_label'); ?></span></a></li>
	</ul>
	<div class="form_inputs" id="referencesource-content-tab">
		<fieldset>
		    <ul>
				<li>
					<label for="name"><?php echo lang('referencesource.name_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_input('name', $referencesource->name);?></div>
				</li>
		    </ul>
	    </fieldset>
	</div>
</div>
<div class="buttons">
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))) ?>
</div>
</div>
<?php echo form_close();?>
</section>
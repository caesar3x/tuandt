<fieldset id="filters">
	<legend><?php echo lang('global:filters'); ?></legend>
	<?php echo form_open('', '', array('f_module' => $module_details['slug'])) ?>
		<ul>
			<li><?php echo lang('languagelevel:keywords', 'keywords'); ?><?php echo form_input('keywords'); ?></li>
			<li><br/><br/><?php echo anchor(current_url() . '#', lang('buttons:cancel'), 'class="cancel"'); ?></li>
		</ul>
	<?php echo form_close(); ?>
</fieldset>


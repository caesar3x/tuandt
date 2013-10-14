<div class="one_full">
	<section class="title">
		<h4><?php echo lang('filetype.list'); ?></h4>
	</section>

	<section class="item">
	<div class="content">
		<?php if ($filetypes) : ?>
		<?php echo $this->load->view('admin/partials/filters'); ?>
		<?php echo form_open('admin/filetype/action'); ?>
			<div id="filter-stage">
				<?php echo $this->load->view('admin/partials/filetypes'); ?>
			</div>
		<?php echo form_close(); ?>
		<?php else : ?>
			<div class="no_data"><?php echo lang('filetype.currently_no_items'); ?></div>
		<?php endif; ?>
	</div>
	</section>
</div>

<div class="one_full">
	<section class="title">
		<h4><?php echo lang('publicationtype.list'); ?></h4>
	</section>

	<section class="item">
	<div class="content">
		<?php if ($publicationtypes) : ?>
		<?php echo $this->load->view('admin/partials/filters'); ?>
		<?php echo form_open('admin/publicationtype/action'); ?>
			<div id="filter-stage">
				<?php echo $this->load->view('admin/partials/publicationtypes'); ?>
			</div>
		<?php echo form_close(); ?>
		<?php else : ?>
			<div class="no_data"><?php echo lang('publicationtype.currently_no_items'); ?></div>
		<?php endif; ?>
	</div>
	</section>
</div>

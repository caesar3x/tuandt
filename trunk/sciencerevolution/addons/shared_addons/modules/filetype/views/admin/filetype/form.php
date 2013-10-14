<?php if ($this->method == 'edit'): ?>
	<section class="title">
    	<h4><?php echo sprintf(lang('filetype.edit_title'), $filetype->file_type); ?></h4>
	</section>
<?php else: ?>
	<section class="title">
    	<h4><?php echo lang('filetype.add_title'); ?></h4>
	</section>
<?php endif; ?>

<section class="item">
<div class="content">
<?php echo form_open_multipart(uri_string(), 'class="crud"'); ?>
<div class="tabs">
	<ul class="tab-menu">
		<li><a href="#filetype-content-tab"><span><?php echo lang('filetype.tab_content_label'); ?></span></a></li>
	</ul>
	<div class="form_inputs" id="filetype-content-tab">
		<fieldset>
		    <ul>
				<li>
					<label for="file_type"><?php echo lang('filetype.file_type_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_input('file_type', $filetype->file_type);?></div>
				</li>
				<li>
					<label for="extension"><?php echo lang('filetype.extension_label');?> <span>*</span></label>
					<div class="input"><?php echo form_input('extension', $filetype->extension);?></div>
				</li>
				<li>
					<label for="icon"><?php echo lang('filetype.icon_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_upload(array('type' => 'file','name' => 'icon')); ?>
						<br/>
						<?php if($filetype->icon != null && trim($filetype->icon != "")):?>
							<img style="width:80px;height:40px" src="/uploads/<?php echo config_item('file_type:folder_name').'/'.$filetype->icon;?>"/>
						<?php endif;?>
					</div>
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
<?php if ($this->method == 'edit'): ?>
	<section class="title">
    	<h4><?php echo sprintf(lang('publicationtype.edit_title'), $publicationtype->name); ?></h4>
	</section>
<?php else: ?>
	<section class="title">
    	<h4><?php echo lang('publicationtype.add_title'); ?></h4>
	</section>
<?php endif; ?>

<section class="item">
<div class="content">
<?php echo form_open_multipart(uri_string(), 'class="crud"'); ?>
<div class="tabs">

	<ul class="tab-menu">
		<li><a href="#publicationtype-content-tab"><span><?php echo lang('publicationtype.tab_content_label'); ?></span></a></li>
	</ul>
	<div class="form_inputs" id="publicationtype-content-tab">
		<fieldset>
		    <ul>
				<li>
					<label for="name"><?php echo lang('publicationtype.name_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_input('name', $publicationtype->name);?></div>
				</li>
				<li>
					<label for="icon"><?php echo lang('publicationtype.icon_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_upload(array('type' => 'file','name' => 'icon')); ?>
						<br/>
						<?php if($publicationtype->icon != null && trim($publicationtype->icon != "")):?>
							<img style="width:80px;height:40px" src="/uploads/<?php echo config_item('publication_type:folder_name').'/'.$publicationtype->icon;?>"/>
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

<?php if ($this->method == 'edit'): ?>
	<section class="title">
    	<h4><?php echo sprintf(lang('category.edit_title'), $category->name); ?></h4>
	</section>
<?php else: ?>
	<section class="title">
    	<h4><?php echo lang('category.add_title'); ?></h4>
	</section>
<?php endif; ?>

<section class="item">
<div class="content">
<?php echo form_open_multipart(uri_string(), 'class="crud"'); ?>
<div class="tabs">

	<ul class="tab-menu">
		<li><a href="#category-content-tab"><span><?php echo lang('category.tab_content_label'); ?></span></a></li>
	</ul>
	<div class="form_inputs" id="category-content-tab">
		<fieldset>
		    <ul>
				<li>
					<label for="name"><?php echo lang('category.name_label');?>  <span>*</span></label>
					<div class="input"><?php echo form_input('name', $category->name);?></div>
				</li>
				<li>
					<label for="parent_id"><?php echo lang('category.parent_id_label');?> </label>
					<div class="input">
						<?php echo form_dropdown('parent_id',array('0'=>'Select Parent')+$parents , $category->parent_id) ?>
					</div>
				</li>
				<li class="<?php echo alternator('', 'even'); ?>">
					<label for="icon"><?php echo lang('category.icon_label'); ?> <span>*</span></label>
					<div class="input"><?php echo form_upload(array('type' => 'file','name' => 'icon')); ?>
						<br/>
						<?php if($category->icon != null && trim($category->icon != "")):?>
							<img style="width:80px;height:40px" src="/uploads/<?php echo config_item('category:folder_name').'/'.$category->icon;?>"/>
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
<table>
	<thead>
		<tr>
			<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')) ?></th>
			<th><?php echo lang('filetype.file_type_label'); ?></th>
			<th><?php echo lang('filetype.extension_label'); ?></th>
			<th class="collapse"><?php echo lang('filetype.created_on_label'); ?></th>
			<th width="180"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($filetypes as $filetype) : ?>
			<tr>
				<td><?php echo form_checkbox('action_to[]', $filetype->id) ?></td>
				<td><?php echo $filetype->file_type; ?></td>
				<td><?php echo $filetype->extension; ?></td>
				<td class="collapse"><?php echo format_date($filetype->created_on); ?></td>
				<td>
					<?php echo anchor('admin/filetype/edit/' . $filetype->id, lang('global:edit'), 'class="btn orange edit"'); ?>
					<?php echo anchor('admin/filetype/delete/' . $filetype->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php $this->load->view('admin/partials/pagination') ?>
<br>
<div class="table_action_buttons">
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))) ?>
</div>
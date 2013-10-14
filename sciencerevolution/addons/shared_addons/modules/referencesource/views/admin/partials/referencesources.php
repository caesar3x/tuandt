<table>
	<thead>
		<tr>
			<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')) ?></th>
			<th><?php echo lang('referencesource.name_label'); ?></th>
			<th class="collapse"><?php echo lang('referencesource.created_date_label'); ?></th>
			<th width="180"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($referencesources as $referencesource) : ?>
			<tr>
				<td><?php echo form_checkbox('action_to[]', $referencesource->id) ?></td>
				<td><?php echo $referencesource->name; ?></td>
				<td class="collapse"><?php echo format_date($referencesource->created_on); ?></td>
				<td>
					<?php echo anchor('admin/referencesource/edit/' . $referencesource->id, lang('global:edit'), 'class="btn orange edit"'); ?>
					<?php echo anchor('admin/referencesource/delete/' . $referencesource->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
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
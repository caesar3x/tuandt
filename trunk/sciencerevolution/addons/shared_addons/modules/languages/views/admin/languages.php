<section class="title">
    <h4><?php echo lang('languages:item_list'); ?></h4>
</section>
<section class="item">
    <div class="content">
    <?php echo form_open('admin/languages/delete');?>
    <?php if (!empty($languages)): ?>
        <table>
            <thead>
            <tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('languages:code'); ?></th>
                <th><?php echo lang('languages:name'); ?></th>
                <th><?php echo lang('languages:flag'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="5">
                    <div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <?php foreach( $languages as $language ): ?>
                <tr>
                    <td><?php echo form_checkbox('action_to[]', $language->id); ?></td>
                    <td><?php echo $language->code; ?></td>
                    <td><?php echo $language->name; ?></td>
                    <td><?php echo $language->flag; ?></td>
                    <td class="actions">
                        <?php echo
                            //    anchor('contact_reasons', lang('contact:view'), 'class="button" target="_blank"').' '.
                            anchor('admin/languages/edit/'.$language->id, lang('languages:edit'), 'class="button"').' '.
                            anchor('admin/languages/delete/'.$language->id, 	lang('languages:delete'), array('class'=>'button')); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table_action_buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
        </div>
    <?php else: ?>
        <div class="no_data"><?php echo lang('languages:no_items'); ?></div>
    <?php endif;?>
    <?php echo form_close(); ?>
    </div>
</section>
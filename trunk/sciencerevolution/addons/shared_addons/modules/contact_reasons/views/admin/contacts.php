<section class="title">
    <h4><?php echo lang('contact:item_list'); ?></h4>
</section>
<section class="item">
    <div class="content">
    <?php echo form_open('admin/contact_reasons/delete');?>
    <?php if (!empty($contacts)): ?>
        <table>
            <thead>
            <tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('contact:name'); ?></th>
            <!--    <th><?php echo lang('contact:created_on'); ?></th>
                <th><?php echo lang('contact:updated_on'); ?></th> -->
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
            <?php foreach( $contacts as $contact ): ?>
                <tr>
                    <td><?php echo form_checkbox('action_to[]', $contact->id); ?></td>
                    <td><?php echo $contact->name; ?></td>
                <!--    <td><?php echo $contact->name; ?></td>
                    <td><?php echo $contact->name; ?></td> -->
                    <td class="actions">
                        <?php echo
                        //    anchor('contact_reasons', lang('contact:view'), 'class="button" target="_blank"').' '.
                            anchor('admin/contact_reasons/edit/'.$contact->id, lang('contact:edit'), 'class="button"').' '.
                            anchor('admin/contact_reasons/delete/'.$contact->id, 	lang('contact:delete'), array('class'=>'button')); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table_action_buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
        </div>
    <?php else: ?>
        <div class="no_data"><?php echo lang('contact:no_items'); ?></div>
    <?php endif;?>
    <?php echo form_close(); ?>
    </div>
</section>
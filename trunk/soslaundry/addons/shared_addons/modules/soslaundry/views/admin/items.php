<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<section class="title">
    <h4><?php echo lang('soslaundry:item_list'); ?></h4>
</section>

<section class="item">
    <?php if (!empty($items)): ?>
        <table>
            <thead>
            <tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('soslaundry:name'); ?></th>
                <th><?php echo lang('soslaundry:phone'); ?></th>
                <th><?php echo lang('soslaundry:email'); ?></th>
                <th></th>
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
            <?php foreach( $items as $item ): ?>
                <tr>
                    <td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $item->phone; ?></td>
                    <td><?php echo $item->email; ?></td>
                    <td class="actions">
                        <?php echo
                            anchor('winner', lang('soslaundry:view'), 'class="button" target="_blank"').' '.
                            anchor('admin/winner/edit/'.$item->id, lang('soslaundry:edit'), 'class="button"').' '.
                            anchor('admin/winner/delete/'.$item->id, 	lang('soslaundry:delete'), array('class'=>'button')); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="table_action_buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
        </div>

    <?php else: ?>
        <div class="no_data"><?php echo lang('soslaundry:no_items'); ?></div>
    <?php endif;?>

    <?php echo form_close(); ?>
</section>
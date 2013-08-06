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
                <th><?php echo lang('soslaundry:first_name'); ?></th>
                <th><?php echo lang('soslaundry:last_name'); ?></th>
                <th><?php echo lang('soslaundry:phone'); ?></th>
                <th><?php echo lang('soslaundry:email'); ?></th>
                <th><?php echo lang('soslaundry:register_on'); ?></th>
                <th><?php echo lang('soslaundry:is_winner'); ?></th>
                <th><?php echo lang('soslaundry:winner_on'); ?></th>
                <th><?php echo lang('soslaundry:hotel_chosen'); ?></th>
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
                    <td><?php echo $item->first_name; ?></td>
                    <td><?php echo $item->last_name; ?></td>
                    <td><?php echo $item->phone; ?></td>
                    <td><?php echo $item->email; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$item->register_on); ?></td>
                    <td><?php echo ((int)$item->is_winner == 1) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($item->winner_on != null && $item->winner_on != 0) ? date('Y-m-d H:i:s',$item->winner_on) : ''; ?></td>
                    <td><?php echo (getHotelName($item->hotel) != null) ? getHotelName($item->hotel)->name : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no_data"><?php echo lang('soslaundry:no_items'); ?></div>
    <?php endif;?>

    <?php echo form_close(); ?>
</section>
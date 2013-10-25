<section class="title">
    <h4><?php echo lang('news:item_list'); ?></h4>
</section>
<section class="item">
    <div class="content">
    <?php echo form_open('admin/news/delete');?>
    <?php if (!empty($news)): ?>
        <table>
            <thead>
            <tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('news:title'); ?></th>
                <th><?php echo lang('news:sub_header'); ?></th>
                <th><?php echo lang('news:thumbnail'); ?></th>
                <th><?php echo lang('news:content'); ?></th>
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
            <?php foreach( $news as $new ): ?>
                <tr>
                    <td><?php echo form_checkbox('action_to[]', $new->id); ?></td>
                    <td><?php echo $new->title; ?></td>
                    <td><?php echo $new->sub_header; ?></td>
                    <td><?php echo $new->thumbnail; ?></td>
                    <td><?php echo $new->content; ?></td>
                    <td class="actions">
                        <?php echo
                            //    anchor('contact_reasons', lang('contact:view'), 'class="button" target="_blank"').' '.
                            anchor('admin/news/edit/'.$new->id, lang('news:edit'), 'class="button"').' '.
                            anchor('admin/news/delete/'.$new->id, 	lang('news:delete'), array('class'=>'button')); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table_action_buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
        </div>
    <?php else: ?>
        <div class="no_data"><?php echo lang('news:no_items'); ?></div>
    <?php endif;?>
    <?php echo form_close(); ?>
    </div>
</section>
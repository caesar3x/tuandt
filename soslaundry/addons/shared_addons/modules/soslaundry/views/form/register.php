<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/4/13
 */
?>
<header>
    <h2><?php echo lang('soslaundry:form_title'); ?></h2>
</header>
<form action="<?php echo base_url('winner/form');?>" enctype="multipart/form-data" accept-charset="utf-8" method="post">

    <div><label class="desc" for="Field1" id="title1"><?php echo lang('soslaundry:name'); ?></label>

        <div><input class="field text fn" id="Field1" name="full_name" size="8" tabindex="1" type="text" value="" /></div>
    </div>

    <div>
        <label class="desc" for="Field3" id="title3"><?php echo lang('soslaundry:email'); ?> </label>
        <div><input id="Field3" maxlength="255" name="email" spellcheck="false" tabindex="3" type="email" value="" /></div>
    </div>
    <div>
        <label class="desc" for="Field9" id="title9"><?php echo lang('soslaundry:phone'); ?> </label>
        <div><input id="Field9" maxlength="255" name="phone" spellcheck="false" tabindex="3" type="text" value="" /></div>
    </div>
    <div>
        <label class="desc" for="Field106" id="title106"><?php echo lang('soslaundry:choose_hotel'); ?></label>

        <div><select class="field select medium" id="Field106" name="hotel" tabindex="11">
                <option value="0"><?php echo lang('soslaundry:choose_hotel'); ?></option>
                <?php if(!empty($hotels)):?>
                <?php foreach($hotels as $hotel):?>
                <option value="<?php echo $hotel->id;?>"><?php echo $hotel->name;?></option>
                <?php endforeach;?>
                <?php endif;?>
            </select></div>
    </div>

    <div>
        <div><input id="saveForm" name="saveForm" type="submit" value="<?php echo lang('soslaundry:submit_label'); ?>" /></div>
    </div>
</form>

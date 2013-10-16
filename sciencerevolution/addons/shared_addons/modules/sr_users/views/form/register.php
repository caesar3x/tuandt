<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
?>
<div class="container">
    <h2 class="page-title"><span><?php echo lang('sign_up');?></span></h2>
    <div class="row-fluid">
        <div class="col-main span9">
            <div class="page-content">
                <div class="row-fluid">
                    <div class="span12">
                        <a href="{{url:site}}signup/company" class="button large left" title="Search" ><span><?php echo lang('signup_as_company')?></span></a>
                        <div class="span1">&nbsp;</div>
                        <a href="{{url:site}}signup/personal" class="button large left" title="Search" ><span><?php echo lang('signup_as_personal')?></span></a>
                    </div>
                </div>
            </div>
        </div>
        {{ theme:partial name="block-quicklinks" }}
    </div>
</div>
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/29/13
 */
return array(
    'guest' => array(
        'application\index\index',
        'application\index\test',
        'application\admin\index',
    ),
    'editor'=> array(
        'application\login\index',
        'application\login\auth',
        'application\index\index',
        'application\logout\index',
        'application\admin\index',
        'application\admin\add',
        'application\admin\edit',
    ),
    'admin'=> array(
        'application\admin\delete'
    ),
);
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/29/13
 */
return array(
    'guest' => array(
        'application\index\index',
        'backend\login\index',
        'backend\login\auth',
        'backend\logout\index'
    ),
    'editor'=> array(
        'backend\login\index',
        'backend\login\auth',
        'backend\index\index',
        'backend\logout\index',
    ),
    'admin'=> array(
        'backend\admin\index',
        'backend\admin\add',
        'backend\admin\edit',
        'backend\admin\delete',
        'backend\term\index',
        'backend\term\add',
        'backend\term\edit',
        'backend\term\delete',
    ),
);
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/29/13
 */
return array(
    'guest' => array(
        'application\index\index',
        'application\login\index',
        'application\login\auth',
        'application\logout\index'
    ),
    'editor'=> array(
        'backend\login\index',
        'backend\login\auth',
        'backend\index\index',
        'backend\logout\index',
        'backend\page\index',
        'backend\page\add',
        'backend\page\edit',
        'backend\page\delete',
        'backend\post\index',
        'backend\post\add',
        'backend\post\edit',
        'backend\post\delete',
    ),
    'admin'=> array(
        'backend\admin\index',
        'backend\admin\add',
        'backend\admin\edit',
        'backend\admin\delete',
        'backend\term\index',
        'backend\term\add-category',
        'backend\term\edit-category',
        'backend\term\delete',
        'backend\term\category',
        'backend\term\save',
        'backend\term\tag',
        'backend\term\add',
        'backend\term\edit',
    ),
);
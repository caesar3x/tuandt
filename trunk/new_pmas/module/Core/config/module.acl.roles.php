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
        'application\admin\users',
        'application\recycler\index',
        'application\recycler\add',
        'application\recycler\detail',
        'application\model\index',
        'application\model\add',
        'application\model\detail',
        'application\exchange\index',
        'application\exchange\update',
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
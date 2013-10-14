<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Category module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Category
 * @created 9/17/2013
 */
class Module_Category extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Category Management',
			),
			'description' => array(
				'en' => 'Module for management category.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			//'menu' => 'general-gettings',
			'roles' => array(
				'index', 'edit', 'delete'
			),
			'sections' => array(
				'category' => array(
					'name' => 'category.list',
					'uri' => 'admin/category',
					'shortcuts' => array(
						array(
						    'name' => 'category.add',
						    'uri' => 'admin/category/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}
	public function install()
	{
		return true;
	}

	public function uninstall()
	{
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}
	public function admin_menu(&$menu)
	{
		add_admin_menu_place('General Settings', 2);
	    $menu['General Settings'] = array(
	        'Category Management'      => 'admin/category'
	    );
	}
}
/* End of file details.php */

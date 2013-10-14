<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Languagelevel module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Languagelevel
 * @created 9/22/2013
 */
class Module_Languagelevel extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Language level',
			),
			'description' => array(
				'en' => 'Module for Language level.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			//'menu' => 'content',
			'roles' => array(
				'index', 'edit', 'delete'
			),
			'sections' => array(
				'languagelevel' => array(
					'name' => 'languagelevel.list',
					'uri' => 'admin/languagelevel',
					'shortcuts' => array(
						array(
						    'name' => 'languagelevel.add',
						    'uri' => 'admin/languagelevel/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->db->delete('settings', array('module' => 'languagelevel'));
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
		//add_admin_menu_place('General Settings', 2);
	    $menu['General Settings']['Language Level'] = 'admin/languagelevel';
	}
}
/* End of file details.php */

<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Referencesource module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Referencesource
 * @created 9/22/2013
 */
class Module_Referencesource extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Reference source management',
			),
			'description' => array(
				'en' => 'Module for management reference source.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			//'menu' => 'content',
			'roles' => array(
				'index', 'edit', 'delete'
			),
			'sections' => array(
				'referencesource' => array(
					'name' => 'referencesource.list',
					'uri' => 'admin/referencesource',
					'shortcuts' => array(
						array(
						    'name' => 'referencesource.add',
						    'uri' => 'admin/referencesource/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->db->delete('settings', array('module' => 'referencesource'));
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
	    $menu['General Settings']['Reference Source'] = 'admin/referencesource';
	}
}
/* End of file details.php */

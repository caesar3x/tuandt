<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Publicationtype module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Publicationtype
 * @created 9/22/2013
 */
class Module_Publicationtype extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Publication type management',
			),
			'description' => array(
				'en' => 'Module for management publication type.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			//'menu' => 'content',
			'roles' => array(
				'index','edit', 'delete'
			),
			'sections' => array(
				'publicationtype' => array(
					'name' => 'publicationtype.list',
					'uri' => 'admin/publicationtype',
					'shortcuts' => array(
						array(
						    'name' => 'publicationtype.add',
						    'uri' => 'admin/publicationtype/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->db->delete('settings', array('module' => 'publicationtype'));
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
	    $menu['General Settings']['Publication type'] = 'admin/publicationtype';
	}
}
/* End of file details.php */

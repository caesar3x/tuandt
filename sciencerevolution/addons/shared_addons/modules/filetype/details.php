<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Filetype module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Filetype
 * @created 9/22/2013
 */
class Module_Filetype extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'File type',
			),
			'description' => array(
				'en' => 'Module for management file type.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			//'menu' => 'content',
			'roles' => array(
				'index', 'edit', 'delete'
			),
			'sections' => array(
				'filetype' => array(
					'name' => 'filetype.list',
					'uri' => 'admin/filetype',
					'shortcuts' => array(
						array(
						    'name' => 'filetype.add',
						    'uri' => 'admin/filetype/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->db->delete('settings', array('module' => 'filetype'));
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
	    $menu['General Settings']['File Type'] = 'admin/filetype';
	}
}
/* End of file details.php */

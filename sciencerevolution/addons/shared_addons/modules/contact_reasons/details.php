<?php defined('BASEPATH') or exit('No direct script access allowed');
class Module_Contact_Reasons extends Module
{
    public $version = '1.0.0';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Contact Reasons'
            ),
            'description' => array(
                'en' => 'Module for Contact Reasons'
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'sections' => array(
                'contact_reasons' => array(
                    'name' => 'contact:list',
                    'uri' => 'admin/contact_reasons',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'contact:create',
                            'uri' => 'admin/contact_reasons/create',
                            'class' => 'add'
                        )
                    )
                )
            )
        );
    }

    public function install(){
        $this->db->delete('settings', array('module' => 'sr_contact_reasons'));

        $contact_setting = array(
            'slug' => 'contact_setting',
            'title' => 'Contact Reasons Setting',
            'description' => 'A Yes or No option for the Contact module',
            '`default`' => '1',
            '`value`' => '1',
            'type' => 'select',
            '`options`' => '1=Yes|0=No',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'contact_reasons'
        );

        if($this->db->insert('settings', $contact_setting) AND
            is_dir($this->upload_path.'contact_reasons') OR @mkdir($this->upload_path.'contact_reasons',0777,TRUE))
        {
            return true;
        }
    }

    public function uninstall()
    {
        $this->db->delete('settings', array('module' => 'contact_reasons'));
        {
            return TRUE;
        }
    }

    public function admin_menu(&$menu)
    {
        $menu['General Settings']['Contact Reasons'] = 'admin/contact_reasons';
    }

    public function upgrade($old_version)
    {
        return TRUE;
    }

    public function help()
    {
        return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
    }
}

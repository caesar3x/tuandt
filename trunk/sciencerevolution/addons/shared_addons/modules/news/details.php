<?php defined('BASEPATH') or exit('No direct script access allowed');
class Module_News extends Module
{
    public $version = '1.0.0';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'News'
            ),
            'description' => array(
                'en' => 'Module for News'
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'sections' => array(
                'news' => array(
                    'name' => 'news:list',
                    'uri' => 'admin/news',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'news:create',
                            'uri' => 'admin/news/create',
                            'class' => 'add'
                        )
                    )
                )
            )
        );
    }

    public function install(){

       
        $news_setting = array(
            'slug' => 'news_setting',
            'title' => 'News Setting',
            'description' => 'A Yes or No option for the Contact module',
            '`default`' => '1',
            '`value`' => '1',
            'type' => 'select',
            '`options`' => '1=Yes|0=No',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'news'
        );


        if($this->db->insert('settings', $news_setting) AND
            is_dir($this->upload_path.'news') OR @mkdir($this->upload_path.'news',0777,TRUE))
        {
            return true;
        }
    }

    public function uninstall()
    {
        $this->db->delete('settings', array('module' => 'news'));
        {
            return TRUE;
        }
    }

    public function admin_menu(&$menu)
    {
        $menu['General Settings']['News'] = 'admin/news';
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

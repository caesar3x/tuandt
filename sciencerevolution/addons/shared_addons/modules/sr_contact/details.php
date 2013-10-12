<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php defined('BASEPATH') or exit('No direct script access allowed');
class Module_Sr_contact extends Module {

    public $version = '1.0.1';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Science Revolution Contact'
            ),
            'description' => array(
                'en' => 'Science Revolution Contact Module.'
            ),
            'frontend' => TRUE,
            'backend' => FALSE,
        );
    }

    public function install()
    {
        return TRUE;
    }

    public function uninstall()
    {
        return TRUE;
    }


    public function upgrade($old_version)
    {
        // Your Upgrade Logic
        return TRUE;
    }

    public function help()
    {
        // Return a string containing help info
        // You could include a file and return it here.
        return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
    }
}
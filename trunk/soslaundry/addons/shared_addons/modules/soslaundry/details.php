<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php defined('BASEPATH') or exit('No direct script access allowed');
class Module_Soslaundry extends Module {

    public $version = '1.0.1';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Soslaundry'
            ),
            'description' => array(
                'en' => 'Soslaundry module.'
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'menu' => 'content',
            'sections' => array(
                'items' => array(
                    'name' 	=> 'soslaundry:item_list',
                    'uri' 	=> 'admin/soslaundry',
                    'shortcuts' => array(
                        'create' => array(
                            'name' 	=> 'soslaundry:create',
                            'uri' 	=> 'admin/soslaundry/create',
                            'class' => 'add'
                        )
                    )
                )
            )
        );
    }

    public function install()
    {
        $this->dbforge->drop_table('winner');
        $this->db->delete('settings', array('module' => 'soslaundry'));

        $winner = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'is_winner' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'register_on' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'winner_on' => array(
                'type' => 'INT',
                'constraint' => '11'
            )
        );

        $winner_setting = array(
            'slug' => 'soslaundry_setting',
            'title' => 'Soslaundry Setting',
            'description' => 'Number of winner',
            '`default`' => '1',
            '`value`' => '1',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'soslaundry'
        );

        $this->dbforge->add_field($winner);
        $this->dbforge->add_key('id', TRUE);

        if($this->dbforge->create_table('winner') AND
            $this->db->insert('settings', $winner_setting) AND
            is_dir($this->upload_path.'winner') OR @mkdir($this->upload_path.'soslaundry',0777,TRUE))
        {
            return TRUE;
        }
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('winner');
        $this->db->delete('settings', array('module' => 'soslaundry'));
        {
            return TRUE;
        }
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
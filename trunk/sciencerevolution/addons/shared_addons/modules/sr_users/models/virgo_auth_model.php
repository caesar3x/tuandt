<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Virgo_auth_model extends CI_Model
{
    /**
     * Holds an array of tables used
     *
     * @var array
     **/
    public $table = 'sr_user';

    /**
     * activation code
     *
     * @var string
     **/
    public $activation_code;

    /**
     * forgotten password key
     *
     * @var string
     **/
    public $forgotten_password_code;

    /**
     * new password
     *
     * @var string
     **/
    public $new_password;

    /**
     * Identity
     *
     * @var string
     **/
    public $identity;

    /**
     * Identity column
     *
     * @var string
     **/
    public $identity_column = 'username';
    /**
     * Where
     *
     * @var array
     **/
    public $_auth_where = array();

    /**
     * Select
     *
     * @var array
     **/
    public $_auth_select = array();

    /**
     * Like
     *
     * @var array
     **/
    public $_auth_like = array();

    /**
     * Response
     *
     * @var string
     **/
    protected $response = NULL;

    /**
     * message (uses lang file)
     *
     * @var string
     **/
    protected $messages;

    /**
     * error message (uses lang file)
     *
     * @var string
     **/
    protected $errors;

    /**
     * error start delimiter
     *
     * @var string
     **/
    protected $error_start_delimiter;

    /**
     * error end delimiter
     *
     * @var string
     **/
    protected $error_end_delimiter;

    /**
     * caching of users and their groups
     *
     * @var array
     **/
    public $_cache_user_in_group = array();

    /**
     * caching of groups
     *
     * @var array
     **/
    protected $_cache_groups = array();

    /**
     * @var array
     */
    protected $users = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('cookie');
        $this->load->helper('date');
        /*$this->lang->load('virgo_auth');*/
        $this->lang->load('sr_users/virgo_auth');
        $this->load->model('sr_users/sr_user_m');
        $this->users = $this->sr_user_m->get_all_active_users();
        //initialize messages and error
        $this->messages    = array();
        $this->errors      = array();
    }
    public function username_check($username)
    {
        if (empty($username))
        {
            return false;
        }

        echo $this->db->where('username', $username)
            ->count_all_results($this->table) > 0;
    }
    /**
     * Basic functionality
     *
     * Register
     * Login
     *
     * @author Mathew
     */

    // --------------------------------------------------------------------------

    /**
     * register
     *
     * @return bool
     * @author Mathew
     **/
    public function register($username, $password)
    {
        if($this->username_check($username)){
            $this->set_error('account_creation_duplicate_username');
            return false;
        }

        // Users table.
        $data = array(
            'username'   => $username,
            'password'   => $password,
            'user_type'   => 'personal',
            'created_on' => time(),
            'updated_on' => time(),
            'status' => 'new',
            'profile_id'     => 1,
            'verification_code'     => md5($username),
            'sr_private_profile_id'     => 2,
            'sr_company_profile_id'     => 1,
        );

        return $this->db->insert($this->table, $data);

        // For the profiles tables.
        if ($this->db->dbdriver == 'mysql')
        {
            $last = $this->db->query("SELECT LAST_INSERT_ID() as last_id")->row();
            $id = $last->last_id;
        }
        else
        {
            $id = $this->db->insert_id();
        }

        // Use streams to add the profile data.
        // Even if there is not data to add, we still want an entry
        // for the profile data.
        if ( ! class_exists('Streams'))
        {
            $this->load->driver('Streams');
        }

        // This is the profile data that we are not running through streams
        $extra = array(
            'user_id'			=> $id,
            'display_name' 		=> $additional_data['display_name']
        );

        if ($this->streams->entries->insert_entry($additional_data, 'profiles', 'users', array(), $extra))
        {
            return $id;
        }
        else
        {
            return false;
        }
    }

    // --------------------------------------------------------------------------
    /**
     * set_error
     *
     * Set an error message
     *
     * @return void
     * @author Ben Edmunds
     **/
    public function set_error($error)
    {
        $this->errors[] = lang($error);

        return $error;
    }
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * @param $username
     * @param $password
     * @param $remember
     * @return bool
     */
    public function login($username,$password,$remember = false)
    {
        if (empty($username) || empty($password))
        {
            return false;
        }
        $query = $this->db->where('status', 'activated')
            ->limit(1)
            ->get($this->table);
        $user = $query->row();
        if ($query->num_rows() == 1)
        {
            if($user->password === $password){
                $this->_set_login($user, $remember);
                return true;
            }
        }
        return false;
    }
    // --------------------------------------------------------------------------

    public function _set_login($user, $remember)
    {
        $this->update_last_login($user->id);

        $this->session->set_userdata(array('current_sr_user' => array(
            'username' 			   => $user->username,
            'id'                   => $user->id, //kept for backwards compatibility
            'user_id'              => $user->id, //everyone likes to overwrite id so we'll use user_id
            'profile_id'           => $user->profile_id,
            'sr_private_profile_id'           => $user->sr_private_profile_id,
            'sr_company_profile_id'           => $user->sr_company_profile_id,
            'user_type'           => $user->user_type,
        )));

        if ($remember)
        {
            $this->remember_user($user->id);
        }
    }

    // --------------------------------------------------------------------------
    public function update_last_login($id)
    {

        $this->db->update($this->table, array('updated_on' => time()), array('id' => $id));

        return $this->db->affected_rows() == 1;
    }
    private function remember_user($id)
    {
        if (!$id)
        {
            return false;
        }

        $user = $this->get_user_by_username($id)->row();

        $salt = sha1($user->password);

        if ($this->db->affected_rows() > -1)
        {
            set_cookie(array(
                'name'   => 'identity',
                'value'  => $user->{$this->identity_column},
                'expire' => 3600,
            ));

            set_cookie(array(
                'name'   => 'remember_code',
                'value'  => $salt,
                'expire' => 3600,
            ));

            return true;
        }

        return false;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function get_user_by_username($username)
    {
        $query = $this->db->where($this->table.'.username', $username)->limit(1)->get($this->table);
        return $query;
    }

    // --------------------------------------------------------------------------
    public function get_current_sr_user()
    {
        $current_sr_user = $this->session->userdata('current_sr_user');
        if(!$current_sr_user){
            return null;
        }
        $id = $current_sr_user['id'];
        if(is_numeric($id)){
            return $this->sr_user_m->get_user($id);
        }
        return null;
    }
}
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

    public $private_profile_table = 'sr_private_profile';
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
        $this->load->model('sr_users/sr_private_profile_m');
        $this->load->model('sr_users/sr_company_profile_m');
        $this->load->model('sales/sr_private_billing_address_m');
        $this->load->model('sales/sr_company_billing_address_m');
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

        return $this->db->where('username', $username)->count_all_results($this->table) > 0;
    }
    public function email_check($email)
    {
        if (empty($email))
        {
            return false;
        }

        return $this->db->where('email', $email)->count_all_results($this->private_profile_table) > 0;
    }
    /**
     * Basic functionality
     *
     * Register
     * Login
     *
     * @author datnguyen.cntt@gmail.com
     */

    // --------------------------------------------------------------------------
    public function register_company($input)
    {
        if(empty($input)){
            return false;
        }
        if (version_compare(phpversion(), '5.3.0', '<')===true) {
            $dob = DateTime::createFromFormat('m/d/Y',$input['representative_date_of_birth']);
            if($dob){
                $dobTime = $dob->getTimestamp();
            }else{
                $dobTime = time();
            }
        }else{
            $dobTime = strtotime($input['representative_date_of_birth']);
        }
        $company_profile_data = array(
            'representative_name' => $input['representative_name'],
            'representative_surname' => $input['representative_surname'],
            'representative_email' => $input['representative_email'],
            'representative_phone_number' => $input['representative_phone_number'],
            'representative_date_of_birth' => $dobTime,
            'company_name' => $input['company_name'],
            'company_tax_number' => $input['company_tax_number'],
            'company_street_address' => $input['company_street_address'],
            'company_postcode' => $input['company_postcode'],
            'company_city' => $input['company_city'],
            'company_state' => $input['company_state'],
            'company_country' => $input['company_country'],
            'created_on' => time(),
            'updated_on' => time(),
        );
        try {
            $id = $this->sr_company_profile_m->insert($company_profile_data);
        } catch (Exception $e) {
            log_message('error',$e->getMessage());
        }
        /**
         * Billing address
         */
        if(isset($input['billing:country'])){
            $billing_data = array(
                'company_profile_id' => $id,
                'street' => $input['billing:street'],
                'city' => $input['billing:city'],
                'state' => $input['billing:state'],
                'country' => $input['billing:country'],
                'postal_code' => $input['billing:postcode'],
            );
        }else{
            $billing_data = array(
                'company_profile_id' => $id,
                'street' => $input['company_street_address'],
                'postal_code' => $input['company_postcode'],
                'city' => $input['company_city'],
                'state' => $input['company_state'],
                'country' => $input['company_country'],
            );
        }
        if(!empty($billing_data)){
            try {
                $this->sr_company_billing_address_m->insert($billing_data);
            } catch (Exception $e) {
                log_message('error',$e->getMessage());
            }
        }
        if($id){
            // Users table.
            $data = array(
                'username'   => $input['username'],
                'password'   => $input['password'],
                'user_type'   => $input['user_type'],
                'created_on' => time(),
                'updated_on' => time(),
                'status' => 'new',
                'profile_id'     => $id,
                'verification_code'     => md5($input['username']),
            );
            try {
                $this->sr_user_m->insert($data);
            } catch (Exception $e) {
                log_message('error',$e->getMessage());
            }
            if($this->db->affected_rows()){
                /**
                 * Activate user
                 */
                $this->send_activate_email($id);
            }
            return $this->db->affected_rows() == 1;
        }else{
            return false;
        }
    }
    /**
     * register
     *
     * @return bool
     * @author datnguyen.cntt@gmail.com
     **/
    public function register($input)
    {
        if(empty($input)){
            return false;
        }
        if (version_compare(phpversion(), '5.3.0', '<')===true) {
            $dob = DateTime::createFromFormat('m/d/Y',$input['dob']);
            if($dob){
                $dobTime = $dob->getTimestamp();
            }else{
                $dobTime = time();
            }
        }else{
            $dobTime = strtotime($input['dob']);
        }
        $private_profile_data = array(
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'tax_number' => $input['tax_number'],
            'job_title' => $input['job_title'],
            'date_of_birth' => $dobTime,
            'email' => $input['email'],
            'phone_number' => $input['phone'],
            'street' => $input['street'],
            'city' => $input['city'],
            'state' => $input['state'],
            'country' => $input['country'],
            'postcode' => $input['postcode'],
            'created_on' => time(),
            'updated_on' => time(),
        );
        try {
            $id = $this->sr_private_profile_m->insert($private_profile_data);
        } catch (Exception $e) {
            log_message('error',$e->getMessage());
        }
        /**
         * Billing address
         */
        if(isset($input['billing:country'])){
            $billing_data = array(
                'profile_id' => $id,
                'street' => $input['billing:street'],
                'city' => $input['billing:city'],
                'state' => $input['billing:state'],
                'country' => $input['billing:country'],
                'postal_code' => $input['billing:postcode'],
            );
        }else{
            $billing_data = array(
                'profile_id' => $id,
                'street' => $input['street'],
                'city' => $input['city'],
                'state' => $input['state'],
                'country' => $input['country'],
                'postal_code' => $input['postcode'],
            );
        }
        if(!empty($billing_data)){
            try {
                $this->sr_private_billing_address_m->insert($billing_data);
            } catch (Exception $e) {
                log_message('error',$e->getMessage());
            }
        }
        if($id){
            // Users table.
            $data = array(
                'username'   => $input['username'],
                'password'   => $input['password'],
                'user_type'   => $input['user_type'],
                'created_on' => time(),
                'updated_on' => time(),
                'status' => 'new',
                'profile_id'     => $id,
                'verification_code'     => md5($input['username']),
            );
            try {
                $this->sr_user_m->insert($data);
            } catch (Exception $e) {
                log_message('error',$e->getMessage());
            }
            if($this->db->affected_rows()){
                /**
                 * Activate user
                 */
                $this->send_activate_email($id);
            }
            return $this->db->affected_rows() == 1;
        }else{
            return false;
        }
    }

    // --------------------------------------------------------------------------
    public function send_activate_email($id)
    {
        /**
         * Send email
         */
        return true;
    }
    // --------------------------------------------------------------------------
    /**
     * Process activate
     * @return bool
     */
    public function activate($user_id,$code)
    {
        if(!$user_id || !$code){
            return false;
        }

        $user = $this->sr_user_m->get_user($user_id);
        if(!empty($user)){
            $verification_code = $user->verification_code;
            if($verification_code == $code){
                $update = $this->sr_user_m->update_status($user_id,'activated');
                if($update){
                    $this->send_activate_success_email($user_id);
                }
                return $update;
            }else{
                return false;
            }
        }
        return true;
    }
    // --------------------------------------------------------------------------
    public function send_activate_success_email($user_id)
    {
        return true;
    }
    // --------------------------------------------------------------------------
    /**
     * set_error
     *
     * Set an error message
     *
     * @return void
     * @author datnguyen.cntt@gmail.com
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

        $user = $this->get_user_by_id($id);

        $salt = sha1($user->password);

        if ($this->db->affected_rows() > -1)
        {
            set_cookie(array(
                'name'   => 'identity',
                'value'  => $user->{$this->identity_column},
                'expire' => 86400,
            ));

            set_cookie(array(
                'name'   => 'remember_code',
                'value'  => $salt,
                'expire' => 86400,
            ));

            return true;
        }

        return false;
    }
    public function get_user_by_id($id)
    {
        $user = $this->sr_user_m->get_user($id);
        return $user;
    }
    /**
     * @param $username
     * @return mixed
     */
    public function get_user_by_username($username)
    {
        $user = $this->sr_user_m->get_user_by_username($username);
        return $user;
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
    public function logout()
    {
        $this->session->unset_userdata('current_sr_user');
        if (get_cookie('remember_code'))
        {
            delete_cookie('remember_code');
        }
        $this->session->sess_regenerate(true);
        return true;
    }

}
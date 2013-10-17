<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/12/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_users extends Standard_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sr_user_m');
        $this->load->model('sr_private_profile_m');
        $this->load->model('sr_users/virgo_auth_model');
        $this->lang->load('users');
        $this->load->helper('virgo');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
    }
    public function index()
    {
        die('user index ');
    }
    public function login()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        $params = $this->input->post();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('form/login');
        }else{
            if($this->virgo_auth_model->login($params['username'],$params['password'])){
                redirect('?success');
            }
        }
        /**========================================================**/
        $this->template
            ->set_layout('login.html')
            ->title('Login')
            ->build('form/login');
    }
    public function logout()
    {
        if($this->virgo_auth_model->logout()){
            $this->session->set_flashdata('flass_messages', lang('logout_success'));
        }
        redirect('/');
    }
    public function profile()
    {
        $this->template
            ->set_layout('profile.html')
            ->title('User Profile')
            ->build('profile/index');
    }
    public function dashboard()
    {
        $this->template
            ->set_layout('profile.html')
            ->title('User Profile')
            ->build('profile/index');
    }
    public function register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }

        /**========================================================**/
        $this->template
            ->set_layout('signup.html')
            ->title('Sign up')
            ->build('form/register');
    }
    public function personal_register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        $this->load->model('sr_users/virgo_auth_model');
        $params = $this->input->post();
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]|callback_check_username');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('form/register');
        }else{
            if($this->virgo_auth_model->register($params['username'],$params['password'])){
                $this->session->set_flashdata('flash_messages', array(lang('login_success')));
                redirect('?success');
            }
        }
        /**========================================================**/
        $this->template
            ->set_layout('signup.html')
            ->title('Sign up')
            ->build('form/personal');
    }
    public function company_register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        $this->load->model('sr_users/virgo_auth_model');
        $params = $this->input->post();
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]|callback_check_username');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('form/register');
        }else{
            if($this->virgo_auth_model->register($params['username'],$params['password'])){
                redirect('?success');
            }
        }
        /**========================================================**/
        $this->template
            ->set_layout('signup.html')
            ->title('Sign up')
            ->build('form/company');
    }
    public function check_username($username)
    {
        $this->load->model('sr_users/virgo_auth_model');
        if(!$this->virgo_auth_model->username_check($username)){
            $this->form_validation->set_message('check_username', 'Username existed.');
            return false;
        }
        return true;
    }
}
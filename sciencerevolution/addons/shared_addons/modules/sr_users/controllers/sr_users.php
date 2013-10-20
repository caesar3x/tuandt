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
        $this->load->helper('email');
        $this->lang->load('users');
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
            $remember = isset($params['remember_me']) ? true : false;
            if($this->virgo_auth_model->login($params['username'],$params['password'],$remember)){
                $this->session->set_flashdata('flash_messages',array('success' => array(lang('You have been login success'))));
                redirect('/');
            }else{
                $this->session->set_flashdata('flash_messages',array('error' => array(lang('Invalid username or password.'))));
                redirect('/login');
            }
        }
        /**========================================================**/
        $this->template
            ->title('Login')
            ->build('form/login');
    }
    public function logout()
    {
        if($this->virgo_auth_model->logout()){
            $this->session->set_flashdata('flash_messages',array('success' => array(lang('You have been logout success'))));
        }
        redirect('/');
    }
    public function profile()
    {
        if(!is_sr_user_loggin()){
            /*show_404();*/
            redirect(site_url('login'));
        }
        $this->template
            ->title('User Profile')
            ->build('profile/index');
    }
    public function dashboard()
    {
        if(!is_sr_user_loggin()){
            redirect(site_url('login'));
        }
        $this->template
            ->title('User Profile')
            ->build('dashboard/index');
    }
    public function register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        $billing_check = false;
        $this->load->model('sr_users/virgo_auth_model');
        $params = $this->input->post();
        $this->form_validation->set_rules('first_name', lang('First name'), 'required');
        $this->form_validation->set_rules('last_name', lang('Last Name'), 'required');
        $this->form_validation->set_rules('username', lang('Username'), 'required|min_length[5]|max_length[12]|callback_check_username');
        $this->form_validation->set_rules('password', lang('Password'), 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', lang('Confirm Password'), 'required');
        $this->form_validation->set_rules('email', lang('Email Address'), 'required|valid_email|callback_check_email');

        $this->form_validation->set_rules('dob', lang('Date of Birth'), 'required');
        $this->form_validation->set_rules('phone', lang('Phone Number'), 'required');
        $this->form_validation->set_rules('job_title', lang('Job Title'), 'required');
        $this->form_validation->set_rules('street', lang('Street Name & Number'), 'required');
        $this->form_validation->set_rules('postcode', lang('Postal(zip) code'), 'required');
        $this->form_validation->set_rules('city', lang('City'), 'required');
        $this->form_validation->set_rules('state', lang('State'), 'required');
        $this->form_validation->set_rules('country', lang('Country'), 'required');

        $billing = (isset($params['billing:residental'])) ? $params['billing:residental'] : null;
        $billing_check = (isset($params['billing:residental'])) ? true : false;
        $this->form_validation->set_rules('billing:street',lang('Billing Street address'),'callback_valid_billing_street['.$billing.']');
        $this->form_validation->set_rules('billing:city',lang('Billing City'),'callback_valid_billing_city['.$billing.']');
        $this->form_validation->set_rules('billing:state',lang('Billing State'),'callback_valid_billing_state['.$billing.']');
        $this->form_validation->set_rules('billing:country',lang('Billing Country'),'callback_valid_billing_country['.$billing.']');
        $this->form_validation->set_rules('billing:postcode',lang('Billing Postcode'),'callback_valid_billing_postcode['.$billing.']');
        if ($this->form_validation->run() == FALSE)
        {
            /**========================================================**/
            $this->template
                ->title('Sign up')
                ->set('billing_check',$billing_check)
                ->build('form/register');
        }else{
            if($this->virgo_auth_model->register($params)){
                $this->session->set_flashdata('flash_messages', array('success' => array(lang('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please click').' <a href="'.site_url('account/confirmation/'.$params['username']).'">here</a>')));
                redirect('/login');
            }else{
                $this->session->set_flashdata('flash_messages', array('success' => array(lang('Register fail. Please try again.'))));
                redirect('signup');
            }
        }
    }
    public function personal_register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        redirect('signup');
        $this->load->model('sr_users/virgo_auth_model');
    }
    public function company_register()
    {
        if(is_sr_user_loggin()){
            redirect('/');
        }
        $billing_check = false;
        $this->load->model('sr_users/virgo_auth_model');
        $params = $this->input->post();
        $this->form_validation->set_rules('representative_name', lang('Representative name'), 'required');
        $this->form_validation->set_rules('representative_surname', lang('Representative Surname'), 'required');
        $this->form_validation->set_rules('username', lang('Username'), 'required|min_length[5]|max_length[12]|callback_check_username');
        $this->form_validation->set_rules('password', lang('Password'), 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', lang('Confirm Password'), 'required');
        $this->form_validation->set_rules('representative_email', lang('Representative Email Address'), 'required|valid_email|callback_check_email');

        $this->form_validation->set_rules('representative_phone_number', lang('Phone Number'), 'required');
        $this->form_validation->set_rules('representative_date_of_birth', lang('Representative Date of Birth'), 'required');
        $this->form_validation->set_rules('company_name', lang('Company name'), 'required');
        $this->form_validation->set_rules('company_tax_number', lang('Company Tax number'), 'required');
        $this->form_validation->set_rules('company_street_address', lang('Street Name & Number'), 'required');
        $this->form_validation->set_rules('company_postcode', lang('Postal(zip) code'), 'required');
        $this->form_validation->set_rules('company_city', lang('City'), 'required');
        $this->form_validation->set_rules('company_state', lang('State'), 'required');
        $this->form_validation->set_rules('company_country', lang('Country'), 'required');

        $billing = (isset($params['billing:residental'])) ? $params['billing:residental'] : null;
        $billing_check = (isset($params['billing:residental'])) ? true : false;
        $this->form_validation->set_rules('billing:street',lang('Billing Street address'),'callback_valid_billing_street['.$billing.']');
        $this->form_validation->set_rules('billing:city',lang('Billing City'),'callback_valid_billing_city['.$billing.']');
        $this->form_validation->set_rules('billing:state',lang('Billing State'),'callback_valid_billing_state['.$billing.']');
        $this->form_validation->set_rules('billing:country',lang('Billing Country'),'callback_valid_billing_country['.$billing.']');
        $this->form_validation->set_rules('billing:postcode',lang('Billing Postcode'),'callback_valid_billing_postcode['.$billing.']');
        if ($this->form_validation->run() == FALSE)
        {
            /**========================================================**/
            $this->template
                ->title('Sign up')
                ->set('billing_check',$billing_check)
                ->build('form/company');
        }else{
            if($this->virgo_auth_model->register_company($params)){
                $this->session->set_flashdata('flash_messages', array('success' => array(lang('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please click').' <a href="'.site_url('account/confirmation/'.$params['username']).'">here</a>')));
                redirect('/login');
            }else{
                $this->session->set_flashdata('flash_messages', array('success' => array(lang('Register fail. Please try again.'))));
                redirect('signup/company');
            }
        }
    }
    public function confirmation($identity)
    {
        return true;
    }
    public function check_username($username)
    {
        if($this->sr_user_m->count_by_username($username) > 0){
            $this->form_validation->set_message('check_username', lang('Username existed.'));
            return false;
        }
        return true;
    }
    public function check_email($email)
    {
        if($this->sr_private_profile_m->count_by_email($email) > 0){
            $this->form_validation->set_message('check_email', lang('Email existed. Please use other email.'));
            return false;
        }
        return true;
    }
    public function valid_billing_street($field,$billing)
    {
        if($billing != null){
            return true;
        }
        if(trim($field) == ''){
            $this->form_validation->set_message('valid_billing_street', lang('Billing Street address can\'t be empty'));
            return false;
        }
        return true;
    }
    public function valid_billing_city($field,$billing)
    {
        if($billing != null){
            return true;
        }
        if(trim($field) == ''){
            $this->form_validation->set_message('valid_billing_city', lang('Billing City address can\'t be empty'));
            return false;
        }
        return true;
    }
    public function valid_billing_state($field,$billing)
    {
        if($billing != null){
            return true;
        }
        if(trim($field) == ''){
            $this->form_validation->set_message('valid_billing_state', lang('Billing State address can\'t be empty'));
            return false;
        }
        return true;
    }
    public function valid_billing_country($field,$billing)
    {
        if($billing != null){
            return true;
        }
        if(trim($field) == ''){
            $this->form_validation->set_message('valid_billing_country', lang('Billing Country address can\'t be empty'));
            return false;
        }
        return true;
    }
    public function valid_billing_postcode($field,$billing)
    {
        if($billing != null){
            return true;
        }
        if(trim($field) == ''){
            $this->form_validation->set_message('valid_billing_postcode', lang('Billing Postal(zip) code can\'t be empty'));
            return false;
        }
        return true;
    }
    /**
     * Activate user action
     */
    public function activate($id,$code)
    {
        if(!$code || !$id){
            $this->session->set_flashdata('flash_messages', array('error' => array(lang('Activation processing fail. Please try again.'))));
            redirect('login');
        }
        $activation = $this->virgo_auth_model->activate($id,$code);
        if(!$activation){
            $this->session->set_flashdata('flash_messages', array('error' => array(lang('Activation processing fail. Please try again.'))));
        }else{
            $this->session->set_flashdata('flash_messages', array('success' => array(lang('You activate success.'))));
        }
        redirect('login');
        $this->template->set_layout(false);
    }
}
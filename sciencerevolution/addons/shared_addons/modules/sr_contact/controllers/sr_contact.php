<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_contact extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sr_contacts_m');
        $this->load->model('sr_contact_reasons_m');
        $this->load->helper(array('form', 'url'));
        $this->load->helper('contact');
        $this->load->library('form_validation');
        $this->lang->load('sr_contact');
    }
    public function index()
    {
        $params = $this->input->post();
        $this->form_validation->set_rules('full_name', lang('Name'), 'required');
        $this->form_validation->set_rules('phone_number', lang('Phone number'), 'required');
        $this->form_validation->set_rules('email', lang('Email address'), 'required');
        $this->form_validation->set_rules('website', lang('Website'), 'required');
        $this->form_validation->set_rules('reason', lang('Reason'), 'required');
        $this->form_validation->set_rules('message', lang('Messages'), 'required');
        if ($this->form_validation->run() == FALSE){
            $this->template
                ->title('Contact us')
                ->build('form/contact');
        }else{
            if($this->sr_contacts_m->create($params)){
                $this->session->set_flashdata('flash_messages',array('success' => array(lang('You have been send contact messages success'))));
            }else{
                $this->session->set_flashdata('flash_messages',array('error' => array(lang('Contact messages can\'t be send.Please try again'))));
            }
            redirect(current_url());
        }
    }
}
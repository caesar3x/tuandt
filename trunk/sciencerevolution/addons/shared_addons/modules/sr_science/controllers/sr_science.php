<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_science extends Standard_Controller
{
    public function __construct()
    {
        parent::__construct();
        /*$this->load->model('sr_user_m');*/
        $this->load->model('sr_users/virgo_auth_model');
        $this->lang->load('sr_users/virgo_auth');
        $this->load->model('sr_science/url_rewrite');
        $this->load->helper('virgo');
    }
    public function index()
    {
        $this->template
            ->title('Homepage')
            ->build('home/index');
    }
}
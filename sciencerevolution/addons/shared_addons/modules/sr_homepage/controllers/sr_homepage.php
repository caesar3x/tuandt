<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_homepage extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        /*$this->load->model('sr_user_m');*/
        $this->load->model('sr_users/virgo_auth_model');
        $this->lang->load('sr_users/virgo_auth');
        $this->load->helper('virgo');
    }
    public function index()
    {
        $current_sr_user = $this->virgo_auth_model->get_current_sr_user();
        vdebug($current_sr_user);
        $this->template
            ->set_layout('homepage.html')
            ->title('Homepage')
            ->build('home/index');
    }
    public function num()
    {
        die('==dsadsadsa==');
    }
    public function taxo()
    {
        die('test function');
    }
    public function check()
    {
        die('chekc');
    }
}
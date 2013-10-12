<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Science extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        /*$this->load->model('settings_m');
        $this->load->helper('virgo');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');*/
    }
    public function index()
    {
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
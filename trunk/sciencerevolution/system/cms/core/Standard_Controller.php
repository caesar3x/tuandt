<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Standard_Controller extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sr_users/virgo_auth_model');
        $this->load->helper('virgo');
        $this->load->helper('locations');
        $this->template->current_sr_user = ci()->current_sr_user = $this->current_sr_user = $this->virgo_auth_model->get_current_sr_user();
    }
}
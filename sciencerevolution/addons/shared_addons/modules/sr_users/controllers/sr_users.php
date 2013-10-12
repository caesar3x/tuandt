<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/12/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_users extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        die('user index ');
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
}
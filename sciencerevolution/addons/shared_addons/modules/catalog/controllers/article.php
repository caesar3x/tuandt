<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/11/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Article extends Standard_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->template
            ->title('Article Sample')
            ->build('article/index');
    }
    public function add()
    {
        if(!is_sr_user_loggin()){
            redirect('login');
        }
        $this->template
            ->title('Add Article')
            ->build('article/add');
    }
}
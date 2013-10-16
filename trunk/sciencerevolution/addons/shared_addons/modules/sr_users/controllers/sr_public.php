<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/16/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_public extends Public_Controller
{
    protected $sr_current_user;

    public function __construct()
    {
        parent::__construct();
    }
}
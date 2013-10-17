<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/18/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_company_employee_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_company_employee';
    }
}
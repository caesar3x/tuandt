<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/20/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sr_contact_reasons_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'sr_contact_reasons';
    }
}
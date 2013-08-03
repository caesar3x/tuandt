<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Winner extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('winner_m');
        $this->lang->load('winner');
    }

    /**
     * All items
     */
    public function index($offset = 0)
    {
        /*$data = array(
            'name' => 'Dat Nguyen',
            'phone' => '123456789',
            'email' => 'datnguyen.cntt@gmail.com'
        );
        $this->winner_m->create($data);*/
        $this->template
            ->title('Register Form')
            ->build('form/register');

    }
    public function form()
    {
        die('form action');
    }
}
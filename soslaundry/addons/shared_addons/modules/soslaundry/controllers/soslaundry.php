<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Soslaundry extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('winner_m');
        $this->load->model('hotel_m');
        $this->lang->load('winner');
    }

    /**
     * All items
     */
    public function index($offset = 0)
    {
        $hotels = $this->hotel_m->get_all();
        /*print_r("<pre>");var_dump($hotels);die;*/
        /*$data = array(
            'name' => 'Dat Nguyen',
            'phone' => '123456789',
            'email' => 'datnguyen.cntt@gmail.com'
        );
        $this->winner_m->create($data);*/
        $this->template
            ->title('Register Form')
            ->set('hotels', $hotels)
            ->build('form/register');

    }
    public function form()
    {
        $params = $this->input->post();
        print_r("<pre>");var_dump($params);die;
    }
    public function test()
    {
        $data = array();
        $data['sender_agent'] = $this->agent->browser() . ' ' . $this->agent->version();
        $data['sender_ip']    = $this->input->ip_address();
        $data['sender_os']    = $this->agent->platform();
        $data['to']      = 'kiemsilangthang.dat@gmail.com';
        $data['from']    = 'testemail@gmail.com';
        $data['slug']    = 'contact';
        $data['name']    = 'Dat Nguyen';
        $data['subject'] = 'PyroCms test mail';
        $data['attach']['1.jpg'] = UPLOAD_PATH.'1.jpg';
        Events::trigger('email', $data, 'array');
        die('---------test action----------------');
    }
}
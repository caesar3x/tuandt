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
        $this->template
            ->title('Register Form')
            ->set('hotels', $hotels)
            ->build('form/register');

    }
    public function form()
    {
        $params = $this->input->post();
        $phone = '';
        if(isset($params['phone'])){
            $phone = implode('-',$params['phone']);
        }
        $params['phone'] = $phone;
        if($this->winner_m->create($params)){
            redirect('soslaundry/success');
        }else{
            redirect('soslaundry/fail');
        }
    }
    public function success()
    {
        $this->template
            ->title('Register Success')
            ->build('form/success');
    }
    public function fail()
    {
        $this->template
            ->title('Register Fail')
            ->build('form/fail');
    }
    public function model()
    {
        $row = $this->hotel_m->get(7);
        echo $row->name;
        print_r("<pre>");var_dump($row);die;
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
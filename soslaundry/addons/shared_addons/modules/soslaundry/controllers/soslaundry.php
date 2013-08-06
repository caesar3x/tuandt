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
        $this->load->model('settings_m');
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
        $emails_saved = $this->winner_m->getAllEmail();
        if(in_array($params['email'],$emails_saved)){
            redirect('?message=3');
        }
        $phone = '';
        if(isset($params['phone'])){
            $phone = implode('-',$params['phone']);
        }
        $params['phone'] = $phone;
        if($this->winner_m->create($params)){
            $email = $this->settings_m->get("admin_email");
            $server_email = ($email->value != null && $email->value != '') ? $email->value : $email->default;
            /**Event send email**/
            $data = array();
            $data['to']      = $params['email'];
            $data['from']    = $server_email;
            $data['slug']    = 'user-registration-complete';
            $data['first_name']    = $params['first_name'];
            $data['subject'] = 'User Registration Complete';

            Events::trigger('email', $data, 'array');
            redirect('?message=1');
        }else{
            redirect('?message=2');
        }
    }
    public function create()
    {
        $this->load->library('exportdataexcel');
        $email = $this->settings_m->get("admin_email");
        $server_email = ($email->value != null && $email->value != '') ? $email->value : $email->default;
        $number_setting = $this->settings_m->get("number_winner");
        $number = ($number_setting->value != null && $number_setting->value != '') ? $number_setting->value : $number_setting->default;
        $winners = $this->winner_m->get_random_winner((int)$number);
        if(!empty($winners)){
            foreach($winners as $w){
                $this->winner_m->update($w['id'],array('winner_on' => time(),'is_winner' => 1));
                $data = array();
                $data['to']      = $w['email'];
                $data['from']    = $server_email;
                $data['slug']    = 'user-is-winner';
                $data['first_name']    = $w['first_name'];
                $data['subject'] = 'You are winner!';
                Events::trigger('email', $data, 'array');
            }
        }
        exit();
    }
    public function model()
    {
        /**
         * test update
         */
        $this->winner_m->update(array(''));
        /**
         * test big file excel
         */
        $this->load->library('exportdataexcel');
        $this->load->helper('vd_sos');
        $excel = new ExportDataExcel('file');
        $excel->filename = "export/test_big_excel.xls";

        $excel->initialize();
        for($i = 1; $i<1000; $i++) {
            $row = array($i, genRandomString(), genRandomString(), genRandomString(), genRandomString(), genRandomString());
            $excel->addRow($row);
        }
        $excel->finalize();


        print "memory used: " . number_format(memory_get_peak_usage());
        /**
         * test get all email
         */
        $data = $this->winner_m->getAllEmail();
        print_r("<pre>");var_dump($data);die;
        /**
         * test get setting
         */
        $email = $this->settings_m->get("server_email");
        print_r("<pre>");var_dump($email);die;
        /**
         * test excel
         */
        ///$this->load->helper('vd_excel');
        $this->load->library('exportdataexcel');
        $exporter = new ExportDataExcel('browser', 'test.xls');

        $exporter->initialize();
        $exporter->addRow(array("This", "is", "a", "test"));
        $exporter->addRow(array(1, 2, 3, "123-456-7890"));
        $exporter->addRow(array("foo"));

        $exporter->finalize();

        exit();
        die('====================');
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
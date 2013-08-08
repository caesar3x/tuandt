<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/3/13
 */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
    protected $section = 'items';

    public function __construct()
    {
        parent::__construct();

        // Load all the required classes
        $this->load->model('winner_m');
        $this->lang->load('winner');
        $this->load->helper('vd_sos');
        $this->load->helper('vd_debug');
    }

    /**
     * List all items
     */
    public function index()
    {
        //$items = $this->winner_m->get_all();
        $base_where = array('1' => '1');
        $total_rows = $this->winner_m->count_by($base_where);
        $pagination = create_pagination('admin/soslaundry/index', $total_rows);

        // Using this data, get the relevant results
        $items = $this->winner_m
            ->limit($pagination['limit'], $pagination['offset'])
            ->getAll();


        $this->template
            ->title($this->module_details['name'])
            ->set('items', $items)
            ->set('pagination', $pagination)
            ->build('admin/items');
    }
    public function delete($id = 0)
    {
        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
        {
            // pass the ids and let MY_Model delete the items
            $this->sample_m->delete_many($this->input->post('action_to'));
        }
        elseif (is_numeric($id))
        {
            // they just clicked the link so we'll delete that one
            $this->sample_m->delete($id);
        }
        redirect('admin/soslaundry');
    }
}

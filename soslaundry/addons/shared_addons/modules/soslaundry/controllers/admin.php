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
    }

    /**
     * List all items
     */
    public function index()
    {
        $items = $this->winner_m->get_all();
        $this->template
            ->title($this->module_details['name'])
            ->set('items', $items)
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

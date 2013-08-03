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

    public function create()
    {
        // Set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if ($this->form_validation->run())
        {
            // See if the model can create the record
            if ($this->sample_m->create($this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('sample.success'));
                redirect('admin/sample');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('sample.error'));
                redirect('admin/sample/create');
            }
        }

        $sample = new stdClass;
        foreach ($this->item_validation_rules as $rule)
        {
            $sample->{$rule['field']} = $this->input->post($rule['field']);
        }

        // Build the view using sample/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('sample.new_item'))
            ->set('sample', $sample)
            ->build('admin/form');
    }

    public function edit($id = 0)
    {
        $sample = $this->sample_m->get($id);

        // Set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if ($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);

            // See if the model can create the record
            if ($this->sample_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('sample.success'));
                redirect('admin/sample');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('sample.error'));
                redirect('admin/sample/create');
            }
        }

        // Build the view using sample/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('sample.edit'))
            ->build('admin/form');
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
        redirect('admin/sample');
    }
}

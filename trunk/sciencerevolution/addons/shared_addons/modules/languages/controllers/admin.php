<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
    protected $section = 'languages';

    public function __construct()
    {
        parent::__construct();

        //Load all required classes
        $this->load->model('languages_m');
        $this->load->library('form_validation');
        $this->lang->load('languages');

        //Set the validation
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'trim|max_length[45]|required'
            ),
            array(
                'field' => 'code',
                'label' => 'Code',
                'rules' => 'trim|max_length[45]|required'
            ),
            array(
                'field' => 'flag',
                'label' => 'Flag',
                'rules' => 'trim|max_length[45]|required'
            )
        );
    }

    public function index()
    {
        // set the pagination limit
        $limit = 5;
        $base_where = array('1' => '1');
        $total_rows = $this->languages_m->count_by($base_where);
        $pagination = create_pagination('admin/languages/index', $total_rows, $limit, 4, true);

        $languages = $this->languages_m
            ->limit($pagination['limit'], $pagination['offset'])
            ->get_all();

        // we'll do a quick check here so we can tell tags whether there is data or not
        $languages_exist = count($languages) > 0;

        //build the view in standard/views/admin/items.php
        $this->template
            ->title($this->module_details['name'])
            ->set('languages', $languages)
            ->set('languages_exist', $languages_exist)
            ->set('pagination', $pagination)
            ->build('admin/languages');
    }

    public function create()
    {
        //set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);

        //check if the form validation passed
        if($this->form_validation->run())
        {
            //see if the model can create the record
            if($this->languages_m->create($this->input->post()))
            {
                $this->session->set_flashdata('success', lang('languages.success'));
                redirect('admin/languages');
            }
            else
            {
                $this->session->set_flashdata('error', lang('languages.error'));
                redirect('admin/languages/create');
            }
        }

        $languages = new stdClass;
        foreach($this->item_validation_rules as $rule)
        {
            $languages->{$rule['field']} = $this->input->post($rule['field']);
        }

        //build the view using standard/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('languages.new_item'))
            ->set('languages', $languages)
            ->build('admin/form');
    }

    public function edit($id = 0)
    {
        $languages = $this->languages_m->get($id);
        //    var_dump($standard);
        // Set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if ($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);

            // See if the model can create the record
            if ($this->languages_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('languages.success'));
                redirect('admin/languages');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('languages.error'));
                redirect('admin/languages/create');
            }
        }

        // Build the view using standard/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('languages.edit'))
            ->build('admin/form_edit', $languages);
    }

    public function delete($id = 0)
    {
        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
        {
            // pass the ids and let MY_Model delete the items
            $this->languages_m->delete_many($this->input->post('action_to'));
        }
        elseif (is_numeric($id))
        {
            // they just clicked the link so we'll delete that one
            $this->languages_m->delete($id);
        }
        redirect('admin/languages');
    }
}
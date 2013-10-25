<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
    protected $section = 'news';

    public function __construct()
    {
        parent::__construct();

        //Load all required classes
        $this->load->model('news_m');
        $this->load->library('form_validation');
        $this->lang->load('news');

        //Set the validation
        $this->item_validation_rules = array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'trim|max_length[500]|required'
            ),
            array(
                'field' => 'sub_header',
                'label' => 'Sub Header',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'thumbnail',
                'label' => 'Thumbnail',
                'rules' => 'trim|max_length[255]|required'
            ),
            array(
                'field' => 'content',
                'label' => 'Content',
                'rules' => 'trim|required'
            )
        );
    }

    public function index()
    {
        // set the pagination limit
        $limit = 5;
        $base_where = array('1' => '1');
        $total_rows = $this->news_m->count_by($base_where);
        $pagination = create_pagination('admin/news/index', $total_rows, $limit, 4, true);

        $news = $this->news_m
            ->limit($pagination['limit'], $pagination['offset'])
            ->get_all();

        // we'll do a quick check here so we can tell tags whether there is data or not
        $news_exist = count($news) > 0;

        //build the view in standard/views/admin/items.php
        $this->template
            ->title($this->module_details['name'])
            ->set('news', $news)
            ->set('news_exist', $news_exist)
            ->set('pagination', $pagination)
            ->build('admin/news');
    }

    public function create()
    {
        //set the validation rules from the array above
        $this->form_validation->set_rules($this->item_validation_rules);

        //check if the form validation passed
        if($this->form_validation->run())
        {
            //see if the model can create the record
            if($this->news_m->create($this->input->post()))
            {
                $this->session->set_flashdata('success', lang('news.success'));
                redirect('admin/news');
            }
            else
            {
                $this->session->set_flashdata('error', lang('news.error'));
                redirect('admin/news/create');
            }
        }

        $news = new stdClass;
        foreach($this->item_validation_rules as $rule)
        {
            $news->{$rule['field']} = $this->input->post($rule['field']);
        }

        //build the view using standard/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('news.new_item'))
            ->set('languages', $news)
            ->build('admin/form');
    }

    public function edit($id = 0)
    {
        $news = $this->news_m->get($id);
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
            if ($this->news_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('news.success'));
                redirect('admin/news');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('news.error'));
                redirect('admin/news/create');
            }
        }

        // Build the view using standard/views/admin/form.php
        $this->template
            ->title($this->module_details['name'], lang('news.edit'))
            ->build('admin/form_edit', $news);
    }

    public function delete($id = 0)
    {
        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
        {
            // pass the ids and let MY_Model delete the items
            $this->news_m->delete_many($this->input->post('action_to'));
        }
        elseif (is_numeric($id))
        {
            // they just clicked the link so we'll delete that one
            $this->news_m->delete($id);
        }
        redirect('admin/news');
    }
}
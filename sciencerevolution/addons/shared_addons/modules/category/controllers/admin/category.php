<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Category\Controllers\Admin
 * @created 9/17/2013
 */

class Category extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'category';

	/**
	 * Array that contains the validation rules
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		'name' => array(
			'field' => 'name',
			'label' => 'lang:category.name_label',
			'rules' => 'trim|required|max_length[255]|callback__check_name'
		),
		'icon' => array(
			'field' => 'icon',
			'label' => 'lang:category.icon_label',
			'rules' => 'trim|max_length[255]'
		),
		'parent_id' => array(
			'field' => 'parent_id',
			'label' => 'lang:category.parent_label',
			'rules' => 'trim|numeric'
		),
	);
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->config->load('category');
		$this->load->model(array('category_m'));
		$this->lang->load(array('category'));
		$this->load->library('files/files');
		$this->load->library(array('form_validation'));

		// Date ranges for select boxes
		$_parents = array();
		if ($categories = $this->category_m->order_by('name')->search(array('parent_id'=>0)))
		{
			foreach ($categories as $category)
			{
				$_parents[$category->id] = $category->name;
			}
		}
		$this->template->set('parents',$_parents);
	}

	/**
	 * Show all categorys
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//set the base/default where clause
		$wheres = array('order'=>'id DESC');
		//add post values to base_where if f_module is posted
		$wheres = $this->input->post('keywords') ? $wheres + array('keywords' => $this->input->post('keywords')) : $wheres;
		// Create pagination links
		$total_rows = $this->category_m->count_by($wheres);

		$pagination = create_pagination('admin/category/index', $total_rows);

		// Using this data, get the relevant results
		$categories = $this->category_m->limit($pagination['limit'], $pagination['offset'])->search($wheres);
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template->title($this->module_details['name'])
						->set('active_section', 'category')
						->set('categories',$categories)
						->set('pagination',$pagination)
						->append_js('admin/filter.js');

			$this->input->is_ajax_request()
			? $this->template->build('admin/partials/categorys')
			: $this->template->build('admin/category/index');
	}

	/**
	 * Add new category
	 * @access public
	 * @return void
	 */
	public function create(){
		// The user needs to be able to add category.
		role_or_die('category', 'edit');

		$this->form_validation->set_rules($this->validation_rules);
		if (empty($_FILES['icon']['name']))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		if ($this->form_validation->run())
		{
			$this->load->library('upload');
	        if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('category:folder_name');
	            $config['allowed_types'] = config_item('category:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
					$id = $this->category_m->insert(array(
						'name'		=> $this->input->post('name'),
						'icon'		=> $file_name,
						'parent_id'		=> (int) $this->input->post('parent_id'),
						'created_on'		=> now(),
						'updated_on'		=> now(),
					));
	            }
	            else
	            {
	                $this->session->set_flashdata('error', $this->lang->line('category.upload_error'));
	            }
	        }

			if ($id){
				$this->session->set_flashdata('success', sprintf($this->lang->line('category.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('category.add_error'));
			}

			// Redirect back to the form or main page
			redirect('admin/category/create');
		}
		else
		{
			$category = new stdClass();
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $key => $field)
			{
				$category->$field['field'] = set_value($field['field']);
			}
			$category->created_on = now();
		}

		$this->template
			->title($this->module_details['name'], sprintf(lang('category.add_title')))
			->set('active_section', 'category')
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->set('category',$category)
			->build('admin/category/form');
	}

	/**
	 * Edit category with $id
	 * @access public
	 * @param int $id the ID of the category to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/category');

		// The user needs to be able to edit pages.
		role_or_die('category', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$category = $this->category_m->get($id);

		// Got page?
		if ( ! $category OR empty($category))
		{
			$this->session->set_flashdata('error', lang('category.not_found_error'));
			redirect('admin/category');
		}
		$this->form_validation->set_rules($this->validation_rules);
		$this->form_validation->set_rules(array_merge($this->validation_rules, array(
			'name' => array(
				'field' => 'name',
				'label' => 'lang:category.name_label',
				'rules' => 'trim|required|max_length[255]|callback__check_name['.$id.']'
			),
		)));
		if (empty($_FILES['icon']['name']) && empty($category->icon))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		// Validate the results
		if ($this->form_validation->run())
		{
			$this->load->library('upload');
	        if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('category:folder_name');
	            $config['allowed_types'] = config_item('category:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
	                $category->icon	= $file_name;
	            }
	        }
			$category->name			= $this->input->post('name');
			$category->parent_id			= (int) $this->input->post('parent_id');
			$category->updated_on	= now();

			// Update the comment
			$this->category_m->update($id, $category)
				? $this->session->set_flashdata('success', lang('category.edit_success'))
				: $this->session->set_flashdata('error', lang('category.edit_error'));

			redirect('admin/category');
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== null)
			{
				$category->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title($this->module_details['name'], sprintf(lang('category.edit_title'), $category->id))
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->set('category', $category)
			->build('admin/category/form');
	}
	/**
	 * Helper method to determine what to do with selected items from form post
	 * @access public
	 * @return void
	 */
	public function action()
	{
		switch ($this->input->post('btnAction'))
		{
			case 'publish':
				$this->publish();
			break;

			case 'delete':
				$this->delete();
			break;

			default:
				redirect('admin/category');
			break;
		}
	}

	/**
	 * Publish category
	 * @access public
	 * @param int $id the ID of the category to make public
	 * @return void
	 */
	public function publish($id = 0)
	{
		role_or_die('category', 'publish');
		// Publish one
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		if ( ! empty($ids))
		{
			$category_titles = array();
			foreach ($ids as $id)
			{
				if ($category = $this->category_m->get($id))
				{
					$this->category_m->publish($id);
					$this->pyrocache->delete('category_m');
					$category_titles[] = $category->name;
				}
			}
		}

		if ( ! empty($category_titles))
		{
			if (count($category_titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('category:publish_success'), $category_titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('category:mass_publish_success'), implode('", "', $category_titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', $this->lang->line('category:publish_error'));
		}

		redirect('admin/category');
	}
	/**
	 * For user have role delete category
	 * @param int $id the ID of the category to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		role_or_die('category', 'delete');
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($category = $this->category_m->get($id))
				{
					if ($this->category_m->delete($id))
					{
						// Wipe cache for this model, the content has changed
						$this->pyrocache->delete('category_m');
						$titles[] = $category->name;
					}
				}
			}
		}
		if ( ! empty($titles))
		{
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('category:delete_success'), $titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('category:mass_delete_success'), implode('", "', $titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', lang('category:delete_error'));
		}

		redirect('admin/category');

	}
	/**
	 * Callback method that checks the name of an category
	 * @param string name the Name of category to check
	 * @return bool
	 */
	public function _check_name($name, $id = null)
	{
		$this->form_validation->set_message('_check_name', lang('category.already_exist_error'));
		return $this->category_m->check_exists('name', $name, $id);
	}

	/**
	 * Callback method that checks the name of an category
	 * @param string name the Name of category to check
	 * @return bool
	 */
	public function _check_icon($icon, $id = null)
	{

	}
}


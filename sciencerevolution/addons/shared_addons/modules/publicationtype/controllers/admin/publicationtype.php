<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Publicationtype\Controllers\Admin
 * @created 9/22/2013
 */

class Publicationtype extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'publicationtype';

	/**
	 * Array that contains the validation rules
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		'name' => array(
			'field' => 'name',
			'label' => 'lang:publicationtype.name_label',
			'rules' => 'trim|required|max_length[255]|callback__check_name'
		),
		'icon' => array(
			'field' => 'icon',
			'label' => 'lang:publicationtype.icon_label',
			'rules' => 'trim|max_length[255]'
		),
	);
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->config->load('publicationtype');
		$this->load->model(array('publicationtype_m'));
		$this->lang->load(array('publicationtype'));
		$this->load->library(array('form_validation','upload'));
	}

	/**
	 * Show all publicationtypes
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
		$total_rows = $this->publicationtype_m->count_by($wheres);

		$pagination = create_pagination('admin/publicationtype/index', $total_rows);

		// Using this data, get the relevant results
		$publicationtypes = $this->publicationtype_m->limit($pagination['limit'], $pagination['offset'])->search($wheres);
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template->title($this->module_details['name'])
						->set('active_section', 'publicationtype')
						->set('publicationtypes',$publicationtypes)
						->set('pagination',$pagination)
						->append_js('admin/filter.js');

			$this->input->is_ajax_request()
			? $this->template->build('admin/partials/publicationtypes')
			: $this->template->build('admin/publicationtype/index');
	}

	/**
	 * Add new publicationtype
	 * @access public
	 * @return void
	 */
	public function create(){
		// The user needs to be able to add publicationtype.
		role_or_die('publicationtype', 'edit');

		$this->form_validation->set_rules($this->validation_rules);
		if (empty($_FILES['icon']['name']))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		if ($this->form_validation->run())
		{
			if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('publication_type:folder_name');
	            $config['allowed_types'] = config_item('publication_type:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
					$id = $this->publicationtype_m->insert(array(
						'name'		=> $this->input->post('name'),
						'icon'		=> $file_name,
						'created_on'		=> now(),
						'updated_on'		=> now(),
					));
	            }
	            else {
	            	 $this->session->set_flashdata('error', $this->lang->line('publication_type.upload_error'));
	            }
	        }

			if ($id){
				$this->session->set_flashdata('success', sprintf($this->lang->line('publicationtype.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('publicationtype.add_error'));
			}
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/publicationtype') : redirect('admin/publicationtype/create');
		}
		else
		{
			$publicationtype = new stdClass();
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $key => $field)
			{
				$publicationtype->$field['field'] = set_value($field['field']);
			}
			$publicationtype->created_on = now();
		}

		$this->template
			->title($this->module_details['name'], sprintf(lang('publicationtype.add_title')))
			->set('active_section', 'publicationtype')
			->set('publicationtype',$publicationtype)
			->build('admin/publicationtype/form');
	}

	/**
	 * Edit publicationtype with $id
	 * @access public
	 * @param int $id the ID of the publicationtype to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/publicationtype');

		// The user needs to be able to edit pages.
		role_or_die('publicationtype', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$publicationtype = $this->publicationtype_m->get($id);

		// Got page?
		if ( ! $publicationtype OR empty($publicationtype))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('publicationtype.not_found_error'));
			redirect('admin/publicationtype');
		}
		$this->form_validation->set_rules($this->validation_rules);
		$this->form_validation->set_rules(array_merge($this->validation_rules, array(
			'name' => array(
				'field' => 'name',
				'label' => 'lang:publicationtype_name_label',
				'rules' => 'trim|required|max_length[255]|callback__check_name['.$id.']'
			),
		)));
		if (empty($_FILES['icon']['name']) && empty($publicationtype->icon))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		// Validate the results
		if ($this->form_validation->run())
		{
			if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('publication_type:folder_name');
	            $config['allowed_types'] = config_item('publication_type:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
	                $publicationtype->icon	= $file_name;
	            }
	        }

			$publicationtype->name			= $this->input->post('name');
			$publicationtype->updated_on	= now();

			// Update the comment
			$this->publicationtype_m->update($id, $publicationtype)
				? $this->session->set_flashdata('success', lang('publicationtype.edit_success'))
				: $this->session->set_flashdata('error', lang('publicationtype.edit_error'));
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/publicationtype') : redirect('admin/publicationtype/edit/'.$id);
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== null)
			{
				$publicationtype->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title($this->module_details['name'], sprintf(lang('publicationtype.edit_title'), $publicationtype->id))
			->set('publicationtype', $publicationtype)
			->build('admin/publicationtype/form');
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
			case 'delete':
				$this->delete();
			break;

			default:
				redirect('admin/publicationtype');
			break;
		}
	}

	/**
	 * For user have role delete publicationtype
	 * @param int $id the ID of the publicationtype to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		role_or_die('publicationtype', 'delete');
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($publicationtype = $this->publicationtype_m->get($id))
				{
					if ($this->publicationtype_m->delete($id))
					{
						// Wipe cache for this model, the content has changed
						$this->pyrocache->delete('publicationtype_m');
						$titles[] = $publicationtype->name;
					}
				}
			}
		}
		if ( ! empty($titles))
		{
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('publicationtype:delete_success'), $titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('publicationtype:mass_delete_success'), implode('", "', $titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', lang('publicationtype:delete_error'));
		}

		redirect('admin/publicationtype');

	}
	/**
	 * Callback method that checks the name of an publicationtype
	 * @param string name the name of publicationtype to check
	 * @return bool
	 */
	public function _check_name($name, $id = null)
	{
		$this->form_validation->set_message('_check_name', lang('publicationtype.already_exist_error'));
		return $this->publicationtype_m->check_exists('name', $name, $id);
	}
}


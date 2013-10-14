<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Filetype\Controllers\Admin
 * @created 9/22/2013
 */

class Filetype extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'filetype';

	/**
	 * Array that contains the validation rules
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		'file_type' => array(
			'field' => 'file_type',
			'label' => 'lang:filetype.file_type_label',
			'rules' => 'trim|required|max_length[255]|callback__check_file_type'
		),
		'extension' => array(
			'field' => 'extension',
			'label' => 'lang:filetype.extension_label',
			'rules' => 'trim|required|max_length[45]'
		),
		'icon' => array(
			'field' => 'icon',
			'label' => 'lang:filetype.icon_label',
			'rules' => 'trim|max_length[255]'
		),
	);
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->config->load('filetype');
		$this->load->model(array('filetype_m'));
		$this->lang->load(array('filetype'));
		$this->load->library(array('form_validation','upload'));
	}

	/**
	 * Show all filetypes
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
		$total_rows = $this->filetype_m->count_by($wheres);

		$pagination = create_pagination('admin/filetype/index', $total_rows);

		// Using this data, get the relevant results
		$filetypes = $this->filetype_m->limit($pagination['limit'], $pagination['offset'])->search($wheres);
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template->title($this->module_details['name'])
						->set('active_section', 'filetype')
						->set('filetypes',$filetypes)
						->set('pagination',$pagination)
						->append_js('admin/filter.js');

			$this->input->is_ajax_request()
			? $this->template->build('admin/partials/filetypes')
			: $this->template->build('admin/filetype/index');
	}

	/**
	 * Add new filetype
	 * @access public
	 * @return void
	 */
	public function create(){
		// The user needs to be able to add filetype.
		role_or_die('filetype', 'edit');

		$this->form_validation->set_rules($this->validation_rules);
		if (empty($_FILES['icon']['name']))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		if ($this->form_validation->run())
		{
			if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('file_type:folder_name');
	            $config['allowed_types'] = config_item('file_type:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
					$id = $this->filetype_m->insert(array(
						'file_type'		=> $this->input->post('file_type'),
						'extension'		=> $this->input->post('extension'),
						'icon'			=> $file_name,
						'created_on'	=> now(),
						'updated_on'	=> now(),
					));
	            }
	            else {
	            	 $this->session->set_flashdata('error', $this->lang->line('file_type.upload_error'));
	            }
	        }
			if (isset($id) && $id > 0){
				$this->session->set_flashdata('success', sprintf($this->lang->line('filetype.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('filetype.add_error'));
			}
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/filetype') : redirect('admin/filetype/create');
		}
		else
		{
			$filetype = new stdClass();
			foreach ($this->validation_rules as $key => $field)
			{
				$filetype->$field['field'] = set_value($field['field']);
			}
			$filetype->created_on = now();
		}

		$this->template
			->title($this->module_details['name'], sprintf(lang('filetype.add_title')))
			->set('active_section', 'filetype')
			->set('filetype',$filetype)
			->build('admin/filetype/form');
	}

	/**
	 * Edit filetype with $id
	 * @access public
	 * @param int $id the ID of the filetype to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/filetype');

		// The user needs to be able to edit pages.
		role_or_die('filetype', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$filetype = $this->filetype_m->get($id);

		// Got page?
		if ( ! $filetype OR empty($filetype))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('filetype.not_found_error'));
			redirect('admin/filetype');
		}
		$this->form_validation->set_rules($this->validation_rules);
		$this->form_validation->set_rules(array_merge($this->validation_rules, array(
			'file_type' => array(
				'field' => 'file_type',
				'label' => 'lang:filetype_file_type_label',
				'rules' => 'trim|required|max_length[255]|callback__check_file_type['.$id.']'
			),
		)));
		if (empty($_FILES['icon']['name']) && empty($filetype->icon))
		{
	    	$this->form_validation->set_rules('icon', 'Icon', 'required');
		}
		// Validate the results
		if ($this->form_validation->run())
		{
	        if (!empty($_FILES['icon']['name']))
	        {
	            $config['upload_path'] = './uploads/'.config_item('file_type:folder_name');
	            $config['allowed_types'] = config_item('file_type:allowed_file_ext');
	            $this->upload->initialize($config);
	            if ($this->upload->do_upload('icon'))
	            {
	                $img = $this->upload->data();
	                $file_name = $img['file_name'];
	                $filetype->icon	= $file_name;
	            }
	        }
			$filetype->file_type			= $this->input->post('file_type');
			$filetype->extension			= $this->input->post('extension');
			$filetype->updated_on	= now();
			// Update the comment
			$this->filetype_m->update($id, $filetype)
				? $this->session->set_flashdata('success', lang('filetype.edit_success'))
				: $this->session->set_flashdata('error', lang('filetype.edit_error'));
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/filetype') : redirect('admin/filetype/edit/'.$id);
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== null)
			{
				$filetype->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title($this->module_details['name'], sprintf(lang('filetype.edit_title'), $filetype->id))
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->set('filetype', $filetype)
			->build('admin/filetype/form');
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
				redirect('admin/filetype');
			break;
		}
	}

	/**
	 * For user have role delete filetype
	 * @param int $id the ID of the filetype to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		role_or_die('filetype', 'delete');
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($filetype = $this->filetype_m->get($id))
				{
					if ($this->filetype_m->delete($id))
					{
						// Wipe cache for this model, the content has changed
						$this->pyrocache->delete('filetype_m');
						$titles[] = $filetype->name;
					}
				}
			}
		}
		if ( ! empty($titles))
		{
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('filetype:delete_success'), $titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('filetype:mass_delete_success'), implode('", "', $titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', lang('filetype:delete_error'));
		}

		redirect('admin/filetype');

	}
	/**
	 * Callback method that checks the file_type of an filetype
	 * @param string file_type the file_type of filetype to check
	 * @return bool
	 */
	public function _check_file_type($file_type, $id = null)
	{
		$this->form_validation->set_message('_check_file_type', lang('filetype.already_exist_error'));
		return $this->filetype_m->check_exists('file_type', $file_type, $id);
	}
}


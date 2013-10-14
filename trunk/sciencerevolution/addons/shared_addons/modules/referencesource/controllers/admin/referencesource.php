<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Referencesource\Controllers\Admin
 * @created 9/22/2013
 */

class Referencesource extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'referencesource';

	/**
	 * Array that contains the validation rules
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		'name' => array(
			'field' => 'name',
			'label' => 'lang:referencesource.name_label',
			'rules' => 'trim|required|max_length[255]|callback__check_name'
		),
	);
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('referencesource_m'));
		$this->lang->load(array('referencesource'));
		$this->load->library(array('form_validation'));
	}

	/**
	 * Show all referencesources
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//set the base/default where clause
		$wheres = array('order'=>'id DESC');

		$wheres = $this->input->post('keywords') ? $wheres + array('keywords' => $this->input->post('keywords')) : $wheres;
		// Create pagination links
		$total_rows = $this->referencesource_m->count_by($wheres);

		$pagination = create_pagination('admin/referencesource/index', $total_rows);

		// Using this data, get the relevant results
		$referencesources = $this->referencesource_m->limit($pagination['limit'], $pagination['offset'])->search($wheres);
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template->title($this->module_details['name'])
						->set('active_section', 'referencesource')
						->set('referencesources',$referencesources)
						->set('pagination',$pagination)
						->append_js('admin/filter.js');

			$this->input->is_ajax_request()
			? $this->template->build('admin/partials/referencesources')
			: $this->template->build('admin/referencesource/index');
	}

	/**
	 * Add new referencesource
	 * @access public
	 * @return void
	 */
	public function create(){
		// The user needs to be able to add referencesource.
		role_or_die('referencesource', 'edit');
		$this->form_validation->set_rules($this->validation_rules);
		if ($this->form_validation->run())
		{
			$id = $this->referencesource_m->insert(array(
				'name'		=> $this->input->post('name'),
				'created_on'		=> now(),
				'updated_on'		=> now(),
			));
			if ($id){
				$this->session->set_flashdata('success', sprintf($this->lang->line('referencesource.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('referencesource.add_error'));
			}
			// Redirect back to the form or main page
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/referencesource') : redirect('admin/referencesource/create');
		}
		else
		{
			$referencesource = new stdClass();
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $key => $field)
			{
				$referencesource->$field['field'] = set_value($field['field']);
			}
			$referencesource->created_on = now();
		}

		$this->template
			->title($this->module_details['name'], sprintf(lang('referencesource.add_title')))
			->set('active_section', 'referencesource')
			->set('referencesource',$referencesource)
			->build('admin/referencesource/form');
	}

	/**
	 * Edit referencesource with $id
	 * @access public
	 * @param int $id the ID of the referencesource to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/referencesource');

		// The user needs to be able to edit pages.
		role_or_die('referencesource', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$referencesource = $this->referencesource_m->get($id);

		// Got page?
		if ( ! $referencesource OR empty($referencesource))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('referencesource.not_found_error'));
			redirect('admin/referencesource');
		}
		$this->form_validation->set_rules($this->validation_rules);
		$this->form_validation->set_rules(array_merge($this->validation_rules, array(
			'name' => array(
				'field' => 'name',
				'label' => 'lang:referencesource_name_label',
				'rules' => 'trim|required|max_length[255]|callback__check_name['.$id.']'
			),
		)));

		// Validate the results
		if ($this->form_validation->run())
		{

			$referencesource->name			= $this->input->post('name');
			$referencesource->updated_on	= now();

			// Update the comment
			$this->referencesource_m->update($id, $referencesource)
				? $this->session->set_flashdata('success', lang('referencesource.edit_success'))
				: $this->session->set_flashdata('error', lang('referencesource.edit_error'));
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/referencesource') : redirect('admin/referencesource/edit/'.$id);
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== null)
			{
				$referencesource->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title($this->module_details['name'], sprintf(lang('referencesource.edit_title'), $referencesource->id))
			->set('referencesource', $referencesource)
			->build('admin/referencesource/form');
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
				redirect('admin/referencesource');
			break;
		}
	}

	/**
	 * For user have role delete referencesource
	 * @param int $id the ID of the referencesource to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		role_or_die('referencesource', 'delete');
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($referencesource = $this->referencesource_m->get($id))
				{
					if ($this->referencesource_m->delete($id))
					{
						// Wipe cache for this model, the content has changed
						$this->pyrocache->delete('referencesource_m');
						$titles[] = $referencesource->name;
					}
				}
			}
		}
		if ( ! empty($titles))
		{
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('referencesource:delete_success'), $titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('referencesource:mass_delete_success'), implode('", "', $titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', lang('referencesource:delete_error'));
		}

		redirect('admin/referencesource');

	}
	/**
	 * Callback method that checks the name of an referencesource
	 * @param string name the name of referencesource to check
	 * @return bool
	 */
	public function _check_name($name, $id = null)
	{
		$this->form_validation->set_message('_check_name', lang('referencesource.already_exist_error'));
		return $this->referencesource_m->check_exists('name', $name, $id);
	}
}


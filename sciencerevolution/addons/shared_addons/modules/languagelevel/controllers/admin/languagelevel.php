<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @package Addons\Shared_addons\Modules\Languagelevel\Controllers\Admin
 * @created 9/22/2013
 */

class Languagelevel extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'languagelevel';

	/**
	 * Array that contains the validation rules
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		'name' => array(
			'field' => 'name',
			'label' => 'lang:languagelevel.name_label',
			'rules' => 'trim|required|max_length[255]|callback__check_name'
		),
	);
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('languagelevel_m'));
		$this->lang->load(array('languagelevel'));
		$this->load->library(array('form_validation'));
	}

	/**
	 * Show all languagelevels
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
		$total_rows = $this->languagelevel_m->count_by($wheres);

		$pagination = create_pagination('admin/languagelevel/index', $total_rows);

		// Using this data, get the relevant results
		$languagelevels = $this->languagelevel_m->limit($pagination['limit'], $pagination['offset'])->search($wheres);
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template->title($this->module_details['name'])
						->set('active_section', 'languagelevel')
						->set('languagelevels',$languagelevels)
						->set('pagination',$pagination)
						->append_js('admin/filter.js');

			$this->input->is_ajax_request()
			? $this->template->build('admin/partials/languagelevels')
			: $this->template->build('admin/languagelevel/index');
	}

	/**
	 * Add new languagelevel
	 * @access public
	 * @return void
	 */
	public function create(){
		// The user needs to be able to add languagelevel.
		role_or_die('languagelevel', 'edit');

		$this->form_validation->set_rules($this->validation_rules);

		if ($this->form_validation->run())
		{
			$id = $this->languagelevel_m->insert(array(
				'name'		=> $this->input->post('name'),
				'created_on'		=> now(),
				'updated_on'		=> now(),
			));
			if ($id){
				$this->session->set_flashdata('success', sprintf($this->lang->line('languagelevel.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('languagelevel.add_error'));
			}

			// Redirect back to the form or main page
			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/languagelevel') : redirect('admin/languagelevel/create');
		}
		else
		{
			$languagelevel = new stdClass();
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $key => $field)
			{
				$languagelevel->$field['field'] = set_value($field['field']);
			}
			$languagelevel->created_on = now();
		}

		$this->template
			->title($this->module_details['name'], sprintf(lang('languagelevel.add_title')))
			->set('active_section', 'languagelevel')
			->set('languagelevel',$languagelevel)
			->build('admin/languagelevel/form');
	}

	/**
	 * Edit languagelevel with $id
	 * @access public
	 * @param int $id the ID of the languagelevel to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/languagelevel');

		// The user needs to be able to edit pages.
		role_or_die('languagelevel', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$languagelevel = $this->languagelevel_m->get($id);

		// Got page?
		if ( ! $languagelevel OR empty($languagelevel))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('languagelevel.not_found_error'));
			redirect('admin/languagelevel');
		}
		$this->form_validation->set_rules($this->validation_rules);
		$this->form_validation->set_rules(array_merge($this->validation_rules, array(
			'name' => array(
				'field' => 'name',
				'label' => 'lang:languagelevel_slug_label',
				'rules' => 'trim|required|max_length[255]|callback__check_name['.$id.']'
			),
		)));

		// Validate the results
		if ($this->form_validation->run())
		{

			$languagelevel->name			= $this->input->post('name');
			$languagelevel->updated_on	= now();

			// Update the comment
			$this->languagelevel_m->update($id, $languagelevel)
				? $this->session->set_flashdata('success', lang('languagelevel.edit_success'))
				: $this->session->set_flashdata('error', lang('languagelevel.edit_error'));

			($this->input->post('btnAction') == 'save_exit') ? redirect('admin/languagelevel') : redirect('admin/languagelevel/edit/'.$id);
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== null)
			{
				$languagelevel->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title($this->module_details['name'], sprintf(lang('languagelevel.edit_title'), $languagelevel->id))
			->set('languagelevel', $languagelevel)
			->build('admin/languagelevel/form');
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
				redirect('admin/languagelevel');
			break;
		}
	}

	/**
	 * For user have role delete languagelevel
	 * @param int $id the ID of the languagelevel to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		role_or_die('languagelevel', 'delete');
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($languagelevel = $this->languagelevel_m->get($id))
				{
					if ($this->languagelevel_m->delete($id))
					{
						// Wipe cache for this model, the content has changed
						$this->pyrocache->delete('languagelevel_m');
						$titles[] = $languagelevel->name;
					}
				}
			}
		}
		if ( ! empty($titles))
		{
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('languagelevel:delete_success'), $titles[0]));
			}
			else
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('languagelevel:mass_delete_success'), implode('", "', $titles)));
			}
		}
		else
		{
			$this->session->set_flashdata('notice', lang('languagelevel:delete_error'));
		}

		redirect('admin/languagelevel');

	}
	/**
	 * Callback method that checks the name of an languagelevel
	 * @param string name the name of languagelevel to check
	 * @return bool
	 */
	public function _check_name($name, $id = null)
	{
		$this->form_validation->set_message('_check_name', lang('languagelevel.already_exist_error'));
		return $this->languagelevel_m->check_exists('name', $name, $id);
	}
}


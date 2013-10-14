<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
  * @created 9/17/2013
 */


class category_m extends MY_Model {
	protected $_table = 'sr_categories';

	function get_all()
	{
		$this->db->select('sr_categories.*');
		return $this->db->get('sr_categories')->result();
	}

	function get($id)
	{
		return $this->db->select('sr_categories.*')
				->where(array('sr_categories.id' => $id))
				->get('sr_categories')
				->row();
	}

	public function get_by($key = null, $value = null)
	{
		$this->db->select('sr_categories.*')
			 ->where(array('sr_categories.status' => 'live'));

		if (is_array($key))
		{
			$this->db->where($key);
		}
		else
		{
			$this->db->where($key, $value);
		}

		return $this->db->get($this->_table)->row();
	}

	public function count($params = array())
	{
		return $this->db->count_all_results('sr_categories');
	}

	function count_by($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_categories.name', $params['keywords']);
		}

		if (!empty($params['parent_id']))
		{
			$this->db->where('sr_categories.parent_id', $params['parent_id']);
		}

		return $this->db->count_all_results('sr_categories');
	}

	public function delete($id)
	{
		return $this->db->delete('sr_categories', array('id' => $id));
	}

	function check_exists($field, $value = '', $id = 0)
	{
		if (is_array($field))
		{
			$params = $field;
			$id = $value;
		}
		else
		{
			$params[$field] = $value;
		}
		$params['id !='] = (int) $id;

		return parent::count_by($params) == 0;
	}

	/**
	 * Searches category based on supplied data array
	 * @param $data array
	 * @return array
	 */
	public function search($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_categories.name', $params['keywords']);
		}

		if (!empty($params['parent_id']))
		{
			$this->db->where('sr_categories.parent_id', $params['parent_id']);
		}

		if(isset($params['order']))
			$this->db->order_by($params['order']);
		else
			$this->db->order_by('id', 'DESC');

		// Limit the results based on 1 number or 2 (2nd is offset)
		if(isset($params['limit']) && is_int($params['limit']))
			$this->db->limit($params['limit']);
		elseif(isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		return $this->get_all();
	}

}

/* End of file category.php */

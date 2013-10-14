<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @created 9/22/2013
 */


class languagelevel_m extends MY_Model {
	protected $_table = 'sr_sr_language_level';

	function get_all()
	{
		$this->db->select('sr_language_level.*');
		return $this->db->get('sr_language_level')->result();
	}

	function get($id)
	{
		return $this->db->select('sr_language_level.*')
				->where(array('sr_language_level.id' => $id))
				->get('sr_language_level')
				->row();
	}

	public function get_by($key = null, $value = null)
	{
		$this->db->select('sr_language_level.*');

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
		return $this->db->count_all_results('sr_language_level');
	}

	function count_by($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_language_level.name', $params['keywords']);
		}

		return $this->db->count_all_results('sr_language_level');
	}

	public function delete($id)
	{
		return $this->db->delete('sr_language_level', array('id' => $id));
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
	 * Searches sr_language_level based on supplied data array
	 * @param $data array
	 * @return array
	 */
	public function search($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_language_level.name', $params['keywords']);
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

/* End of file languagelevel.php */

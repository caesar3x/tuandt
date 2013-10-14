<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @created 9/22/2013
 */


class publicationtype_m extends MY_Model {
	protected $_table = 'sr_sr_publication_type';

	function get_all()
	{
		$this->db->select('sr_publication_type.*');
		return $this->db->get('sr_publication_type')->result();
	}

	function get($id)
	{
		return $this->db->select('sr_publication_type.*')
				->where(array('sr_publication_type.id' => $id))
				->get('sr_publication_type')
				->row();
	}

	public function get_by($key = null, $value = null)
	{
		$this->db->select('sr_publication_type.*');

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
		return $this->db->count_all_results('sr_publication_type');
	}

	function count_by($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_publication_type.name', $params['keywords']);
		}

		return $this->db->count_all_results('sr_publication_type');
	}

	public function delete($id)
	{
		return $this->db->delete('sr_publication_type', array('id' => $id));
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
	 * Searches publicationtype based on supplied data array
	 * @param $data array
	 * @return array
	 */
	public function search($params = array())
	{
		if (!empty($params['keywords']))
		{
			$this->db->like('sr_publication_type.name', $params['keywords']);
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
/* End of file publicationtype.php */

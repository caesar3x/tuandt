<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/20/13
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Url_rewrite extends MY_Model
{
    const USER_TYPE = 'user';
    const SYSTEM_TYPE = 'system';
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'url_rewrite';
    }
    public function add_url_rewrite($entity_id,$type,$slug,$target = null)
    {
        if(!$entity_id || !$type || !$slug){
            return false;
        }
    }
    public function add_single_url_rewrite($entity_id,$type,$slug,$target = null,$canonical = 0,$note = null)
    {
        if(!$entity_id || !$type || !$slug){
            return false;
        }

        if(!empty($slug) && is_string($slug)){
            $slug = '/' . ltrim($slug,'/');
            if($target == null){
                $target = $slug;
            }
            $rewrite = $this->get_single_url_rewrite($entity_id,$type);
            if(!empty($rewrite)){
                $data = array(
                    'request_path' => $slug,
                    'target_path' => $target,
                    'canonical' => $canonical,
                    'note' => $note
                );
                $this->db->where('entity_id',$entity_id);
                $this->db->where('type',$type);
                return $this->db->update($this->_table,$data);
            }else{
                $data = array(
                    'request_path' => $slug,
                    'target_path' => $target,
                    'entity_id' => $entity_id,
                    'type' => $type,
                    'canonical' => $canonical,
                    'note' => $note
                );
                $inset_id = $this->insert($data);
                return ($inset_id > 0);
            }
        }
        return false;
    }
    public function caching_url_rewrite()
    {
        $types = array(self::USER_TYPE,self::SYSTEM_TYPE);
        $this->db->where_in('type',$types);
        $query = $this->db->get($this->_table);
        $rowset = $query->result();
        $data = array();
        $key = get_defined_constant('URL_REWRITE_CACHE_KEY');
        if(!empty($rowset)){
            foreach($rowset as $row){
                $data[$row->type.'-rewrite'][$row->request_path] = $row->entity_id;
            }
        }
        if(is_cache_file_support()){
            _set_cache($key,$data);
            return _get_cache($key);
        }
        return $data;
    }
    /**
     * Get single url rewrite
     * @param $entity_id
     * @param $type
     * @return null
     */
    public function get_single_url_rewrite($entity_id,$type)
    {
        $query = $this->get_url_rewrite_query($entity_id,$type);
        if(!empty($query)){
            return $query->row();
        }
        return null;
    }
    /**
     * @param $entity_id
     * @param $type
     * @return mixed
     */
    public function get_url_rewrite_query($entity_id,$type)
    {
        if(!$entity_id || !$type){
            return false;
        }
        $this->db->where('entity_id',$entity_id);
        $this->db->where('type',$type);
        $query = $this->db->get($this->_table);
        return $query;
    }
}
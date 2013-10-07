<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/7/13
 */
namespace Core\Model;

class UsermetaTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('meta_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Usermeta $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->meta_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('meta_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    /**
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('meta_id' => $id));
    }
    /**
     * @param $post_id
     * @return int
     */
    public function deleteByUser($post_id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('user_id' => $post_id));
    }

    public function checkExistUsermeta($user_id,$meta_key)
    {
        if(!$user_id || !$meta_key){
            return false;
        }
        $rowset = $this->tableGateway->select(array('user_id' => $user_id,'meta_key' => $meta_key));
        if($rowset->count() <= 0){
            return false;
        }
        return true;
    }
    public function updatePostMeta($user_id,$meta_key,$meta_value,$single = true)
    {
        if(!$user_id || !$meta_key || !$meta_value){
            return false;
        }
        if($single == true){
            return $this->updateSingleUsermeta($user_id,$meta_key,$meta_value);
        }else{
            if(!is_array($meta_value)){
                return false;
            }
            $success = true;
            foreach($meta_value as $value){
                $success = $success && $this->tableGateway->insert(array('user_id' => $user_id,'meta_key' => trim($meta_key),'meta_value' => $value,'time' => time()));
            }
            return $success;
        }
    }
    public function updateSingleUsermeta($user_id,$meta_key,$meta_value)
    {
        if(is_array($meta_value)){
            $value = serialize($meta_value);
        }else{
            $value = $meta_value;
        }
        if($this->checkExistUsermeta($user_id,$meta_key)){
            return $this->tableGateway->update(array('meta_value' => $value),array('user_id' => $user_id,'meta_key' => trim($meta_key)));
        }else{
            return $this->tableGateway->insert(array('user_id' => $user_id,'meta_key' => trim($meta_key),'meta_value' => $value));
        }
    }

    /**
     * @param $user_id
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getUserLog($user_id)
    {
        $rowset = $this->tableGateway->select(array('user_id' => $user_id,'meta_key' => 'user_log','deleted' => 0));
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }
}
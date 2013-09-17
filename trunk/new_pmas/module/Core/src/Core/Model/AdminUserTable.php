<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/7/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;
use Zend\Debug\Debug;

class AdminUserTable extends AbstractModel
{
    public function save(AdminUser $entry)
    {
        $data = array(
            'username' => $entry->username,
            'first_name' => $entry->first_name,
            'last_name' => $entry->last_name,
            'password'  => $entry->password,
            'created_at'  => $entry->created_at,
            'updated_at'  => $entry->updated_at,
            'token'  => $entry->token,
            'note'  => $entry->note,
            'role'  => $entry->role,
            'status'  => $entry->status,
            'email'  => $entry->email,
            'hidden'  => $entry->hidden,
            'deleted'  => $entry->deleted,
        );

        $id = (int)$entry->id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function getAvaiableUsers()
    {
        $resultSet = $this->tableGateway->select(array('hidden' => 0,'deleted' => 0));
        return $resultSet;
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('id' => $id));
    }
}
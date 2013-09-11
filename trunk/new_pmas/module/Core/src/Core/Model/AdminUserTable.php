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
            'password'  => $entry->password,
            'created_at'  => $entry->created_at,
            'updated_at'  => $entry->updated_at,
            'token'  => $entry->token,
            'note'  => $entry->note,
            'role'  => $entry->role,
            'status'  => $entry->status,
            'email'  => $entry->email
        );

        $id = (int)$entry->id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dữ liệu không tồn tại.');
            }
        }
    }
    public function getAdministrators()
    {
        $resultSet = $this->tableGateway->select(array('role' => array('admin','editor')));
        return $resultSet;
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('id' => $id));
    }
}
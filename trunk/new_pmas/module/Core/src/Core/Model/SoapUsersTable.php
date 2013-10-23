<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Core\Model;

class SoapUsersTable extends AbstractModel
{
    public function save(SoapUsers $entry)
    {
        $data = (array) $entry;

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
        $resultSet = $this->tableGateway->select(array('deleted' => 0));
        return $resultSet;
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('id' => $id));
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class DeviceConditionTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('condition_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(DeviceCondition $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->condition_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('condition_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('condition_id' => $id));
    }
    public function getRecyclerConditions($recycler = 'tdm')
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'recycler' => $recycler));
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
}
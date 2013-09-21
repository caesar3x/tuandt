<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

use Zend\Debug\Debug;

class RecyclerTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('recycler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Recycler $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->recycler_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('recycler_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('recycler_id' => $id));
    }

    /**
     * Get available recyclers
     * @param null $ids
     * @param string $order
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvailabeRecyclers($ids = null)
    {
        if($ids == null){
            $rowset = $this->tableGateway->select(array('deleted' => 0));
        }else{
            $rowset = $this->tableGateway->select(array('deleted' => 0,'recycler_id' => $ids));
        }
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

class RecyclerProductTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('product_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(RecyclerProduct $entry)
    {
        $data = (array) $entry;
        return $this->tableGateway->insert($data);
    }

    /**
     * Delete by product id
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('product_id' => $id));
    }

    /**
     * Rollback
     * @param $id
     * @return int
     */
    public function rollbackDeleted($id)
    {
        return $this->tableGateway->update(array('deleted' => 0),array('product_id' => $id));
    }
    /**
     * Delete by condition id
     * @param $condition_id
     * @return int
     */
    public function deleteByCondition($condition_id)
    {
        return $this->tableGateway->update(array('condition_id' => 0),array('condition_id' => $condition_id));
    }
    /**
     * Delete by recycler id
     * @param $recycler_id
     * @return int
     */
    public function deleteByRecyclerId($recycler_id)
    {
        return $this->tableGateway->update(array('recycler_id' => 0),array('recycler_id' => $recycler_id));
    }
    /**
     * @param $condition_id
     * @return bool
     */
    public function checkHasRowContainConditionId($condition_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'condition_id' => $condition_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }
}
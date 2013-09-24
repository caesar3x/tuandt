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
     * Delete by recycler id
     * @param $recycler_id
     * @return int
     */
    public function deleteByRecyclerId($recycler_id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('recycler_id' => $recycler_id));
    }
}
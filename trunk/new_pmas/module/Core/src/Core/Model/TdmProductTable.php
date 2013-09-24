<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Debug\Debug;

class TdmProductTable extends AbstractModel
{
    public function getAvaiableRows()
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'country_deleted' => 0));
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset;
    }
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
    public function save(TdmProduct $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->product_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('product_id' => $id));
            } else {
                return $this->tableGateway->insert($data);
            }
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('product_id' => $id));
    }
    /**
     * Delete by country id
     * @param $country_id
     * @return int
     */
    public function deleteByCountry($country_id)
    {
        return $this->tableGateway->update(array('country_deleted' => 1),array('country_id' => $country_id));
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
     * Roll back
     * @param $country_id
     * @return int
     */
    public function rollbackDeleteCountry($country_id)
    {
        return $this->tableGateway->update(array('country_deleted' => 0),array('country_id' => $country_id));
    }

    /**
     * check if has record contain country id
     * @param $country_id
     * @return bool
     */
    public function checkAvailabelCountry($country_id)
    {
        $rowset = $this->tableGateway->select(array('country_id' => $country_id,'deleted' => 0));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }
}
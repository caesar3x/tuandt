<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;

class BrandTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('brand_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function getEntryByName($name)
    {
        $name  = (string) $name;
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Brand $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->brand_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('brand_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('brand_id' => $id));
    }

    /**
     * Get tdm product info by product_id
     * @param $product_id
     * @return null
     */
    public function getTdmProduct($product_id)
    {
        if($product_id == null){
            return;
        }
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $productEntry = $tdmProductTable->getEntry($product_id);
        if(!empty($productEntry)){
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->from(array('m' => $this->tableGateway->table));
            $select->join(array('td' => $tdmProductTable->tableGateway->table),'m.product_id = td.product_id');
            $select->where(array('m.deleted' => 0,'m.product_id' => $product_id));
            $select->order('m.product_id DESC');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        }
        return null;
    }

    /**
     * Get all available tdm products
     * @param string $order
     * @return mixed
     */
    public function getAvaiableTdmProducts($ids = null,$order = 'DESC')
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('td' => $tdmProductTable->tableGateway->table),'m.product_id = td.product_id');
        if($ids == null){
            $select->where(array('m.deleted' => 0));
        }else{
            $select->where(array('m.deleted' => 0,'m.product_id' => $ids));
        }
        $select->order('m.product_id '.$order);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    /**
     * Check has row has type id
     * @param $type_id
     * @return bool
     */
    public function checkHasRowHasTypeId($type_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'type_id' => $type_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * Update when product type is deleted
     * @param $type_id
     * @return int
     */
    public function productTypeDeleted($type_id)
    {
        return $this->tableGateway->update(array('type_id' => 0),array('type_id' => $type_id));
    }

    /**
     * @param $id
     * @return null
     */
    public function getNameById($id)
    {
        $entry = $this->getEntry($id);
        if($entry != null){
            return $entry->name;
        }
        return null;
    }
    /**
     * @param $brand_id
     * @return bool
     */
    public function clearBrand($brand_id)
    {
        if($this->deleteEntry($brand_id)){
            $success = true;
            $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
            $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
            if($tdmProductTable->checkHasRowHasBrandId($brand_id)){
                $success = $success && $tdmProductTable->clearBrandId($brand_id);
            }
            if($recyclerProductTable->checkHasRowHasBrandId($brand_id)){
                $success = $success && $recyclerProductTable->clearBrandId($brand_id);
            }
            return $success;
        }else{
            return false;
        }
    }
}
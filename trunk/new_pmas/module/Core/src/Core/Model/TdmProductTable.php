<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;

class TdmProductTable extends AbstractModel
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
     * @return Select
     */
    public function getTdmProductQuery()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where(array('deleted' => 0));
        $select->order('product_id DESC');
        return $select;
    }
    /**
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        /*return $this->tableGateway->update(array('deleted' => 1),array('product_id' => $id));*/
        return $this->tableGateway->delete(array('product_id' => $id));
    }
    /**
     * Delete by country id
     * @param $country_id
     * @return int
     */
    public function clearCountry($country_id)
    {
        return $this->tableGateway->update(array('country_id' => 0),array('country_id' => $country_id));
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
     * @param $brand_id
     * @return int
     */
    public function clearBrandId($brand_id)
    {
        return $this->tableGateway->update(array('brand_id' => 0),array('brand_id' => $brand_id));
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

    /**
     * @param $brand_id
     * @return bool
     */
    public function checkHasRowHasBrandId($brand_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'brand_id' => $brand_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $ids
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getProductsFilter($ids)
    {
        if(empty($ids)){
            $rowset = $this->tableGateway->select(array('deleted' => 0));
        }else{
            $rowset = $this->tableGateway->select(array('deleted' => 0,'product_id' => $ids));
        }
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset;
    }

    /**
     * @param $ids
     * @param null $tdm_country
     * @return null
     */
    public function getProductsFilterExport($ids,$tdm_country = null)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $where->equalTo('deleted',0);
        if(!empty($ids)){
            $where->equalTo('product_id',$ids);
        }
        if(!empty($tdm_country)){
            $where->equalTo('country_id',$tdm_country);
        }
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where($where);
        $select->order('product_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($result->count() <= 0) {
            return null;
        }
        return $result;
    }

    /**
     * @param $model
     * @param $condition
     * @return array|\ArrayObject|null
     */
    public function getRowByModel($model,$condition)
    {
        if(!$model || !$condition){
            return null;
        }
        $rowset = $this->tableGateway->select(array('deleted' => 0,'condition_id' => $condition,'model' => trim($model)));
        $row = $rowset->current();
        if(!$row){
            return null;
        }
        return $row;
    }
    public function getLastProducts()
    {
        $rowset = $this->tableGateway->select(function (Select $select){
            $select->where('deleted = 0');
            $select->order('product_id DESC')->limit(10);
        });
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
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
     * @param $country_id
     * @return Select
     */
    public function getProductsByCountryQuery($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('country_id',$country_id);
        $where->equalTo('deleted',0);
        /*$select->where(array('r.country_id' => $country_id,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        return $select;
    }

    /**
     * @param $country_id
     * @return mixed
     */
    public function getProductsByCountry($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('country_id',$country_id);
        $where->equalTo('deleted',0);
        /*$select->where(array('r.country_id' => $country_id,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($result->count() <= 0) {
            return null;
        }
        return $result;
    }
}
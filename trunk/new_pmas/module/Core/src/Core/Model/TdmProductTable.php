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
    public function getTdmProduct()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->order('product_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($result->count() <= 0) {
            return null;
        }
        return $result;
    }

    /**
     * @param array $params
     * @return Select
     */
    public function getTdmProductQuery($params = array())
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('b' => 'brand'),'m.brand_id = b.brand_id',array('brand_name' => 'name'));
        $select->join(array('c' => 'country'),'m.country_id = c.country_id',array('country_name' => 'name'));
        $select->join(array('t' => 'product_type'),'m.type_id = t.type_id',array('type_name' => 'name'));
        $select->join(array('cd' => 'tdm_product_condition'),'m.condition_id = cd.condition_id',array('condition_name' => 'name'));
        $where = new Where();
        if(isset($params['id']) && trim($params['id']) != ''){
            $where->equalTo('m.product_id',$params['id']);
        }
        if(isset($params['brand']) && trim($params['brand']) != ''){
            $brand = $params['brand'];
            $where->like('b.name',"%$brand%");
        }
        if(isset($params['country']) && trim($params['country']) != ''){
            $country = $params['country'];
            $where->like('c.name',"%$country%");
        }
        if(isset($params['type']) && trim($params['type']) != ''){
            $type = $params['type'];
            $where->like('t.name',"%$type%");
        }
        if(isset($params['condition']) && trim($params['condition']) != ''){
            $condition = $params['condition'];
            $where->like('cd.name',"%$condition%");
        }
        if(isset($params['name']) && trim($params['name']) != ''){
            $name = $params['name'];
            $where->like('m.name',"%$name%");
        }
        /*$select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        Debug::dump($selectString);die;*/
        /**
         * Process orderby
         */
        if(isset($params['orderby']) && trim($params['orderby']) != ''){
            $dir = (isset($params['dir']) && trim($params['dir']) != '') ? $params['dir'] : 'desc';
            if($params['orderby'] == 'brand'){
                $orderby = "brand_name";
            }elseif($params['orderby'] == 'country'){
                $orderby = "country_name";
            }elseif($params['orderby'] == 'type'){
                $orderby = "type_name";
            }elseif($params['orderby'] == 'condition'){
                $orderby = "condition_name";
            }elseif($params['orderby'] == 'name'){
                $orderby = "m.name";
            }elseif($params['orderby'] == 'id'){
                $orderby = "m.product_id";
            }else{
                $orderby = "m.product_id";
            }
            $select->order("$orderby $dir");
        }else{
            $select->order("m.product_id DESC");
        }
        $select->where($where);
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
        $rowset = $this->tableGateway->select(array('country_id' => $country_id));
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
        $rowset = $this->tableGateway->select(array('condition_id' => $condition_id));
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
        $rowset = $this->tableGateway->select(array('brand_id' => $brand_id));
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
            $rowset = $this->tableGateway->select();
        }else{
            $rowset = $this->tableGateway->select(array('product_id' => $ids));
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
        $rowset = $this->tableGateway->select(array('condition_id' => $condition,'model' => trim($model)));
        $row = $rowset->current();
        if(!$row){
            return null;
        }
        return $row;
    }
    public function getLastProducts()
    {
        $rowset = $this->tableGateway->select(function (Select $select){
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
        $rowset = $this->tableGateway->select(array('type_id' => $type_id));
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
        /*$select->where(array('r.country_id' => $country_id,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($result->count() <= 0) {
            return null;
        }
        return $result;
    }

    /**
     * Create tdm product index table
     * @return bool
     */
    public function create_tdm_product_index_table()
    {
        $query = "CREATE TABLE IF NOT EXISTS `tdm_product_index` (
                  `product_id` bigint(20) NOT NULL,
                  `country_id` int(11) DEFAULT NULL,
                  `country_name` varchar(255) DEFAULT NULL,
                  `product_name` varchar(255) DEFAULT NULL,
                  `product_model` varchar(255) DEFAULT NULL,
                  `type_id` int(11) DEFAULT NULL,
                  `type_name` varchar(255) DEFAULT NULL,
                  `brand_id` int(11) DEFAULT NULL,
                  `brand_name` varchar(255) DEFAULT NULL,
                  `condition_id` int(11) DEFAULT NULL,
                  `condition_name` varchar(255) DEFAULT NULL,
                  `popular` tinyint(4) DEFAULT '0',
                  `ssa_price` decimal(12,4) DEFAULT NULL,
                  PRIMARY KEY (`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $adapter = $this->tableGateway->adapter;
        $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        return true;
    }
    /**
     * Index data
     */
    public function index_data()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('b' => 'brand'),'m.brand_id = b.brand_id',array('brand_name' => 'name'));
        $select->join(array('c' => 'country'),'m.country_id = c.country_id',array('country_name' => 'name'));
        $select->join(array('t' => 'product_type'),'m.type_id = t.type_id',array('type_name' => 'name'));
        $select->join(array('cd' => 'tdm_product_condition'),'m.condition_id = cd.condition_id',array('condition_name' => 'name'));
        $where = new Where();
        $selectString = $sql->getSqlStringForSqlObject($select);
        Debug::dump($selectString);die;
    }
}
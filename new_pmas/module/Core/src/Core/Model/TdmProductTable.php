<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\In;
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
    public function getTdmProductsIndex($params = array())
    {
        /**
         * Create index table if not exist
         */
        $this->create_tdm_product_index_table(false);
        $this->create_tdm_product_match(false);
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => 'tdm_product_index'));
        $where = new Where();
        if(isset($params['id']) && trim($params['id']) != ''){
            $where->equalTo('m.product_id',$params['id']);
        }
        if(isset($params['brand']) && trim($params['brand']) != ''){
            $brand = $params['brand'];
            $where->like('m.brand_name',"%$brand%");
        }
        if(isset($params['country']) && trim($params['country']) != ''){
            $country = $params['country'];
            $where->like('m.country_name',"%$country%");
        }
        if(isset($params['type']) && trim($params['type']) != ''){
            $type = $params['type'];
            $where->like('m.type_name',"%$type%");
        }
        if(isset($params['condition']) && trim($params['condition']) != ''){
            $condition = $params['condition'];
            $where->like('m.condition_name',"%$condition%");
        }
        if(isset($params['name']) && trim($params['name']) != ''){
            $name = $params['name'];
            $where->like('m.name',"%$name%");
        }
        if(isset($params['ssa_price']) && trim($params['ssa_price']) != ''){
            $sss_price = $params['ssa_price'];
            $where->equalTo('ssa_price',$sss_price);
        }
        if(isset($params['ssa_price_from']) && trim($params['ssa_price_from']) != ''){
            $where->greaterThanOrEqualTo('ssa_price',$params['ssa_price_from']);
        }
        if(isset($params['ssa_price_to']) && trim($params['ssa_price_to']) != ''){
            $where->lessThanOrEqualTo('ssa_price',$params['ssa_price_to']);
        }
        if(isset($params['tdmcountry']) && trim($params['tdmcountry']) != ''){
            $where->equalTo('country_id',$params['tdmcountry']);
        }
        /**
         * Process filtered products without price
         */
        if(isset($params['higher']) && trim($params['higher']) != ''){
            $select_price_index = $sql->select()->from('tdm_product_match')->columns(array('product_id'));
            $where2 = new Where();
            $where2->greaterThanOrEqualTo('percentage',$params['higher']);
            $select_price_index->where($where2);
            $select_price_index_string = $sql->getSqlStringForSqlObject($select_price_index);
            /*Debug::dump($sql->getSqlStringForSqlObject($select_price_index));die;*/
            /*$where->in('m.product_id',"(".$select_price_index_string.")");*/
            $where->addPredicate(new Expression("m.product_id IN (".$select_price_index_string.")"));
        }
        /*$select->where($where);
        Debug::dump($sql->getSqlStringForSqlObject($select));die;*/
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
            }elseif($params['orderby'] == 'ssa_price'){
                $orderby = "m.ssa_price";
            }else{
                $orderby = "m.product_id";
            }
            $select->order("$orderby $dir");
        }else{
            $select->order("m.product_id DESC");
        }
        $select->where($where);
        $select->group('m.product_id');
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
     */
    public function create_tdm_product_index_table($do_truncate = true)
    {
        $query = "CREATE TABLE IF NOT EXISTS `tdm_product_index` (
                  `product_id` bigint(20) NOT NULL,
                  `country_id` int(11) DEFAULT NULL,
                  `country_name` varchar(255) DEFAULT NULL,
                  `name` varchar(255) DEFAULT NULL,
                  `model` varchar(255) DEFAULT NULL,
                  `type_id` int(11) DEFAULT NULL,
                  `type_name` varchar(255) DEFAULT NULL,
                  `brand_id` int(11) DEFAULT NULL,
                  `brand_name` varchar(255) DEFAULT NULL,
                  `condition_id` int(11) DEFAULT NULL,
                  `condition_name` varchar(255) DEFAULT NULL,
                  `popular` tinyint(4) DEFAULT '0',
                  `ssa_price` decimal(12,4) DEFAULT NULL,
                  `ssa_currency` varchar(10) DEFAULT NULL,
                  PRIMARY KEY (`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $adapter = $this->tableGateway->adapter;
        $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        if($do_truncate !== false){
            $truncate = "TRUNCATE TABLE `tdm_product_index`";
            $adapter->query($truncate, $adapter::QUERY_MODE_EXECUTE);
        }
        return true;
    }
    public function create_tdm_product_match($do_truncate = true)
    {
        $query = "CREATE TABLE IF NOT EXISTS `tdm_product_match` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `product_id` bigint(20) DEFAULT NULL,
                  `recycler_id` int(11) DEFAULT NULL,
                  `recycler_name` varchar(255) DEFAULT NULL,
                  `price` decimal(12,6) DEFAULT NULL,
                  `price_in_hkd` decimal(12,6) DEFAULT NULL,
                  `percentage` decimal(4,2) DEFAULT NULL,
                  `recycler_country_id` int(11) DEFAULT NULL,
                  `recycler_country_name` varchar(255) DEFAULT NULL,
                  `date` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $adapter = $this->tableGateway->adapter;
        $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        if($do_truncate !== false){
            $truncate = "TRUNCATE TABLE `tdm_product_match`";
            $adapter->query($truncate, $adapter::QUERY_MODE_EXECUTE);
        }
        return true;
    }
    public function getProducts($limit = null)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('b' => 'brand'),'m.brand_id = b.brand_id',array('brand_name' => 'name'));
        $select->join(array('c' => 'country'),'m.country_id = c.country_id',array('country_name' => 'name'));
        $select->join(array('t' => 'product_type'),'m.type_id = t.type_id',array('type_name' => 'name'));
        $select->join(array('cd' => 'tdm_product_condition'),'m.condition_id = cd.condition_id',array('condition_name' => 'name'));
        if($limit != null){
            $select->limit($limit);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rowset = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset;
    }
    /**
     * Index data
     */
    public function index_data()
    {
        $this->create_tdm_product_index_table();
        $this->create_tdm_product_match();
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $products = $this->getProducts();
        $recylers = $this->serviceLocator->get('RecyclerTable')->get_all();
        if(!empty($products)){
            $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
            foreach($products as $row){
                $row_parse = (array) $row;
                /**
                 * get SSA price
                 */
                $ssa_price = $recyclerProductTable->getSSAPrice($row->model,$row->condition_id);
                $ssa_currency = $recyclerProductTable->getSSACurrency($row->model,$row->condition_id);
                $row_parse['ssa_price'] = $ssa_price;
                $row_parse['ssa_currency'] = $ssa_currency;
                /**
                 * Check product existed
                 */
                /*if($this->getIndexEntry($row->product_id)){
                    $sql_query = $sql->update('tdm_product_index')->set($row_parse)->where(array('product_id' => $row->product_id));
                }else{
                    $sql_query = $sql->insert('tdm_product_index')->values($row_parse);
                }*/
                $sql_query = $sql->insert('tdm_product_index')->values($row_parse);
                $statement = $sql->prepareStatementForSqlObject($sql_query);
                $statement->execute();
                /**
                 * Tdm product match
                 */
                $recycler_products = $recyclerProductTable->getRowsMatching($row->model,$row->condition_id,false);
                if(!empty($recycler_products)){
                    foreach($recycler_products as $rp){
                        $current_exchange = $this->getViewHelper('exchange')->getCurrentExchangeOfCurrency($rp->currency);
                        $priceExchange = ((float) $rp->price )/ $current_exchange;
                        if($ssa_price != 0){
                            $sub = $priceExchange-$ssa_price;
                            $percentage = $sub/$ssa_price;
                        }else{
                            $percentage = null;
                        }
                        $row_data = array(
                            'recycler_product_id' => $rp->product_id,
                            'product_id' => $row->product_id,
                            'recycler_id' => $rp->recycler_id,
                            'recycler_name' => $recylers[$rp->recycler_id]['name'],
                            'price' => $rp->price,
                            'price_in_hkd' => $priceExchange,
                            'percentage' => $percentage*100,
                            'recycler_country_id' => $recylers[$rp->recycler_id]['country_id'],
                            'recycler_country_name' => $recylers[$rp->recycler_id]['country_name'],
                            'date' => $rp->date
                        );
                        $insert_model_match = $sql->insert('tdm_product_match')->values($row_data);
                        $statement2 = $sql->prepareStatementForSqlObject($insert_model_match);
                        $statement2->execute();
                    }
                }
            }
        }
        return true;
    }
    public function getIndexEntry($product_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from('tdm_product_index');
        $select->where(array('product_id' => $product_id));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rowset = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset->current();
    }
    /**
     * Get data from tdm product match table
     */
    public function get_recycler_products_matching($object,$params = array())
    {
        if(isset($params['date_from']) || isset($params['date_to'])){
            $date_from_time = null;
            if(isset($params['date_from'])){
                $date_from = \DateTime::createFromFormat('d-m-Y H:i:s',$params['date_from'].' 00:00:00');
                if($date_from != null){
                    $date_from_time = $date_from->getTimestamp();
                }
            }
            $date_to_time = null;
            if(isset($params['date_to'])){
                $date_to = \DateTime::createFromFormat('d-m-Y H:i:s',$params['date_to'].' 23:59:59');
                if($date_to != null){
                    $date_to_time = $date_to->getTimestamp();
                }
            }
            $country = (isset($params['rcountry'])) ? $params['rcountry'] : null;
            $rowset = $this->serviceLocator->get('RecyclerProductTable')->getRowsMatchingByDate($object->model,$object->condition_id,false,$country,$date_from_time,$date_to_time);
            return $rowset;
        }else{
            $tdm_product_id = $object->product_id;
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->from(array('m' => 'tdm_product_match'));
            $where = new Where();
            $where->equalTo('product_id',$tdm_product_id);
            /**
             * Filter percentage higher than
             */
            if(isset($params['higher']) && trim($params['higher']) != ''){
                $where->greaterThanOrEqualTo('percentage',$params['higher']);
            }
            /**
             * Filter by country
             */
            if(isset($params['rcountry']) && trim($params['rcountry'])){
                $where->equalTo('recycler_country_id',$params['rcountry']);
            }
            $select->where($where);
            $select->order("percentage DESC");
            $select->limit(3);
            $selectString = $sql->getSqlStringForSqlObject($select);
            $rowset = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            if ($rowset->count() <= 0) {
                return null;
            }
            return $rowset;
        }
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;
use Zend\I18n\Validator\DateTime;

class RecyclerProductTable extends AbstractModel
{
    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
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
        $id = $entry->product_id;
        if($id == 0){
            return $this->tableGateway->insert($data);
        }else{
            if($row = $this->getEntry($id)){
                /**
                 * Reset lastest
                 */
                $this->resetLatest($entry->model,$entry->condition_id,$entry->recycler_id);
                return $this->tableGateway->update($data, array('product_id' => $id));
            }else{
                throw new \Exception('Data does not exist.');
            }
        }

    }
    public function saveData($data)
    {
        if(isset($data['model']) && isset($data['condition_id']) && isset($data['recycler_id'])){
            $this->resetLatest($data['model'],$data['condition_id'],$data['recycler_id']);
        }
        if(isset($data['product_id'])){
            return $this->tableGateway->update($data,array('product_id' => $data['product_id']));
        }else{
            return $this->tableGateway->insert($data);
        }
    }
    /**
     * @param $model
     * @param $condition
     * @return bool|int
     */
    public function resetLatest($model,$condition,$recycler_id)
    {
        return $this->tableGateway->update(array('lastest' => 0),array('model' => $model,'condition_id' => $condition,'recycler_id' => $recycler_id));
    }
    /**
     * Delete by product id
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('product_id' => $id));
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
     * @param $recycler_id
     * @return int
     */
    public function clearRecyclerId($recycler_id)
    {
        return $this->tableGateway->update(array('recycler_id' => 0),array('recycler_id' => $recycler_id));
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
     * @param $recycler_id
     * @return bool
     */
    public function checkHasRowHasRecyclerId($recycler_id)
    {
        $rowset = $this->tableGateway->select(array('recycler_id' => $recycler_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param null $recycler_id
     * @return array|null
     */
    public function getAllTempIdsProduct($recycler_id = null)
    {
        if($recycler_id == null){
            $rowset = $this->tableGateway->select();
        }else{
            $rowset = $this->tableGateway->select(array('recycler_id' => $recycler_id));
        }
        if($rowset->count() <= 0){
            return null;
        }
        $data = array();
        foreach($rowset as $row){
            if($row->temp_id != null && $row->temp_id != 0){
                $data[] = $row->temp_id;
            }
        }
        return $data;
    }

    /**
     * @param $recycler_id
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getProductsByRecycler($recycler_id)
    {
        if($recycler_id == null){
            return $recycler_id;
        }
        $rowset = $this->tableGateway->select(array('recycler_id' => $recycler_id));
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }

    /**
     * @param $recycler_id
     * @param array $params
     * @return \Zend\Db\Sql\Select
     */
    public function getProductsByRecyclerQuery($recycler_id,$params = array())
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('b' => 'brand'),'m.brand_id = b.brand_id',array('brand_name' => 'name'));
        $select->join(array('t' => 'product_type'),'m.type_id = t.type_id',array('type_name' => 'name'));
        $select->join(array('cd' => 'tdm_product_condition'),'m.condition_id = cd.condition_id',array('condition_name' => 'name'));
        $where = new Where();
        $where->equalTo('m.lastest',1);
        $where->equalTo('m.recycler_id',$recycler_id);
        /**
         * process filter
         */
        if(isset($params['id']) && trim($params['id']) != ''){
            $where->equalTo('m.product_id',$params['id']);
        }
        if(isset($params['brand']) && trim($params['brand']) != ''){
            $brand = $params['brand'];
            $where->like('b.name',"%$brand%");
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
        if(isset($params['date_from']) && trim($params['date_from']) != ''){
            $date_from_parse = \DateTime::createFromFormat('d-m-Y H:i:s',$params['date_from'].' 00:00:00');
            if($date_from_parse != null){
                $date_from = $date_from_parse->getTimestamp();
                $where->greaterThanOrEqualTo('date',$date_from);
            }
        }
        if(isset($params['date_to']) && trim($params['date_to']) != ''){
            $date_to_parse = \DateTime::createFromFormat('d-m-Y H:i:s',$params['date_to'].' 23:59:59');
            if($date_to_parse != null){
                $date_to = $date_to_parse->getTimestamp();
                $where->lessThanOrEqualTo('date',$date_to);
            }
        }
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
            }elseif($params['orderby'] == 'date'){
                $orderby = "m.date";
            }else{
                $orderby = "m.product_id";
            }
            $select->order("$orderby $dir");
        }else{
            $select->order("m.product_id DESC");
        }
        $select->where($where);
        $select->order(array('m.date DESC', 'm.product_id DESC', "m.name ASC"));
        return $select;
    }

    /**
     * Filter by matching
     * @param $model
     * @param $condition
     * @param int $limit
     * @param null $country
     * @return null
     */
    public function getRowsMatching($model,$condition,$limit = 3,$country = null)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from(array('m' => $this->tableGateway->table));
        $where = new Where();
        if(!empty($country)){
            $select->join(array('r' => 'recycler'),'m.recycler_id = r.recycler_id',array('country_id'));
            $where->equalTo('r.country_id',$country);
        }
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->equalTo('m.lastest',1);
        $where->greaterThan('m.recycler_id',1);
        $select->where($where);
        $select->order("m.product_id DESC");
        $selectString = $sql->getSqlStringForSqlObject($select);
        $selectStringFinal = "SELECT * FROM ($selectString) AS tmp_table GROUP BY recycler_id";
        if($limit !== false){
            $selectStringFinal .= " LIMIT $limit";
        }
        $result = $adapter->query($selectStringFinal, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }

    /**
     * Filter by date range
     */
    public function getRowsMatchingByDate($model,$condition,$limit = 3,$country = null,$date_from = null,$date_to = null)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('r' => 'recycler'),'m.recycler_id = r.recycler_id',array('country_id','recycler_name' => 'name'));
        $where = new Where();
        if(!empty($country)){
            $where->equalTo('r.country_id',$country);
        }
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->greaterThan('m.recycler_id',1);
        if(!empty($date_from)){
            $where->greaterThanOrEqualTo('date',$date_from);
        }
        if(!empty($date_to)){
            $where->lessThanOrEqualTo('date',$date_to);
        }
        $select->where($where);
        $select->order("m.product_id DESC");
        $selectString = $sql->getSqlStringForSqlObject($select);
        /*Debug::dump($selectString);die;*/
        $selectStringFinal = "SELECT * FROM ($selectString) AS tmp_table GROUP BY recycler_id";
        if($limit !== false){
            $selectStringFinal .= " LIMIT $limit";
        }
        $result = $adapter->query($selectStringFinal, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    /**
     * @param $model
     * @param $condition
     * @param $start
     * @param $end
     * @return null
     */
    public function getRowsByModelInTimeRange($model,$condition,$start,$end)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->equalTo('lastest',1);
        $where->greaterThan('recycler_id',1);
        if(!empty($start)){
            $where->greaterThanOrEqualTo('date',$start);
        }
        if(!empty($end)){
            $where->lessThanOrEqualTo('date',$end);
        }else{
            $where->lessThanOrEqualTo('date',time());
        }
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }

    /**
     * @param $recycler_id
     * @param $model
     * @param $condition
     * @param $start
     * @param $end
     * @return null
     */
    public function getAllProductsHistoricalByRecycler($recycler_id,$model,$condition,$start,$end)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->equalTo('recycler_id',$recycler_id);
        if(!empty($start)){
            $where->greaterThanOrEqualTo('date',$start);
        }
        if(!empty($end)){
            $where->lessThanOrEqualTo('date',$end);
        }else{
            $where->lessThanOrEqualTo('date',time());
        }
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    /**
     * @param $model
     * @param $condition
     * @param $start
     * @param $end
     * @return array|null
     */
    public function getAllRecyclersOfModel($model,$condition,$start,$end)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table)->columns(array('recycler_id'));
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        if(!empty($start)){
            $where->greaterThanOrEqualTo('date',$start);
        }
        if(!empty($end)){
            $where->lessThanOrEqualTo('date',$end);
        }else{
            $where->lessThanOrEqualTo('date',time());
        }
        $select->where($where);
        $select->group('recycler_id');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        $data = array();
        foreach($result as $row){
            $data[] = $row->recycler_id;
        }
        return $data;
    }
    /**
     * @param $model
     * @param $condition
     * @return null
     */
    public function getRowsByModel($model,$condition)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }

    /**
     * @param $model
     * @param $condition
     * @return null|\Zend\Db\Sql\Select
     */
    public function getRowsByModelQuery($model,$condition)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        return $select;
    }
    /**
     * @param $model
     * @param $condition
     * @param $limit
     * @return Resultset
     */
    public function getProductsByModel($model,$condition,$limit)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }

    /**
     * @param $model
     * @param $condition
     * @return null|string
     */
    public function getSSACurrency($model,$condition)
    {
        if(!$model || !$condition){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where(array('condition_id' => (int) $condition,'model' => trim($model),'recycler_id' => 1));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if(!$row){
            return null;
        }
        return (!empty($row->currency)) ? $row->currency : 'HKD';
    }
    /**
     * @param $model
     * @param $condition
     * @return float
     */
    public function getSSAPrice($model,$condition)
    {
        if(!$model || !$condition){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where(array('condition_id' => (int) $condition,'model' => trim($model),'recycler_id' => 1));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if(!$row){
            return null;
        }
        $exchange = $this->getViewHelper('exchange')->getCurrentExchangeOfCurrency($row->currency);
        $price = (float) $row->price;
        $ssa = $price/$exchange;
        return (float) $ssa;
        /*return (float)round($ssa,2,PHP_ROUND_HALF_UP);*/
    }
    public function getTopPriceProductsByModel($model,$condition,$limit = 3,$country = null)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        if(!empty($country)){

            $select->join(array('r' => 'recycler'),'m.recycler_id = r.recycler_id',array('country_id'));
            $where->equalTo('r.country_id',$country);
        }
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->greaterThan('m.recycler_id',1);
        $select->where($where);
        $select->order('m.price DESC');
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    /**
     * @param $model
     * @return $result
     */
    public function getHighestPrice($model)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('model',$model);
        $select->where($where);
        $select->order('price DESC')->limit(1);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        $row = $result->current();
        if(empty($row)){
            return null;
        }
        return $row;
    }
    public function getProductsByCountry($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->greaterThan('r.recycler_id',1);
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getProductsByCountryQuery($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'))->columns(array('rid' => 'recycler_id'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->greaterThan('r.recycler_id',1);
        $select->where($where);
        $select->group('m.product_id');
        return $select;
    }
    public function getProductsByCountryAndModelAndCondition($country_id,$model,$condition)
    {
        if(!$country_id || $country_id == null || !$model || $model==null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'))->columns(array('rid' => 'recycler_id'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->equalTo('m.lastest',1);
        $where->greaterThan('r.recycler_id',1);
        $select->where($where);
        $select->order('product_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $selectStringFinal = "SELECT * FROM ($selectString) AS tmp_table GROUP BY recycler_id";
        $result = $adapter->query($selectStringFinal, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getProductsByCountryAndModelAndConditionQuery($country_id,$model,$condition)
    {
        if(!$country_id || $country_id == null || !$model || $model==null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->greaterThan('r.recycler_id',1);
        $select->where($where);
        return $select;
    }
    /**
     * @param $ids
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvailabeRecyclerProducts($ids)
    {
        if($ids == null){
            $rowset = $this->tableGateway->select();
        }else{
            $rowset = $this->tableGateway->select(array('product_id' => $ids));
        }
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }

    /**
     * Check temp id saved
     * @param $temp_id
     * @return bool
     */
    public function hasTempId($temp_id)
    {
        $rowset = $this->tableGateway->select(array('temp_id' => $temp_id));
        $row = $rowset->current();
        if(!$row){
            return false;
        }
        return true;
    }
    public function delete($id)
    {
        return $this->tableGateway->delete(array('product_id' => $id));
    }
}
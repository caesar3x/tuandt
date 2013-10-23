<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/17/13
 */
namespace Core\Soap;

use Core\Model\RecyclerProduct;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;
use Zend\Session\Container;

class Server1
{
    protected $_authen;

    protected $_adapter;

    protected $_loggedin = false;

    protected $_recycler_product_table = 'recycler_product';

    public function __construct()
    {
        $authen = new Authentication();
        $this->_authen = $authen;
        $this->_adapter = $authen->getAdapter();
    }

    /**
     * @param array $input
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function setPrice(Array $input)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!isset($input['model']) || !isset($input['price']) || !isset($input['currency']) || !isset($input['datetime']))
        {
            return false;
        }
        if(!is_numeric($input['price'])){
            return false;
        }

        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $where->equalTo('model' , $input['model']);
        $where->equalTo('currency' , $input['currency']);
        if(is_string($input['datetime'])){
            $parseDateFrom = \DateTime::createFromFormat('d-m-Y H:i:s',$input['datetime']." 00:00:00");
            $parseDateTo = \DateTime::createFromFormat('d-m-Y H:i:s',$input['datetime']." 23:59:59");
            $timeFrom = $parseDateFrom->getTimestamp();
            $timeTo = $parseDateTo->getTimestamp();
            $where->between('date',$timeFrom, $timeTo);
        }
        $update = $sql->update($this->_recycler_product_table);
        $update->set(array('price' => $input['price']));
        $update->where($where);
        $selectString = $sql->getSqlStringForSqlObject($update);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return (bool) $result->getAffectedRows();
    }
    /**
     * @param $product_id
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function delete($product_id)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!is_numeric($product_id)){
            return false;
        }
        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $where->equalTo('product_id',$product_id);
        $delete = $sql->delete($this->_recycler_product_table);
        $delete->where($where);
        $selectString = $sql->getSqlStringForSqlObject($delete);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return (bool) $result->getAffectedRows();
    }
    /**
     * @param $product_id
     * @param $model
     * @param array $input
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function update($product_id,$model,Array $input)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!is_numeric($product_id)|| !$model || empty($model)){
            return false;
        }
        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $where->equalTo('product_id',$product_id);
        $where->equalTo('model',$model);
        $update = $sql->update($this->_recycler_product_table);
        $update->where($where);
        $complex = new ComplexRecyclerProductTypeB();
        $exchange = (array)$complex->exchangeArray($input);
        $update->set($exchange);
        $selectString = $sql->getSqlStringForSqlObject($update);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return (bool) $result->getAffectedRows();
    }
    /**
     * @param array $data
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function create(Array $input)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!is_array($input)){
            return false;
        }
        $recycler_product = new ComplexRecyclerProductType();
        $recycler_product->exchangeArray($input);
        $id = $recycler_product->product_id;
        if($id != 0){
            return false;
        }
        $adapter = $this->_adapter;
        $data = (array) $recycler_product;
        $sql = new Sql($adapter);
        $insert = $sql->insert($this->_recycler_product_table);
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return (int) $result->getGeneratedValue();
    }

    /**
     * @param $model
     * @param array $input
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function getPrice($model,Array $input)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!is_string($model)){
            return null;
        }
        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->_recycler_product_table);
        $complex = new ComplexRecyclerProductTypeB();
        $exchange = (array)$complex->exchangeArray($input);
        $select->where(array('model' => $model));
        $select->where($exchange);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        $data = array();
        foreach( $result->toArray() as $row){
            $data[$row['product_id']] = $row['price'];
        }
        return $data;
    }
    /**
     * @param array $filters
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function get(Array $filters)
    {
        if(!$this->_authen->isLoggedin()){
            return false;
        }
        if(!is_array($filters)){
            return null;
        }
        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->_recycler_product_table);
        $where = new Where();
        $dataFinal = $filters;
        if(isset($filters['updated_at'])){
            $parseDateFrom = \DateTime::createFromFormat('d-m-Y H:i:s',$filters['date']." 00:00:00");
            $parseDateTo = \DateTime::createFromFormat('d-m-Y H:i:s',$filters['date']." 23:59:59");
            $timeFrom = $parseDateFrom->getTimestamp();
            $timeTo = $parseDateTo->getTimestamp();
            $where->between('date',$timeFrom, $timeTo);
            unset($dataFinal['date']);
        }
        if(isset($filters['product_name'])){
            $where->equalTo('name',$filters['product_name']);
        }
        $filter_fields = array('recycler_id','product_id','type_id','brand_id','condition_id');
        foreach($filter_fields as $field){
            if(array_key_exists($field,$filters)){
                $where->equalTo($field,$filters[$field]);
            }
        }
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result->toArray();
    }
    /**
     * @param  string $username
     * @param  string $password
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function login($username,$password)
    {
        if(!$username || !$password){
            $this->_authen->unsetUserSession();
            return false;
        }
        $adapter = $this->_adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->_authen->_soap_users_table);
        $select->where(array('username' => $username,'password' => $password));
        $select->limit(1);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if(!$row){
            $this->_authen->unsetUserSession();
            return false;
        }
        $session = $this->_authen->getUserSession();
        $session->soap_username = $username;
        $session->soap_password = $password;
        $session->soap_logged = true;
        $this->_loggedin = true;
        $this->_username = $username;
        $this->_password = $password;
        return $this->_loggedin;
    }
}
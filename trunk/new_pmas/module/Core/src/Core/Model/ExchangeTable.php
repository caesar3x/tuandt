<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\Model;

use Zend\Db\Sql\Select;

class ExchangeTable extends AbstractModel
{
    /**
     * Get row by country id
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getRowByCountry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('country_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Exchange $entry)
    {
        $data = (array) $entry;
        return $this->tableGateway->insert($data);
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getLastExchange()
    {
        $rowset = $this->tableGateway->select(function (Select $select){
            $select->order('time DESC')->limit(5);
        });
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }

    /**
     * @return array|null
     */
    public function getAvailableCurrencies()
    {
        $rowset = $this->tableGateway->select();
        if($rowset->count() <= 0){
            return null;
        }
        $data = array();
        foreach($rowset as $row){
            $data[$row->currency] = $row->currency;
        }
        return $data;
    }
}
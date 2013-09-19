<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\Model;

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
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;

class LanguagesTable extends AbstractModel
{
    public function save(Languages $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('id' => $id));
    }

    /**
     * @param $code
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getByCode($code)
    {
        $rowset = $this->tableGateway->select(array('lang_code' => $code));
        $row = $rowset->current();
        if(!$row){
            return null;
        }
        return $row;
    }

    /**
     * @param $code
     * @return bool
     */
    public function has_lang($code)
    {
        $rowset= $this->getByCode($code);
        if(empty($rowset)){
            return false;
        }
        return true;
    }

    /**
     * @return null
     */
    public function getLanguages()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->order('sort_order ASC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getLang($id)
    {
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if(!$row){
            return null;
        }
        return $row;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;

class DeviceTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('device_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Device $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->device_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('device_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('device_id' => $id));
    }

    /**
     * Get tdm device info by device_id
     * @param $device_id
     * @return null
     */
    public function getTdmDevice($device_id)
    {
        if($device_id == null){
            return;
        }
        $tdmDeviceTable = $this->serviceLocator->get('TdmDeviceTable');
        $deviceEntry = $tdmDeviceTable->getEntry($device_id);
        if(!empty($deviceEntry)){
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->from(array('m' => $this->tableGateway->table));
            $select->join(array('td' => $tdmDeviceTable->tableGateway->table),'m.device_id = td.device_id');
            $select->where(array('m.deleted' => 0,'m.device_id' => $device_id));
            $select->order('m.device_id DESC');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        }
        return null;
    }

    /**
     * Get all available tdm devices
     * @param string $order
     * @return mixed
     */
    public function getAvaiableTdmDevices($order = 'DESC')
    {
        $tdmDeviceTable = $this->serviceLocator->get('TdmDeviceTable');
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('td' => $tdmDeviceTable->tableGateway->table),'m.device_id = td.device_id');
        $select->where(array('m.deleted' => 0));
        $select->order('m.device_id '.$order);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class CountryTable extends AbstractModel
{
    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('country_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Country $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->country_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('country_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('country_id' => $id));
    }

    /**
     * Clear all data has country
     * @param $id
     * @return bool
     */
    public function clearCountry($id)
    {
        if($this->deleteEntry($id)){
            $success = true;
            $tdmProduct = $this->serviceLocator->get('TdmProductTable');
            if($tdmProduct->checkAvailabelCountry($id)){
                $success = $success && $tdmProduct->clearCountry($id);
            }
            $recyclerTable = $this->serviceLocator->get('RecyclerTable');
            if($recyclerTable->checkAvailabelCountry($id)){
                $success = $success && $recyclerTable->clearCountry($id);
            }
            if($success == false){
                $this->rollBackDeleteEntry($id);
            }
            return $success;
        }else{
            return false;
        }
    }

    /**
     * Rollback delete entry
     * @param $id
     * @return int
     */
    public function rollBackDeleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 0),array('country_id' => $id));
    }
    /**
     * Get country name by country id
     * @param $id
     * @return mixed
     */
    public function getCountryNameById($id)
    {
        $entry = $this->getEntry($id);
        if(!empty($entry) && $entry != null){
            return $entry->name;
        }
        return ;
    }

    /**
     * @param $country
     * @return array|\ArrayObject|null
     */
    public function getEntryByName($country)
    {
        $country  = (string) $country;
        $rowset = $this->tableGateway->select(array('name' => $country));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    /**
     * @param $currency
     * @return null
     */
    public function getCurrencyIdByName($currency)
    {
        $currency  = (string) $currency;
        $rowset = $this->tableGateway->select(array('currency' => $currency));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row->country_id;
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkCountryAvailable($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('country_id' => $id,'deleted' => 0));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }
}
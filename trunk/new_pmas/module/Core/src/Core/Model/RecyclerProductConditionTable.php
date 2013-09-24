<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\Model;
class RecyclerProductConditionTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('condition_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(RecyclerProductCondition $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->condition_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('condition_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }

    /**
     * Delete
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('condition_id' => $id));
    }
    /**
     * Rollback delete
     * @param $id
     * @return int
     */
    public function rollbackDeleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 0),array('condition_id' => $id));
    }
    /**
     * Clear data which has condition
     * @param $id
     * @return bool
     */
    public function clearRecyclerCondition($id)
    {
        if($this->deleteEntry($id)){
            $success = true;
            $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
            if($recyclerProductTable->checkHasRowContainConditionId($id)){
                $success = $success && $recyclerProductTable->deleteByCondition($id);
            }
            if($success == false){
                $this->rollbackDeleteEntry($id);
            }
            return $success;
        }else{
            return false;
        }
    }
    /**
     * @param $name
     * @return array|\ArrayObject|null
     */
    public function getEntryByName($name)
    {
        $name  = (string) $name;
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
}
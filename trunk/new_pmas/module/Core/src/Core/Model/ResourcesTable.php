<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class ResourcesTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('resource_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Resources $entry)
    {
        $data = array(
            'hidden' => $entry->hidden,
            'deleted' => $entry->deleted,
            'sort_order' => $entry->sort_order,
            'name'  => $entry->name,
            'path'  => $entry->path,
        );

        $id = (int)$entry->resource_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('resource_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }

    /**
     * Get all resources avaiable : no deleted, no hidden
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvaiableResources()
    {
        $rowset = $this->tableGateway->select(array('hidden' => 0,'deleted' => 0));
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
}
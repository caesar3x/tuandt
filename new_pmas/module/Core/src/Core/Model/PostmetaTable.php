<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;
use Core\Model\Postmeta;

class PostmetaTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('meta_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Postmeta $entry)
    {
        $data = array(
            'post_id' => $entry->post_id,
            'meta_key'  => $entry->meta_key,
            'meta_value'  => $entry->meta_value
        );

        $id = (int)$entry->meta_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('meta_id' => $id));
            } else {
                throw new \Exception('Dữ liệu không tồn tại.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('meta_id' => $id));
    }
}
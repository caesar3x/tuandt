<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;
use Core\Model\Posts;

class PostsTable extends AbstractModel
{
    public function save(Posts $entry)
    {
        $data = array(
            'post_author' => $entry->post_author,
            'post_date'  => $entry->post_date,
            'post_title'  => $entry->post_title,
            'post_content'  => $entry->post_content,
            'post_excerpt'  => $entry->post_excerpt,
            'post_status'  => $entry->post_status,
            'comment_status'  => $entry->comment_status,
            'post_password'  => $entry->post_password,
            'post_slug'  => $entry->post_slug,
            'post_modified'  => $entry->post_modified,
            'post_parent'  => $entry->post_parent,
            'post_type'  => $entry->post_type,
            'comment_count'  => $entry->comment_count,
        );

        $id = (int)$entry->id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Dữ liệu không tồn tại.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('id' => $id));
    }
}
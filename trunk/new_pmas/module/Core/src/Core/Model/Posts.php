<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Model;

class Posts
{
    public $id;
    public $post_author;
    public $post_date;
    public $post_title;
    public $post_content;
    public $post_excerpt;
    public $post_status;
    public $comment_status;
    public $post_password;
    public $post_slug;
    public $post_modified;
    public $post_parent;
    public $post_type;
    public $comment_count;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : 0;
        $this->post_author = (isset($data['post_author'])) ? $data['post_author'] : null;
        $this->post_date  = (isset($data['post_date'])) ? $data['post_date'] : time();
        $this->post_title  = (isset($data['post_title'])) ? $data['post_title'] : null;
        $this->post_content  = (isset($data['post_content'])) ? $data['post_content'] : null;
        $this->post_excerpt  = (isset($data['post_excerpt'])) ? $data['post_excerpt'] : null;
        $this->post_status  = (isset($data['post_status'])) ? $data['post_status'] : 'draft';
        $this->comment_status  = (isset($data['comment_status'])) ? $data['comment_status'] : 'open';
        $this->post_password  = (isset($data['post_password'])) ? $data['post_password'] : null;
        $this->post_slug  = (isset($data['post_slug'])) ? $data['post_slug'] : null;
        $this->post_modified  = (isset($data['post_modified'])) ? $data['post_modified'] : time();
        $this->post_parent  = (isset($data['post_parent'])) ? $data['post_parent'] : 0;
        $this->post_type  = (isset($data['post_type'])) ? $data['post_type'] : 'post';
        $this->comment_count  = (isset($data['comment_count'])) ? $data['comment_count'] : 0;
    }
}
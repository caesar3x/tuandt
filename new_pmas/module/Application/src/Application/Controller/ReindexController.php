<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/27/13
 */
namespace Application\Controller;

use Core\Cache\CacheSerializer;
use Core\Controller\AbstractController;
use Zend\Debug\Debug;

class ReindexController extends AbstractController
{
    public function tdmProductAction()
    {
        parent::initAction();
        /*$recyclerTable = $this->sm->get('RecyclerTable');
        Debug::dump($recyclerTable->get_all());
        die('===========');*/
        $tdmProductTable = $this->sm->get('TdmProductTable');
        $tdmProductTable->index_data();
        /*Debug::dump($entry->product_id);*/
        die('==========');
    }
}
<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/27/13
 */
namespace Application\Controller;

use Core\Controller\AbstractController;

class ReindexController extends AbstractController
{
    public function tdmProductAction()
    {
        parent::initAction();
        $tdmProductTable = $this->sm->get('TdmProductTable');
        $tdmProductTable->index_data();
        die('==========');
    }
}
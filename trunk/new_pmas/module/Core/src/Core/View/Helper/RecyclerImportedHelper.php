<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/25/13
 */
namespace Core\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class RecyclerImportedHelper extends AbstractHelper
{
    /**
 * @var $serviceLocator
 */
    protected $serviceLocator;

    /**
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param null $recycler_id
     * @return mixed
     */
    public function getAllTempProductInRecycler($recycler_id = null)
    {
        $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
        $temp_ids =  $recyclerProductTable->getAllTempIdsProduct($recycler_id);
        return $temp_ids;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function getTdmProductWithSameModel($model,$condition)
    {
        $tdmProductTable = $this->serviceLocator->get('TdmProductTable');
        $row = $tdmProductTable->getRowByModel(trim($model),$condition);
        return $row;
    }
}
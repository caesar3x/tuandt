<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Condition extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke($id = null,$tdm = true)
    {
        if($id == null){
            return $id;
        }
        if($tdm == true){
            $tdmConditionTable = $this->serviceLocator->get('TdmDeviceConditionTable');
        }else{
            $tdmConditionTable = $this->serviceLocator->get('RecyclerDeviceConditionTable');
        }
        $conditionEntry = $tdmConditionTable->getEntry($id);
        if(empty($conditionEntry)){
            return null;
        }
        return $conditionEntry->name;
    }
}